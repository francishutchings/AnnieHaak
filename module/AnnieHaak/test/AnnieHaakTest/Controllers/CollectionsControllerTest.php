<?php

namespace AnnieHaakTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CollectionsControllerTest extends AbstractHttpControllerTestCase {

    protected $traceError = true;

    public function setUp() {
        $this->setApplicationConfig(
                include 'F:\xampp\htdocs\AnnieHaak\config\application.config.php'
        );
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed() {

        $collectionsTableMock = $this->getMockBuilder('AnnieHaak\Model\CollectionsTable')
                ->disableOriginalConstructor()
                ->getMock();

        $collectionsTableMock->expects($this->once())
                ->method('fetchAll')
                ->will($this->returnValue(array()));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('AnnieHaak\Model\CollectionsTable', $collectionsTableMock);

        $this->getRequest()->setMethod('GET');

        $this->dispatch('/business-admin/collections', 'GET', array('PHPSESSID' => '4kcvh7a124sbborql56tkntog7'));
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('AnnieHaak');
        $this->assertControllerName('AnnieHaak\Controller\Collections');
        $this->assertControllerClass('CollectionsController');
        $this->assertMatchedRouteName('business-admin/collections');
    }

}
