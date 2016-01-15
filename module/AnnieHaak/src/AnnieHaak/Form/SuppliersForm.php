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
        ));

        $this->add(array(
            'name' => 'RMSupplierName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'RMSupplierName',
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
