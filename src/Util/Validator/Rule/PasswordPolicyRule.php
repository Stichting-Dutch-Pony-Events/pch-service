<?php

namespace App\Util\Validator\Rule;

use App\Util\Validator\Translator;
use Illuminate\Contracts\Validation\Rule;

class PasswordPolicyRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return (
            preg_match("/[a-z]/", $value) &&
            preg_match("/[A-Z]/", $value) &&
            preg_match("/[0-9]/", $value) &&
            preg_match('/[\!\@#$%^&\*\(\)\-_\+=\.,\/\?\><\\\]/', $value) &&
            strlen($value) <= 255 &&
            strlen($value) >= 13
        );
    }

    public function message(): string
    {
        return Translator::translate('validation.passwordpolicy');
    }
}
