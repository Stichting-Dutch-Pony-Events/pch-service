<?php

namespace App\Util\Validator\Rule;

use App\Util\Validator\Translator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\Validator;
use Symfony\Component\Uid\Uuid;

class UuidExistsRule implements ValidatorAwareRule, Rule
{
    private Validator $validator;

    public function __construct(
        private string $table,
        private string $column
    ) {
    }

    public function passes($attribute, $value)
    {
        $count = $this->validator->getPresenceVerifier()->getCount(
            $this->table,
            $this->column,
            (Uuid::fromString($value)->toBinary())
        );

        return $count > 0;
    }

    public function message()
    {
        return Translator::translate('validation.exists');
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;
    }
}
