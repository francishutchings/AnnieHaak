<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;

class ProductTypesTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
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

    public function saveProductTypes(ProductTypes $ProductTypes) {
        $data = array(
            'ProductTypeName' => $ProductTypes->ProductTypeName
        );

        $id = (int) $ProductTypes->ProductTypeId;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getProductTypes($id)) {
                $this->tableGateway->update($data, array('ProductTypeId' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deleteProductTypes($id) {
        $this->tableGateway->delete(array('ProductTypeId' => (int) $id));
    }

}
