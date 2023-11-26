<?php

return [
    'validation' => [
        'required'         => 'Input is required',
        'min'              => 'Input is too short. Minimum is: {{ min }}',
        'max'              => 'Input is too long. Maximum is: {{ max }}',
        'lessThan'         => 'Input is too large. Must be less than: {{ lessThan }}',
        'lessThanEqual'    => 'Input is too large. Must be less than or equal to: {{ lessThanEqual }}',
        'greaterThan'      => 'Input is too small. Must be greater than: {{ greaterThan }}',
        'greaterThanEqual' => 'Input is too small. Must be greater than or equal to: {{ greaterThanEqual }}',
        'dateBefore'       => 'Date is not valid',
        'dateAfter'        => 'Date is not valid',
        'bsn'              => 'Input is not a valid BSN number',
        'integer'          => 'Input is not an integer.',
        'iban'             => 'Input is not a valid IBAN.',
        'date'             => 'Input is not a valid date.',
        'file'             => 'Uploaded file is not valid.',
        'bool'             => 'Input is not a boolean',
        'size'             => 'File is to large. Max: {{ allowedSize }}',
        'numeric'          => 'Input is not numeric.',
        'mimetypes'        => 'Uploaded file type is not permitted.',
        'includes'         => 'Input value does not exist within the list of approved values',
        'excludes'         => 'Input value is not permitted',
        'intersects'       => 'Input list is not a subset of the list of approved values',
        'email'            => 'Input value is not a valid email adress.',
        'equals'           => 'Input is not the same',
        'passwordpolicy'   => 'Your password is not strong enough. It has to contain 1 special character, 1 small letter, 1 big letter, at least 13 characters short and maximum 255 characters long.',
        'hasNoScriptTags'  => 'Input cannot contain script tags'
    ],
];
