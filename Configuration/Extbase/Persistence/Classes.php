<?php
declare(strict_types=1);

return [
    \WapplerSystems\Address\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
    ],
    \WapplerSystems\Address\Domain\Model\TtContent::class => [
        'tableName' => 'tt_content',
    ],
    \WapplerSystems\Address\Domain\Model\Category::class => [
        'tableName' => 'sys_category',
        'properties' => [
            'parentcategory' => [
                'fieldName' => 'parent'
            ],
        ],
    ],
];
