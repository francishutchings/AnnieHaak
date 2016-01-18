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
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function fetchFullDataPaginated($sortBy, $search) {
        $select = new Select();
        $select->from('rawmaterialtypelookup');
        $select->columns(array('RMTypeID', 'RMTypeName'));

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

        #dump($select->getSqlString());
        #exit();

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new RawMaterialTypes());
        $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function saveRawMaterialTypes(RawMaterialTypes $RawMaterialTypes) {
        $data = array(
            'RMTypeName' => $RawMaterialTypes->RMTypeName
        );

        $id = (int) $RawMaterialTypes->RMTypeID;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRawMaterialTypes($id)) {
                $this->tableGateway->update($data, array('RMTypeID' => $id));
            } else {
                throw new \Exception('Product Type id does not exist');
            }
        }
    }

    public function deleteRawMaterialTypes($id) {
        $this->tableGateway->delete(array('RMTypeID' => (int) $id));
    }

}
