<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class ProductTypesForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('productTypes');

        $this->add(array(
            'name' => 'ProductTypeId',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'ProductTypeName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductTypeName',
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
