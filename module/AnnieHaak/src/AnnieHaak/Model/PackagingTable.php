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

    public function savePackaging(Packaging $Packaging, Auditing $auditingObj) {
        $data = array(
            'PackagingName' => $Packaging->PackagingName,
            'PackagingUnitCost' => $Packaging->PackagingUnitCost,
            'PackagingCode' => $Packaging->PackagingCode,
            'PackagingType' => $Packaging->PackagingType
        );
        $id = (int) $Packaging->PackagingID;

        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'PackagingLookup';
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
                throw new \Exception("Could not add new Packaging. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            $packagingCurrentData = new Packaging();
            $packagingCurrentData = $this->getPackaging($id);
            $packagingCurrentArr = (Array) $packagingCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'PackagingLookup';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($packagingCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('PackagingID' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Packaging. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function deletePackaging($id, Auditing $auditingObj) {
        $packagingCurrentData = new Packaging();
        $packagingCurrentData = $this->getPackaging($id);
        $packagingCurrentArr = (Array) $packagingCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'PackagingLookup';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($packagingCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('PackagingID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Packaging. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
