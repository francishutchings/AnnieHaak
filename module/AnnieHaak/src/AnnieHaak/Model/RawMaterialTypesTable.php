<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class RawMaterialTypesTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('RMTypeName ASC');
        });
        return $resultSet;
    }

    public function getRawMaterialTypes($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('RMTypeID' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveRawMaterialTypes(RawMaterialTypes $RawMaterialTypes) {
        $data = array(
            'RMTypeName' => $RawMaterialTypes->RMTypeName
        );

        $id = (int) $RawMaterialTypes->RMTypeID;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRawMaterialTypes($id)) {
                $this->tableGateway->update($data, array('RMTypeID' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deleteRawMaterialTypes($id) {
        $this->tableGateway->delete(array('RMTypeID' => (int) $id));
    }

}
