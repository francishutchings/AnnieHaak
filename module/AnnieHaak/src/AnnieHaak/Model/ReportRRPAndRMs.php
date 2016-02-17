<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;

class ReportRRPAndRMs {

    public $reportDataArr;
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    public function getReport() {
        $sql = <<<SQL
SELECT
	P.ProductID,
	P.SKU,
	P.ProductName,
	ProductTypes.ProductTypeName,
	ProductCollections.ProductCollectionCode,
	ProductCollections.Current,
	P.PartOfTradePack,
	P.RRP,
	(
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

        $RRPAndRMsArr = array();
        foreach ($results as $result) {
            $tempDataArr['SKU'] = $result['SKU'];
            $tempDataArr['Name'] = $result['ProductName'];
            $tempDataArr['Type'] = $result['ProductTypeName'];
            $tempDataArr['Collection'] = $result['ProductCollectionCode'];
            $tempDataArr['TradePack'] = ($result['PartOfTradePack']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['RRP'] = '£' . number_format($result['RRP'], 2);
            $tempDataArr['RMTotal'] = '£' . number_format($result['TotalRMCost'], 2);

            $RRPAndRMsArr[] = $tempDataArr;
        }

        return $RRPAndRMsArr;
    }

}
