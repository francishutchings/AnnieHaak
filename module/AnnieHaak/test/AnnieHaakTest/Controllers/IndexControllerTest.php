<?php

namespace AnnieHaakTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\Stdlib\Parameters;

class IndexControllerTest extends AbstractHttpControllerTestCase {

    protected $traceError = true;

    public function setUp() {
        $this->setApplicationConfig(
                include 'F:\xampp\htdocs\AnnieHaak\config\application.config.php'
        );
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed() {

        $this->dispatch('/');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('AnnieHaak');
        $this->assertControllerName('AnnieHaak\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('');
    }

}
