<?php

namespace AnnieHaak\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

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
            throw new \Exception("Error: No record found.");
        }
        return $row;
    }

    public function fetchFullDataPaginated($sortBy, $search) {
        $select = new Select();
        $select->from('RawMaterialTypeLookup');
        $select->columns(array('RMTypeID', 'RMTypeName'));

        if (isset($search)) {
            $search['searchString'] = str_replace(array("+"), array(" "), $search['searchString']);
            switch ($search['searchOper']) {
                case 'eq':
                    $select->where(array($search['searchColumn'] => $search['searchString']));
                    break;
                case 'cn':
                    $select->where->like($search['searchColumn'], '%' . $search['searchString'] . '%');
                    break;
                case 'bw':
                    $select->where->like($search['searchColumn'], $search['searchString'] . '%');
                    break;
                case 'ew':
                    $select->where->like($search['searchColumn'], '%' . $search['searchString']);
                    break;
                case 'lt':
                    $select->where->lessThan($search['searchColumn'], $search['searchString']);
                    break;
                case 'le':
                    $select->where->lessThanOrEqualTo($search['searchColumn'], $search['searchString']);
                    break;
                case 'gt':
                    $select->where->greaterThan($search['searchColumn'], $search['searchString']);
                    break;
                case 'ge':
                    $select->where->greaterThanOrEqualTo($search['searchColumn'], $search['searchString']);
                    break;
            }
        }
        $select->order($sortBy);

        #echo $select->getSqlString();
        #exit();

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new RawMaterialTypes());
        $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function saveRawMaterialTypes(RawMaterialTypes $RawMaterialTypes, Auditing $auditingObj) {
        $data = array(
            'RMTypeName' => $RawMaterialTypes->RMTypeName
        );
        $id = (int) $RawMaterialTypes->RMTypeID;

        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'RawMaterialTypeLookup';
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
                throw new \Exception("Could not add new Raw Material Type. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            $rawMaterialTypesCurrentData = new ProductTypes();
            $rawMaterialTypesCurrentData = $this->getRawMaterialTypes($id);
            $rawMaterialTypesCurrentArr = (Array) $rawMaterialTypesCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'RawMaterialTypeLookup';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($rawMaterialTypesCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('RMTypeID' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Raw Material Type. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function deleteRawMaterialTypes($id, Auditing $auditingObj) {
        $rawMaterialTypesCurrentData = new ProductTypes();
        $rawMaterialTypesCurrentData = $this->getRawMaterialTypes($id);
        $rawMaterialTypesCurrentArr = (Array) $rawMaterialTypesCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'RawMaterialTypeLookup';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($rawMaterialTypesCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('RMTypeID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Packaging. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
