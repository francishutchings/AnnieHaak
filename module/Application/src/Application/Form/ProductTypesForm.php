<?php

namespace Application\Form;

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
            'options' => array(
                'label' => 'Product Type Name: ',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}
