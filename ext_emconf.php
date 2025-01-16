<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'LIA Hyphenator',
    'description' => 'Wrapper for Word-Hyphenation for PHP based on the TeX-Hyphenation algorithm',
    'category' => 'fe',
    'author' => 'LOUIS TYPO3 Developers',
    'author_company' => 'LOUIS INTERNET',
    'author_email' => 'info@dev.louis.info',
    'state' => 'stable',
    'version' => '1.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
