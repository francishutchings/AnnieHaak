<?php

namespace AnnieHaakTest\Model;

use AnnieHaak\Model\Collections;
use PHPUnit_Framework_TestCase;

class CollectionsTest extends PHPUnit_Framework_TestCase {

    public function testCollectionsInitialState() {
        $collections = new Collections();

        $this->assertEquals(
                $collections->ProductCollectionID, 0, '"ProductCollectionID" should initially be 0'
        );
        $this->assertNull(
                $collections->ProductCollectionName, '"ProductCollectionName" should initially be null'
        );
        $this->assertNull(
                $collections->ProductCollectionCode, '"ProductCollectionCode" should initially be null'
        );
        $this->assertEquals(
                $collections->Current, 0, '"Current" should initially be 0'
        );
    }

    public function testExchangeArraySetsPropertiesCorrectly() {
        $collections = new Collections();
        $data = array(
            'ProductCollectionID' => 123,
            'ProductCollectionName' => 'Foo Bar',
            'ProductCollectionCode' => 'Code',
            'Current' => 1
        );

        $collections->exchangeArray($data);

        $this->assertSame(
                $data['ProductCollectionID'], $collections->ProductCollectionID, '"ProductCollectionID" was not set correctly'
        );
        $this->assertSame(
                $data['ProductCollectionName'], $collections->ProductCollectionName, '"ProductCollectionName" was not set correctly'
        );
        $this->assertSame(
                $data['ProductCollectionCode'], $collections->ProductCollectionCode, '"ProductCollectionCode" was not set correctly'
        );
        $this->assertSame(
                $data['Current'], $collections->Current, '"Current" was not set correctly'
        );
    }

    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent() {
        $collections = new Collections();
        $collections->exchangeArray(array(
            'ProductCollectionID' => 123,
            'ProductCollectionName' => 'Foo Bar',
            'ProductCollectionCode' => 'Code',
            'Current' => 1
        ));
        $collections->exchangeArray(array());

        $this->assertEquals(
                $collections->ProductCollectionID, 0, '"ProductCollectionID" should have defaulted to 0'
        );
        $this->assertNull(
                $collections->ProductCollectionName, '"ProductCollectionName" should have defaulted to null'
        );
        $this->assertNull(
                $collections->ProductCollectionCode, '"ProductCollectionCode" should have defaulted to null'
        );
        $this->assertEquals(
                $collections->Current, 0, '"Current" should have defaulted to 0'
        );
    }

    public function testGetArrayCopyReturnsAnArrayWithPropertyValues() {
        $collections = new Collections();
        $data = array(
            'ProductCollectionID' => 123,
            'ProductCollectionName' => 'Foo Bar',
            'ProductCollectionCode' => 'Code',
            'Current' => 1
        );

        $collections->exchangeArray($data);
        $copyArray = $collections->getArrayCopy();

        $this->assertSame(
                $data['ProductCollectionID'], $copyArray['ProductCollectionID'], '"ProductCollectionID" was not set correctly'
        );
        $this->assertSame(
                $data['ProductCollectionName'], $copyArray['ProductCollectionName'], '"ProductCollectionName" was not set correctly'
        );
        $this->assertSame(
                $data['ProductCollectionCode'], $copyArray['ProductCollectionCode'], '"ProductCollectionCode" was not set correctly'
        );
        $this->assertSame(
                $data['Current'], $copyArray['Current'], '"Current" was not set correctly'
        );
    }

    public function testInputFiltersAreSetCorrectly() {
        $collections = new Collections();

        $inputFilter = $collections->getInputFilter();

        $this->assertSame(4, $inputFilter->count());
        $this->assertTrue($inputFilter->has('ProductCollectionID'));
        $this->assertTrue($inputFilter->has('ProductCollectionName'));
        $this->assertTrue($inputFilter->has('ProductCollectionCode'));
        $this->assertTrue($inputFilter->has('Current'));
    }

}
