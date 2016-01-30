<?php

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIProductLabourManager extends AbstractHelper {

    public function __invoke($productId) {
        $productId = (int) $productId;

        $HTMLBlock = <<<HTML
        <input type="hidden" name="labourItemsGridData" id="labourItemsGridData" />
        <label class="col-lg-2 control-label">Add, Edit, Delete Labour:</label>
        <div class="col-lg-10">
            <div class="table-responsive">
                <table id="labourItemsGrid"></table>
                <div id="labourItemsGridPager"></div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                var LabourLookupData = function () {
                    var rtnData = null;
                    $.ajax({
                        async: false,
                        url: "/business-admin/labour-items/jsonAllLaboutItems",
                        success: function (data, result) {
                            rtnData = '0:Select ...;';
                            if (!result) {
                                alert('ERROR');
                            } else {
                                for (var x = 0; x < data.labourItems.length; x++) {
                                    rtnData += data.labourItems[x]['id'] + ':' + data.labourItems[x]['value'];
                                    if (data.labourItems.length - 1 > x) {
                                        rtnData += ';';
                                    }
                                }
                            }
                        }
                    });
                    return rtnData;
                }();

                function LabourItemsData() {
                    $("#LabourQty").unbind('keyup change');
                    $('#LabourQty').numericInput(false, false);
                    $('#LabourQty').on('keyup change', function () {
                        $('#SubtotalLabour').val(parseFloat((parseFloat($("#LabourUnitCost").val()) * parseInt($("#LabourQty").val())).toFixed(4)));
                    });
                    $("#LabourID").bind("change", function (e) {
                        $('#LabourCode').val('');
                        $('#LabourQty').val(1);
                        $('#LabourUnitCost').val(0);
                        $('#SubtotalLabour').val(0);
                        updateLabourItemCallBack($(this).val(), false);
                    });
                    return false;
                }

                function updateLabourItemCallBack(LabourID, setselected) {
                    $.ajax({
                        url: "/business-admin/labour-items/jsonLabourItemByType?LabourId=" + LabourID,
                        type: "GET",
                        async: true,
                        success: function (data) {
                            $('#LabourQty').val(1);
                            $('#LabourUnitCost').val(0);
                            $('#LabourCode').val(data.labourItems[0]['LabourCode']);
                            $('#LabourUnitCost').val(data.labourItems[0]['LabourUnitCost']);
                            $('#SubtotalLabour').val(parseFloat((parseFloat($("#LabourUnitCost").val()) * parseInt($("#LabourQty").val())).toFixed(4)));
                        }
                    });
                }

                $("#labourItemsGrid").jqGrid({
                    url: '/business-admin/labour-items/jsonLabourItemsByProduct?productId=$productId',
                    async: true,
                    mtype: "GET",
                    datatype: "json",
                    styleUI: 'Bootstrap',
                    responsive: true,
                    viewrecords: true,
                    loadonce: true,
                    height: 'auto',
                    viewsortcols: [false, 'vertical', false],
                    pager: "#labourItemsGridPager",
                    editurl: 'clientArray',
                    autowidth: true,
                    shrinkToFit: true,
                    altRows: true,
                    altclass: 'altTableRowBackColour',
                    loadComplete: function (data) {
                        $('#labourItemsGridPager_center').remove();
                        for (var x = 0; x < data.rows.length; x++) {
                            subtotals.labour += parseFloat(data.rows[x]['SubtotalLabour']);
                        }
                    },
                    colNames: [
                        'Labour Name',
                        'Code',
                        'Quantity',
                        'Unit Cost',
                        'Subtotal'
                    ],
                    colModel: [
                        {
                            name: 'LabourID',
                            width: 200,
                            editable: true,
                            edittype: 'select',
                            formatter: 'select',
                            editoptions: {
                                value: LabourLookupData
                            }
                        },
                        {
                            name: 'LabourCode',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'LabourQty',
                            width: 75,
                            edittype: "text",
                            editable: true
                        },
                        {
                            name: 'LabourUnitCost',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'SubtotalLabour',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true,
                                size: 15,
                                maxlengh: 10,
                            }
                        }
                    ]
                });
                $('#labourItemsGrid').navGrid('#labourItemsGridPager', {
                    edit: true,
                    add: true,
                    del: true,
                    search: false,
                    refresh: false,
                    view: false,
                    position: "left",
                    cloneToTop: true
                },
                {
                    // EDIT
                    editCaption: "Edit this Labour Item",
                    recreateForm: false,
                    closeAfterEdit: true,
                    viewPagerButtons: false,
                    reloadAfterSubmit: false,
                    closeOnEscape: true,
                    savekey: [true, 13],
                    afterShowForm: LabourItemsData,
                    onclickSubmit: onclickSubmitLocal,
                    errorTextFormat: function (data) {
                        return 'Error: ' + data.responseText
                    }
                },
                {
                    // ADD
                    closeAfterAdd: true,
                    recreateForm: false,
                    afterShowForm: LabourItemsData,
                    onclickSubmit: onclickSubmitLocal,
                    errorTextFormat: function (data) {
                        return 'Error: ' + data.responseText
                    }
                },
                {
                    // DELETE
                    onclickSubmit: onclickSubmitLocal,
                    errorTextFormat: function (data) {
                        return 'Error: ' + data.responseText
                    }
                });
            });
        </script>
HTML;

        return $HTMLBlock;
    }

}
