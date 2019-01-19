require([
    "jquery",
    "mage/mage",
    "mage/translate"
], function ($) {
    $(function () {

        jQuery(document).ready(function () {
            setTimeout(function() {
            GtsValidator.notify = jQuery('<div/>', {id: 'GtsValidator'});

            if (typeof GtsValidator !== "undefined") {
                if (typeof GtsValidator.badge !== "undefined") {
                    if (GtsValidator.badge) {

                        // check potential errors
                        var errors = "";
                        // check badge position
                        var badge_position = "";

                        for (var option in gts) {
                            switch (gts[option][0]) {
                                case "id":
                                    if (gts[option][1] == "") {
                                        errors += "- " + jQuery.mage.__("Google Trusted Stores account id is empty") + "<br/>";
                                    }
                                    break;
                                case "google_base_country":
                                    if (gts[option][1] == "") {
                                        errors += "- " + jQuery.mage.__("Country is empty") + "<br/>";
                                    }
                                    break;
                                case "google_base_country":
                                    if (gts[option][1] == "") {
                                        errors += "- " + jQuery.mage.__("Country is empty") + "<br/>";
                                    }
                                    break;
                                case "badge_position":
                                    badge_position = gts[option][1];
                                    break;
                                default:
                                    break;
                            }
                        }

                        if (errors.length == 0) {
                            GtsValidator.notify.text(jQuery.mage.__('Google Trusted Stores badge implemented!'));
                            GtsValidator.notify.css({"color": "green"});

                            if (jQuery("#gts_container")) {
                                jQuery("#gts_container").css({
                                    "width": "150px",
                                    "height": "75px",
                                    "background": "white",
                                    "border": "1px solid green",
                                    "border-radius": "2px"
                                });
                                if (badge_position == "BOTTOM_LEFT") {
                                    jQuery("#gts_container").css({
                                        "position": "fixed",
                                        "z-index": 999,
                                        "padding": "5px",
                                        "bottom": "0px",
                                        "left": "0px"
                                    });
                                } else if (badge_position == "BOTTOM_RIGHT") {
                                    jQuery("#gts_container").css({
                                        "position": "fixed",
                                        "z-index": 999,
                                        "padding": "5px",
                                        "bottom": "0px",
                                        "right": "0px"
                                    });
                                }
                                jQuery("#gts_container").html(jQuery.mage.__("Google Trusted Badge will be displayed<br/>here"));
                            }

                        } else {
                            GtsValidator.notify.html(jQuery.mage.__('Google Trusted Stores badge implemented but some errors have been found!')+'<br/>' + errors);
                            GtsValidator.notify.css({"color": "orange"});
                        }
                    } else {
                        GtsValidator.notify.text(jQuery.mage.__("Google Trusted Stores badge can't be found!"));
                        GtsValidator.notify.css({"color": "red"});
                    }
                }
                if (typeof GtsValidator.order !== "undefined") {
                    if (GtsValidator.order) {
                        GtsValidator.notify.text(jQuery.mage.__("Google Trusted Stores confirmation module implemented!"));
                        GtsValidator.notify.css({"color": "green"});
                    } else {
                        GtsValidator.notify.text(jQuery.mage.__("Google Trusted Stores confirmation module can't be found!"));
                        GtsValidator.notify.css({"color": "red"});
                    }
                }
            } else {
                GtsValidator.notify(jQuery.mage.__("Google Trusted Stores doesn't seem to be implemented!"));
                GtsValidator.notify.setStyle({"color": "red"});
            }
            jQuery("body").append(GtsValidator.notify);
            },2000);
        });

    });
});
        