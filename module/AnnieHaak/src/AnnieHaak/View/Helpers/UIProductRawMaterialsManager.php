<?php

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIProductRawMaterialsManager extends AbstractHelper {

    public function __invoke($edit, $productsRawMaterials) {



        return false;
    }

    private function getRawMaterialTable() {

        $rmReturnBlock = '
            <div class="row">
                <div class="col-lg-10 col-lg-offset-2">
                    <div class="table-responsive no-margin">
                        <table class="table table-striped table-hover table-condensed" id="rawMaterialsTable">
                            <thead>
                                <tr class="text-info">
                                    <th class="">Raw Material Type</th>
                                    <th class="">Raw Material Name</th>
                                    <th class="">Code</th>
                                    <th class="">Supplier</th>
                                    <th class="">Qty</th>
                                    <th class="">Cost</th>
                                    <th class="">Subtotal</th>
                                    <th class="">Edit</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <hr/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <button id="addRawMaterialAction" class="btn btn-success btn-sm pull-right">Add Raw Material</button>
                                    </td>
                                </tr>
                            </tfoot>
                            <tbody>';
        if ($edit) {
            $tmp = 0;
            foreach ($productsRawMaterials as $key => $value) {
                $rmReturnBlock .= '<tr>';
                $rmReturnBlock .= '<td>' . $value->RMTypeName . '</td>';
                $rmReturnBlock .= '<td>' . $value->RawMaterialName . '</td>';
                $rmReturnBlock .= '<td>' . $value->RawMaterialCode . '</td>';
                $rmReturnBlock .= '<td>' . $value->RMSupplierName . '</td>';
                $rmReturnBlock .= '<td>0</td>';
                $rmReturnBlock .= '<td>' . $value->RawMaterialUnitCost . '</td>';
                $rmReturnBlock .= '<td>0</td>';
                $rmReturnBlock .= '<td><button onclick="rawMaterialEdit(' . $tmp . ')" type="button" class="btn btn-warning btn-sm margin-right-5"><span class="glyphicon glyphicon-pencil"></span></button>';
                $rmReturnBlock .= '<button onclick="rawMaterialDelete(this)" type="button" id="rmD_' . $tmp . '" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></button></td>';
                $rmReturnBlock .= '<tr>';
                $tmp++;
            }
        }
        $rmReturnBlock .= '</tbody></table></div></div></div>';

        return array(
            'rmReturnBlock' => $rmReturnBlock
        );
    }

}
