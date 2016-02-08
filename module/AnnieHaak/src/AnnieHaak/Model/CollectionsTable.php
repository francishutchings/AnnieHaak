<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class CollectionsTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('ProductCollectionName ASC');
        });
        return $resultSet;
    }

    public function getCollections($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('ProductCollectionID' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveCollections(Collections $Collections, Auditing $auditingObj) {
        $data = array(
            'ProductCollectionName' => $Collections->ProductCollectionName,
            'ProductCollectionCode' => $Collections->ProductCollectionCode,
            'Current' => $Collections->Current
        );
        $id = (int) $Collections->ProductCollectionID;
        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'ProductCollections';
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
                throw new \Exception("Could not add new Collection. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            $collectionsCurrentData = new Collections();
            $collectionsCurrentData = $this->getCollections($id);
            $collectionsCurrentArr = (Array) $collectionsCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'ProductCollections';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($collectionsCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('ProductCollectionID' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Collection. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function deleteCollections($id, Auditing $auditingObj) {
        $collectionsCurrentData = new Collections();
        $collectionsCurrentData = $this->getCollections($id);
        $collectionsCurrentArr = (Array) $collectionsCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'ProductCollections';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($collectionsCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('ProductCollectionID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Collection. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
