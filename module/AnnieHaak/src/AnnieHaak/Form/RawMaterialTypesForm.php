<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class RawMaterialTypesForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('rawMaterialTypes');

        $this->add(array(
            'name' => 'RMTypeID',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'RMTypeName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'RMTypeName',
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
