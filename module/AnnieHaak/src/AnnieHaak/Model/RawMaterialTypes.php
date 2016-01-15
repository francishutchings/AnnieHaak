<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RawMaterialTypes implements InputFilterAwareInterface {
    /*
      RMTypeID` INT(11) NOT NULL AUTO_INCREMENT,
      RMTypeName` VARCHAR(255) NOT NULL
     */

    public $RMTypeID;
    public $RMTypeName;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->RMTypeID = (!empty($data['RMTypeID'])) ? $data['RMTypeID'] : null;
        $this->RMTypeName = (!empty($data['RMTypeName'])) ? $data['RMTypeName'] : null;
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
                'name' => 'RMTypeID',
                'required' => true,
                'validators' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'RMTypeName',
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
