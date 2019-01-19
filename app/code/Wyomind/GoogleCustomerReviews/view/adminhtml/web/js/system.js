var gtsBadgeCode = null;
var gtsOrderCode = null;
var gcrBadgeCode = null;
var gcrOrderCode = null;

GoogleTrustedStores = {
    testBadge: function (website_id, url_test) {
        var fieldset = jQuery('#googlecustomerreviews_badge');
        var data = {};
        fieldset.find('input,select,textarea').each(function () {
            data[jQuery(this).prop('id')] = jQuery(this).val();
        });
        data.website = website_id;
        data['product-sku'] = jQuery('#product-sku').val();

        jQuery.ajax({
            url: url_test,
            data: data,
            type: 'POST',
            showLoader: true,
            success: function (data) {
                gtsBadgeCode.setValue(data);
                gtsBadgeCode.refresh();
                var url = jQuery('#GtsValidatorBadgeUrl').attr("base") + "id/" + jQuery('#product-sku').val();
                jQuery('#GtsValidatorBadgeUrl').attr('href', url);
                jQuery('#GtsValidatorBadgeUrl').text(url);
            }
        });
    },
    testOrder: function (website_id, url_test) {
        var fieldset = jQuery('#googlecustomerreviews_badge');
        var data = {};
        fieldset.find('input,select,textarea').each(function () {
            data[jQuery(this).prop('id')] = jQuery(this).val();
        });
        fieldset = jQuery("#googlecustomerreviews_orders");
        fieldset.find('input,select,textarea').each(function () {
            data[jQuery(this).prop('id')] = jQuery(this).val();
        });
        data.website = website_id;
        data['order-number'] = jQuery('#gts-order-number').val();

        jQuery.ajax({
            url: url_test,
            data: data,
            type: 'POST',
            showLoader: true,
            success: function (data) {
                gtsOrderCode.setValue(data);
                gtsOrderCode.refresh();
                var url = jQuery('#GtsValidatorOrderUrl').attr("base") + "id/" + jQuery('#gts-order-number').val();
                jQuery('#GtsValidatorOrderUrl').attr('href', url);
                jQuery('#GtsValidatorOrderUrl').text(url);
            }
        });
    }

};


GoogleCustomerReviews = {
    testBadge: function (urlTest) {
        var fieldset = jQuery('#googlecustomerreviews_badge');
        var data = {};
        fieldset.find('input,select,textarea').each(function () {
            data[jQuery(this).prop('id')] = jQuery(this).val();
        });

        jQuery.ajax({
            url: urlTest,
            data: data,
            type: 'POST',
            showLoader: true,
            success: function (data) {
                gcrBadgeCode.setValue(data);
                gcrBadgeCode.refresh();
                var url = jQuery('#GcrValidatorBadgeUrl').attr("base");
                jQuery('#GcrValidatorBadgeUrl').attr('href', url);
                jQuery('#GcrValidatorBadgeUrl').text(url);
            }
        });
    },
    testOrder: function (website_id, url_test) {
        var fieldset = jQuery('#googlecustomerreviews_badge');
        var data = {};
        fieldset.find('input,select,textarea').each(function () {
            data[jQuery(this).prop('id')] = jQuery(this).val();
        });
        fieldset = jQuery("#googlecustomerreviews_orders");
        fieldset.find('input,select,textarea').each(function () {
            data[jQuery(this).prop('id')] = jQuery(this).val();
        });
        data.website = website_id;
        data['order-number'] = jQuery('#gcr-order-number').val();

        jQuery.ajax({
            url: url_test,
            data: data,
            type: 'POST',
            showLoader: true,
            success: function (data) {
                gcrOrderCode.setValue(data);
                gcrOrderCode.refresh();
                var url = jQuery('#GcrValidatorOrderUrl').attr("base") + "id/" + jQuery('#gcr-order-number').val();
                jQuery('#GcrValidatorOrderUrl').attr('href', url);
                jQuery('#GcrValidatorOrderUrl').text(url);
            }
        });
    }

};

require([
    "jquery",
    "mage/mage"
], function ($) {
    $(function () {

        jQuery(document).ready(function () {

            if (document.getElementById('gts-badge-test-page')) {
                gtsBadgeCode = CodeMirror.fromTextArea(document.getElementById('gts-badge-test-page'), {
                    matchBrackets: true,
                    mode: "text/html",
                    readOnly: true,
                    indentUnit: 2,
                    indentWithTabs: false,
                    lineNumbers: true,
                    styleActiveLine: true
                });

                gtsOrderCode = CodeMirror.fromTextArea(document.getElementById('gts-badge-test-order'), {
                    matchBrackets: true,
                    mode: "text/html",
                    readOnly: true,
                    indentUnit: 2,
                    indentWithTabs: false,
                    lineNumbers: true,
                    styleActiveLine: true
                });
                gcrBadgeCode = CodeMirror.fromTextArea(document.getElementById('gcr-badge-test-page'), {
                    matchBrackets: true,
                    mode: "text/html",
                    readOnly: true,
                    indentUnit: 2,
                    indentWithTabs: false,
                    lineNumbers: true,
                    styleActiveLine: true
                });

                gcrOrderCode = CodeMirror.fromTextArea(document.getElementById('gcr-badge-test-order'), {
                    matchBrackets: true,
                    mode: "text/html",
                    readOnly: true,
                    indentUnit: 2,
                    indentWithTabs: false,
                    lineNumbers: true,
                    styleActiveLine: true
                });
            }

        });

    });
});