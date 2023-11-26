<?php

namespace App\Util\Validator;

use App\Util\Validator\Validation\DoctrinePresenceVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as IlluminateValidator;
use Symfony\Component\HttpFoundation\Request;

class Validator
{
    private static ?EntityManagerInterface $entityManager = null;

    /** @throws ValidationException */
    public static function validate(
        Request|array $input,
        array $rules = [],
        array $messages = [],
        array $attributes = []
    ): IlluminateValidator {
        $request = $input instanceof Request ? $input : null;

        if (count(Translator::$translations) === 0) {
            Translator::loadTranslationsByRequest($request);
        }

        Translator::mergeIlluminateValidatorTranslations($request);

        $validatorFactory = new Factory(Translator::instance());

        if (null !== self::$entityManager) {
            $validatorFactory->setPresenceVerifier(new DoctrinePresenceVerifier(self::$entityManager));
        }

        $data = $input instanceof Request ? self::getRequestData($input) : $input;

        $validator = $validatorFactory->make(
            $data,
            $rules,
            $messages,
            $attributes
        );

        $validator->validate();

        return $validator;
    }

    public static function setEntityManager(?EntityManagerInterface $entityManager): void
    {
        self::$entityManager = $entityManager;
    }

    private static function getRequestData(Request $request): array
    {
        return $request->files->all() + $request->request->all() + $request->query->all();
    }
}
