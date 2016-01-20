<?php

namespace AnnieHaak\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Predicate\PredicateSet;

class ProductsTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->order('ProductName ASC');
        });
        return $resultSet;
    }

    public function fetchFullDataPaginated($sortBy, $search) {

        $select = new Select();
        $select->from(array('P' => 'Products'));
        $select->columns(array('ProductID', 'ProductName', 'IntroDate'));
        $select->join(array('PT' => 'ProductTypes'), 'PT.ProductTypeID = P.ProductTypeID', array('ProductTypeName'));
        $select->join(array('C' => 'ProductCollections'), 'C.ProductCollectionID = P.CollectionID', array('ProductCollectionName', 'Current'));

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
        $resultSetPrototype->setArrayObjectPrototype(new Products());
        $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function fetchProductsByMaterial($rawMaterialIn) {
        $rawMaterial = (int) $rawMaterialIn;
        $select = new Select();
        $select->from(array('P' => 'Products'));
        $select->columns(array('ProductID', 'ProductName'));
        $select->join(array('RMPL' => 'RawMaterialPickLists'), 'RMPL.ProductID = P.ProductID', array());
        $select->where(array('RMPL.RawMaterialID' => $rawMaterial));
        $select->order('P.ProductName ASC');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

}
