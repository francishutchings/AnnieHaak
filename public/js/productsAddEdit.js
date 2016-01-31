var onclickSubmitLocal = function (options, postdata) {

    console.log(options.caption);
    //console.log(postdata);


    switch (options.caption) {
        case 'Add Labour Item':
            temp = parseFloat($('#financialCalculator').data('SubtotalLabour'));
            temp += parseFloat(postdata.SubtotalLabour);
            console.log(temp);
            $('#financialCalculator').data('SubtotalLabour', temp);
            break;
        case 'Edit Labour Item':

            break;
        case 'Delete Labour Item':

            break;
    }
    updateFinancialSubTots();

    this.processing = true;
    return {};
};
var updateFinancialSubTots = function () {

    console.log('#financialCalculator RAN:');
    console.log($('#financialCalculator').data());
    temp = 0;
    temp += $('#financialCalculator').data('SubtotalRM');
    temp += $('#financialCalculator').data('SubtotalLabour');
    temp += 1.1; //$('#financialCalculator').data('SubtotalDispatch');

    $('#subRawMaterialDsp').text($('#financialCalculator').data('SubtotalRM'));
    $('#subLabourDsp').text($('#financialCalculator').data('SubtotalLabour'));
    $('#subDispatchDsp').text(1.1);
    $('#subTotalsDsp').text(parseFloat(temp).toFixed(4));
};
function sum(obj) {
    var sum = 0;
    for (var el in obj) {
        if (obj.hasOwnProperty(el)) {
            sum += parseFloat(obj[el]);
        }
    }
    return sum;
}

console.log('financialCalculator - ASSIGN');
$("#financialCalculator").data("SubtotalRM", 0);
$("#financialCalculator").data("SubtotalLabour", 0);
$("#financialCalculator").data("SubtotalDispatchPack", 0);
$("#financialCalculator").data("SubtotalPackaging", 0);
$(document).ready(function () {

    $("#RRP").change(function () {
        updateFinancials();
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
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameCharm").val());
        }
        if ($("#NameCrystal").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameCrystal").val());
        }
        if ($("#NameColour").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameColour").val());
        }
        if ($("#NameLength").val()) {
            $("#ProductName").val($("#ProductName").val() + ' - ' + $("#NameLength").val());
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
