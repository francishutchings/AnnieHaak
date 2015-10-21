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
            ),
        ));
        $this->add(array(
            'name' => 'lastname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'lastname',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'username',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'password',
                'class' => 'form-control',
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
