<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class LabourItemsTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('LabourName ASC');
        });
        return $resultSet;
    }

    public function getLabourItems($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('LabourID' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveLabourItems(LabourItems $labourItems) {
        $data = array(
            'LabourName' => $labourItems->LabourName,
            'LabourUnitCost' => $labourItems->LabourUnitCost,
            'LabourCode' => $labourItems->LabourCode
        );

        $id = (int) $labourItems->LabourID;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getLabourItems($id)) {
                $this->tableGateway->update($data, array('LabourID' => $id));
            } else {
                throw new \Exception('Labour Item id does not exist');
            }
        }
    }

    public function deleteLabourItems($id) {
        $this->tableGateway->delete(array('LabourID' => (int) $id));
    }

}
