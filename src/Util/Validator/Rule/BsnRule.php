<?php

namespace App\Util\Validator\Rule;

use App\Util\Validator\Translator;
use Illuminate\Contracts\Validation\Rule;

class BsnRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $testBsn = strlen($value) < 9 ? '0' . $value : $value;
        $result  = 0;

        $products   = range(9, 2);
        $products[] = -1;

        foreach (str_split($testBsn) as $i => $char) {
            $result += (int)$char * $products[$i];
        }

        return $result % 11 === 0;
    }

    public function message(): array|string
    {
        return Translator::translate('validation.bsn');
    }
}
