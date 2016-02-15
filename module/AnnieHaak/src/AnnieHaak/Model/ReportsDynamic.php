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
	,P.RRP
	,P.KeyPiece
	,P.MinsToBuild
	,P.QtyInTradePack
	,PT.ProductTypeName
	,PC.ProductCollectionName
	,PC.Current
FROM
	products AS P

INNER JOIN
	producttypes AS PT
ON
	PT.ProductTypeId = P.ProductTypeID

INNER JOIN
	productcollections AS PC
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

        #dump($sql);
        #exit();

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        #echo $statement->getSql();

        $returnArr = array();
        foreach ($results as $result) {
            #dump($result);
            #exit();

            $returnArr[] = $result;
        }
        #dump($returnArr);
        #exit();

        return $returnArr;
    }

}
