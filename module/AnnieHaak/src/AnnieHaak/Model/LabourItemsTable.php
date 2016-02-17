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
            throw new \Exception("Error: No record found.");
        }
        return $row;
    }

    public function saveLabourItems(LabourItems $labourItems, Auditing $auditingObj) {
        $data = array(
            'LabourName' => $labourItems->LabourName,
            'LabourUnitCost' => $labourItems->LabourUnitCost,
            'LabourCode' => $labourItems->LabourCode
        );
        $id = (int) $labourItems->LabourID;

        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'LabourLookup';
            $auditingObj->OldDataJSON = '';

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->insert($data);
                $newID = $this->tableGateway->lastInsertValue;
                $auditingObj->TableIndex = $newID;
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not add new Labour Item. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            $labourItemsCurrentData = new LabourItems();
            $labourItemsCurrentData = $this->getLabourItems($id);
            $labourItemsCurrentArr = (Array) $labourItemsCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'LabourLookup';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($labourItemsCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('LabourID' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Labour Item. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function getLabourItemsByProduct($productId) {
        $select = new Select();
        $select->from(array('LL' => 'LabourLookup'));
        $select->columns(array('LabourID', 'LabourName', 'LabourUnitCost', 'LabourCode'));
        $select->join(array('LT' => 'LabourTime'), 'LT.LabourID = LL.LabourID', array('LabourTimeID', 'LabourQty', 'SubtotalLabour' => 'LabourQty * LabourUnitCost'));
        $select->where(array('LT.ProductID' => $productId));
        $select->order('LL.LabourName');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function fetchLabourItemByType($LabourId) {
        $select = new Select();
        $select->from(array('LL' => 'LabourLookup'));
        $select->columns(array('LabourUnitCost', 'LabourCode'));
        $select->where(array('LabourID' => $LabourId));
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function deleteLabourItems($id, Auditing $auditingObj) {
        $labourItemsCurrentData = new LabourItems();
        $labourItemsCurrentData = $this->getLabourItems($id);
        $labourItemsCurrentArr = (Array) $labourItemsCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'LabourLookup';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($labourItemsCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('LabourID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Labour Item. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
