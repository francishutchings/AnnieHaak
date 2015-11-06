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
        ));

        $this->add(array(
            'name' => 'LabourName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'LabourName',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'LabourUnitCost',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'LabourUnitCost',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'LabourCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'LabourCode',
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
