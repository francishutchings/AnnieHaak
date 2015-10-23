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
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'type' => 'text',
            'attributes' => array(
                'id' => 'username',
                'class' => 'form-control',
                'placeholder' => 'Email address',
                'autocomplete' => 'off',
                'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                'maxlength' => '255',
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
