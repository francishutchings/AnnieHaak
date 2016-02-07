<?php

namespace AnnieHaak\Form;

use Zend\Form\Form;

class RawMaterialsForm extends Form {

    public function __construct($name = null) {

        // we want to ignore the name passed
        parent::__construct('rawMaterialTypes');

        $this->add(array(
            'name' => 'RawMaterialID',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'RawMaterialCode',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'RawMaterialCode',
                'class' => 'form-control',
                'required' => true,
                'maxlength' => 50,
                'placeholder' => 'Code'
            ),
        ));

        $this->add(array(
            'name' => 'RawMaterialName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'RawMaterialName',
                'class' => 'form-control',
                'required' => true,
                'maxlength' => 150,
                'placeholder' => 'Name'
            ),
        ));

        $this->add(array(
            'name' => 'RMTypeID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please select Type ...',
                'value_options' => array(),
            ), 'attributes' => array(
                'id' => 'RMTypeID',
                'class' => 'form-control',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'RMSupplierID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please select Supplier ...',
            ), 'attributes' => array(
                'id' => 'RMSupplierID',
                'class' => 'form-control',
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'RawMaterialUnitCost',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'RawMaterialUnitCost',
                'class' => 'form-control bfh-number',
                'step' => '0.0001',
                'min' => 0.0001,
                'placeholder' => 'Amount',
                'required' => true
            ),
        ));

        $this->add(array(
            'name' => 'DateLastChecked',
            'type' => 'Date',
            'options' => array(
                'format' => 'Y-m-d'
            ),
            'attributes' => array(
                'id' => 'DateLastChecked',
                'class' => 'form-control',
                'readonly' => 'readonly'
            ),
        ));

        $this->add(array(
            'name' => 'LastInvoiceNumber',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'LastInvoiceNumber',
                'class' => 'form-control',
                'maxlength' => 20,
                'placeholder' => 'Invoice #'
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
