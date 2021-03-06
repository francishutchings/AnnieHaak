<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;

class ReportCollectionsTypes {

    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    public function getReport() {
        $sql = <<<SQL
                SELECT
                    PC.ProductCollectionName
                    ,PT.ProductTypeName
                    ,COUNT(P.ProductID) AS ProductCount
                FROM
                    ProductCollections PC
                INNER JOIN
                        Products P
                ON
                        P.CollectionID = PC.ProductCollectionID
                INNER JOIN
                        ProductTypes PT
                ON
                        P.ProductTypeID = PT.ProductTypeId
                GROUP BY
                        PC.ProductCollectionName
                        ,PT.ProductTypeId
                ORDER BY
                        PC.ProductCollectionName ASC
                        ,PT.ProductTypeName
SQL;

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        $collectContArr = array();
        foreach ($results as $result) {
            $tempArr[] = $result;
        }

        for ($x = 0; $x < count($tempArr); $x++) {
            $collectContArr[$tempArr[$x]['ProductCollectionName']] = 0;
        }

        foreach ($collectContArr as $key => $value) {

            $arr = array();

            for ($x = 0; $x < count($tempArr); $x++) {

                if ($key == $tempArr[$x]['ProductCollectionName']) {
                    $arr[] = array($tempArr[$x]['ProductTypeName'], $tempArr[$x]['ProductCount']);
                }

                $collectContArr[$key] = $arr;
            }
        }
        return $collectContArr;
    }

}
