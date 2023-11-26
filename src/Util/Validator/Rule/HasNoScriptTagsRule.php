<?php

namespace App\Util\Validator\Rule;

use App\Util\Validator\Translator;
use Illuminate\Contracts\Validation\Rule;

class HasNoScriptTagsRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return !preg_match("/<[a-z][\s\S]*>/", $value);
    }

    public function message(): array|string
    {
        return Translator::translate('validation.hasNoScriptTags');
    }
}
