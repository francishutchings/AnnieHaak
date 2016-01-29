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
            'attributes' => array(
                'value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'ProductCollectionName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductCollectionName',
                'class' => 'form-control',
                'maxlength' => 255
            ),
        ));

        $this->add(array(
            'name' => 'ProductCollectionCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductCollectionCode',
                'class' => 'form-control',
                'maxlength' => 255
            ),
        ));

        $this->add(array(
            'name' => 'Current',
            'type' => 'checkbox',
            'attributes' => array(
                'id' => 'Current'
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
