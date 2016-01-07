<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;

class CollectionsTypesReport {

    public $reportDataArr;
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this->getReport();
    }

    private function getReport() {
        $sql = <<<SQL
                SELECT
                    PC.ProductCollectionName
                    ,PT.ProductTypeName
                    ,COUNT(P.ProductID) AS ProductCount
                FROM
                    productcollections PC
                INNER JOIN
                        products P
                ON
                        P.CollectionID = PC.ProductCollectionID
                INNER JOIN
                        producttypes PT
                ON
                        P.ProductTypeID = PT.ProductTypeId
                GROUP BY
                        PC.ProductCollectionName
                        ,PT.ProductTypeId
                ORDER BY
                        PC.ProductCollectionName ASC
                        ,PT.ProductTypeName
SQL;

        $statement = $this->dbAdapter->query($sql);
        $results = $statement->execute();

        foreach ($results as $result) {
            $this->reportDataArr[] = $result;
        }
    }

}

/*
SELECT
    PC.ProductCollectionName
    ,PT.ProductTypeName
    ,COUNT(P.ProductID) AS ProductCount
FROM
    productcollections PC
INNER JOIN
	products P
ON
	P.CollectionID = PC.ProductCollectionID
INNER JOIN
	producttypes PT
ON
	P.ProductTypeID = PT.ProductTypeId
GROUP BY
	PC.ProductCollectionName
	,PT.ProductTypeId
ORDER BY
	PC.ProductCollectionName ASC
  	,PT.ProductTypeName
 */