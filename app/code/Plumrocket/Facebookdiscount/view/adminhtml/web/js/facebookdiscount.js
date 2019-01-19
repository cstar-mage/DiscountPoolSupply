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
 * @package     Plumrocket_Facebook_Discount
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

 require([
     'jquery',
     'domReady!'
 ], function ($) {
   var ids = [
       "facebookdiscount_general_callback_url",
       "facebookdiscount_general_verify_token"
   ];
   $.each(ids, function(i,v) {
       $('#' + v).on('focus click', function () {
           $(this).select();
       });
   });
 });