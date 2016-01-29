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
        $select->columns(array('RawMaterialID', 'RawMaterialCode', 'RawMaterialName', 'RawMaterialUnitCost', 'DateLastChecked', 'LastInvoiceNumber'));
        $select->join(array('Supp' => 'rawmaterialsupplierlookup'), 'Supp.RMSupplierID = RM.RMSupplierID', array('RMSupplierName'));
        $select->join(array('RMType' => 'rawmaterialtypelookup'), 'RMType.RMTypeID = RM.RMTypeID', array('RMTypeName'));
        $select->where(array('RM.RawMaterialID' => array($ids)));

        #echo $select->getSqlString();
        #exit();

        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
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

    public function saveRawMaterials(RawMaterials $RawMaterials) {
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
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRawMaterials($id)) {
                $this->tableGateway->update($data, array('RawMaterialID' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deleteRawMaterials($id) {
        $this->tableGateway->delete(array('RawMaterialID' => (int) $id));
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
