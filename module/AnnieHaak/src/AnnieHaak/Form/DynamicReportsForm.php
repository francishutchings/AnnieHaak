<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class DynamicReportsForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('DynamicReports');

        $this->add(array(
            'name' => 'CollectionID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'All Collections',
                'value_options' => array(),
            ), 'attributes' => array(
                'id' => 'CollectionID',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'name' => 'ProductTypeID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'All Product Types',
                'value_options' => array(),
            ), 'attributes' => array(
                'id' => 'ProductTypeID',
                'class' => 'form-control'
            ),
        ));

        $this->add(array(
            'type' => 'Radio',
            'name' => 'ReportType',
            'options' => array(
                'value_options' => array(
                    array(
                        'value' => '1',
                        'label' => 'Retail',
                        'selected' => true,
                        'label_attributes' => array(
                            'class' => 'col-md-10',
                        ),
                    ),
                    array(
                        'value' => '2',
                        'label' => 'Trade Pack',
                        'label_attributes' => array(
                            'class' => 'col-md-10',
                        ),
                    ),
                    array(
                        'value' => '3',
                        'label' => 'Trade Allowed',
                        'label_attributes' => array(
                            'class' => 'col-md-10',
                        ),
                    ),
                ),
            ),
        ));
    }

}
