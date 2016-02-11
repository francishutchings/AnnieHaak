<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class RatesPercentagesForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('ratesPercentagesForm');

        $this->add(array(
            'name' => 'AssayRateUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'AssayRateUnitCost',
                'class' => 'form-control',
                'placeholder' => '£',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'ImportPercentage',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'ImportPercentage',
                'class' => 'form-control',
                'placeholder' => '%',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'MerchantChargePercentage',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'MerchantChargePercentage',
                'class' => 'form-control',
                'placeholder' => '%',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PackageAndDispatchUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'PackageAndDispatchUnitCost',
                'class' => 'form-control',
                'placeholder' => '£',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PostageCostUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'PostageCostUnitCost',
                'class' => 'form-control',
                'placeholder' => '£',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PostageForProfitUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'PostageForProfitUnitCost',
                'class' => 'form-control',
                'placeholder' => '£',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'VATPercentage',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'VATPercentage',
                'class' => 'form-control',
                'placeholder' => '%',
                'step' => 0.01,
                'min' => 0.01,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }

}
