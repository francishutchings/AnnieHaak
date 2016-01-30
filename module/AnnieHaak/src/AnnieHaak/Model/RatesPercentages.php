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

class RatesPercentages implements InputFilterAwareInterface {

    public $AssayRateUnitCost;
    public $ImportPercentage;
    public $MerchantChargePercentage;
    public $PackageAndDispatchUnitCost;
    public $PostageCostUnitCost;
    public $PostageForProfitUnitCost;
    public $VATPercentage;
    protected $inputFilter;
    protected $adapter;
    protected $sql;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->sql = <<<SQL
        CALL additionalproductioncosts
        (
            :ControlValue
            ,:AssayRateUnitCost
            ,:ImportPercentage
            ,:MerchantChargePercentage
            ,:PackageAndDispatchUnitCost
            ,:PostageCostUnitCost
            ,:PostageForProfitUnitCost
            ,:VATPercentage
        )
SQL;
    }

    public function exchangeArray($data) {
        $this->AssayRateUnitCost = (!empty($data['AssayRateUnitCost'])) ? $data['AssayRateUnitCost'] : null;
        $this->ImportPercentage = (!empty($data['ImportPercentage'])) ? $data['ImportPercentage'] : null;
        $this->MerchantChargePercentage = (!empty($data['MerchantChargePercentage'])) ? $data['MerchantChargePercentage'] : null;
        $this->PackageAndDispatchUnitCost = (!empty($data['PackageAndDispatchUnitCost'])) ? $data['PackageAndDispatchUnitCost'] : null;
        $this->PostageCostUnitCost = (!empty($data['PostageCostUnitCost'])) ? $data['PostageCostUnitCost'] : null;
        $this->PostageForProfitUnitCost = (!empty($data['PostageForProfitUnitCost'])) ? $data['PostageForProfitUnitCost'] : null;
        $this->VATPercentage = (!empty($data['VATPercentage'])) ? $data['VATPercentage'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function fetchAll() {
        $data = array(
            "ControlValue" => 0,
            "AssayRateUnitCost" => 0,
            "ImportPercentage" => 0,
            "MerchantChargePercentage" => 0,
            "PackageAndDispatchUnitCost" => 0,
            "PostageCostUnitCost" => 0,
            "PostageForProfitUnitCost" => 0,
            "VATPercentage" => 0
        );

        $DBH = $this->adapter;
        $STH = $DBH->createStatement();
        $STH->prepare($this->sql);

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

    public function saveRatesPercents(RatesPercentages $RatesPercentages) {
        $data = array(
            "ControlValue" => 1,
            "AssayRateUnitCost" => $RatesPercentages->AssayRateUnitCost,
            "ImportPercentage" => $RatesPercentages->ImportPercentage,
            "MerchantChargePercentage" => $RatesPercentages->MerchantChargePercentage,
            "PackageAndDispatchUnitCost" => $RatesPercentages->PackageAndDispatchUnitCost,
            "PostageCostUnitCost" => $RatesPercentages->PostageCostUnitCost,
            "PostageForProfitUnitCost" => $RatesPercentages->PostageForProfitUnitCost,
            "VATPercentage" => $RatesPercentages->VATPercentage
        );
        $DBH = $this->adapter;
        $STH = $DBH->createStatement();
        $STH->prepare($this->sql);
        $STH->execute($data);
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
                'name' => 'ImportPercentage',
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
                'name' => 'MerchantChargePercentage',
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
                'name' => 'PackageAndDispatchUnitCost',
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
                'name' => 'PostageCostUnitCost',
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
                'name' => 'PostageForProfitUnitCost',
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
                'name' => 'VATPercentage',
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
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}
