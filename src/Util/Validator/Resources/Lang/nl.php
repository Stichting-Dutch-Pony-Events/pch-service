<?php

return [
    'validation' => [
        'required'         => 'Input is verplicht',
        'min'              => 'Input is te kort. Min is: {{ min }}',
        'max'              => 'Input is te lang. Max is: {{ max }}',
        'lessThan'         => 'Input is te groot. Moet kleiner zijn dan: {{ lessThan }}',
        'lessThanEqual'    => 'Input is te groot. Moet kleiner zijn dan: {{ lessThanEqual }}',
        'greaterThan'      => 'Input is te klein. Moet groter zijn dan: {{ greaterThan }}',
        'greaterThanEqual' => 'Input is te klein. Moet groter zijn dan: {{ greaterThanEqual }}',
        'dateBefore'       => 'Datum is niet valide',
        'dateAfter'        => 'Datum is niet valide',
        'bsn'              => 'Input is geen valide bsn',
        'integer'          => 'Input is geen integer.',
        'iban'             => 'Input is geen geldige IBAN.',
        'date'             => 'Input is geen valide datum.',
        'file'             => 'Geen valide file.',
        'bool'             => 'Input is geen boolean',
        'size'             => 'Bestand is te groot. Max: {{ allowedSize }}',
        'numeric'          => 'Input is niet numeriek.',
        'mimetypes'        => 'Type bestand niet toegestaan.',
        'includes'         => 'Input is niet in lijst toegestane waarden',
        'excludes'         => 'Input is niet toegestaan',
        'intersects'       => 'Lijst is geen deelverzameling van lijst toegestane waarden',
        'email'            => 'Input is geen valide email.',
        'equals'           => 'Input is niet gelijk',
        'passwordpolicy'   => 'Uw wachtwoord is niet sterk genoeg. Moet bevatten: 1 speciale teken, 1 kleine letter, 1 grote letter, minstens 13 kort, maximaal 255 lang.',
        'hasNoScriptTags'  => 'Input mag geen script tags bevatten'
    ],
];
