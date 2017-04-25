$(function() {
    // Init jsgrid
    $("#voucherGrid").jsGrid(gridOptions);

    // Click on a row to redeem code
    $('#voucherGrid').on('click', '.jsgrid-selected-row', redeemVoucherCode);

    // Generate a new voucher
    $('.generate-voucher').click(generateVoucherCode);
});


// Set jsGrid defaults for whole site
jsGrid.setDefaults({
    width: "100%",
    sorting: true,
    paging: true,
    autoload: true,
    pageSize: 20,
    controller: getController,
});

// Could override this options per page depending on the data
gridOptions = {
    fields: [
        { name: "id", type:"number", width: 50, align: "center", editing: false, inserting: false },
        { name: "code", type:"text", width: 250, validate: "required" },
        { name: "redeemed", type:"checkbox", validate: "required", width: 100 },
        { name: "created_at", type:"text", title: "created", width: 80, editing: false, inserting: false, itemTemplate: getLocaleDateFormat },
        { name: "updated_at", type: "text", title: "updated", width: 80, editing: false, inserting: false, itemTemplate: getLocaleDateFormat },
    ]
};

// Get default controller action
function getController() {
    return {
        loadData: function() {
            var d = $.Deferred();

            $.ajax({
                url: "/voucher/list",
                dataType: "json"
            }).done(function(response) {
                d.resolve(response);
            });

            return d.promise();
        }
    }
}

function generateVoucherCode(e) {
    // Override default click action
    e.preventDefault();
    // Call backend and reload data
    $.get("/voucher/generate", function(rdata) {
        // Should load row data instead of reload whole table
        $("#voucherGrid").jsGrid("loadData");
        // Show notification
        rdata = $.parseJSON(rdata);
        $.notify({
            message: 'You generated a new voucher! '+rdata.code
        },{
            type: 'info'
        });
    });
}

function redeemVoucherCode() {
    // Check if code was already redeemed
    var redeemed = $(this).find('td:nth-child(3) input').is(":checked");
    
    if (redeemed) {
        return false;
    }

    // Call backend and reload data
    var code = $(this).find('td:nth-child(2)').text();
    $.get("/voucher/redeem/" + code, function(rdata) {
        // Should load row data instead of reload whole table
        $("#voucherGrid").jsGrid("loadData");
        // Show notification
        $.notify({
            message: 'You redeemed voucher '+code
        },{
            type: 'warning'
        });
    });
}


function getLocaleDateFormat(date) {
    var d = new Date(date);
    return d.toLocaleString();
}