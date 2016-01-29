<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RawMaterials implements InputFilterAwareInterface {

    public $RawMaterialID;
    public $RawMaterialCode;
    public $RawMaterialName;
    public $RawMaterialUnitCost;
    public $RMSupplierID;
    public $RMSupplierName;
    public $RMTypeID;
    public $RMTypeName;
    public $RawMaterialQty;
    public $SubtotalRM;
    public $DateLastChecked;
    public $LastInvoiceNumber;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->RawMaterialID = (!empty($data['RawMaterialID'])) ? $data['RawMaterialID'] : null;
        $this->RawMaterialCode = (!empty($data['RawMaterialCode'])) ? $data['RawMaterialCode'] : null;
        $this->RawMaterialName = (!empty($data['RawMaterialName'])) ? $data['RawMaterialName'] : null;
        $this->RawMaterialUnitCost = (!empty($data['RawMaterialUnitCost'])) ? $data['RawMaterialUnitCost'] : null;
        $this->RMSupplierID = (!empty($data['RMSupplierID'])) ? $data['RMSupplierID'] : null;
        $this->RMSupplierName = (!empty($data['RMSupplierName'])) ? $data['RMSupplierName'] : null;
        $this->RMTypeID = (!empty($data['RMTypeID'])) ? $data['RMTypeID'] : null;
        $this->RMTypeName = (!empty($data['RMTypeName'])) ? $data['RMTypeName'] : null;
        $this->RawMaterialQty = (!empty($data['RawMaterialQty'])) ? $data['RawMaterialQty'] : null;
        $this->SubtotalRM = (!empty($data['SubtotalRM'])) ? $data['SubtotalRM'] : null;
        $this->DateLastChecked = (!empty($data['DateLastChecked'])) ? $data['DateLastChecked'] : null;
        $this->LastInvoiceNumber = (!empty($data['LastInvoiceNumber'])) ? $data['LastInvoiceNumber'] : null;
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
                'name' => 'RawMaterialID',
                'required' => true,
                'validators' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'RawMaterialCode',
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
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'RawMaterialName',
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
                            'max' => 150,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'RMTypeID',
                'required' => true,
                'validators' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'RMSupplierID',
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
                'name' => 'RawMaterialUnitCost',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'DateLastChecked',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'LastInvoiceNumber',
                'required' => false,
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
                            'max' => 20,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
