<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class PackagingForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('PackagingForm');

        $this->add(array(
            'name' => 'PackagingID',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'PackagingName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PackagingName',
                'class' => 'form-control',
                'maxlength' => 255,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PackagingUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'PackagingUnitCost',
                'class' => 'form-control',
                'step' => '0.0001',
                'min' => 0.0001,
                'placeholder' => 'Amount',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PackagingCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PackagingCode',
                'class' => 'form-control',
                'maxlength' => 50,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PackagingType',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please select type ...',
                'value_options' => array(
                    '1' => 'Box',
                    '2' => 'Bag',
                ),
            ), 'attributes' => array(
                'id' => 'PackagingType',
                'class' => 'form-control',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add',
                'id' => 'submitbutton',
                'class' => 'btn btn-success',
            ),
        ));
    }

}
