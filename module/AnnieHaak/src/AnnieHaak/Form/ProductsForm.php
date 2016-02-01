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
            'attributes' => array(
                'value' => 0
            ),
        ));

        $this->add(array(
            'name' => 'ProductName',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ProductName',
                'class' => 'form-control',
                'required' => true,
                'placeholder' => 'Product Name'
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
                'value_options' => array(),
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
                'value_options' => array(),
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
                'value_options' => array(),
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
                'value_options' => array(),
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
                'class' => 'form-control text-uppercase',
                'required' => true,
                'placeholder' => 'SKU'
            ),
        ));

        $this->add(array(
            'name' => 'CollectionID',
            'type' => 'select',
            'options' => array(
                'empty_option' => 'Select Collection ...',
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
                'empty_option' => 'Select Product Type ...',
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
            'name' => 'RRP',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'RRP',
                'style' => 'width:60%',
                'step' => '1',
                'min' => 1,
                'value' => 0,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'MinsToBuild',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'MinsToBuild',
                'class' => 'form-control',
                'step' => '1',
                'min' => 1,
                'required' => true,
            ),
        ));

        $this->add(array(
            'name' => 'QtyInTradePack',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'QtyInTradePack',
                'class' => 'input-xs',
                'step' => '1',
                'min' => 1
            ),
        ));

        $this->add(array(
            'name' => 'QtyOrderedLastPeriod',
            'type' => 'Number',
            'attributes' => array(
                'id' => 'QtyOrderedLastPeriod',
                'class' => 'form-control',
                'step' => '1',
                'min' => 0,
            ),
        ));

        $this->add(array(
            'name' => 'CurrentURL',
            'type' => 'Url',
            'attributes' => array(
                'id' => 'CurrentURL',
                'class' => 'form-control',
                'placeholder' => 'http://'
            ),
        ));

        $this->add(array(
            'name' => 'OldURL',
            'type' => 'Url',
            'attributes' => array(
                'id' => 'OldURL',
                'class' => 'form-control',
                'placeholder' => 'http://'
            ),
        ));

        $this->add(array(
            'name' => 'Description',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'Description',
                'class' => 'form-control',
                'placeholder' => 'Description'
            ),
        ));

        /*
         *  Checkboxes
         * ////////////////////////////////////////////////////////////////////////////
         */
        $this->add(array(
            'type' => 'Radio',
            'name' => 'Current',
            'options' => array(
                'value_options' => array(
                    '0' => 'Not Live',
                    '1' => 'Currently Live',
                ),
            ),
        ));

        $this->add(array(
            'type' => 'Radio',
            'name' => 'DescriptionStatus',
            'options' => array(
                'value_options' => array(
                    '0' => 'Needs Description',
                    '1' => 'Pending Approval',
                    '2' => 'Approved',
                ),
            ),
        ));

        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'PartOfTradePack',
            'options' => array(
                'use_hidden_element' => false,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'PartOfTradePack',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'KeyPiece',
            'options' => array(
                'use_hidden_element' => false,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'KeyPiece',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Personalisable',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Personalisable',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'RequiresAssay',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'RequiresAssay',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'ExcludeFromTrade',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'ExcludeFromTrade',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Friendship',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Friendship',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Stacks',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Stacks',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'PremiumStacks',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'PremiumStacks',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'SterlingSilver',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'SterlingSilver',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Gold',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Gold',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Weddings',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Weddings',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Birthdays',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Birthdays',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Accessories',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Accessories',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Engraved',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Engraved',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        $this->add(array(
            'type' => 'Checkbox',
            'name' => 'Charm',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            ),
            'attributes' => array(
                'id' => 'Charm',
                'autocomplete' => 'off',
                'class' => 'styled'
            ),
        ));
        /*
         * ////////////////////////////////////////////////////////////////////////////
         */


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
