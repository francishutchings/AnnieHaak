<?php

/*
 * exchangeArray() method is a requirement for form hydrators;
 * specifically the Zend\Stdlib\Hydrator\ArraySerializable hydrator
 * so it can access the domain object's protected properties when binding from form fields.
 */

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

class RatesPercentages {

    public $AssayRateUnitCost;
    public $ImportPercentage;
    public $MerchantChargePercentage;
    public $PackageAndDispatchUnitCost;
    public $PostageCostUnitCost;
    public $PostageForProfitUnitCost;
    public $VATPercentage;
    protected $inputFilter;
    protected $adapter;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function fetchAll() {
        $sql = <<<SQL
        CALL additionalproductioncosts
        (
            0
            ,:AssayRateUnitCost
            ,:ImportPercentage
            ,:MerchantChargePercentage
            ,:PackageAndDispatchUnitCost
            ,:PostageCostUnitCost
            ,:PostageForProfitUnitCost
            ,:VATPercentage
        )
SQL;

        $data = array(
            "AssayRateUnitCost" => 0
            , "ImportPercentage" => 0
            , "MerchantChargePercentage" => 0
            , "PackageAndDispatchUnitCost" => 0
            , "PostageCostUnitCost" => 0
            , "PostageForProfitUnitCost" => 0
            , "VATPercentage" => 0
        );

        $DBH = $this->adapter;
        $STH = $DBH->query($sql);

        $result = $STH->execute($data);
        $resultArr = $result->current();

        $this->AssayRateUnitCost = $resultArr['AssayRateUnitCost'];
        $this->ImportPercentage = $resultArr['ImportPercentage'];
        $this->MerchantChargePercentage = $resultArr['MerchantChargePercentage'];
        $this->PackageAndDispatchUnitCost = $resultArr['PackageAndDispatchUnitCost'];
        $this->PostageCostUnitCost = $resultArr['PostageCostUnitCost'];
        $this->PostageForProfitUnitCost = $resultArr['PostageForProfitUnitCost'];
        $this->VATPercentage = $resultArr['VATPercentage'];

        return $this;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'AssayRateUnitCost',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'ImportPercentage',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'MerchantChargePercentage',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'PackageAndDispatchUnitCost',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'PostageCostUnitCost',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'PostageForProfitUnitCost',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'VATPercentage',
                'required' => true,
                'validators' => array(
                    array('name' => 'Float'),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
