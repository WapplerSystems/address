<?php

declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Extbase\Persistence\Generic\Storage;

use TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedOrderException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\JoinInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\SelectorInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\SourceInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class Typo3DbQueryParserForAddress extends \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser
{


    protected function parseOrderings(array $orderings, SourceInterface $source)
    {
        if ($this->tableName !== 'tx_address_domain_model_address') {
            parent::parseOrderings($orderings, $source);
            return;
        }

        foreach ($orderings as $propertyName => $order) {
            if ($order !== QueryInterface::ORDER_ASCENDING && $order !== QueryInterface::ORDER_DESCENDING) {
                throw new UnsupportedOrderException('Unsupported order encountered.', 1242816074);
            }
            $className = null;
            $tableName = '';
            if ($source instanceof SelectorInterface) {
                $className = $source->getNodeTypeName();
                $tableName = $this->dataMapper->convertClassNameToTableName($className);
                $fullPropertyPath = '';
                while (str_contains($propertyName, '.')) {
                    $this->addUnionStatement($className, $tableName, $propertyName, $fullPropertyPath);
                }
            } elseif ($source instanceof JoinInterface) {
                $tableName = $source->getLeft()->getSelectorName();
            }
            if (str_contains($propertyName, 'FIELD')) {
                $this->queryBuilder->add('orderBy', $propertyName, true);
                continue;
            }
            $columnName = $this->dataMapper->convertPropertyNameToColumnName($propertyName, $className);
            if ($tableName !== '') {
                $this->queryBuilder->addOrderBy($tableName . '.' . $columnName, $order);
            } else {
                $this->queryBuilder->addOrderBy($columnName, $order);
            }
        }
    }

}
