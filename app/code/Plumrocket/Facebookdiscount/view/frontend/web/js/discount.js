/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package Plumrocket_Facebook_Discount
 * @copyright Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license http://wiki.plumrocket.net/wiki/EULA End-user License Agreement
 */

define(
    [
        'jquery',
        'domReady!'
    ],
    function ($) {
        "use strict";
        window._plFacebookDiscount = function (conf) {

            var $this = this;
            $this.conf = conf;

            $this.plumrocketFacebookDiscountFunction = function (api_key) {
                if (api_key) {
                    window.fbAsyncInit = function () {
                        FB.init({
                            appId      : api_key,
                            xfbml      : true,
                            version    : 'v2.11'
                        });
                    };
                };
            };

            $this.updateTotals = function () {
                $('#form-validate').submit();
            };

            $this.plumrocketFacebookDiscountQueue = function(url) {

                var requestInterval;
                var detectInterval = setInterval(function() {
                var facebookFrame = $(".fb-like iframe");

                if (facebookFrame.length == 1) {
                    facebookFrame.iframeTracker({
                        blurCallback: function(){
                            if (!requestInterval) {
                                requestInterval = setInterval(function() {
                                    $.ajax({
                                        method: "post",
                                        url: url,
                                        data: {},
                                        success: function (response) {
                                            if (response && response.success) {
                                                $('.fb_discount_holder').hide();
                                                setTimeout(function () {
                                                    $this.updateTotals();
                                                }, 500);
                                            }
                                        }
                                    });
                                }, 1000);

                                setTimeout(function() {
                                    if (requestInterval) {
                                      clearInterval(requestInterval);
                                      requestInterval = undefined;
                                    }
                                }, 50000)
                            }
                          }
                      });
                    clearInterval(detectInterval);
                  }
                }, 1000);
            };
        };
    }
);
