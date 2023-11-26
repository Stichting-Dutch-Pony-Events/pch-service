<?php

namespace App\Util\Validator;

use App\Util\SymfonyUtils\Mapper;
use Illuminate\Validation\Validator as IlluminateValidator;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractRequest
{
    private ?IlluminateValidator $validator = null;
    protected Request|array|null $dataToValidate = null;

    public function __construct(protected ContainerInterface $container)
    {
    }

    abstract public function rules(): array;

    abstract protected function prepareForValidation(): void;

    public function validate(Request|array $data): void
    {
        $this->dataToValidate = $data;

        if (!$this->authorize()) {
            throw new UnauthorizedHttpException("Bearer");
        }

        $this->prepareForValidation();
        $this->validator = Validator::validate($this->dataToValidate, $this->rules());
    }

    public function get(string $name): mixed
    {
        if ($this->dataToValidate instanceof Request) {
            return $this->dataToValidate->request->get($name);
        }

        return $this->dataToValidate[$name] ?? null;
    }

    public function getValidator(): ?IlluminateValidator
    {
        if ($this->validator === null) {
            throw new RuntimeException("Data needs to be validated before using the validator");
        }

        return $this->validator;
    }

    /**
     * @template T
     *
     * Map the raw data to a DTO
     *
     * @param class-string<T> $viewClassName
     *
     * @return T
     */
    public function mapInto(string $viewClassName)
    {
        return Mapper::mapOne((object)$this->getValidator()->getData(), $viewClassName);
    }

    protected function authorize(): bool
    {
        return true;
    }

    /**
     * @see https://github.com/symfony/framework-bundle/blob/6.2/Controller/AbstractController.php#L361
     */
    protected function user(): ?UserInterface
    {
        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        // @deprecated since 5.4, $user will always be a UserInterface instance
        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    protected function merge(array $alteredInputs): void
    {
        $this->setInput(
            array_replace_recursive($this->input(), $alteredInputs)
        );
    }

    private function input(): Request|array
    {
        return $this->dataToValidate instanceof Request ? $this->dataToValidate->request->all() : $this->dataToValidate;
    }

    private function setInput(array $inputs): void
    {
        if ($this->dataToValidate instanceof Request) {
            $this->dataToValidate?->request?->replace($inputs);
        } else {
            $this->dataToValidate = $inputs;
        }
    }
}
