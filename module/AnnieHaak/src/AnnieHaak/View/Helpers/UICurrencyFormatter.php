<?php

/*
 * Formats float to currency
 */

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UICurrencyFormatter extends AbstractHelper {

    public function __invoke($num, $fractional = false) {

        $process = true;

        if (!gettype($num) == 'double' && !gettype($num) == 'integer') {
            $process = false;
            return 'ERROR - Value passed is not a number!';
        }

        if ($process) {

            if ($fractional) {
                $num = sprintf('%.2f', $num);
            }
            while (true) {
                $replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $num);
                if ($replaced != $num) {
                    $num = $replaced;
                } else {
                    break;
                }
            }
        }

        return '&pound;' . $num;
    }

}
