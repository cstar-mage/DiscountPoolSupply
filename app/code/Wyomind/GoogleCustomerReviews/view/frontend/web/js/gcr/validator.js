require([
    "jquery",
    "mage/mage",
    "mage/translate"
], function ($) {
    $(function () {

        jQuery(document).ready(function () {
            setTimeout(function () {
                GcrValidator.notify = jQuery('<div/>', {id: 'GcrValidator'});

                if (typeof GcrValidator !== "undefined") {
                    if (typeof GcrValidator.badge !== "undefined") {
                        if (GcrValidator.badge) {
                            GcrValidator.notify.html(jQuery.mage.__('Google Customer Reviews badge implemented!'));
                            GcrValidator.notify.css({"color": "green"});
                        } else {
                            GcrValidator.notify.text(jQuery.mage.__("Google Customer Reviews badge can't be found!"));
                            GcrValidator.notify.css({"color": "red"});
                        }
                    }
                    if (typeof GcrValidator.order !== "undefined") {
                        if (GcrValidator.order) {
                            GcrValidator.notify.text(jQuery.mage.__("Google Customer Reviews opt-in module implemented!"));
                            GcrValidator.notify.css({"color": "green"});
                        } else {
                            GcrValidator.notify.text(jQuery.mage.__("Google Customer Reviews opt-in module can't be found!"));
                            GcrValidator.notify.css({"color": "red"});
                        }
                    }
                } else {
                    GcrValidator.notify(jQuery.mage.__("Google Customer Reviews doesn't seem to be implemented!"));
                    GcrValidator.notify.setStyle({"color": "red"});
                }
                jQuery("body").append(GcrValidator.notify);
            }, 2000);
        });
    });
});
        