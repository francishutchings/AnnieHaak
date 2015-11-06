<?php

/*
 * Formats float to currency
 */

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIPageTitleFormatter extends AbstractHelper {

    public function __invoke($titleStr) {

        return '<div class="page-header"><h2>' . $titleStr . '</h2></div>';
    }

}
