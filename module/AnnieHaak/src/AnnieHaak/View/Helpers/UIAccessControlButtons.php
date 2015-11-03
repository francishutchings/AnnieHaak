<?php

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIAccessControlButtons extends AbstractHelper {

    public function __invoke($roleLevel, $buttonType, $buttonAction, $buttonLabel) {

        $returnHTML = '';
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
                        $returnHTML = '<a class="btn btn-success" href="' . $buttonAction . '"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;' . $buttonLabel . '</a>';
                    } else {
                        $returnHTML = '<a class="btn btn-info" href="#"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Not enough permissions to add</a>';
                    }
                    break;
                case 'edit':
                    if ($roleLevel < 3) {
                        $returnHTML = '<a class="btn btn-warning" href="' . $buttonAction . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;' . $buttonLabel . '</a>';
                    } else {
                        $returnHTML = '<a class="btn btn-info" href="' . $buttonAction . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;View details only</a>';
                    }
                    break;
                case 'delete':
                    if ($roleLevel == 1) {
                        $returnHTML = '<a class="btn btn-danger" href="' . $buttonAction . '"><span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;' . $buttonLabel . '</a>';
                    } else {
                        $returnHTML = '&nbsp;';
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
