<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class PackagingTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('PackagingName ASC');
        });
        return $resultSet;
    }

    public function getPackaging($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('PackagingID' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getPackagingByProduct($productId) {
        $select = new Select();
        $select->from(array('PLU' => 'PackagingLookup'));
        $select->columns(array('PackagingID', 'PackagingName', 'PackagingCode', 'PackagingUnitCost'));
        $select->join(array('PL' => 'PackagingPicklists'), 'PL.PackagingID = PLU.PackagingID', array('PackagingQty', 'SubtotalPackaging' => 'PackagingQty * PackagingUnitCost'));
        $select->where(array('PL.ProductID' => $productId));
        $select->order('PLU.PackagingName');

        #echo $select->getSqlString();
        #exit();

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function fetchPackagingByType($packagingId) {
        $select = new Select();
        $select->from(array('PL' => 'PackagingLookup'));
        $select->columns(array('PackagingUnitCost', 'PackagingCode'));
        $select->where(array('PackagingID' => $packagingId));

        #echo $select->getSqlString();
        #exit();

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function savePackaging(Packaging $Packaging) {
        $data = array(
            'PackagingName' => $Packaging->PackagingName,
            'PackagingUnitCost' => $Packaging->PackagingUnitCost,
            'PackagingCode' => $Packaging->PackagingCode,
            'PackagingType' => $Packaging->PackagingType
        );

        $id = (int) $Packaging->PackagingID;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPackaging($id)) {
                $this->tableGateway->update($data, array('PackagingID' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deletePackaging($id) {
        $this->tableGateway->delete(array('PackagingID' => (int) $id));
    }

}
