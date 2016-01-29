<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class SuppliersForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('suppliers');

        $this->add(array(
            'name' => 'RMSupplierID',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'RMSupplierName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'RMSupplierName',
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
