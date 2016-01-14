<?php

namespace AnnieHaak\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RawMaterials {

    public $RawMaterialID;           # INT(11) NOT NULL AUTO_INCREMENT,
    public $RawMaterialCode;         # VARCHAR(50) NULL DEFAULT NULL,
    public $RawMaterialName;         # VARCHAR(150) NOT NULL,
    public $RawMaterialUnitCost;     # DOUBLE NOT NULL DEFAULT '0',
    public $RMSupplierID;            # INT(11) NOT NULL DEFAULT '0',
    public $RMTypeID;                # INT(11) NOT NULL DEFAULT '0',
    public $DateLastChecked;         # DATETIME NULL DEFAULT NULL,
    public $LastInvoiceNumber;       # VARCHAR(20) NULL DEFAULT NULL

    public function exchangeArray($data) {
        $this->RawMaterialID = (!empty($data['RawMaterialID'])) ? $data['RawMaterialID'] : null;
        $this->RawMaterialCode = (!empty($data['RawMaterialCode'])) ? $data['RawMaterialCode'] : null;
        $this->RawMaterialName = (!empty($data['RawMaterialName'])) ? $data['RawMaterialName'] : null;
        $this->RawMaterialUnitCost = (!empty($data['RawMaterialUnitCost'])) ? $data['RawMaterialUnitCost'] : null;
        $this->RMSupplierID = (!empty($data['RMSupplierID'])) ? $data['RMSupplierID'] : null;
        $this->RMTypeID = (!empty($data['RMTypeID'])) ? $data['RMTypeID'] : null;
        $this->DateLastChecked = (!empty($data['DateLastChecked'])) ? $data['DateLastChecked'] : null;
        $this->LastInvoiceNumber = (!empty($data['LastInvoiceNumber'])) ? $data['LastInvoiceNumber'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

}
