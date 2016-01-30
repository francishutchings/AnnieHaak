<?php

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIProductRawMaterialsManager extends AbstractHelper {

    public function __invoke($productId) {
        $productId = (int) $productId;

        $HTMLBlock = <<<HTML
        <input type="hidden" name="rawMaterialsGridData" id="rawMaterialsGridData" />
        <label class="col-lg-2 control-label">Add, Edit, Delete Materials:</label>
        <div class="col-lg-10">
            <div class="table-responsive">
                <table id="rawMaterialGrid"></table>
                <div id="rawMaterialGridPager"></div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                var rawMaterialsCurrentData = [];
                var rawMaterialTypesData = function () {
                    var rtnData = null;
                    $.ajax({
                        async: false,
                        url: "/business-admin/raw-material-types/jsonAllMaterialTypes",
                        success: function (data, result) {
                            rtnData = '0:Select ...;';
                            if (!result) {
                                alert('ERROR');
                            } else {
                                for (var x = 0; x < data.rawMaterialTypes.length; x++) {
                                    rtnData += data.rawMaterialTypes[x]['id'] + ':' + data.rawMaterialTypes[x]['value'];
                                    if (data.rawMaterialTypes.length - 1 > x) {
                                        rtnData += ';';
                                    }
                                }
                            }
                        }
                    });
                    return rtnData;
                }();
                var rawMaterialsData = function () {
                    var rtnData = null;
                    $.ajax({
                        async: false,
                        url: "/business-admin/raw-materials/jsonAllMaterials",
                        success: function (data, result) {
                            rtnData = '0:Select ...;';
                            if (!result) {
                                alert('ERROR');
                            } else {
                                for (var x = 0; x < data.rawMaterials.length; x++) {
                                    rtnData += data.rawMaterials[x]['id'] + ':' + data.rawMaterials[x]['value'];
                                    if (data.rawMaterials.length - 1 > x) {
                                        rtnData += ';'
                                    }
                                }
                            }
                        }
                    });
                    return rtnData;
                }();
                function RawMaterialData() {
                    $("#RawMaterialQty").unbind('keyup change');
                    $('#RawMaterialQty').numericInput(false, false);
                    $('#RawMaterialQty').on('keyup change', function () {
                        $('#SubtotalRM').val(parseFloat((parseFloat($("#RawMaterialUnitCost").val()) * parseInt($("#RawMaterialQty").val())).toFixed(4)));
                    });
                    updateRawMaterialCallBack($("#RMTypeID").val(), true);
                    $("#RMTypeID").bind("change", function (e) {
                        updateRawMaterialCallBack($("#RMTypeID").val(), false);
                        $('#RawMaterialCode').val('');
                        $('#RMSupplierName').val('');
                        $('#RawMaterialQty').val(1);
                        $('#RawMaterialUnitCost').val(0);
                        $('#RawMaterialQty').val(0);
                        $('#SubtotalRM').val(0);
                    });
                    return false;
                }
                function updateRawMaterialCallBack(RawMaterialTypeId, setselected) {
                    var current = $("#rawMaterialGrid").jqGrid('getRowData', $("#rawMaterialGrid")[0].p.selrow).RawMaterialID;
                    $("#RawMaterialID")
                            .html('<option value="">Loading Raw Materials...</option>')
                            .attr("disabled", "disabled");
                    $.ajax({
                        url: "/business-admin/raw-materials/jsonMaterialsByType?RawMaterialTypeId=" + RawMaterialTypeId,
                        type: "GET",
                        async: true,
                        success: function (data) {
                            rawMaterialsCurrentData = data.rawMaterials;
                            var selectHtml = '<select><option value="0">Select ...</option>';
                            for (var x = 0; x < data.rawMaterials.length; x++) {
                                selectHtml += '<option value="' + data.rawMaterials[x]['RawMaterialID'] + '">' + data.rawMaterials[x]['RawMaterialName'] + '</option>';
                            }
                            selectHtml += '</select>';
                            $("#RawMaterialID")
                                    .removeAttr("disabled")
                                    .html(selectHtml);
                            if (setselected) {
                                $("#RawMaterialID").val(current);
                            }
                            $("#RawMaterialID").bind("change", function (e) {
                                rmObject = null;
                                for (var i = 0, len = rawMaterialsCurrentData.length; i < len; i++) {
                                    if (rawMaterialsCurrentData[i].RawMaterialID === e.currentTarget.value)
                                        rmObject = rawMaterialsCurrentData[i];
                                }
                                $('#RawMaterialQty').val(1);
                                $('#RawMaterialUnitCost').val(0);
                                $('#RawMaterialCode').val(rmObject.RawMaterialCode);
                                $('#RMSupplierName').val(rmObject.RMSupplierName);
                                $('#RawMaterialUnitCost').val(rmObject.RawMaterialUnitCost);
                                $('#SubtotalRM').val(parseFloat((parseFloat($("#RawMaterialUnitCost").val()) * parseInt($("#RawMaterialQty").val())).toFixed(4)));
                            });
                        }
                    });
                }

                $("#rawMaterialGrid").jqGrid({
                    url: '/business-admin/raw-materials/jsonMaterialsByProduct?productid=$productId',
                    async: true,
                    mtype: "GET",
                    datatype: "json",
                    styleUI: 'Bootstrap',
                    responsive: true,
                    viewrecords: true,
                    loadonce: true,
                    height: 'auto',
                    viewsortcols: [false, 'vertical', false],
                    pager: "#rawMaterialGridPager",
                    editurl: 'clientArray',
                    autowidth: true,
                    shrinkToFit: true,
                    altRows: true,
                    altclass: 'altTableRowBackColour',
                    loadComplete: function (data) {
                        $('#rawMaterialGridPager_center').remove();
                        for (var x = 0; x < data.rows.length; x++) {
                            subtotals.rawMaterials += parseFloat(data.rows[x]['SubtotalRM']);
                        }
                    },
                    colNames: [
                        'Raw Material Type',
                        'Raw Material Name',
                        'Code',
                        'Supplier',
                        'Quantity',
                        'Cost',
                        'Subtotal'
                    ],
                    colModel: [
                        {
                            name: 'RMTypeID',
                            width: 200,
                            editable: true,
                            edittype: 'select',
                            formatter: 'select',
                            editoptions: {
                                value: rawMaterialTypesData
                            }
                        },
                        {
                            name: 'RawMaterialID',
                            width: 200,
                            editable: true,
                            edittype: "select",
                            formatter: 'select',
                            editoptions: {
                                value: rawMaterialsData
                            },
                            editrules: {
                                required: true,
                                number: true
                            }
                        },
                        {
                            name: 'RawMaterialCode',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'RMSupplierName',
                            width: 150,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'RawMaterialQty',
                            width: 75,
                            edittype: "text",
                            editable: true
                        },
                        {
                            name: 'RawMaterialUnitCost',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'SubtotalRM',
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
                $('#rawMaterialGrid').navGrid('#rawMaterialGridPager', {
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
                    editCaption: "Edit this Raw Material",
                    recreateForm: false,
                    closeAfterEdit: true,
                    viewPagerButtons: false,
                    reloadAfterSubmit: false,
                    closeOnEscape: true,
                    savekey: [true, 13],
                    afterShowForm: RawMaterialData,
                    onclickSubmit: onclickSubmitLocal,
                    errorTextFormat: function (data) {
                        return 'Error: ' + data.responseText
                    }
                },
                {
                    // ADD
                    closeAfterAdd: true,
                    recreateForm: false,
                    afterShowForm: RawMaterialData,
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
