<?php

namespace AnnieHaak\Model;

class RRPCalculator {

    public $AssayRateUnitCost;
    public $ImportPercentage;
    public $MerchantChargePercentage;
    public $PackageAndDispatchUnitCost;
    public $PostageCostUnitCost;
    public $PostageForProfitUnitCost;
    public $VATPercentage;

    public function __construct(Array $ratesPercentagesData) {
        $this->AssayRateUnitCost = $ratesPercentagesData['AssayRateUnitCost'];
        $this->ImportPercentage = $ratesPercentagesData['ImportPercentage'];
        $this->MerchantChargePercentage = $ratesPercentagesData['MerchantChargePercentage'];
        $this->PackageAndDispatchUnitCost = $ratesPercentagesData['PackageAndDispatchUnitCost'];
        $this->PostageCostUnitCost = $ratesPercentagesData['PostageCostUnitCost'];
        $this->PostageForProfitUnitCost = $ratesPercentagesData['PostageForProfitUnitCost'];
        $this->VATPercentage = $ratesPercentagesData['VATPercentage'];

        return $this;
    }

}
