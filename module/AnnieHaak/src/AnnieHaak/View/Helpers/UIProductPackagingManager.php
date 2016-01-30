<?php

namespace AnnieHaak\View\Helpers;

use Zend\View\Helper\AbstractHelper;

class UIProductPackagingManager extends AbstractHelper {

    public function __invoke($productId) {
        $productId = (int) $productId;

        $HTMLBlock = <<<HTML
        <input type="hidden" name="packagingGridData" id="packagingGridData" />
        <label class="col-lg-2 control-label">Add, Edit, Delete Packaging:</label>
        <div class="col-lg-10">
            <div class="table-responsive">
                <table id="packagingGrid"></table>
                <div id="packagingGridPager"></div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                var PackagingLookupData = function () {
                    var rtnData = null;
                    $.ajax({
                        async: false,
                        url: "/business-admin/packaging/jsonAllPackaging",
                        success: function (data, result) {
                            rtnData = '0:Select ...;';
                            if (!result) {
                                alert('ERROR');
                            } else {
                                for (var x = 0; x < data.packaging.length; x++) {
                                    rtnData += data.packaging[x]['id'] + ':' + data.packaging[x]['value'];
                                    if (data.packaging.length - 1 > x) {
                                        rtnData += ';';
                                    }
                                }
                            }
                        }
                    });
                    console.log(rtnData);
                    return rtnData;
                }();

                function PackagingData() {
                    $("#PackagingQty").unbind('keyup change');
                    $('#PackagingQty').numericInput(false, false);
                    $('#PackagingQty').on('keyup change', function () {
                        $('#SubtotalPackaging').val(parseFloat((parseFloat($("#PackagingUnitCost").val()) * parseInt($("#PackagingQty").val())).toFixed(4)));
                    });
                    $("#PackagingID").bind("change", function (e) {
                        $('#PackagingCode').val('');
                        $('#PackagingQty').val(1);
                        $('#PackagingUnitCost').val(0);
                        $('#SubtotalPackaging').val(0);
                        updatePackagingCallBack($(this).val(), false);
                    });
                    return false;
                }

                function updatePackagingCallBack(PackagingID) {
                    $.ajax({
                        url: "/business-admin/packaging/jsonPackagingByType?PackagingID=" + PackagingID,
                        type: "GET",
                        async: true,
                        success: function (data) {
                            $('#PackagingQty').val(1);
                            $('#PackagingUnitCost').val(0);
                            $('#PackagingCode').val(data.packaging[0]['PackagingCode']);
                            $('#PackagingUnitCost').val(data.packaging[0]['PackagingUnitCost']);
                            $('#SubtotalPackaging').val(parseFloat((parseFloat($("#PackagingUnitCost").val()) * parseInt($("#PackagingQty").val())).toFixed(4)));
                        }
                    });
                }

                $("#packagingGrid").jqGrid({
                    url: '/business-admin/packaging/jsonPackagingByProduct?productId=$productId',
                    async: true,
                    mtype: "GET",
                    datatype: "json",
                    styleUI: 'Bootstrap',
                    responsive: true,
                    viewrecords: true,
                    loadonce: true,
                    height: 'auto',
                    viewsortcols: [false, 'vertical', false],
                    pager: "#packagingGridPager",
                    editurl: 'clientArray',
                    autowidth: true,
                    shrinkToFit: true,
                    altRows: true,
                    altclass: 'altTableRowBackColour',
                    loadComplete: function (data) {
                        $('#packagingGridPager_center').remove();
                        for (var x = 0; x < data.rows.length; x++) {
                            subtotals.packaging += parseFloat(data.rows[x]['SubtotalPackaging']);
                        }
                    },
                    colNames: [
                        'Packaging',
                        'Code',
                        'Quantity',
                        'Unit Cost',
                        'Subtotal'
                    ],
                    colModel: [
                        {
                            name: 'PackagingID',
                            width: 200,
                            editable: true,
                            edittype: 'select',
                            formatter: 'select',
                            editoptions: {
                                value: PackagingLookupData
                            }
                        },
                        {
                            name: 'PackagingCode',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'PackagingQty',
                            width: 75,
                            edittype: "text",
                            editable: true
                        },
                        {
                            name: 'PackagingUnitCost',
                            width: 75,
                            edittype: "text",
                            editable: true,
                            editoptions: {
                                readonly: true
                            }
                        },
                        {
                            name: 'SubtotalPackaging',
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
                $('#packagingGrid').navGrid('#packagingGridPager', {
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
                    editCaption: "Edit this Packaging",
                    recreateForm: false,
                    closeAfterEdit: true,
                    viewPagerButtons: false,
                    reloadAfterSubmit: false,
                    closeOnEscape: true,
                    savekey: [true, 13],
                    afterShowForm: PackagingData,
                    onclickSubmit: onclickSubmitLocal,
                    errorTextFormat: function (data) {
                        return 'Error: ' + data.responseText
                    }
                },
                {
                    // ADD
                    closeAfterAdd: true,
                    recreateForm: false,
                    afterShowForm: PackagingData,
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
