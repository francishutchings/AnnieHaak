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
