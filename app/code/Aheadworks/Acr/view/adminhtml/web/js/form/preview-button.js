/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/form/components/button',
    'jquery',
    'Magento_Ui/js/lib/spinner',
], function (Button, $, spinner) {
    'use strict';

    return Button.extend({
        /**
         * @inheritdoc
         */
        applyAction: function (action) {
            var emailData = {
                subject: this.source.get('data.subject'),
                content: this.source.get('data.content'),
                store_ids: this.source.get('data.store_ids'),
            };
            var previewUrl = action.url;

            spinner.show();

            $.ajax({
                url: previewUrl,
                type: "POST",
                dataType: 'json',
                data: {
                    email_data: emailData
                },
                complete: function() {
                    spinner.hide();
                    window.open(previewUrl, '_blank', 'resizable, scrollbars, status, top=0, left=0, width=600, height=500');
                }
            });
        },

        /**
         * Hide element
         *
         * @returns {Abstract} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        },

        /**
         * Show element
         *
         * @returns {Abstract} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        },
    });
});
