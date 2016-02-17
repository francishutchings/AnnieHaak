<?php

/*
 * Controlls button interaction governed by access level of a user
 */

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIAccessControlButtons extends AbstractHelper {

    public function __invoke($roleLevel, $buttonType, $buttonAction, $buttonLabel) {

        $returnHTML = '&nbsp;';
        $proccess = true;

        if (!gettype($roleLevel) == 'integer' || !($roleLevel > 0 && $roleLevel < 4)) {
            $returnHTML = '<h3 class="text-danger">ERROR &mdash; $roleLevel is not an integer and or is not between 1 and 3!</h3>';
            $proccess = false;
        }
        if ($proccess && !gettype($buttonType) == 'string') {
            $returnHTML = '<h3 class="text-danger">ERROR &mdash; $buttonType is not a string!</h3>';
            $proccess = false;
        }
        if ($proccess && !gettype($buttonAction) == 'string') {
            $returnHTML = '<h3 class="text-danger">ERROR &mdash; $buttonAction is not a string!</h3>';
            $proccess = false;
        }
        if ($proccess && !gettype($buttonLabel) == 'string') {
            $returnHTML = '<h3 class="text-danger">ERROR &mdash; $buttonLabel is not a string!</h3>';
            $proccess = false;
        }

        if ($proccess) {
            switch ($buttonType) {
                case 'add':
                    if ($roleLevel == 1) {
                        $returnHTML = '<a class="btn btn-success btn-sm pull-right" href="' . $buttonAction . '"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;' . $buttonLabel . '</a>';
                    }
                    break;
                case 'edit':
                    if ($roleLevel < 3) {
                        $returnHTML = '<a class="btn btn-warning btn-sm" href="' . $buttonAction . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;' . $buttonLabel . '</a>';
                    } else {
                        $returnHTML = '<a class="btn btn-default btn-sm" href="#"><span class="glyphicon glyphicon-ban-circle"></span>&nbsp;&nbsp;None</a>';
                    }
                    break;
                case 'delete':
                    if ($roleLevel == 1) {
                        $returnHTML = '<a class="btn btn-danger btn-sm" href="' . $buttonAction . '"><span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;' . $buttonLabel . '</a>';
                    }
                    break;
                default:
                    $returnHTML = '<h3 class = "text-danger">UNKNOWN ERROR!</h3>';
                    break;
            }
        }

        return $returnHTML;
    }

}
