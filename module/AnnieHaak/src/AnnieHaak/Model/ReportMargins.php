<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;
use AnnieHaak\Model\RatesPercentages;
use AnnieHaak\Model\FinancialCalculator;

class ReportMargins {

    protected $dbAdapter;
    protected $ratesPercentages;

    public function __construct(Adapter $dbAdapter, RatesPercentages $ratesPercentages) {
        $this->dbAdapter = $dbAdapter;
        $this->ratesPercentages = $ratesPercentages;
        return $this;
    }

    public function getReport() {
        foreach ($this->ratesPercentages as $key => $value) {
            $ratesPercentagesArr[$key] = $value;
        }
        $sql = <<<SQL
SELECT
    P.ProductID
    ,P.SKU
    ,P.RequiresAssay
    ,P.ProductName
    ,P.ExcludeFromTrade
    ,ProductTypes.ProductTypeName
    ,ProductCollections.ProductCollectionCode
    ,ProductCollections.Current
    ,P.PartOfTradePack
    ,P.RRP
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
    ) AS TotalPackCost
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
    ProductTypes
INNER JOIN
(
    ProductCollections
        INNER JOIN
            Products AS P
        ON
            ProductCollections.ProductCollectionID = P.CollectionID
)
ON
    ProductTypes.ProductTypeId = P.ProductTypeID
WHERE
    ProductCollections.Current = TRUE
ORDER BY
    P.ProductName;
SQL;

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        foreach ($results as $result) {
            #dump($result);
            #exit();

            $tempDataArr['ProductId'] = $result['ProductID'];
            $tempDataArr['SKU'] = $result['SKU'];
            $tempDataArr['Name'] = $result['ProductName'];
            $tempDataArr['Type'] = $result['ProductTypeName'];
            $tempDataArr['Collection'] = $result['ProductCollectionCode'];
            $tempDataArr['Current'] = ($result['Current']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['TradePack'] = ($result['PartOfTradePack']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';

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
            foreach ($financialData as $key => $value) {
                if ($key != "SubtotalBoxCostTxt") {
                    $financialData[$key] = number_format((float) $value, 2, '.', '');
                }
            }

            $tempDataArr['RetailNewRRP'] = $financialData['RetailNewRRP'];
            $tempDataArr['RetailProfit'] = $financialData['RetailNewProfit'];
            $tempDataArr['RetailMargin'] = $financialData['RetailNewActualPerc'];
            $tempDataArr['TradeProfit'] = $financialData['TradeProfit'];
            $tempDataArr['TradeMargin'] = $financialData['TradeActual'];

            $returnArr[] = $tempDataArr;
        }
        return $returnArr;
    }

}
