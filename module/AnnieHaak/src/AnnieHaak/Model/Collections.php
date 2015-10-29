<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Collections {

    public $ProductCollectionID;
    public $ProductCollectionName;
    public $ProductCollectionCode;
    public $Current;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->ProductCollectionID = (!empty($data['ProductCollectionID'])) ? $data['ProductCollectionID'] : null;
        $this->ProductCollectionName = (!empty($data['ProductCollectionName'])) ? $data['ProductCollectionName'] : null;
        $this->ProductCollectionCode = (!empty($data['ProductCollectionCode'])) ? $data['ProductCollectionCode'] : null;
        $this->Current = (!empty($data['Current'])) ? $data['Current'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'ProductCollectionID',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'ProductCollectionName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'ProductCollectionCode',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'Current',
                'required' => false,
                'filters' => array(
                    array('name' => 'Boolean'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}