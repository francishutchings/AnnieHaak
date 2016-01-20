<?php

namespace AnnieHaak\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

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

    public function getRawMaterials($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('RawMaterialID' => $id));
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
            /*
              {oper: 'eq', text: 'equal'},
              {oper: 'cn', text: 'contains'},
              {oper: 'bw', text: 'begins with'},
              {oper: 'ew', text: 'ends with'},
              {oper: 'lt', text: 'less than'},
              {oper: 'le', text: 'less or equal to'},
              {oper: 'gt', text: 'greater than'},
              {oper: 'ge', text: 'greater or equal to'}
             */
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

}
