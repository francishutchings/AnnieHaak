<?php

namespace AnnieHaak\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

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

    public function getProducts($id) {
        $id = (int) $id;
        $select = new Select();
        $select->from(array('P' => 'Products'));
        $select->columns(array(
            'Accessories',
            'Birthdays',
            'Charm',
            'CollectionID',
            'CurrentURL',
            'Description',
            'DescriptionStatus',
            'Engraved',
            'ExcludeFromTrade',
            'Friendship',
            'Gold',
            'IntroDate',
            'KeyPiece',
            'MinsToBuild',
            'Name',
            'NameCharm',
            'NameColour',
            'NameCrystal',
            'NameLength',
            'OldURL',
            'PartOfTradePack',
            'Personalisable',
            'PremiumStacks',
            'ProductID',
            'ProductName',
            'ProductTypeID',
            'QtyInTradePack',
            'QtyOrderedLastPeriod',
            'RequiresAssay',
            'RRP',
            'SKU',
            'Stacks',
            'SterlingSilver',
            'Strands',
            'Weddings'
        ));
        $select->join(array('C' => 'ProductCollections'), 'C.ProductCollectionID = P.CollectionID', array('Current'));
        $select->where(array('P.ProductID' => $id));

        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Product entry does not exist, ID: $id");
        }
        return $row;
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

    public function getProductNameElements(Adapter $dbAdapter) {
        #http://codingexplained.com/coding/php/zend-framework/multiple-result-sets-from-stored-procedure-in-zf2

        $driver = $dbAdapter->getDriver();
        $connection = $driver->getConnection();
        $result = $connection->execute('CALL productNameElements');
        $statement = $result->getResource();

        $resultSet1 = $statement->fetchAll(\PDO::FETCH_OBJ);
        foreach ($resultSet1 as $row) {
            $charms[$row->CharmIdx] = $row->CharmName;
        }

        $statement->nextRowSet();
        $resultSet2 = $statement->fetchAll(\PDO::FETCH_OBJ);
        foreach ($resultSet2 as $row) {
            $colours[$row->ColourIdx] = $row->ColourName;
        }

        $statement->nextRowSet();
        $resultSet3 = $statement->fetchAll(\PDO::FETCH_OBJ);
        foreach ($resultSet3 as $row) {
            $crystals[$row->CrystalIdx] = $row->CrystalName;
        }

        $statement->nextRowSet();
        $resultSet4 = $statement->fetchAll(\PDO::FETCH_OBJ);
        foreach ($resultSet4 as $row) {
            $lengths[$row->LengthIdx] = $row->Length . 'cm';
        }

        return array(
            'charms' => $charms,
            'colours' => $colours,
            'crystals' => $crystals,
            'lengths' => $lengths
        );
    }

    public function saveProducts(Products $products, array $productAssocData) {
        $data = array(
            'Accessories' => $products->Accessories,
            'Birthdays' => $products->Birthdays,
            'Charm' => $products->Charm,
            'CollectionID' => $products->CollectionID,
            'ProductCollectionName' => trim($products->ProductCollectionName),
            'CurrentURL' => trim($products->CurrentURL),
            'Description' => trim($products->Description),
            'DescriptionStatus' => $products->DescriptionStatus,
            'Engraved' => $products->Engraved,
            'ExcludeFromTrade' => $products->ExcludeFromTrade,
            'Friendship' => $products->Friendship,
            'Gold' => $products->Gold,
            'IntroDate' => $products->IntroDate,
            'KeyPiece' => $products->KeyPiece,
            'MinsToBuild' => $products->MinsToBuild,
            'Name' => trim($products->Name),
            'NameCharm' => $products->NameCharm,
            'NameColour' => $products->NameColour,
            'NameCrystal' => $products->NameCrystal,
            'NameLength' => $products->NameLength,
            'OldURL' => trim($products->OldURL),
            'PartOfTradePack' => $products->PartOfTradePack,
            'Personalisable' => $products->Personalisable,
            'PremiumStacks' => $products->PremiumStacks,
            'ProductName' => trim($products->ProductName),
            'ProductTypeID' => $products->ProductTypeID,
            'QtyInTradePack' => $products->QtyInTradePack,
            'QtyOrderedLastPeriod' => $products->QtyOrderedLastPeriod,
            'RequiresAssay' => $products->RequiresAssay,
            'RRP' => $products->RRP,
            'SKU' => strtoupper($products->SKU),
            'Stacks' => $products->Stacks,
            'SterlingSilver' => $products->SterlingSilver,
            'Strands' => $products->Strands,
            'Weddings' => $products->Weddings
        );
        $id = (int) $products->ProductID;

        #dump($id);
        #dump($data);
        #dump($products);
        #dump($productAssocData);

        $auditingProducts = clone($productAssocData['auditingObj']);
        $auditingRawMaterials = clone($productAssocData['auditingObj']);
        $auditingPackaging = clone($productAssocData['auditingObj']);
        $auditingLabourItems = clone($productAssocData['auditingObj']);

        if ($id == 0) {
            $productAssocData->Auditing->UserName = $productAssocData->user['username'];
            $productAssocData->Auditing->Action = 'insert';
            $productAssocData->Auditing->TableName = 'products';
            $productAssocData->Auditing->OldDataJSON = '';

            $productAssocData->Auditing->saveAuditAction();

            #$this->tableGateway->insert($data);
        } else {
            // GET CURRENT DATA
            // Product Current
            $productsCurrentData = new Products();
            $productsCurrentData = $this->getProducts($id);
            $productsCurrentArr = (Array) $productsCurrentData;
            $auditingProducts->UserName = $productAssocData['user']['username'];
            $auditingProducts->Action = 'update';
            $auditingProducts->TableName = 'products';
            $auditingProducts->OldDataJSON = json_encode($productsCurrentArr);

            // Raw Materials Current
            //===========================================
            $sql_RM = "SELECT ProductID, RawMaterialID, RawMaterialQty FROM RawMaterialPickLists WHERE ProductID = :ProductID";
            $stmt_RM = $productAssocData['dbAdapter']->query($sql_RM);
            $resultSet_RM = $stmt_RM->execute(array('ProductID' => $id));
            foreach ($resultSet_RM as $value) {
                $rawMaterialsCurrentArr[] = $value;
            }
            $auditingRawMaterials->UserName = $productAssocData['user']['username'];
            $auditingRawMaterials->Action = 'update';
            $auditingRawMaterials->TableName = 'RawMaterialPickLists';
            $auditingRawMaterials->OldDataJSON = json_encode($rawMaterialsCurrentArr);

            // Raw Materials Delete
            $sql_RMDel = "DELETE FROM RawMaterialPickLists WHERE ProductID = :ProductID";
            $stmt_RMDel = $productAssocData['dbAdapter']->query($sql_RMDel);

            // Raw Materials Insert
            $sql_RMIn = "INSERT INTO RawMaterialPickLists (ProductID, RawMaterialID, RawMaterialQty) VALUES (:ProductID, :RawMaterialID, :RawMaterialQty)";
            $stmt_RMIn = $productAssocData['dbAdapter']->query($sql_RMIn);

            dump($auditingRawMaterials);
            dump($stmt_RMDel);
            dump($stmt_RMIn);

            // Packaging Current
            //===========================================
            $sql_P = "SELECT ProductID, PackagingID, PackagingQty FROM PackagingPickLists WHERE ProductID = :ProductID";
            $stmt_P = $productAssocData['dbAdapter']->query($sql_P);
            $resultSet_P = $stmt_P->execute(array('ProductID' => $id));
            foreach ($resultSet_P as $value) {
                $packagingCurrentArr[] = $value;
            }
            $auditingPackaging->UserName = $productAssocData['user']['username'];
            $auditingPackaging->Action = 'update';
            $auditingPackaging->TableName = 'PackagingPickLists';
            $auditingPackaging->OldDataJSON = json_encode($packagingCurrentArr);

            // Packaging Delete
            $sql_PDel = "DELETE FROM PackagingPickLists WHERE ProductID = :ProductID";
            $stmt_PDel = $productAssocData['dbAdapter']->query($sql_PDel);

            // Packaging Insert
            $sql_PIn = "INSERT INTO PackagingPickLists (ProductID, PackagingID, PackagingQty) VALUES (:ProductID, :PackagingID, :PackagingID)";
            $stmt_PIn = $productAssocData['dbAdapter']->query($sql_PIn);

            dump($auditingPackaging);
            dump($stmt_PDel);
            dump($stmt_PIn);

            // Labout Items
            //===========================================
            $sql_LI = "SELECT ProductID, LabourID, LabourQty FROM LabourTime WHERE ProductID = :ProductID";
            $stmt_LI = $productAssocData['dbAdapter']->query($sql_LI);
            $resultSet_LI = $stmt_LI->execute(array('ProductID' => $id));
            foreach ($resultSet_LI as $value) {
                $labourItemsCurrentArr[] = $value;
            }
            $auditingLabourItems->UserName = $productAssocData['user']['username'];
            $auditingLabourItems->Action = 'update';
            $auditingLabourItems->TableName = 'LabourTime';
            $auditingLabourItems->OldDataJSON = json_encode($labourItemsCurrentArr);

            // Labout Items Delete
            $sql_LIDel = "DELETE FROM LabourTime WHERE ProductID = :ProductID";
            $stmt_LIDel = $productAssocData['dbAdapter']->query($sql_LIDel);

            // Labout Items Insert
            $sql_LIIn = "INSERT INTO LabourTime (ProductID, LabourID, LabourQty) VALUES (:ProductID, :LabourID, :LabourQty)";
            $stmt_LIIn = $productAssocData['dbAdapter']->query($sql_LIIn);

            dump($auditingLabourItems);
            dump($stmt_LIDel);
            dump($stmt_LIIn);
            exit();

            // SAVE ALL CURRENT DATA
            //===========================================
            $productAssocData['dbAdapter']->getDriver()->getConnection()->beginTransaction();
            try {
                $productAssocData['auditingObj']->saveAuditAction($auditingProducts);
                $productAssocData['auditingObj']->saveAuditAction($auditingRawMaterials);
                $productAssocData['auditingObj']->saveAuditAction($auditingPackaging);
                $productAssocData['auditingObj']->saveAuditAction($auditingLabourItems);

                $stmt_RMDel->execute(array('ProductID' => $id));
                $stmt_PDel->execute(array('ProductID' => $id));
                $stmt_LIDel->execute(array('ProductID' => $id));

                foreach ($productAssocData['rawMaterialsData'] as $value) {
                    $stmt_RMIn->execute($value);
                }

                foreach ($productAssocData['packagingData'] as $value) {
                    $stmt_PIn->execute($value);
                }

                foreach ($productAssocData['labourItemsData'] as $value) {
                    $stmt_LIIn->execute($value);
                }

                $this->tableGateway->update($data, array('ProductID' => $id));
            } catch (Exception $e) {
                $productAssocData['dbAdapter']->getDriver()->getConnection()->rollback();
                dump($e);
                exit();
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
            $productAssocData['dbAdapter']->getDriver()->getConnection()->commit();

            exit();
        }
    }

    public function deleteRawMaterials($id) {
        $this->tableGateway->delete(array('RawMaterialID' => (int) $id));
    }

}
