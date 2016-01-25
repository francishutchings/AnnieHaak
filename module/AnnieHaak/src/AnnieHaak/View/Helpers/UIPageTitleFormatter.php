<?php

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIPageTitleFormatter extends AbstractHelper {

    public function __invoke($titleStr) {

        return '<div class="page-header"><h3>' . $titleStr . '</h3></div>';
    }

}
