<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Model\FinancialCalculator;

class ReportsDynamic {

    protected $dbAdapter;
    protected $ratesPercentages;

    public function __construct(Adapter $dbAdapter, RatesPercentages $ratesPercentages) {
        $this->dbAdapter = $dbAdapter;
        $this->ratesPercentages = $ratesPercentages;
        return $this;
    }

    public function getReport(Array $formData) {
        #dump($formData);
        #exit();

        $reportType = (Int) $formData['ReportType'];
        $collectionId = (Int) $formData['CollectionID'];
        $productTypeId = (Int) $formData['ProductTypeID'];

        foreach ($this->ratesPercentages as $key => $value) {
            $ratesPercentagesArr[$key] = $value;
        }

        $sql = 'SELECT
	P.ProductName
	,P.SKU
	,P.Personalisable
	,P.PartOfTradePack
	,P.RRP
	,P.RequiresAssay
	,P.KeyPiece
	,P.MinsToBuild
	,P.QtyInTradePack
	,PT.ProductTypeName
	,PC.ProductCollectionName
	,PC.Current
        ,(
            SELECT
                IFNULL(SUM(RL2.RawMaterialUnitCost * RMPL2.RawMaterialQty), 0)
            FROM
                RawMaterialLookup AS RL2
            INNER JOIN
                RawMaterialPickLists AS RMPL2
            ON
                RL2.RawMaterialID = RMPL2.RawMaterialID
            WHERE
                RMPL2.ProductID = P.ProductID
        ) AS TotalRMCost
        ,(
            SELECT
                IFNULL(SUM(LLookup.LabourUnitCost * LTime.LabourQty), 0)
            FROM
                LabourLookup AS LLookup
            INNER JOIN
                LabourTime AS LTime
            ON
                LLookup.LabourID = LTime.LabourID
            WHERE
                LTime.ProductID = P.ProductID
        ) AS TotalLabourCost
        ,(
            SELECT
                IFNULL(SUM(PLookup.PackagingUnitCost * PPick.PackagingQty), 0)
            FROM
                PackagingLookup AS PLookup
            INNER JOIN
                PackagingPickLists AS PPick
            ON
                PLookup.PackagingID = PPick.PackagingID
            WHERE
                PPick.ProductID = P.ProductID
            AND
                PLookup.PackagingType = 1
        ) AS SBoxCost
        ,(
            SELECT
                IFNULL(SUM(PLookup.PackagingUnitCost * PPick.PackagingQty), 0)
            FROM
                PackagingLookup AS PLookup
            INNER JOIN
                PackagingPickLists AS PPick
            ON
                PLookup.PackagingID = PPick.PackagingID
            WHERE
                PPick.ProductID = P.ProductID
            AND
                PLookup.PackagingType = 2
        ) AS LBoxCost
        ,(
            SELECT
                IFNULL(SUM(PLookup.PackagingUnitCost * PPick.PackagingQty), 0)
            FROM
                PackagingLookup AS PLookup
            INNER JOIN
                PackagingPickLists AS PPick
            ON
                PLookup.PackagingID = PPick.PackagingID
            WHERE
                PPick.ProductID = P.ProductID
            AND
                PLookup.PackagingType = 3
        ) AS SBagCost
        ,(
            SELECT
                IFNULL(SUM(PLookup.PackagingUnitCost * PPick.PackagingQty), 0)
            FROM
                PackagingLookup AS PLookup
            INNER JOIN
                PackagingPickLists AS PPick
            ON
                PLookup.PackagingID = PPick.PackagingID
            WHERE
                PPick.ProductID = P.ProductID
            AND
                PLookup.PackagingType = 4
        ) AS LBagCost
FROM
	Products AS P

INNER JOIN
	ProductTypes AS PT
ON
	PT.ProductTypeId = P.ProductTypeID

INNER JOIN
	ProductCollections AS PC
ON
	PC.ProductCollectionID = P.CollectionID

';

        switch ($reportType) {
            case 1:
                $sql .= ' WHERE P.PartOfTradePack = FALSE';
                break;
            case 2:
                $sql .= ' WHERE P.PartOfTradePack = TRUE';
                break;
            case 3:
                $sql .= ' WHERE P.ExcludeFromTrade = FALSE';
                break;
        }

        if ($collectionId > 0) {
            $sql .= ' AND P.CollectionID = ' . $collectionId;
        }

        if ($productTypeId > 0) {
            $sql .= ' AND P.ProductTypeId = ' . $productTypeId;
        }


        $sql .= ' ORDER BY P.ProductName';

        #dump($sql);
        #exit();

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        #echo $statement->getSql();

        $returnArr = array();
        foreach ($results as $key => $result) {

            $subtotals['RawMaterials'] = $result['TotalRMCost'];
            $subtotals['LabourItems'] = $result['TotalLabourCost'];
            $subtotals['Packaging'] = array(
                'BAG' => $result['SBagCost'] + $result['LBoxCost'],
                'BOX' => $result['SBoxCost'] + $result['LBagCost']
            );

            $product['RRP'] = $result['RRP'];
            $product['RequiresAssay'] = $result['RequiresAssay'];

            $financialCalcSubTotals = new FinancialCalculator($ratesPercentagesArr, $subtotals, $product);
            $financialData = $financialCalcSubTotals->calculateFinancials();

            #dump($result['ProductName']);
            #dump($result['RRP']);
            #dump($financialData);
            #exit();

            $returnArr[$key] = $result;
            $returnArr[$key]['TradePrice'] = $financialData['TradePrice'];
            $returnArr[$key]['RRP'] = $financialData['RetailNewRRP'];
        }
        #dump($returnArr);
        #exit();

        return $returnArr;
    }

}
