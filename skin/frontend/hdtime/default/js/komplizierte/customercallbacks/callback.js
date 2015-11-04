var customerCallbacks = (function($){

    var actionStop;

    var callbackForm;

    var private_methods = {

        showCallbackPopup: function () {
            $('.callbacks-popup').fadeIn();
        },

        hideCallbackPopup: function () {
            $('.callbacks-popup').fadeOut();
        },

        animateLoader: function(action) {
            if(action == 'start') {
                $('.callbacks_loader').fadeIn();
            } else {
                $('.callbacks_loader').fadeOut();
            }
        },
        setMessage: function(msg) {
            $('.callbacks_message').html(msg);
        },
        clearFields: function() {
            $('#callbacks_name').val('');
            $('#callbacks_phone').val('');
        },
        hideForm: function() {
            $('.callbacks-welcome-message').fadeOut();
            $('.webforms-fields-username').fadeOut();
            $('.webforms-fields-phonenumber').fadeOut();
            $('.webforms-callback .buttons-set').fadeOut();
        },
        callAjaxControllerLogin: function(url){
            if (actionStop != true){

                actionStop = true;
                private_methods.animateLoader('start');
                private_methods.setMessage('');
                var name = $('#callbacks_name').val();
                var phone = $('#callbacks_phone').val();

                jQuery.post( url, { name: name, phone: phone })
                    .done(function(msg) {
                        if (msg.error){
                            private_methods.setMessage(msg.error);
                        } else if(msg.success) {
                            private_methods.clearFields();
                            private_methods.hideForm();
                            private_methods.setMessage(msg.success);
                        }
                        actionStop = false;
                        private_methods.animateLoader('stop');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        actionStop = false;
                        private_methods.animateLoader('stop');
                    });
            }
        }

    }

    var public_methods = {

        callbackInit: function(){
            callbackForm = new VarienForm('form_customercallbacks');
            actionStop = false;
        },
        submitForm: function () {
            $('#form_customercallbacks').submit(function(){
                if(callbackForm.validator.validate()) {
                    private_methods.callAjaxControllerLogin($('#form_customercallbacks').attr('action'));
                }
                return false;
            });
        },
        showPopup: function () {
            private_methods.showCallbackPopup();
        },
        hidePopup: function () {
            private_methods.hideCallbackPopup();
        }
    }

    return public_methods;
})(jQuery);