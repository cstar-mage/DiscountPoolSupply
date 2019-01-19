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
 * @package     Plumrocket_Estimateddelivery
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

/**
 * Estimateddelivery load
 */
 define([
    'domReady!',
    'jquery'
], function (domReady, $) {
    'use strict';

    return function (sourceData, element) {

        $.ajax({
            "url":sourceData.url,
            "method":'post',
            "data":{'source_data':sourceData}
        }).success(function(html) {
            $(element).replaceWith(html)
        }).fail(function(xhr, ajaxOptions, thrownError) {
            console.log(thrownError);
        });
    };

});