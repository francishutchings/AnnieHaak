<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;

class ReportTradePackRmsTime {

    public $reportDataArr;
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    public function getReport() {
        $sql = <<<SQL
SELECT
	SUM(RawMaterialPickLists.RawMaterialQty) AS SumOfRawMaterialQty
	,RawMaterialLookup.RawMaterialName
	,(
		SELECT
			SUM(minstobuild * qtyintradepack)
		FROM
			Products
		WHERE
			Products.PartOfTradePack = True
	)  AS SubTotalMinsToBuild
FROM
	RawMaterialLookup
INNER JOIN
(
	Products
		INNER JOIN
			RawMaterialPickLists
		ON
			Products.ProductID = RawMaterialPickLists.ProductID
)
ON
	RawMaterialLookup.RawMaterialID = RawMaterialPickLists.RawMaterialID
WHERE
	Products.PartOfTradePack = TRUE
GROUP BY
	RawMaterialLookup.RawMaterialName,
	Products.PartOfTradePack
ORDER BY
	SUM(RawMaterialPickLists.RawMaterialQty) DESC;
SQL;

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        $tradePackRmsTimeArr = array();
        foreach ($results as $result) {
            $tempDataArr['Qty'] = $result['SumOfRawMaterialQty'];
            $tempDataArr['RawMaterialName'] = $result['RawMaterialName'];
            $tempDataArr['TotalBuildTime'] = number_format($result['SubTotalMinsToBuild'] / 60, 2);

            $tradePackRmsTimeArr[] = $tempDataArr;
        }

        return $tradePackRmsTimeArr;
    }

}
