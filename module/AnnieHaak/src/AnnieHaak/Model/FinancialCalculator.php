<?php

namespace AnnieHaak\Model;

class FinancialCalculator {

    public $RatesPercentagesData;
    public $Subtotals;
    public $Product;

    /**
     *
     * @param type $ratesPercentagesData Array of additional production costs.
     * @param type $subtotals Array of subtotals for product constituent parts.
     * @param type $product Array of RRP and Assay status.
     * @return \AnnieHaak\Model\FinancialCalculator
     */
    public function __construct($ratesPercentagesData, $subtotals, $product) {
        $this->RatesPercentagesData = $ratesPercentagesData;
        $this->Subtotals = $subtotals;
        $this->Product = $product;
        return $this;
    }

    public function calculateFinancials() {

        $data['AssayRateUnitCost'] = $this->RatesPercentagesData['AssayRateUnitCost'];
        $data['ImportPercentage'] = $this->RatesPercentagesData['ImportPercentage'];
        $data['MerchantChargePercentage'] = $this->RatesPercentagesData['MerchantChargePercentage'];
        $data['PackageAndDispatchUnitCost'] = $this->RatesPercentagesData['PackageAndDispatchUnitCost'];
        $data['PostageCostUnitCost'] = $this->RatesPercentagesData['PostageCostUnitCost'];
        $data['PostageForProfitUnitCost'] = $this->RatesPercentagesData['PostageForProfitUnitCost'];
        $data['VATPercentage'] = $this->RatesPercentagesData['VATPercentage'];

        //SUBTOTALS
        $data['SubtotalRM'] = $this->Subtotals['RawMaterials'];
        $data['SubtotalLabour'] = $this->Subtotals['LabourItems'];
        $data['SubtotalDispatch'] = $this->RatesPercentagesData['PackageAndDispatchUnitCost'];
        $data['SubtotalProductManufac'] = 0;
        $data['SubtotalImportCharges'] = 0;
        $data['SubtotalMarkUp'] = 0;
        $data['SubtotalMarkUpX4'] = 0;
        $data['SubtotalBoxCost'] = $this->Subtotals['Packaging']['BOX'];
        $data['SubtotalBoxCostX4'] = 0;
        $data['SubtotalBoxCostTxt'] = '(Not for Trade)';
        $data['SubtotalBagCost'] = $this->Subtotals['Packaging']['BAG'];
        $data['SubtotalBagCostX4'] = 0;

        if ($this->Product['RequiresAssay']) {
            $data['SubtotalAssayCost'] = $this->RatesPercentagesData['AssayRateUnitCost'];
        } else {
            $data['SubtotalAssayCost'] = 0;
        }
        $data['SubtotalMechCharge'] = 0;
        $data['SubtotalPostage'] = 0;
        $data['SubtotalAll'] = 0;
        $data['SubtotalAllX4'] = 0;
        $data['SubtotalExVAT'] = 0;
        $data['SubtotalExVATX4'] = 0;
        $data['SubtotalMechCharge'] = 0;
        $data['SubtotalPostage'] = 0;
        $data['SubtotalAll'] = 0;
        $data['SubtotalAllX4'] = 0;
        $data['SubtotalExVAT'] = 0;
        $data['SubtotalExVATX4'] = 0;

        //RETAIL
        $data['RetailNewRRP'] = 0;
        $data['RetailRRPLessVAT'] = 0;
        $data['RetailNewRRPLessVAT'] = 0;
        $data['RetailCost'] = 0;
        $data['RetailNewCost'] = 0;
        $data['RetailProfit'] = 0;
        $data['RetailNewProfit'] = 0;
        $data['RetailActualPerc'] = 0;
        $data['RetailNewActualPerc'] = 0;

        //TRADE
        $data['TradePrice'] = 0;
        $data['TradeCost'] = 0;
        $data['TradeSubTotal'] = 0;
        $data['TradeAddBack'] = 0;
        $data['TradeProfit'] = 0;
        $data['TradeActual'] = 0;

        $temp = 0;
        $RRPPostage = 0;

        $data['SubtotalProductManufac'] = $data['SubtotalRM'];
        $data['SubtotalProductManufac'] += $data['SubtotalLabour'];
        $data['SubtotalProductManufac'] += $data['SubtotalDispatch'];

        $data['SubtotalImportCharges'] = $data['SubtotalRM'] * ($this->RatesPercentagesData['ImportPercentage'] / 100);

        $data['SubtotalMarkUp'] = $data['SubtotalProductManufac'] + $data['SubtotalImportCharges'];
        $data['SubtotalMarkUpX4'] = $data['SubtotalMarkUp'] * 4;

        $data['SubtotalBoxCostX4'] = $data['SubtotalBoxCost'] * 4;
        $data['SubtotalBagCostX4'] = $data['SubtotalBagCost'] * 4;

        $RRPPostage = $this->Product['RRP'] + $data['PostageForProfitUnitCost'];
        if ($RRPPostage > 49) {
            $data['SubtotalBoxCostTxt'] = '(Inc for Trade)';
        } else {
            $data['SubtotalBoxCostTxt'] = '(Not for Trade)';
        }
        $data['SubtotalMechCharge'] = $RRPPostage * ($data['MerchantChargePercentage'] / 100);

        $data['SubtotalAll'] = $data['SubtotalBoxCost'];
        $data['SubtotalAll'] += $data['SubtotalBagCost'];
        $data['SubtotalAll'] += $data['SubtotalAssayCost'];
        $data['SubtotalAll'] += $data['SubtotalMechCharge'];
        $data['SubtotalAll'] += $data['PostageCostUnitCost'];

        $data['SubtotalAllX4'] = $data['SubtotalBoxCostX4'];
        $data['SubtotalAllX4'] += $data['SubtotalBagCostX4'];
        $data['SubtotalAllX4'] += $data['SubtotalAssayCost'];
        $data['SubtotalAllX4'] += $data['SubtotalMechCharge'];

        $data['SubtotalExVAT'] = $data['SubtotalAll'];
        $data['SubtotalExVAT'] += $data['SubtotalMarkUp'];

        $data['SubtotalExVATX4'] = $data['SubtotalAllX4'];
        $data['SubtotalExVATX4'] += $data['SubtotalMarkUpX4'];

        //TRADE
        $data['RetailNewRRP'] = $this->Product['RRP'] + $data['PostageForProfitUnitCost'];
        $data['RetailRRPLessVAT'] = $this->Product['RRP'] / (($data['VATPercentage'] / 100) + 1);
        $data['RetailNewRRPLessVAT'] = $data['RetailNewRRP'] / (($data['VATPercentage'] / 100) + 1);

        $data['RetailCost'] = $data['SubtotalExVAT'];
        $data['RetailNewCost'] = $data['SubtotalExVAT'];

        $data['RetailProfit'] = $data['RetailRRPLessVAT'] - $data['RetailCost'];
        $data['RetailNewProfit'] = $data['RetailNewRRPLessVAT'] - $data['RetailNewCost'];

        if ($data['RetailRRPLessVAT'] > 0) {
            $temp = ($data['RetailProfit'] / $data['RetailRRPLessVAT'] * 100);
            if ($temp < 0) {
                $temp = 0;
            }
        } else {
            $temp = 0;
        }
        $data['RetailActualPerc'] = $temp;
        $data['RetailNewActualPerc'] = ($data['RetailNewProfit'] / $data['RetailNewRRPLessVAT'] * 100);

        //TRADE
        $data['TradePrice'] = $data['RetailNewRRPLessVAT'] * (40 / 100);
        $data['TradeSubTotal'] = $data['TradePrice'] - $data['RetailNewCost'];

        $data['TradeAddBack'] = $data['SubtotalDispatch'];
        $data['TradeAddBack'] += $data['SubtotalBoxCost'];
        $data['TradeAddBack'] += $data['SubtotalMechCharge'];
        $data['TradeAddBack'] += $data['PostageCostUnitCost'];

        $data['TradeProfit'] = $data['TradeSubTotal'] + $data['TradeAddBack'];
        $data['TradeActual'] = ($data['TradeProfit'] / $data['TradePrice']) * 100;

        return $data;
    }

}
