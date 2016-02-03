var financialCalcIni = function () {

//SUBTOTAL

    $("#financialCalcAdjustors").data("AssayRateUnitCost", arguments[1].toFixed(4));
    $("#financialCalcAdjustors").data("ImportPercentage", arguments[2].toFixed(4));
    $("#financialCalcAdjustors").data("MerchantChargePercentage", arguments[3].toFixed(4));
    $("#financialCalcAdjustors").data("PostageCostUnitCost", arguments[4].toFixed(4));
    $("#financialCalcAdjustors").data("PostageForProfitUnitCost", arguments[5].toFixed(4));
    $("#financialCalcAdjustors").data("VATPercentage", arguments[5].toFixed(6));

    $("#financialCalcSubTotals").data("SubtotalRM", 0);
    $("#financialCalcSubTotals").data("SubtotalLabour", 0);
    $("#financialCalcSubTotals").data("SubtotalDispatch", 0);

    $("#financialCalcSubTotals").data("SubtotalProductManufac", 0);

    $("#financialCalcSubTotals").data("SubtotalImportCharges", 0);

    $("#financialCalcSubTotals").data("SubtotalMarkUp", 0);
    $("#financialCalcSubTotals").data("SubtotalMarkUpX4", 0);

    $("#financialCalcSubTotals").data("SubtotalBoxCost", 0);
    $("#financialCalcSubTotals").data("SubtotalBoxCostX4", 0);
    $("#financialCalcSubTotals").data("SubtotalBoxCostTxt", '');

    $("#financialCalcSubTotals").data("SubtotalBagCost", 0);
    $("#financialCalcSubTotals").data("SubtotalBagCostX4", 0);

    if (arguments[0]) {
        $("#financialCalcSubTotals").data("SubtotalAssayCost", $("#financialCalcAdjustors").data("AssayRateUnitCost"));
    } else {
        $("#financialCalcSubTotals").data("SubtotalAssayCost", 0);
    }

    $("#financialCalcSubTotals").data("SubtotalMechCharge", 0);

    $("#financialCalcSubTotals").data("SubtotalPostage", 0);

    $("#financialCalcSubTotals").data("SubtotalExVAT", 0);
    $("#financialCalcSubTotals").data("SubtotalExVATX4", 0);

//RETAIL
    $("#financialCalcSubTotals").data("RetailNewRRP", 0);



}

//===========================================================================================
var updateFinancialSubTots = function () {

//SUBTOTAL

    temp = 0;
    subTotal = 0;
    RRPPostage = 0;

    //Raw Mats
    temp = $('#financialCalcSubTotals').data('SubtotalRM');
    subTotal = parseFloat(subTotal) + parseFloat(temp);
    //Labour
    temp = $('#financialCalcSubTotals').data('SubtotalLabour');
    subTotal = parseFloat(subTotal) + parseFloat(temp);
    //Disp
    temp = $('#financialCalcSubTotals').data('SubtotalDispatch');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    $("#financialCalcSubTotals").data("SubtotalProductManufac", parseFloat(subTotal).toFixed(4));

    temp = parseFloat($('#financialCalcSubTotals').data('SubtotalRM')) * ($("#financialCalcAdjustors").data("ImportPercentage") / 100);
    $("#financialCalcSubTotals").data("SubtotalImportCharges", parseFloat(temp).toFixed(4));

    temp = parseFloat($("#financialCalcSubTotals").data("SubtotalProductManufac")) + parseFloat($("#financialCalcSubTotals").data("SubtotalImportCharges"));

    $("#financialCalcSubTotals").data("SubtotalMarkUp", temp.toFixed(4));
    $("#financialCalcSubTotals").data("SubtotalMarkUpX4", (temp * 4).toFixed(4));

    $("#financialCalcSubTotals").data("SubtotalBoxCostX4", ($("#financialCalcSubTotals").data("SubtotalBoxCost") * 4));
    $("#financialCalcSubTotals").data("SubtotalBagCostX4", ($("#financialCalcSubTotals").data("SubtotalBagCost") * 4));

    RRPPostage = parseFloat($('#RRP').val()) + parseFloat($("#financialCalcAdjustors").data("PostageForProfitUnitCost"));

    if (RRPPostage > 49) {
        $("#financialCalcSubTotals").data("SubtotalBoxCostTxt", '(Inc for Trade)');
    } else {
        $("#financialCalcSubTotals").data("SubtotalBoxCostTxt", '(Not for Trade)');
    }

    temp = RRPPostage * ($("#financialCalcAdjustors").data("MerchantChargePercentage") / 100);
    $("#financialCalcSubTotals").data("SubtotalMechCharge", temp);

    temp = parseFloat($("#financialCalcSubTotals").data("SubtotalBoxCost")) + parseFloat($("#financialCalcSubTotals").data("SubtotalBagCost"));
    temp += parseFloat($("#financialCalcSubTotals").data("SubtotalAssayCost"));
    temp += parseFloat($("#financialCalcSubTotals").data("SubtotalMechCharge"));
    temp += parseFloat($("#financialCalcAdjustors").data("PostageCostUnitCost"));
    temp += parseFloat($("#financialCalcSubTotals").data("SubtotalMarkUp"));
    $("#financialCalcSubTotals").data("SubtotalExVAT", temp);

    temp = parseFloat($("#financialCalcSubTotals").data("SubtotalBoxCostX4")) + parseFloat($("#financialCalcSubTotals").data("SubtotalBagCostX4"));
    temp += parseFloat($("#financialCalcSubTotals").data("SubtotalAssayCost"));
    temp += parseFloat($("#financialCalcSubTotals").data("SubtotalMechCharge"));
    temp += parseFloat($("#financialCalcSubTotals").data("SubtotalMarkUpX4"));
    $("#financialCalcSubTotals").data("SubtotalExVATX4", temp);


//RETAIL
    temp = parseFloat($('#RRP').val()) + parseFloat($("#financialCalcAdjustors").data("PostageForProfitUnitCost"));
    $("#financialCalcSubTotals").data("RetailNewRRP", temp);


    updateDisplayFinancialSubTots();
};

//===========================================================================================
var updateDisplayFinancialSubTots = function () {

//SUBTOTAL

    $('#subRawMaterialDsp').text($('#financialCalcSubTotals').data('SubtotalRM'));
    $('#subLabourDsp').text($('#financialCalcSubTotals').data('SubtotalLabour'));
    $('#subDispatchDsp').text($('#financialCalcSubTotals').data('SubtotalDispatch'));

    $('#subTotalsDsp').text($("#financialCalcSubTotals").data("SubtotalProductManufac"));

    $('#subImportChargesDsp').text($("#financialCalcSubTotals").data("SubtotalImportCharges"));

    $('#subMarkUpDsp').text($("#financialCalcSubTotals").data("SubtotalMarkUp"));
    $('#subMarkUpX4Dsp').text($("#financialCalcSubTotals").data("SubtotalMarkUpX4"));

    $('#subBoxCostDsp').text($("#financialCalcSubTotals").data("SubtotalBoxCost"));
    $('#subBoxCostX4Dsp').text($("#financialCalcSubTotals").data("SubtotalBoxCostX4"));
    $('#subBoxCostTxtDsp').text($("#financialCalcSubTotals").data("SubtotalBoxCostTxt"));

    $('#subBagCostDsp').text($("#financialCalcSubTotals").data("SubtotalBagCost"));
    $('#subBagCostX4Dsp').text($("#financialCalcSubTotals").data("SubtotalBagCostX4"));

    $('#subAssayCostDsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalAssayCost")).toFixed(4));
    $('#subAssayCostX4Dsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalAssayCost")).toFixed(4));

    $('#subMerchChargeDsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalMechCharge")).toFixed(4));
    $('#subMerchChargeX4Dsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalMechCharge")).toFixed(4));

    $('#subPostageDsp').text(parseFloat($("#financialCalcAdjustors").data("PostageCostUnitCost")).toFixed(4));

    $('#subExVATCostDsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalExVAT")).toFixed(4));
    $('#subExVATCostX4Dsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalExVATX4")).toFixed(4));




//RETAIL

    $('#retailPostageProfitDsp').text(parseFloat($("#financialCalcAdjustors").data("PostageForProfitUnitCost")).toFixed(4));
    $('#retailNewRRPDsp').text(parseFloat($("#financialCalcSubTotals").data("RetailNewRRP")).toFixed(4));




}

//===========================================================================================
var afterCompleteRawMaterials = function (options, postdata) {
    rawMaterialGridData = $("#rawMaterialGrid").jqGrid("getGridParam", "data");
    subtotalRawMaterialAll = $.map(rawMaterialGridData, function (item) {
        return item.SubtotalRM;
    });
    var subTotal = 0;
    for (var i in subtotalRawMaterialAll) {
        subTotal = parseFloat(subTotal) + parseFloat(subtotalRawMaterialAll[i]);
    }
    $('#financialCalcSubTotals').data('SubtotalRM', parseFloat(subTotal).toFixed(4));
    updateFinancialSubTots();
    this.processing = true;
    return {};
};

var afterCompleteLabour = function (options, postdata) {
    labourItemsGridData = $("#labourItemsGrid").jqGrid("getGridParam", "data");
    subtotalLabourAll = $.map(labourItemsGridData, function (item) {
        return item.SubtotalLabour;
    });
    var subTotal = 0;
    for (var i in subtotalLabourAll) {
        subTotal = parseFloat(subTotal) + parseFloat(subtotalLabourAll[i]);
    }
    $('#financialCalcSubTotals').data('SubtotalLabour', parseFloat(subTotal).toFixed(4));
    updateFinancialSubTots();
    this.processing = true;
    return {};
};

var afterCompletePackaging = function (options, postdata) {
    packagingItemsGridData = $("#packagingGrid").jqGrid("getGridParam", "data");
    var subTotalBx = 0;
    var subTotalBg = 0;
    for (var i in packagingItemsGridData) {
        switch (packagingItemsGridData[i]['PackagingCode']) {
            case 'SBOX':
            case 'LBOX':
                subTotalBx += parseFloat(packagingItemsGridData[i]['SubtotalPackaging']);
                break
            case 'SBAG':
            case 'LBAG':
                subTotalBg += parseFloat(packagingItemsGridData[i]['SubtotalPackaging']);
                break
        }
    }
    $("#financialCalcSubTotals").data("SubtotalBoxCost", parseFloat(subTotalBx).toFixed(4));
    $("#financialCalcSubTotals").data("SubtotalBagCost", parseFloat(subTotalBg).toFixed(4));
    updateFinancialSubTots();
    this.processing = true;
    return {};
};

//===========================================================================================
$(document).ready(function () {

    $("#RequiresAssay").change(function () {
        if ($(this).prop('checked')) {
            $("#financialCalcSubTotals").data("SubtotalAssayCost", $("#financialCalcAdjustors").data("AssayRateUnitCost"));
        } else {
            $("#financialCalcSubTotals").data("SubtotalAssayCost", 0);
        }
        updateFinancialSubTots();
    });
    //=============================================
    $("#RRP").change(function () {
        updateFinancialSubTots();
    });
    //=============================================
    $('#products').submit(function () {
        $('#rawMaterialsGridData').val(JSON.stringify($("#rawMaterialGrid").jqGrid('getGridParam', 'data')));
    });
    //=============================================
    $('#DescRadio1,#DescRadio2,#DescRadio3').change(function (elem) {
        $('#DescBlock').find("span").each(function (index) {
            $(this).removeClass().addClass('label label-default');
        });
        switch (elem.currentTarget.id) {
            case 'DescRadio1':
                $(elem.currentTarget).next().children().switchClass('label-default', 'label-danger', 500, 'easeInOutQuad');
                break;
            case 'DescRadio2':
                $(elem.currentTarget).next().children().switchClass('label-default', 'label-warning', 500, 'easeInOutQuad');
                break;
            case 'DescRadio3':
                $(elem.currentTarget).next().children().switchClass('label-default', 'label-success', 500, 'easeInOutQuad');
                break;
        }
    });
    //=============================================
    $('#optionsBlock input:checkbox').each(function () {
        $(this).change(function () {
            if ($(this).is(':checked')) {
                $(this).next().children().switchClass('label-default', 'label-success', 500, 'easeInOutQuad');
            } else {
                $(this).next().children().switchClass('label-success', 'label-default', 500, 'easeInOutQuad');
            }
        });
    });
    //=============================================
    $('#ProductName').focus(function () {
        $('#buildProductName').modal('show');
        $('#Name').focus();
    });
    $('#buildProductName').on('hidden.bs.modal', function () {
        $('#SKU').focus();
    });
    //=============================================
    $("#IntroDate").datepicker({
        autoclose: true,
        dateFormat: "yy-mm-dd",
        format: 'dd-mm-yy',
        altFormat: "yy-mm-dd",
        orientation: 'bottom',
        changeMonth: true,
        changeYear: true
    });
    //=============================================
    $("#Name").bind('keyup', function () {
        concatProductName();
    });
    $("#Charm").bind('change', function () {
        concatProductName();
    });
    $("#Chrystal").bind('change', function () {
        concatProductName();
    });
    $("#Colour").bind('change', function () {
        concatProductName();
    });
    $("#Length").bind('change', function () {
        concatProductName();
    });
    $("#buildProductNameSave").click(function () {
        concatProductName();
    });
    concatProductName = function () {
        $("#ProductName").val($("#Name").val());
        if ($("#NameCharm").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameCharm option:selected").text());
        }
        if ($("#NameCrystal").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameCrystal option:selected").text());
        }
        if ($("#NameColour").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameColour option:selected").text());
        }
        if ($("#NameLength").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameLength option:selected").text());
        }
    };
    //=============================================
    $("#currentUrlThumb").click(function () {
        $('#currentUlrDisplay').attr("src", $('#CurrentURL').val());
        $('#currentUlrModal').modal('show');
        return false;
    });
    $("#oldUrlThumb").click(function () {
        $('#currentUlrDisplay').attr("src", $('#OldURL').val());
        $('#currentUlrModal').modal('show');
        return false;
    });
});
