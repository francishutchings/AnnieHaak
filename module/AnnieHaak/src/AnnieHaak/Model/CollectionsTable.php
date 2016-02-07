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

    public function saveCollections(Collections $Collections, array $collectionsAssocData) {
        $data = array(
            'ProductCollectionName' => $Collections->ProductCollectionName,
            'ProductCollectionCode' => $Collections->ProductCollectionCode,
            'Current' => $Collections->Current
        );

        $id = (int) $Collections->ProductCollectionID;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $auditingProducts->UserName = $productAssocData['user']['username'];
            $auditingProducts->Action = 'insert';
            $auditingProducts->TableName = 'products';
            $auditingProducts->OldDataJSON = '';
        } else {
            if ($this->getCollections($id)) {
                $this->tableGateway->update($data, array('ProductCollectionID' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deleteCollections($id) {
        $this->tableGateway->delete(array('ProductCollectionID' => (int) $id));
    }

}
