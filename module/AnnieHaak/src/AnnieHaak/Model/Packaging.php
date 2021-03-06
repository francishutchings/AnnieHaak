<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Packaging implements InputFilterAwareInterface {

    public $PackagingID;
    public $PackagingName;
    public $PackagingUnitCost;
    public $PackagingCode;
    public $PackagingType;
    public $PackagingQty;
    public $SubtotalPackaging;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->PackagingID = (!empty($data['PackagingID'])) ? $data['PackagingID'] : null;
        $this->PackagingName = (!empty($data['PackagingName'])) ? $data['PackagingName'] : null;
        $this->PackagingUnitCost = (!empty($data['PackagingUnitCost'])) ? $data['PackagingUnitCost'] : null;
        $this->PackagingCode = (!empty($data['PackagingCode'])) ? $data['PackagingCode'] : null;
        $this->PackagingType = (!empty($data['PackagingType'])) ? $data['PackagingType'] : null;
        $this->PackagingQty = (!empty($data['PackagingQty'])) ? $data['PackagingQty'] : null;
        $this->SubtotalPackaging = (!empty($data['SubtotalPackaging'])) ? $data['SubtotalPackaging'] : null;
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
                'name' => 'PackagingID',
                'required' => true,
                'validators' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'PackagingName',
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
                'name' => 'PackagingUnitCost',
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
                'name' => 'PackagingCode',
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
                'name' => 'PackagingType',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
