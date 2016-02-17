<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class SuppliersTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('RMSupplierName ASC');
        });
        return $resultSet;
    }

    public function getSuppliers($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('RMSupplierID' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Error: No record found.");
        }
        return $row;
    }

    public function saveSuppliers(Suppliers $Supplier, Auditing $auditingObj) {
        $data = array(
            'RMSupplierName' => $Supplier->RMSupplierName
        );
        $id = (int) $Supplier->RMSupplierID;

        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'RawMaterialSupplierLookup';
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
                throw new \Exception("Could not add new Supplier. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            $suppliersCurrentData = new Suppliers();
            $suppliersCurrentData = $this->getSuppliers($id);
            $suppliersCurrentArr = (Array) $suppliersCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'RawMaterialSupplierLookup';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($suppliersCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('RMSupplierID' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Supplier. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function deleteSuppliers($id, Auditing $auditingObj) {
        $suppliersCurrentData = new Suppliers();
        $suppliersCurrentData = $this->getSuppliers($id);
        $suppliersCurrentArr = (Array) $suppliersCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'RawMaterialSupplierLookup';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($suppliersCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('RMSupplierID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Collection. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
