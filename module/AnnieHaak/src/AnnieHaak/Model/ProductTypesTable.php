<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ProductTypesTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('ProductTypeName ASC');
        });
        return $resultSet;
    }

    public function getProductTypes($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('ProductTypeId' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveProductTypes(ProductTypes $ProductTypes, Auditing $auditingObj) {
        $data = array(
            'ProductTypeName' => $ProductTypes->ProductTypeName
        );
        $id = (int) $ProductTypes->ProductTypeId;
        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'ProductTypes';
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
                throw new \Exception("Could not add new Packaging. ERROR: " . $ex->getMessage());
            }
            $connectCntrl->commit();
        } else {
            $productTypesCurrentData = new ProductTypes();
            $productTypesCurrentData = $this->getProductTypes($id);
            $productTypesCurrentArr = (Array) $productTypesCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'ProductTypes';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($productTypesCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('ProductTypeId' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Packaging. ERROR: " . $ex->getMessage());
            }
            $connectCntrl->commit();
        }
    }

    public function deleteProductTypes($id, Auditing $auditingObj) {
        $productTypesCurrentData = new ProductTypes();
        $productTypesCurrentData = $this->getProductTypes($id);
        $productTypesCurrentArr = (Array) $productTypesCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'ProductTypes';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($productTypesCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('ProductTypeId' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Packaging. ERROR: " . $ex->getMessage());
        }
        $connectCntrl->commit();
    }

}
