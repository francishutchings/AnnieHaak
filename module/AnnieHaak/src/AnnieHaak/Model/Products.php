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
        $this->ProductCollectionID = (!empty($data['ProductCollectionID'])) ? $data['ProductCollectionID'] : 0;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        throw new \Exception("Not used");
    }

}
