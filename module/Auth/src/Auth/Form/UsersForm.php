<?php

namespace Auth\Form;

use Zend\Form\Form;

class UsersForm extends Form {

    public function __construct($name = null) {
        parent::__construct('Users');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'firstname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'firstname',
                'class' => 'form-control',
                'placeholder' => 'Maximum length 30',
                'maxlength' => '30',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'lastname',
            'type' => 'text',
            'attributes' => array(
                'id' => 'lastname',
                'class' => 'form-control',
                'placeholder' => 'Maximum length 30',
                'maxlength' => '30',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'type' => 'Email',
            'attributes' => array(
                'id' => 'username',
                'class' => 'form-control',
                'placeholder' => 'Email address',
                'autocomplete' => 'off',
                'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                'maxlength' => '255',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'role_level',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Please choose user level',
                'value_options' => array(
                    '1' => 'Admin User - full control',
                    '2' => 'Standard User - alter only',
                    '3' => 'Guest User - view only',
                ),
            ),
            'attributes' => array(
                'id' => 'role_level',
                'class' => 'form-control',
                'required' => true,
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Password length minimum 8 maximum 25',
                'autocomplete' => 'off',
                'pattern' => '.{5,25}',
                'maxlength' => '25',
                'required' => true
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
