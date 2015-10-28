<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class CollectionsForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('collections');

        $this->add(array(
            'name' => 'ProductCollectionID',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'ProductCollectionName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductCollectionName',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'ProductCollectionCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductCollectionCode',
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'Current',
            'type' => 'checkbox',
            'attributes' => array(
                'id' => 'Current',
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
