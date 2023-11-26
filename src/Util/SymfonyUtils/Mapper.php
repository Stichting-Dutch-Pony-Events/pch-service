<?php

namespace App\Util\SymfonyUtils;

use App\Util\SymfonyUtils\Attribute\MapsMany;
use App\Util\SymfonyUtils\Attribute\ResolveType;
use App\Util\SymfonyUtils\Exception\UnknownPropertyException;
use App\Util\SymfonyUtils\Exception\WrongTypeException;
use App\Util\SymfonyUtils\Interface\TypeResolverInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class Mapper
{
    private static array $properties = [];
    /**
     * @var TypeResolverInterface[]
     */
    private array $typeResolvers;

    /**
     * @param TypeResolverInterface[] $typeResolvers
     */
    public function __construct(array $typeResolvers = [])
    {
        $this->typeResolvers = $typeResolvers;
    }

    /**
     * @template T
     *
     * @param array|Collection $entityObjects
     * @param string|null $viewClass
     * @param TypeResolverInterface[] $typeResolvers
     * @param string|null $supportedTypeResolverInterface
     * @return T
     * @throws ReflectionException
     * @throws WrongTypeException
     */
    public static function mapMany(
        array|Collection $entityObjects,
        ?string          $viewClass = null,
        array            $typeResolvers = [],
        ?string          $supportedTypeResolverInterface = null
    ): array {
        $mapper  = new static($typeResolvers);
        $results = [];

        foreach ($entityObjects as $entityObject) {
            $type = $viewClass;
            if (!$viewClass && ($resolver = $mapper->getSupportedTypeResolver(
                    $entityObject,
                    $supportedTypeResolverInterface
                ))) {
                $type = $resolver->determineType($entityObject);
            } elseif (!$viewClass) {
                throw new WrongTypeException(
                    "'Mapmany' called without ViewClass, but no supported resolver found"
                );
            }

            $results[] = $mapper->map(
                $entityObject,
                $type
            );
        }

        return $results;
    }

    /**
     * @throws ReflectionException
     */
    private function map(mixed $object, string $viewClassName)
    {
        // entity can be `null` for example in a 1-1 relation where parent has no child-item (yet)
        if (is_null($object)) {
            return null;
        }

        if (!class_exists($viewClassName)) {
            throw new InvalidArgumentException(sprintf('Non existant class %s.', $viewClassName));
        }

        $reflectedView = new ReflectionClass($viewClassName);

        if (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            if ($reflectedView->isEnum()) {
                if (is_string($object) || is_int($object)) {
                    return $viewClassName::from($object);
                }
                return $object;
            }
        }

        $viewInstance = $reflectedView->newInstanceWithoutConstructor();

        foreach ($reflectedView->getProperties() as $property) {
            $type      = $property->getType();
            $isBuiltin = $type instanceof ReflectionNamedType && $type->isBuiltin();

            if ($property->getType() === null) {
                throw new InvalidArgumentException(
                    "Property {$property->getName()} of $viewClassName doesn't have a type, property type is required"
                );
            }

            $viewInstance->{$property->getName()} = match (true) {
                $this->hasMapsMany($property) => $this->handleMapsMany($object, $property, $viewInstance),
                $isBuiltin => $this->handleBuiltIn(
                    $object,
                    $property,
                    $viewInstance
                ),
                default => $this->handleUnknown($object, $property, $viewInstance)
            };
        }

        return $viewInstance;
    }

    private function getCustomTypeResolver(ReflectionProperty $property): ?ResolveType
    {
        if (empty($property->getAttributes(ResolveType::class))) {
            return null;
        }

        return current($property->getAttributes(ResolveType::class))->newInstance();
    }


    private function hasMapsMany(ReflectionProperty $property): bool
    {
        return count($property->getAttributes(MapsMany::class)) > 0;
    }

    /**
     * @throws ReflectionException
     */
    private function handleMapsMany(mixed $object, ReflectionProperty $property, mixed $viewInstance)
    {
        $typeName              = $property->getType()->getName();
        $reflectedPropertyType = null;
        $isCollection          = false;

        $propertyValue = $this->getPropertyValue($object, $property, $viewInstance) ?? [];

        // Older views could be type hinted to the Collection Interface
        // Return normal array collection to prevent type conflicts.
        if ($typeName === Collection::class) {
            return new ArrayCollection($propertyValue);
        }

        if ($typeName !== 'array') {
            $reflectedPropertyType = new ReflectionClass($typeName);
            $isCollection          = $reflectedPropertyType->implementsInterface(Collection::class);
        }

        return $isCollection ? $reflectedPropertyType->newInstance($propertyValue) : $propertyValue;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function getPropertyValue(mixed $object, ReflectionProperty $property, mixed $viewInstance)
    {
        $getter              = null;
        $propertyValue       = null;
        $pascalCasedProperty = ucfirst($property->getName());

        if (is_object($object)) {
            if (method_exists($object, "get$pascalCasedProperty")) {
                $getter = "get$pascalCasedProperty";
            } elseif (method_exists($object, "is$pascalCasedProperty")) {
                $getter = "is$pascalCasedProperty";
            } elseif (method_exists($object, "has$pascalCasedProperty")) {
                $getter = "has$pascalCasedProperty";
            }

            if ($getter) {
                $propertyValue = $object->{$getter}();
            } elseif (property_exists($object, $property->getName())) {
                $propertyValue = $object->{$property->getName()};
            }
        }

        if (is_array($object) && isset($object[$property->getName()])) {
            $propertyValue = $object[$property->getName()];
        }

        if ($property->getType() instanceof ReflectionUnionType && !$this->getCustomTypeResolver($property)) {
            throw new InvalidArgumentException(
                "Property {$property->getName()} of {$this->getObjectName($viewInstance )} has a union type, Mapper only supports union types in combination with a type resolver"
            );
        }

        if (is_object($propertyValue) && version_compare(PHP_VERSION, '8.1.0') >= 0) {
            if (enum_exists($propertyValue::class) &&
                ($property->getType()->getName() === 'string' || $property->getType()->getName() === 'int')) {
                $propertyValue = $propertyValue->value;
            }
        }

        // When input and output types are the same
        if (is_object($propertyValue) && $property->getType() instanceof ReflectionNamedType && get_class($propertyValue) === $property->getType()?->getName()) {
            return $propertyValue;
        }

        if (is_array($propertyValue) || $propertyValue instanceof Collection) {
            $viewClass                      = null;
            $useCustomResolver              = false;
            $supportedTypeResolverInterface = null;

            if ($propertyValue instanceof Collection) {
                // Resets the indexes in case an element has been deleted from the collection
                $propertyValue = $propertyValue->getValues();
            }

            $mapsManyAttribute = current($property->getAttributes(MapsMany::class));

            if ($mapsManyAttribute) {
                /** @var MapsMany $attributeInstance */
                $attributeInstance = $mapsManyAttribute->newInstance();

                if ($attributeInstance->useTypeResolver) {
                    $useCustomResolver              = true;
                    $supportedTypeResolverInterface = $attributeInstance->supportedTypeResolverInterface;
                } else {
                    $viewClass = $attributeInstance->viewClass;
                }
            }

            if ($viewClass || $useCustomResolver) {
                try {
                    return self::mapMany(
                        $propertyValue instanceof Collection ? $propertyValue->toArray() : $propertyValue,
                        $viewClass,
                        $this->typeResolvers,
                        $supportedTypeResolverInterface
                    );
                } catch (WrongTypeException $wrongTypeException) {
                    throw new InvalidArgumentException(
                        "Typeresolver could not resolve type of property '{$property->getName()}' in class {$this->getObjectName($viewInstance)}",
                        0,
                        $wrongTypeException
                    );
                }
            }

            if ($propertyValue instanceof Collection && $property->getType()->getName() === 'array') {
                return $propertyValue->toArray();
            }
        }

        if ($customTypeResolver = $this->getCustomTypeResolver($property)) {
            $supportedTypeResolver = $this->getSupportedTypeResolver(
                $propertyValue,
                $customTypeResolver->supportedTypeResolverInterface
            );
            if (!$supportedTypeResolver) {
                throw new InvalidArgumentException(
                    "Typeresolver could not resolve type of property '{$property->getName()}' in class {$this->getObjectName($viewInstance)}"
                );
            }

            $type = $supportedTypeResolver->determineType($propertyValue);
        } else {
            $type = $property->getType()->getName();
        }

        // Will be caught by class_exists below if not intercepted here
        if ($type === DateTime::class) {
            return $this->handleDateTime($propertyValue);
        }

        if (class_exists($type)) {
            return self::mapOne($propertyValue, $type);
        }

        $getAttributeMethod = 'get' . ucfirst($property->getName());

        if (method_exists($viewInstance, $getAttributeMethod)) {
            if (!$this->hasReturnType($viewInstance, $getAttributeMethod)) {
                throw new InvalidArgumentException(
                    "Executed method in targeted view should have a returnType and actually return a value, function $getAttributeMethod of class {$this->getObjectName($viewInstance)}"
                );
            }

            return $viewInstance->{$getAttributeMethod}($object);
        }

        return $propertyValue;
    }

    /**
     * @param string $supportedTypeResolverInterface
     * @param TypeResolverInterface $typeResolver
     * @return bool
     */
    public function doesTypeResolverImplementsInterface(
        string                $supportedTypeResolverInterface,
        TypeResolverInterface $typeResolver
    ): bool {
        return isset(class_implements($typeResolver)[$supportedTypeResolverInterface]);
    }

    /**
     * @param mixed $propertyValue
     * @param string|null $supportedTypeResolverInterface
     * @return TypeResolverInterface|null
     */
    public function getSupportedTypeResolver(
        mixed   $propertyValue,
        ?string $supportedTypeResolverInterface = null
    ): ?TypeResolverInterface {
        foreach ($this->typeResolvers as $typeResolver) {
            if (
                $supportedTypeResolverInterface
                && !$this->doesTypeResolverImplementsInterface(
                    $supportedTypeResolverInterface,
                    $typeResolver
                )
            ) {
                continue;
            }

            if ($typeResolver->supports($propertyValue)) {
                return $typeResolver;
            }
        }

        return null;
    }

    /**
     * @throws ReflectionException
     */
    private function getObjectName($object): string
    {
        return (new ReflectionClass($object))->getName();
    }

    /**
     * @throws Exception
     */
    private function handleDateTime(mixed $propertyValue): DateTime|null
    {
        return match (true) {
            $propertyValue === null => null,
            is_int($propertyValue) => (new DateTime())->setTimestamp($propertyValue),
            $propertyValue instanceof DateTime => $propertyValue,
            default => new DateTime($propertyValue)
        };
    }

    /**
     * @template T
     *
     * @param class-string<T> $viewClassName
     * @param TypeResolverInterface[] $typeResolvers
     *
     * @return T
     * @throws ReflectionException
     */
    public static function mapOne(mixed $entityObject, string $viewClassName, array $typeResolvers = []): mixed
    {
        return (new static($typeResolvers))->map($entityObject, $viewClassName);
    }

    /**
     * @throws ReflectionException
     */
    private function hasReturnType($object, $method): bool
    {
        $reflectedMethod = new ReflectionMethod($object, $method);

        return !(is_null($reflectedMethod->getReturnType()) || $reflectedMethod->getReturnType()->getName() === 'void');
    }

    /**
     * @throws ReflectionException
     */
    private function handleBuiltIn(mixed $object, ReflectionProperty $property, mixed $viewInstance): mixed
    {
        $value   = $this->getPropertyValue($object, $property, $viewInstance);
        $default = $value;

        // destructure associative array
        if (is_array($object) && isset($object[$property->getName()])) {
            return $object[$property->getName()];
        }

        if (is_null($default) && $property->hasDefaultValue()) {
            return $property->getDefaultValue();
        }

        if (is_null($default) && $property->getType()->allowsNull()) {
            return null;
        }

        return match ($property->getType()->getName()) {
            'bool' => (bool)$value,
            'int' => (int)$value,
            'float' => (float)$value,
            default => $default
        };
    }

    /**
     * @throws ReflectionException
     */
    private function handleUnknown(mixed $object, ReflectionProperty $property, mixed $viewInstance): mixed
    {
        // Nested view support is built in getPropertyValue which are the same as unknown types
        $propertyValue = $this->getPropertyValue($object, $property, $viewInstance);

        if (is_null($propertyValue) && $property->hasDefaultValue()) {
            return $property->getDefaultValue();
        }

        return $propertyValue;
    }

    public static function addProperty(string $property, string $value): void
    {
        self::$properties[$property] = $value;
    }

    /**
     * @throws UnknownPropertyException
     */
    public static function getProperty(string $property)
    {
        if (!array_key_exists($property, self::$properties)) {
            throw new UnknownPropertyException(
                "Property $property does not exist in Mapper, use Mapper::addProperty to add it"
            );
        }

        return self::$properties[$property];
    }
}
