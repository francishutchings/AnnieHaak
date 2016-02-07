<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class LabourItemsForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('labourItemsForm');

        $this->add(array(
            'name' => 'LabourID',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'LabourName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'LabourName',
                'class' => 'form-control',
                'maxlength' => 255,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'LabourUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'LabourUnitCost',
                'class' => 'form-control',
                'step' => '0.0001',
                'min' => 0.0001,
                'placeholder' => 'Amount',
                'required' => true,
                'value' => 0.0001
            ),
        ));

        $this->add(array(
            'name' => 'LabourCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'LabourCode',
                'class' => 'form-control',
                'maxlength' => 255,
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
