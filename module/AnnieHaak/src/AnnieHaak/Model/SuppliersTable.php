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
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveSuppliers(Suppliers $Supplier) {
        $data = array(
            'RMSupplierName' => $Supplier->RMSupplierName
        );

        $id = (int) $Supplier->RMSupplierID;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSuppliers($id)) {
                $this->tableGateway->update($data, array('RMSupplierID' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deleteSuppliers($id) {
        $this->tableGateway->delete(array('RMSupplierID' => (int) $id));
    }

}
