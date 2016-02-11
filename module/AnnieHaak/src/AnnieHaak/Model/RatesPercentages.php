<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RatesPercentages implements InputFilterAwareInterface {

    public $AssayRateUnitCost;
    public $ImportPercentage;
    public $MerchantChargePercentage;
    public $PackageAndDispatchUnitCost;
    public $PostageCostUnitCost;
    public $PostageForProfitUnitCost;
    public $VATPercentage;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->AssayRateUnitCost = (!empty($data['AssayRateUnitCost'])) ? $data['AssayRateUnitCost'] : 0;
        $this->ImportPercentage = (!empty($data['ImportPercentage'])) ? $data['ImportPercentage'] : 0;
        $this->MerchantChargePercentage = (!empty($data['MerchantChargePercentage'])) ? $data['MerchantChargePercentage'] : 0;
        $this->PackageAndDispatchUnitCost = (!empty($data['PackageAndDispatchUnitCost'])) ? $data['PackageAndDispatchUnitCost'] : 0;
        $this->PostageCostUnitCost = (!empty($data['PostageCostUnitCost'])) ? $data['PostageCostUnitCost'] : 0;
        $this->PostageForProfitUnitCost = (!empty($data['PostageForProfitUnitCost'])) ? $data['PostageForProfitUnitCost'] : 0;
        $this->VATPercentage = (!empty($data['VATPercentage'])) ? $data['VATPercentage'] : 0;
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
                'name' => 'AssayRateUnitCost',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Float',
                        'options' => array(
                            'min' => 0.01,
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
                            'min' => 0.01,
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
                            'min' => 0.01,
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
                            'min' => 0.01,
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
                            'min' => 0.01,
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
                            'min' => 0.01,
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
                            'min' => 0.01,
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
