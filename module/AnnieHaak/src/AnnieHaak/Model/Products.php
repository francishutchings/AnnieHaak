<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Products implements InputFilterAwareInterface {

    public $Accessories;
    public $Birthdays;
    public $Charm;
    public $CollectionID;
    public $ProductCollectionName;
    public $Current;
    public $CurrentURL;
    public $Description;
    public $DescriptionStatus;
    public $Engraved;
    public $ExcludeFromTrade;
    public $Friendship;
    public $Gold;
    public $ImagePath;
    public $IntroDate;
    public $KeyPiece;
    public $MinsToBuild;
    public $OldURL;
    public $PartOfTradePack;
    public $Personalisable;
    public $PremiumStacks;
    public $ProductID;
    public $ProductName;
    public $ProductTypeID;
    public $ProductTypeName;
    public $QtyInTradePack;
    public $QtyOrderedLastPeriod;
    public $RequiresAssay;
    public $RRP;
    public $SKU;
    public $Stacks;
    public $SterlingSilver;
    public $Strands;
    public $Weddings;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->Accessories = (!empty($data['Accessories'])) ? $data['Accessories'] : 0;
        $this->Birthdays = (!empty($data['Birthdays'])) ? $data['Birthdays'] : 0;
        $this->Charm = (!empty($data['Charm'])) ? $data['Charm'] : 0;
        $this->CollectionID = (!empty($data['CollectionID'])) ? $data['CollectionID'] : 0;
        $this->ProductCollectionName = (!empty($data['ProductCollectionName'])) ? $data['ProductCollectionName'] : 0;
        $this->Current = (!empty($data['Current'])) ? $data['Current'] : 0;
        $this->CurrentURL = (!empty($data['CurrentURL'])) ? $data['CurrentURL'] : 0;
        $this->Description = (!empty($data['Description'])) ? $data['Description'] : 0;
        $this->DescriptionStatus = (!empty($data['DescriptionStatus'])) ? $data['DescriptionStatus'] : 0;
        $this->Engraved = (!empty($data['Engraved'])) ? $data['Engraved'] : 0;
        $this->ExcludeFromTrade = (!empty($data['ExcludeFromTrade'])) ? $data['ExcludeFromTrade'] : 0;
        $this->Friendship = (!empty($data['Friendship'])) ? $data['Friendship'] : 0;
        $this->Gold = (!empty($data['Gold'])) ? $data['Gold'] : 0;
        $this->ImagePath = (!empty($data['ImagePath'])) ? $data['ImagePath'] : 0;
        $this->IntroDate = (!empty($data['IntroDate'])) ? $data['IntroDate'] : 0;
        $this->KeyPiece = (!empty($data['KeyPiece'])) ? $data['KeyPiece'] : 0;
        $this->MinsToBuild = (!empty($data['MinsToBuild'])) ? $data['MinsToBuild'] : 0;
        $this->OldURL = (!empty($data['OldURL'])) ? $data['OldURL'] : 0;
        $this->PartOfTradePack = (!empty($data['PartOfTradePack'])) ? $data['PartOfTradePack'] : 0;
        $this->Personalisable = (!empty($data['Personalisable'])) ? $data['Personalisable'] : 0;
        $this->PremiumStacks = (!empty($data['PremiumStacks'])) ? $data['PremiumStacks'] : 0;
        $this->ProductID = (!empty($data['ProductID'])) ? $data['ProductID'] : 0;
        $this->ProductName = (!empty($data['ProductName'])) ? $data['ProductName'] : 0;
        $this->ProductTypeID = (!empty($data['ProductTypeID'])) ? $data['ProductTypeID'] : 0;
        $this->ProductTypeName = (!empty($data['ProductTypeName'])) ? $data['ProductTypeName'] : 0;
        $this->QtyInTradePack = (!empty($data['QtyInTradePack'])) ? $data['QtyInTradePack'] : 0;
        $this->QtyOrderedLastPeriod = (!empty($data['QtyOrderedLastPeriod'])) ? $data['QtyOrderedLastPeriod'] : 0;
        $this->RequiresAssay = (!empty($data['RequiresAssay'])) ? $data['RequiresAssay'] : 0;
        $this->RRP = (!empty($data['RRP'])) ? $data['RRP'] : 0;
        $this->SKU = (!empty($data['SKU'])) ? $data['SKU'] : 0;
        $this->Stacks = (!empty($data['Stacks'])) ? $data['Stacks'] : 0;
        $this->SterlingSilver = (!empty($data['SterlingSilver'])) ? $data['SterlingSilver'] : 0;
        $this->Strands = (!empty($data['Strands'])) ? $data['Strands'] : 0;
        $this->Weddings = (!empty($data['Weddings'])) ? $data['Weddings'] : 0;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'ProductID',
                'required' => true,
                'validators' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'ProductName',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}