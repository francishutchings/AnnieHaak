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
                'required' => true
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
            'name' => 'NameCharm',
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
                'id' => 'NameCharm',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'NameCrystal',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Crystal ...',
                'value_options' => array(
                    'Crystal 1' => 'Crystal 1',
                    'Crystal 2' => 'Crystal 2',
                    'Crystal 3' => 'Crystal 3'
                ),
            ),
            'attributes' => array(
                'id' => 'NameCrystal',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'NameColour',
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
                'id' => 'NameColour',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'NameLength',
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
                'id' => 'NameLength',
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
            'name' => 'CollectionID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please select Collection ...',
                'value_options' => array(),
            ), 'attributes' => array(
                'id' => 'CollectionID',
                'class' => 'form-control',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'ProductTypeID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please select Product Type ...',
                'value_options' => array(),
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
                'value' => 'Add',
                'id' => 'submitbutton',
                'class' => 'btn btn-success'
            ),
        ));
    }

}
