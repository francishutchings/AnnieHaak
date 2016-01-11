<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;

class MarginsReport {

    public $reportDataArr;
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this->getReport();
    }

    private function getReport() {
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
            SUM(RL2.RawMaterialUnitCost * RMPL2.RawMaterialQty)
        FROM
            RawMaterialLookup AS RL2
        INNER JOIN
            RawMaterialPicklists AS RMPL2
        ON
            RL2.RawMaterialID = RMPL2.RawMaterialID
        WHERE
            RMPL2.ProductID = P.ProductID
    ) AS TotalRMCost
    ,(
        SELECT
            SUM(LLookup.LabourUnitCost * LTime.LabourQty)
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
            SUM(PLookup.PackagingUnitCost * PPick.PackagingQty)
        FROM
            PackagingLookup AS PLookup
        INNER JOIN
            PackagingPicklists AS PPick
        ON
            PLookup.PackagingID = PPick.PackagingID
        WHERE
            PPick.ProductID = P.ProductID
    ) AS TotalPackCost
    ,(
        SELECT
            SUM(PLookup.PackagingUnitCost * PPick.PackagingQty)
        FROM
            PackagingLookup AS PLookup
        INNER JOIN
            PackagingPicklists AS PPick
        ON
            PLookup.PackagingID = PPick.PackagingID
        WHERE
            PPick.ProductID = P.ProductID
        AND
            PLookup.PackagingType = 1
    ) AS BoxCost
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
    P.ProductID;
SQL;

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        foreach ($results as $result) {
            $this->reportDataArr[] = $result;
        }
    }

}
