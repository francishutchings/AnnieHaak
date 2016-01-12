<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class RatesPercentagesForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('ratesPercentagesForm');

        $this->add(array(
            'name' => 'AssayRateUnitCost',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'AssayRateUnitCost',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'ImportPercentage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ImportPercentage',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'MerchantChargePercentage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'MerchantChargePercentage',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'PackageAndDispatchUnitCost',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PackageAndDispatchUnitCost',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'PostageCostUnitCost',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PostageCostUnitCost',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'PostageForProfitUnitCost',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PostageForProfitUnitCost',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'VATPercentage',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'VATPercentage',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-default',
            ),
        ));
    }

}
