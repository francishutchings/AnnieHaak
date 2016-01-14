<?php

namespace AnnieHaak\Model;

use Zend\Db\Adapter\Adapter;
use AnnieHaak\Model\RatesPercentages;

class MarginsReport {

    public $reportDataArr;
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
        return $this->getReport();
    }

    private function getReport() {
        $ratePercents = new RatesPercentages($this->dbAdapter);
        $ratesPercentages = $ratePercents->fetchAll();

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
    P.ProductName;
SQL;

        $statement = $this->dbAdapter->createStatement($sql);
        $results = $statement->execute();

        foreach ($results as $result) {
            $tempDataArr[] = $result;
        }

        #       dump($tempDataArr);
#        exit();

        for ($x = 0; $x < count($tempDataArr); $x++) {

            #START
            if ($tempDataArr[$x]['ProductName'] == 'Ankle Silver Charm Bracelet - Butterfly') {

                $tempArr = array();
                foreach ($tempDataArr[$x] as $key => $value) {
                    switch ($key) {
                        case 'SKU':
                            $tempArr['SKU'] = $value;
                            break;
                        case 'ProductName':
                            $tempArr['Name'] = $value;
                            break;
                        case 'ProductTypeName':
                            $tempArr['Type'] = $value;
                            break;
                        case 'ProductCollectionCode':
                            $tempArr['Collection'] = $value;
                            break;
                        case 'Current':
                            $tempArr['Current'] = ($value) ? 'Yes' : 'No';
                            break;
                        case 'PartOfTradePack':
                            $tempArr['TradePack'] = ($value) ? 'Yes' : 'No';
                            break;
                        case 'RRP':
                            $tempArr['RRP'] = ($value + $ratesPercentages->PostageForProfitUnitCost);
                            break;
                    }
                }

                /* Retail Profit =
                 *  [RRP+Postage-Vat]
                 * -
                 *  [ExVatCost]
                 *      => ([TotalForMarkup]+[TotalPackCost]+[AssTotal]+[MCTotal]+[PostageTotal])
                 *
                 * [TotalPackCost] = SQL
                 * [AssTotal] = IIf([RequiresAssay]=-1,DLookUp("AssayRateUnitCost","AssayRateLookup")*1,0)
                 * [MCTotal] = (([RRP+Postage])*((DLookUp("MerchantChargePercentage","MerchantChargeLookup")/100)))
                 * [PostageTotal] = DLookUp("PostageCostUnitCost","PostageCostLookup")
                 */
                #[RRP+Postage-Vat]
                $RRP_Postage = ($tempDataArr[$x]['RRP'] + $ratesPercentages->PostageForProfitUnitCost);
                $Vat = ($tempDataArr[$x]['RRP'] + $ratesPercentages->PostageForProfitUnitCost) * ($ratesPercentages->VATPercentage / 100);

                dump($RRP_Postage);
                dump($Vat);
                dump($tempDataArr[$x]['TotalRMCost']);
                dump($tempDataArr[$x]['TotalLabourCost']);
                dump($ratesPercentages->PackageAndDispatchUnitCost);



                #[TotalForMarkup]
                #[TotalSoFar]
                $ExVatCost = ($tempDataArr[$x]['TotalRMCost'] + $tempDataArr[$x]['TotalLabourCost'] + $ratesPercentages->PackageAndDispatchUnitCost);
                dump($ExVatCost);

                dump($tempDataArr[$x]['TotalRMCost'] * ($ratesPercentages->ImportPercentage / 100));

                #[ImpTotal]
                $ExVatCost += ($tempDataArr[$x]['TotalRMCost'] * ($ratesPercentages->ImportPercentage / 100));

                dump($ExVatCost);

                exit();

                #[TotalPackCost]
                $ExVatCost += $tempDataArr[$x]['TotalPackCost'];
                #[AssTotal]
                $ExVatCost += ($tempDataArr[$x]['RequiresAssay']) ? $ratesPercentages->AssayRateUnitCost : 0;
                #[MCTotal]
                $ExVatCost += ($RRP_Postage * ($ratesPercentages->MerchantChargePercentage / 100));
                #[PostageTotal]
                $ExVatCost += $ratesPercentages->PostageCostUnitCost;
                $tempArr['RetailProfit'] = ($RRP_Postage - $Vat) - $ExVatCost;

                # ['RetailMargin'] = [Retail Profit] / [RRP+Postage-Vat]
                $tempArr['RetailMargin'] = ($tempArr['RetailProfit'] / ($RRP_Postage - $Vat) * 100);

                /* ['TradeProfit'] = IIf([ExcludeFromTrade]=-1,'N/A',[tradeprofsub]+[addback])
                 * [tradeprofsub] = [tradeprice]-[ExVatCost]
                 * [tradeprice] = [RRP+Postage-Vat] * 0.4
                 * [addback] = IIf([RRP+Postage] > 49, [PDTotal]+[BoxCost]+[MCTotal]+[PostageTotal], [PDTotal]+[MCTotal]+[PostageTotal])
                 */
                $tradePrice = (($RRP_Postage - $Vat) * 0.4);
                $addBack = ($RRP_Postage > 49) ? ($ratesPercentages->PostageCostUnitCost + $tempDataArr[$x]['BoxCost'] + ($RRP_Postage * ($ratesPercentages->MerchantChargePercentage / 100)) + $ratesPercentages->PostageCostUnitCost) : 0;
                $tempArr['TradeProfit'] = ($tempDataArr[$x]['ExcludeFromTrade']) ? 'N/A' : (($tradePrice - $ExVatCost) + $addBack);

                # ['TradeMargin'] = IIf([ExcludeFromTrade]=-1,'N/A',[Trade Profit] / [TradePrice])
                $tempArr['TradeMargin'] = ($tempDataArr[$x]['ExcludeFromTrade']) ? 'N/A' : ($tempArr['TradeProfit'] / (($RRP_Postage - $Vat) * 0.4) * 100);



                exit();
                $this->reportDataArr[$x] = $tempArr;
            }
        } #END
    }

}
