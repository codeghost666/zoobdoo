var ProfileController = function() {
    this.dateRange = $( '.date' );
};

ProfileController.prototype.newTitle = function() {
    var itemText = $( '.menu-item.current a' ).text(),
        pageTitle = $( '.page-title' );
         
    pageTitle.html( itemText );
};

ProfileController.prototype.datePicker = function() {
    this.dateRange.datepicker({
        autoclose: true
    });
};

ProfileController.prototype.run = function() {
    var widgetMessage = $( '.widget-message' ),
        widgetRequest = $( '.widget-service-request'),
        widgetPayRent = $( 'form[name="ps_pay_rent_form"]'),
        widgetAskPro = $( 'form[name="erp_users_ask_pro_form"]'),
        widgetCheckEmail = $( 'form[name="sm_email_form"]');

    this.newTitle();
    this.datePicker();

    widgetMessage.validate({
        success: function() {
            widgetMessage.find('button[type=submit]').prop('disabled', false);
        },
        errorPlacement: function( error, element ) {
            return false;
        }
    });

    widgetRequest.validate({
        success: function() {
            widgetRequest.find('button[type=submit]').prop('disabled', false);
        },
        errorPlacement: function( error, element ) {
            return false;
        }
    });

    widgetPayRent.validate({
        success: function() {
            widgetPayRent.find('button[type=submit]').prop('disabled', false);
        },
        errorPlacement: function( error, element ) {
            return false;
        }
    });

    widgetAskPro.validate({
        success: function() {
            widgetAskPro.find('button[type=submit]').prop('disabled', false);
        },
        errorPlacement: function( error, element ) {
            return false;
        }
    });

    widgetCheckEmail.validate({
        success: function() {
            widgetCheckEmail.find('button[type=submit]').prop('disabled', false);
        },
        errorPlacement: function( error, element ) {
            return false;
        }
    });
};

$(function() {
    var controller = new ProfileController();
    controller.run();
});
