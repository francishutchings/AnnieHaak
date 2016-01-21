<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class ProductsForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('products');

        $this->add(array(
            'name' => 'ProductID',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'ProductName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductName',
                'class' => 'form-control',
                'required' => true,
                'readonly' => 'readonly'
            ),
        ));

        $this->add(array(
            'name' => 'Name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'Name',
                'class' => 'form-control',
                'required' => true
            ),
        ));

        $this->add(array(
            'name' => 'Charm',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Charm ...',
                'value_options' => array(
                    'Charm 1' => 'Charm 1',
                    'Charm 2' => 'Charm 2',
                    'Charm 3' => 'Charm 3'
                ),
            ),
            'attributes' => array(
                'id' => 'Charm',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'Chrystal',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Chrystal ...',
                'value_options' => array(
                    'Chrystal 1' => 'Chrystal 1',
                    'Chrystal 2' => 'Chrystal 2',
                    'Chrystal 3' => 'Chrystal 3'
                ),
            ),
            'attributes' => array(
                'id' => 'Chrystal',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'Colour',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Colour ...',
                'value_options' => array(
                    'Colour 1' => 'Colour 1',
                    'Colour 2' => 'Colour 2',
                    'Colour 3' => 'Colour 3'
                ),
            ),
            'attributes' => array(
                'id' => 'Colour',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'Length',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Length ...',
                'value_options' => array(
                    '14cm' => '14cm',
                    '17cm' => '17cm',
                    '19cm' => '19cm',
                    '41cm' => '41cm',
                    '46cm' => '46cm'
                ),
            ),
            'attributes' => array(
                'id' => 'Length',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'SKU',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'SKU',
                'class' => 'form-control',
                'required' => true
            ),
        ));

        $this->add(array(
            'name' => 'ProductTypeID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please select Product Type ...',
                'value_options' => array(
                    '1' => 'Data',
                    '2' => 'Data',
                ),
            ), 'attributes' => array(
                'id' => 'ProductTypeID',
                'class' => 'form-control',
                'required' => true,
            ),
        ));


        $this->add(array(
            'name' => 'Strands',
            'type' => 'select',
            'options' => array(
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                ),
            ),
            'attributes' => array(
                'id' => 'Strands',
                'class' => 'form-control',
                'required' => true
            ),
        ));

        $this->add(array(
            'name' => 'IntroDate',
            'type' => 'Date',
            'options' => array(
                'format' => 'Y-m-d'
            ),
            'attributes' => array(
                'id' => 'IntroDate',
                'class' => 'form-control',
                'readonly' => 'readonly'
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
