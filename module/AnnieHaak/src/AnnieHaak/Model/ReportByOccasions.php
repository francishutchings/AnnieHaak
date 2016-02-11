<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;

class ReportByOccasions {

    public $reportDataArr;
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    public function getReport() {
        $sql = <<<SQL
SELECT
	P.SKU,
	P.ProductID,
	P.ProductName,
	ProductTypes.ProductTypeName,
	ProductCollections.ProductCollectionCode,
	ProductCollections.Current,
	P.PartOfTradePack,
	P.RRP,
	P.Friendship,
	P.Stacks,
	P.PremiumStacks,
	P.SterlingSilver,
	P.Gold,
	P.Charm,
	P.Weddings,
	P.Birthdays,
	P.Accessories,
	P.Engraved
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

        $occasionsArr = array();
        foreach ($results as $result) {
            $tempDataArr['SKU'] = $result['SKU'];
            $tempDataArr['Product'] = $result['ProductName'];
            $tempDataArr['Type'] = $result['ProductTypeName'];
            $tempDataArr['Collection'] = $result['ProductCollectionCode'];
            $tempDataArr['TradePack'] = ($result['PartOfTradePack']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['RRP'] = '£' . number_format($result['RRP'], 2);

            $tempDataArr['Friendship'] = ($result['Friendship']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Stacks'] = ($result['Stacks']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['PremiumStacks'] = ($result['PremiumStacks']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['SterlingSilver'] = ($result['SterlingSilver']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Gold'] = ($result['Gold']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Charm'] = ($result['Charm']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Weddings'] = ($result['Weddings']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Birthdays'] = ($result['Birthdays']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Birthdays'] = ($result['Birthdays']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Accessories'] = ($result['Accessories']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
            $tempDataArr['Engraved'] = ($result['Engraved']) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';

            $occasionsArr[] = $tempDataArr;
        }

        return $occasionsArr;
    }

}
