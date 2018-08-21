(function ($) {
    /**
     * @param options
     */
    $.fn.erpPopup = function (options) {
        var settings = $.extend({
            selector: 'a[ role="popup" ]',
            callback: null
        }, options);

        $(this).on('click', settings.selector, function (e) {
            e.preventDefault();
            init($(this), settings);
        });

        if (!$('#epr-modal-popup').length) {
            var modalHtml =
                    '<div class="modal fade" id="epr-modal-popup" tabindex="-1" role="dialog" aria-labelledby="my-modal-label" aria-hidden="true">'
                    + '<div class="modal-dialog">'
                    + '<div class="modal-content">'
                    + '<div class="modal-header">'
                    + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
                    + '<h4 class="modal-title" id="myModalLabel"></h4>'
                    + '</div>'
                    + '<div class="modal-body"></div>'
                    + '<div class="modal-footer"></div>'
                    + '</div>';
            +'</div>';
            +'</div>';

            $(modalHtml).appendTo($(this));
        }
    };

    /**
     * @param element
     * @param settings
     */
    function init(element, settings) {
        var trigger = $(element);

        $.ajax({
            type: 'GET',
            cache: false,
            url: trigger.attr('href'),
            dataType: 'json',
            async: true,
            success: function (response) {
                var noAjax = trigger.data('noajax');

                processResponse(response, noAjax);
                var fn = settings.callback;
                if (fn) {
                    fn();
                }

                return false;
            }
        });

        $('#epr-modal-popup').modal('show');
    }

    /**
     * @param json
     * @param noAjax
     */
    function processResponse(json, noAjax)
    {
        if (json.redirect) {
            document.location = json.redirect;
        } else {

            $('#epr-modal-popup').find('.modal-body').html(json.html);
            $('#epr-modal-popup').find('.modal-title').html(json.modalTitle);
            $('body').trigger('popup-open');
            $('#cf-modal').erpPopup();

            var modalBody = $('#epr-modal-popup').find('.modal-body');
            //check if we have form in modal
            var form = modalBody.find('form');

            if (form && !noAjax) {
                form.submit(function (e) {
                    var $form = $(this);
                    $form.find('button[type=submit]').prop('disabled', true);

                    $.ajax({
                        type: 'POST',
                        cache: false,
                        url: form.attr('action'),
                        data: form.serialize(),
                        async: true,
                        dataType: 'json',
                        success: function (response) {
                            processResponse(response);
                        }

                    });
                    return false;
                });

            }
        }
    }
})($);

$(document).ready(function () {
    $('body').erpPopup();

    $('form[name!=contract-form]').on('submit', function () {
        if ($(this).prop('name') != 'ps_history_export_form' && $(this).prop('name') != 'erp_stripe_transactions_export') {
            $(this).find('button[type=submit]').prop('disabled', true);
        }
    });

    $('form[name=contract-form] input[name=sign], .payment-type a').on('click dblclick', function () {
        $('body').addClass('overlay preloader');
    });
});
