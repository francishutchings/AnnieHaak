console.log('financialCalculator - INI ASSIGN');
$("#financialCalculator").data("SubtotalRM", 0);
$("#financialCalculator").data("SubtotalLabour", 0);
$("#financialCalculator").data("SubtotalDispatch", 0);
$("#financialCalculator").data("SubtotalPackaging", 0);

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
    $('#financialCalculator').data('SubtotalRM', parseFloat(subTotal).toFixed(4));
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
    $('#financialCalculator').data('SubtotalLabour', parseFloat(subTotal).toFixed(4));
    updateFinancialSubTots();
    this.processing = true;
    return {};
};

var afterCompletePackaging = function (options, postdata) {
    packagingItemsGridData = $("#packagingGrid").jqGrid("getGridParam", "data");
    subtotalPackagingAll = $.map(packagingItemsGridData, function (item) {
        return item.SubtotalPackaging;
    });
    var subTotal = 0;
    for (var i in subtotalPackagingAll) {
        subTotal = parseFloat(subTotal) + parseFloat(subtotalPackagingAll[i]);
    }
    $('#financialCalculator').data('SubtotalPackaging', parseFloat(subTotal).toFixed(4));
    updateFinancialSubTots();
    this.processing = true;
    return {};
};

//===========================================================================================
var updateFinancialSubTots = function () {

    //console.log('updateFinancialSubTots() - RAN:');
    //console.log($('#financialCalculator').data());

    temp = 0;
    subTotal = '0.0';

    temp = $('#financialCalculator').data('SubtotalRM');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    temp = $('#financialCalculator').data('SubtotalLabour');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    temp = $('#financialCalculator').data('SubtotalDispatch');
    subTotal = parseFloat(subTotal) + parseFloat(temp);

    $('#subRawMaterialDsp').text($('#financialCalculator').data('SubtotalRM'));
    $('#subLabourDsp').text($('#financialCalculator').data('SubtotalLabour'));
    $('#subDispatchDsp').text($('#financialCalculator').data('SubtotalDispatch'));
    $('#subTotalsDsp').text(parseFloat(subTotal).toFixed(4));

    //$('#subImportCharges').text(parseFloat(subTotal).toFixed(4));

};

//===========================================================================================
$(document).ready(function () {

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
