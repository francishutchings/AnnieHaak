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
        ));

        $this->add(array(
            'name' => 'PackagingName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PackagingName',
                'class' => 'form-control',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'PackagingUnitCost',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PackagingUnitCost',
                'class' => 'form-control',
                'required' => true,
                'placeholder' => '£'
            ),
        ));

        $this->add(array(
            'name' => 'PackagingCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'PackagingCode',
                'class' => 'form-control',
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
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-default',
            ),
        ));
    }

}
