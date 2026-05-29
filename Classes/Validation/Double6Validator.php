<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Validation;

/**
 *
 *
 * @package address
 *
 */
class Double6Validator
{

    public function returnFieldJS()
    {
        return '
         return parseFloat(value).toFixed(6);
      ';
    }

    public function evaluateFieldValue($value, $is_in, &$set)
    {
        if (empty($value)) {
            return null;
        }
        return sprintf('%01.6f', $value);
    }
}
