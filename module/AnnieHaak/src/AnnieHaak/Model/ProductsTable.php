<?php

namespace AnnieHaak\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
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
        $select->join(array('C' => 'ProductCollections'), 'C.ProductCollectionID = P.CollectionID', array('Current', 'ProductCollectionName'));
        $select->join(array('PT' => 'ProductTypes'), 'PT.ProductTypeId = P.ProductTypeID', array('ProductTypeName'));
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

    public function getProductNameElements() {
        #http://codingexplained.com/coding/php/zend-framework/multiple-result-sets-from-stored-procedure-in-zf2

        $connection = $this->tableGateway->getAdapter()->getDriver()->getConnection();
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

    public function saveProducts(Products $products, Auditing $auditingObj, array $productAssocData) {
        $data = array(
            'Accessories' => $products->Accessories,
            'Birthdays' => $products->Birthdays,
            'Charm' => $products->Charm,
            'CollectionID' => $products->CollectionID,
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

        $auditingProducts = clone($auditingObj);
        $auditingRawMaterials = clone($auditingObj);
        $auditingPackaging = clone($auditingObj);
        $auditingLabourItems = clone($auditingObj);

// Raw Materials Insert
        $sql_RMIn = "INSERT INTO RawMaterialPickLists (ProductID, RawMaterialID, RawMaterialQty) VALUES (:ProductID, :RawMaterialID, :RawMaterialQty)";
        $stmt_RMIn = $this->tableGateway->getAdapter()->query($sql_RMIn);

// Packaging Insert
        $sql_PIn = "INSERT INTO PackagingPickLists (ProductID, PackagingID, PackagingQty) VALUES (:ProductID, :PackagingID, :PackagingID)";
        $stmt_PIn = $this->tableGateway->getAdapter()->query($sql_PIn);

// Labout Items Insert
        $sql_LIIn = "INSERT INTO LabourTime (ProductID, LabourID, LabourQty) VALUES (:ProductID, :LabourID, :LabourQty)";
        $stmt_LIIn = $this->tableGateway->getAdapter()->query($sql_LIIn);

        if ($id == 0) {
            $auditingProducts->Action = 'Insert';
            $auditingProducts->TableName = 'Products';
            $auditingProducts->OldDataJSON = '';

            $auditingRawMaterials->Action = 'Insert';
            $auditingRawMaterials->TableName = 'RawMaterialPickLists';
            $auditingRawMaterials->OldDataJSON = '';

            $auditingPackaging->Action = 'Insert';
            $auditingPackaging->TableName = 'PackagingPickLists';
            $auditingPackaging->OldDataJSON = '';

            $auditingLabourItems->Action = 'Insert';
            $auditingLabourItems->TableName = 'LabourTime';
            $auditingLabourItems->OldDataJSON = '';

            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                $this->tableGateway->insert($data);
                $newProductID = $this->tableGateway->lastInsertValue;

                //Insert + Update new data
                foreach ($productAssocData['rawMaterialsData'] as $key => $value) {
                    $value['ProductID'] = $newProductID;
                    $stmt_RMIn->execute($value);
                }
                foreach ($productAssocData['packagingData'] as $key => $value) {
                    $value['ProductID'] = $newProductID;
                    $stmt_PIn->execute($value);
                }
                foreach ($productAssocData['labourItemsData'] as $key => $value) {
                    $value['ProductID'] = $newProductID;
                    $stmt_LIIn->execute($value);
                }
                $auditingProducts->TableIndex = $newProductID;
                $auditingRawMaterials->TableIndex = $newProductID;
                $auditingPackaging->TableIndex = $newProductID;
                $auditingLabourItems->TableIndex = $newProductID;

                $auditingProducts->saveAuditAction();
                $auditingRawMaterials->saveAuditAction();
                $auditingPackaging->saveAuditAction();
                $auditingLabourItems->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not create new Product. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        } else {
            // GET CURRENT DATA
            // Product Current
            $productsCurrentData = new Products();
            $productsCurrentData = $this->getProducts($id);
            $productsCurrentArr = (Array) $productsCurrentData;

            $auditingProducts->Action = 'Update';
            $auditingProducts->TableName = 'Products';
            $auditingProducts->TableIndex = $id;
            $auditingProducts->OldDataJSON = json_encode($productsCurrentArr);

            // Raw Materials Current
            //===========================================
            $sql_RM = "SELECT ProductID, RawMaterialID, RawMaterialQty FROM RawMaterialPickLists WHERE ProductID = :ProductID";
            $stmt_RM = $this->tableGateway->getAdapter()->query($sql_RM);
            $resultSet_RM = $stmt_RM->execute(array('ProductID' => $id));
            foreach ($resultSet_RM as $value) {
                $rawMaterialsCurrentArr[] = $value;
            }
            $auditingRawMaterials->Action = 'Delete - Insert';
            $auditingRawMaterials->TableName = 'RawMaterialPickLists';
            $auditingRawMaterials->TableIndex = $id;
            $auditingRawMaterials->OldDataJSON = json_encode($rawMaterialsCurrentArr);

            // Raw Materials Delete
            $sql_RMDel = "DELETE FROM RawMaterialPickLists WHERE ProductID = :ProductID";
            $stmt_RMDel = $this->tableGateway->getAdapter()->query($sql_RMDel);

            // Packaging Current
            //===========================================
            $sql_P = "SELECT ProductID, PackagingID, PackagingQty FROM PackagingPickLists WHERE ProductID = :ProductID";
            $stmt_P = $this->tableGateway->getAdapter()->query($sql_P);
            $resultSet_P = $stmt_P->execute(array('ProductID' => $id));
            foreach ($resultSet_P as $value) {
                $packagingCurrentArr[] = $value;
            }
            $auditingPackaging->Action = 'Delete - Insert';
            $auditingPackaging->TableName = 'PackagingPickLists';
            $auditingPackaging->TableIndex = $id;
            $auditingPackaging->OldDataJSON = json_encode($packagingCurrentArr);

            // Packaging Delete
            $sql_PDel = "DELETE FROM PackagingPickLists WHERE ProductID = :ProductID";
            $stmt_PDel = $this->tableGateway->getAdapter()->query($sql_PDel);

            // Labout Items
            //===========================================
            $sql_LI = "SELECT ProductID, LabourID, LabourQty FROM LabourTime WHERE ProductID = :ProductID";
            $stmt_LI = $this->tableGateway->getAdapter()->query($sql_LI);
            $resultSet_LI = $stmt_LI->execute(array('ProductID' => $id));
            foreach ($resultSet_LI as $value) {
                $labourItemsCurrentArr[] = $value;
            }
            $auditingLabourItems->Action = 'Delete - Insert';
            $auditingLabourItems->TableName = 'LabourTime';
            $auditingLabourItems->TableIndex = $id;
            $auditingLabourItems->OldDataJSON = json_encode($labourItemsCurrentArr);

            // Labout Items Delete
            $sql_LIDel = "DELETE FROM LabourTime WHERE ProductID = :ProductID";
            $stmt_LIDel = $this->tableGateway->getAdapter()->query($sql_LIDel);

            // SAVE ALL CURRENT DATA
            //===========================================
            $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connectCntrl->beginTransaction();
            try {
                //Delete related Product data
                $stmt_RMDel->execute(array('ProductID' => $id));
                $stmt_PDel->execute(array('ProductID' => $id));
                $stmt_LIDel->execute(array('ProductID' => $id));

                //Insert + Update new data
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

                //Record actions
                $auditingProducts->saveAuditAction();
                $auditingRawMaterials->saveAuditAction();
                $auditingPackaging->saveAuditAction();
                $auditingLabourItems->saveAuditAction();
            } catch (\Exception $ex) {
                $connectCntrl->rollback();
                throw new \Exception("Could not update Product. " . $ex->getPrevious()->errorInfo[2]);
            }
            $connectCntrl->commit();
        }
    }

    public function deleteProducts($id, Auditing $auditingObj) {
        $collectionsCurrentData = new Collections();
        $collectionsCurrentData = $this->getCollections($id);
        $collectionsCurrentArr = (Array) $collectionsCurrentData;

        $auditingObj->Action = 'Delete';
        $auditingObj->TableName = 'ProductCollections';
        $auditingObj->TableIndex = $id;
        $auditingObj->OldDataJSON = json_encode($collectionsCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->delete(array('ProductID' => (int) $id));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not delete Collection. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
