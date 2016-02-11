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
	SUM(RawMaterialPicklists.RawMaterialQty) AS SumOfRawMaterialQty
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
			RawMaterialPicklists
		ON
			Products.ProductID = RawMaterialPicklists.ProductID
)
ON
	RawMaterialLookup.RawMaterialID = RawMaterialPicklists.RawMaterialID
WHERE
	Products.PartOfTradePack = TRUE
GROUP BY
	RawMaterialLookup.RawMaterialName,
	Products.PartOfTradePack
ORDER BY
	SUM(RawMaterialPicklists.RawMaterialQty) DESC;
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
