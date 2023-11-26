<?php

namespace App\Util\Validator\Rule;

use App\Util\Validator\Translator;
use Iban\Validation\Validator as IbanValidator;
use Illuminate\Contracts\Validation\Rule;

class IbanRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return (new IbanValidator())->validate($value);
    }

    public function message(): string
    {
        return Translator::translate('validation.iban');
    }
}
