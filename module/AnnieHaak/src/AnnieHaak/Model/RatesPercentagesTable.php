<?php

namespace AnnieHaak\Model;

use Zend\Db\TableGateway\TableGateway;

class RatesPercentagesTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getRatesPercentages($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('RatesPercentagesIdx' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Error: No record found.");
        }
        return $row;
    }

    public function saveRatesPercentages(RatesPercentages $RatesPercentages, Auditing $auditingObj) {
        $data = array(
            'AssayRateUnitCost' => $RatesPercentages->AssayRateUnitCost,
            'ImportPercentage' => $RatesPercentages->ImportPercentage,
            'MerchantChargePercentage' => $RatesPercentages->MerchantChargePercentage,
            'PackageAndDispatchUnitCost' => $RatesPercentages->PackageAndDispatchUnitCost,
            'PostageCostUnitCost' => $RatesPercentages->PostageCostUnitCost,
            'PostageForProfitUnitCost' => $RatesPercentages->PostageForProfitUnitCost,
            'VATPercentage' => $RatesPercentages->VATPercentage
        );

        $ratesPercentagesCurrentData = new Collections();
        $ratesPercentagesCurrentData = $this->getRatesPercentages(1);
        $ratesPercentagesCurrentArr = (Array) $ratesPercentagesCurrentData;

        $auditingObj->Action = 'Update';
        $auditingObj->TableName = 'RatesPercentages';
        $auditingObj->TableIndex = 1;
        $auditingObj->OldDataJSON = json_encode($ratesPercentagesCurrentArr);

        $connectCntrl = $this->tableGateway->getAdapter()->getDriver()->getConnection();
        $connectCntrl->beginTransaction();
        try {
            $this->tableGateway->update($data, array('RatesPercentagesIdx' => 1));
            $auditingObj->saveAuditAction();
        } catch (\Exception $ex) {
            $connectCntrl->rollback();
            throw new \Exception("Could not update Rates Percentages. " . $ex->getPrevious()->errorInfo[2]);
        }
        $connectCntrl->commit();
    }

}
