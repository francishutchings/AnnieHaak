<?php

namespace AnnieHaak\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Predicate\PredicateSet;

class RawMaterialsTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('RawMaterialName ASC');
        });
        return $resultSet;
    }

    public function getRawMaterials($ids) {
        $select = new Select();
        $select->from(array('RM' => 'rawmateriallookup'));
        $select->columns(array('RawMaterialID', 'RawMaterialCode', 'RMTypeID', 'RMSupplierID', 'RawMaterialName', 'RawMaterialUnitCost', 'DateLastChecked', 'LastInvoiceNumber'));
        $select->join(array('Supp' => 'rawmaterialsupplierlookup'), 'Supp.RMSupplierID = RM.RMSupplierID', array('RMSupplierName'));
        $select->join(array('RMType' => 'rawmaterialtypelookup'), 'RMType.RMTypeID = RM.RMTypeID', array('RMTypeName'));
        $select->where(array('RM.RawMaterialID' => array($ids)));

        #echo $select->getSqlString();
        #exit();

        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Error: No record found.");
        }
        return $row;
    }

    public function fetchFullData($paginated = FALSE) {
        $select = new Select();
        $select->from(array('RM' => 'rawmateriallookup'));
        $select->columns(array('RawMaterialCode', 'RawMaterialName', 'RawMaterialUnitCost', 'DateLastChecked', 'LastInvoiceNumber'));
        $select->join(array('Supp' => 'rawmaterialsupplierlookup'), 'Supp.RMSupplierID = RM.RMSupplierID', array('RMSupplierName'));
        $select->join(array('RMType' => 'rawmaterialtypelookup'), 'RMType.RMTypeID = RM.RMTypeID', array('RMTypeName'));
        $select->order('RawMaterialCode ASC');

        if ($paginated) {
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new RawMaterials());
            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet = $this->tableGateway->selectWith($select);
            return $resultSet;
        }
    }

    public function fetchFullDataPaginated($sortBy, $search) {

        $select = new Select();
        $select->from(array('RM' => 'rawmateriallookup'));
        $select->columns(array('RawMaterialID', 'RawMaterialCode', 'RawMaterialName', 'RawMaterialUnitCost', 'DateLastChecked', 'LastInvoiceNumber'));
        $select->join(array('Supp' => 'rawmaterialsupplierlookup'), 'Supp.RMSupplierID = RM.RMSupplierID', array('RMSupplierName'));
        $select->join(array('RMType' => 'rawmaterialtypelookup'), 'RMType.RMTypeID = RM.RMTypeID', array('RMTypeName'));

        if (isset($search)) {
            $orOperator = FALSE;
            if ($search['groupOp'] == 'OR') {
                $orOperator = TRUE;
            }

            foreach ($search['rules'] as $rule) {
                switch ($rule['searchOper']) {
                    case 'eq':
                        $select->where(array($rule['searchColumn'] => $rule['searchString']));
                        break;
                    case 'cn':
                        $select->where->like($rule['searchColumn'], '%' . $rule['searchString'] . '%');
                        break;
                    case 'bw':
                        $select->where->like($rule['searchColumn'], $rule['searchString'] . '%');
                        break;
                    case 'ew':
                        $select->where->like($rule['searchColumn'], '%' . $rule['searchString']);
                        break;
                    case 'lt':
                        $select->where->lessThan($rule['searchColumn'], $rule['searchString']);
                        break;
                    case 'le':
                        $select->where->lessThanOrEqualTo($rule['searchColumn'], $rule['searchString']);
                        break;
                    case 'gt':
                        $select->where->greaterThan($rule['searchColumn'], $rule['searchString']);
                        break;
                    case 'ge':
                        $select->where->greaterThanOrEqualTo($rule['searchColumn'], $rule['searchString']);
                        break;
                }
                if (count($search['rules']) > 1) {
                    ($orOperator) ? $select->where->OR : $select->where->AND;
                }
            }
        }
        $select->order($sortBy);

        #echo $select->getSqlString();
        #exit();

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new RawMaterials());
        $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function saveRawMaterials(RawMaterials $RawMaterials, Auditing $auditingObj) {
        $data = array(
            'RawMaterialCode' => $RawMaterials->RawMaterialCode,
            'RawMaterialName' => $RawMaterials->RawMaterialName,
            'RMTypeID' => $RawMaterials->RMTypeID,
            'RMSupplierID' => $RawMaterials->RMSupplierID,
            'RawMaterialUnitCost' => $RawMaterials->RawMaterialUnitCost,
            'DateLastChecked' => $RawMaterials->DateLastChecked,
            'LastInvoiceNumber' => $RawMaterials->LastInvoiceNumber
        );
        $id = (int) $RawMaterials->RawMaterialID;

        if ($id == 0) {
            $auditingObj->Action = 'Insert';
            $auditingObj->TableName = 'RawMaterialLookup';
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
                throw new \Exception("Could not add new Raw Material. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            $rawMaterialCurrentData = new RawMaterials();
            $rawMaterialCurrentData = $this->getRawMaterials($id);
            $rawMaterialCurrentArr = (Array) $rawMaterialCurrentData;

            $auditingObj->Action = 'Update';
            $auditingObj->TableName = 'RawMaterialLookup';
            $auditingObj->TableIndex = $id;
            $auditingObj->OldDataJSON = json_encode($rawMaterialCurrentArr);

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->update($data, array('RawMaterialID' => $id));
                $auditingObj->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Raw Material. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function deleteRawMaterials($id, Auditing $auditingObj) {
        $rawMaterialCurrentData = new RawMaterials();
        $rawMaterialCurrentData = $this->getRawMaterials($id);
        $rawMaterialCurrentArr = (Array) $rawMaterialCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'RawMaterialLookup';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($rawMaterialCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('RawMaterialID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Raw Material. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

    public function fetchMaterialsByProduct($productId) {
        $select = new Select();
        $select->from(array('RML' => 'rawmateriallookup'));
        $select->columns(array('RawMaterialID', 'RawMaterialCode', 'RawMaterialName', 'RawMaterialUnitCost', 'RMTypeID'));
        $select->join(array('RMPL' => 'RawMaterialPicklists'), 'RMPL.RawMaterialID = RML.RawMaterialID', array('RawMaterialQty', 'SubtotalRM' => 'RawMaterialQty * RawMaterialUnitCost'));
        $select->join(array('RMT' => 'rawmaterialtypelookup'), 'RMT.RMTypeID = RML.RMTypeID', array('RMTypeName'));
        $select->join(array('SUPP' => 'rawmaterialsupplierlookup'), 'SUPP.RMSupplierID = RML.RMSupplierID', array('RMSupplierName'));
        $select->where(array('RMPL.ProductID' => $productId));

        #echo $select->getSqlString();
        #exit();

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function fetchMaterialsByType($RawMaterialTypeId) {
        $select = new Select();
        $select->from(array('RML' => 'rawmateriallookup'));
        $select->columns(array('RawMaterialID', 'RawMaterialCode', 'RawMaterialName', 'RawMaterialUnitCost'));
        $select->join(array('SUPP' => 'rawmaterialsupplierlookup'), 'SUPP.RMSupplierID = RML.RMSupplierID', array('RMSupplierName'));
        $select->where(array('RML.RMTypeID' => $RawMaterialTypeId));

        #echo $select->getSqlString();
        #exit();

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

}
