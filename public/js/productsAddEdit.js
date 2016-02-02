var financialCalcIni = function () {

    //console.log('financialCalc - INI ASSIGN: ');
    //console.log(arguments);

    $("#financialCalcAdjustors").data("AssayRateUnitCost", arguments[1].toFixed(4));
    $("#financialCalcAdjustors").data("ImportPercentage", arguments[2].toFixed(4));
    $("#financialCalcAdjustors").data("MerchantChargePercentage", arguments[3].toFixed(4));
    $("#financialCalcAdjustors").data("PostageCostUnitCost", arguments[4].toFixed(4));
    $("#financialCalcAdjustors").data("PostageForProfitUnitCost", arguments[5].toFixed(4));
    $("#financialCalcAdjustors").data("VATPercentage", arguments[5].toFixed(6));

    console.log($("#financialCalcAdjustors").data());

    $("#financialCalcSubTotals").data("SubtotalRM", 0);
    $("#financialCalcSubTotals").data("SubtotalLabour", 0);
    $("#financialCalcSubTotals").data("SubtotalDispatch", 0);

    $("#financialCalcSubTotals").data("SubtotalProductManufac", 0);

    $("#financialCalcSubTotals").data("SubtotalImportCharges", 0);

    $("#financialCalcSubTotals").data("SubtotalMarkUp", 0);
    $("#financialCalcSubTotals").data("SubtotalMarkUpX4", 0);

    $("#financialCalcSubTotals").data("SubtotalBoxCost", 0);
    $("#financialCalcSubTotals").data("SubtotalBoxCostX4", 0);

    $("#financialCalcSubTotals").data("SubtotalBagCost", 0);
    $("#financialCalcSubTotals").data("SubtotalBagCostX4", 0);

    if (arguments[0]) {
        $("#financialCalcSubTotals").data("SubtotalAssayCost", $("#financialCalcAdjustors").data("AssayRateUnitCost"));
        $("#financialCalcSubTotals").data("SubtotalAssayCostX4", ($("#financialCalcAdjustors").data("AssayRateUnitCost") * 4));
    } else {
        $("#financialCalcSubTotals").data("SubtotalAssayCost", 0);
        $("#financialCalcSubTotals").data("SubtotalAssayCostX4", 0);
    }

    $("#financialCalcSubTotals").data("SubtotalPostage", 0);

    $("#financialCalcSubTotals").data("SubtotalExVAT", 0);
    $("#financialCalcSubTotals").data("SubtotalExVATX4", 0);

    console.log($("#financialCalcSubTotals").data());

}

//===========================================================================================
var updateFinancialSubTots = function () {

    //console.log('updateFinancialSubTots() - RAN:');
    //console.log($('#financialCalcSubTotals').data());

    temp = 0;
    subTotal = '0.0';

    temp = $('#financialCalcSubTotals').data('SubtotalRM');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    temp = $('#financialCalcSubTotals').data('SubtotalLabour');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    temp = $('#financialCalcSubTotals').data('SubtotalDispatch');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    $("#financialCalcSubTotals").data("SubtotalProductManufac", parseFloat(subTotal).toFixed(4));

    temp = parseFloat(subTotal) * ($("#financialCalcAdjustors").data("ImportPercentage") / 100);
    $("#financialCalcSubTotals").data("SubtotalImportCharges", parseFloat(temp).toFixed(4));

    temp = parseFloat(subTotal) + parseFloat($("#financialCalcSubTotals").data("SubtotalImportCharges"));
    $("#financialCalcSubTotals").data("SubtotalMarkUp", temp.toFixed(4));
    $("#financialCalcSubTotals").data("SubtotalMarkUpX4", (temp * 4).toFixed(4));

    $("#financialCalcSubTotals").data("SubtotalBoxCostX4", ($("#financialCalcSubTotals").data("SubtotalBoxCost") * 4));
    $("#financialCalcSubTotals").data("SubtotalBagCostX4", ($("#financialCalcSubTotals").data("SubtotalBagCost") * 4));


    updateDisplayFinancialSubTots();
};

//===========================================================================================
var updateDisplayFinancialSubTots = function () {

    $('#subRawMaterialDsp').text($('#financialCalcSubTotals').data('SubtotalRM'));
    $('#subLabourDsp').text($('#financialCalcSubTotals').data('SubtotalLabour'));
    $('#subDispatchDsp').text($('#financialCalcSubTotals').data('SubtotalDispatch'));

    $('#subTotalsDsp').text($("#financialCalcSubTotals").data("SubtotalProductManufac"));

    $('#subImportChargesDsp').text($("#financialCalcSubTotals").data("SubtotalImportCharges"));

    $('#subMarkUpDsp').text($("#financialCalcSubTotals").data("SubtotalMarkUp"));
    $('#subMarkUpX4Dsp').text($("#financialCalcSubTotals").data("SubtotalMarkUpX4"));

    $('#subBoxCostDsp').text($("#financialCalcSubTotals").data("SubtotalBoxCost"));
    $('#subBoxCostX4Dsp').text($("#financialCalcSubTotals").data("SubtotalBoxCostX4"));

    $('#subBagCostDsp').text($("#financialCalcSubTotals").data("SubtotalBagCost"));
    $('#subBagCostX4Dsp').text($("#financialCalcSubTotals").data("SubtotalBagCostX4"));

    $('#subAssayCostDsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalAssayCost")).toFixed(4));
    $('#subAssayCostX4Dsp').text(parseFloat($("#financialCalcSubTotals").data("SubtotalAssayCostX4")).toFixed(4));

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
        console.log(packagingItemsGridData[i]['PackagingCode']);
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
            $("#financialCalcSubTotals").data("SubtotalAssayCostX4", ($("#financialCalcAdjustors").data("AssayRateUnitCost") * 4));
        } else {
            $("#financialCalcSubTotals").data("SubtotalAssayCost", 0);
            $("#financialCalcSubTotals").data("SubtotalAssayCostX4", 0);
        }
        updateFinancialSubTots();
    });
    //=============================================
    $("#RRP").change(function () {
        //updateFinancialSubTots();
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
