<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class LabourItems {

    public $LabourID;
    public $LabourName;
    public $LabourUnitCost;
    public $LabourCode;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->LabourID = (!empty($data['LabourID'])) ? $data['LabourID'] : null;
        $this->LabourName = (!empty($data['LabourName'])) ? $data['LabourName'] : null;
        $this->LabourUnitCost = (!empty($data['LabourUnitCost'])) ? $data['LabourUnitCost'] : null;
        $this->LabourCode = (!empty($data['LabourCode'])) ? $data['LabourCode'] : null;
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
                'name' => 'LabourID',
                'required' => true,
                'validators' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'LabourName',
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
                'name' => 'LabourUnitCost',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'min' => 0,
                            'locale' => '<my_locale>'
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'LabourCode',
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
