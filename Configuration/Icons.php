<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-address-type-default' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:address/Resources/Public/Icons/address_domain_model_address.svg',
    ],
    'ext-address-type-person' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:address/Resources/Public/Icons/address_domain_model_address_person.svg',
    ],
    'ext-address-type-company' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:address/Resources/Public/Icons/address_domain_model_address_company.svg',
    ],
    'ext-address-tag' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:address/Resources/Public/Icons/address_domain_model_tag.svg',
    ],
];
