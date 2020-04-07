/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_MediaGalleryUi/js/grid/columns/image/actions',
    'Magento_Ui/js/modal/alert'
], function ($, Action, uiAlert) {
    'use strict';

    return Action.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/mediaGallery/grid/columns/image/licenseActions',
            licenseAction: {
                name: 'license',
                title: 'License',
                handler: 'licenseImageAction'
            },
            modules: {
                image: '${ $.imageComponent }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super().observe(['visible']);
            this.actionsList.push(this.licenseAction);

            return this;
        },

        /**
         * License image
         *
         * @param {Object} record
         */
        licenseImageAction: function (record) {
            this.getImageRecord(record.id);
        },

        /**
         * Check if image licensed
         *
         * @param {Object} record
         * @param {Object} name
         */
        isVisible: function (record, name) {

            if (name === this.licenseAction.name) {
                return (record.licensed == 0)  ? true  : false;
            }

            return true;
        },

        /**
         * Get image record and start license process
         *
         * @param {Number} imageId
         */
        getImageRecord: function (imageId) {
            $.ajax({
                type: 'GET',
                url: this.imageDetailsUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'id': imageId
                },
                context: this,

                /**
                 * Success handler for deleting image
                 *
                 * @param {Object} response
                 */
                success: function (response) {
                    $.ajaxSetup({
                        async: false
                    });

                    response.imageDetails.id =  response.imageDetails['adobe_stock'][0].value;
                    response.imageDetails.category =  response.imageDetails['adobe_stock'][3].value;
                    this.image().displayedRecord(response.imageDetails);
                    this.image().actions().licenseProcess();

                    $.ajaxSetup({
                        async: true
                    });
                    this.imageModel().reloadGrid();
                }.bind(this),

                /**
                 * Error handler for deleting image
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.message === 'undefined'
                    ) {
                        message = 'There was an error on attempt to get the image details.';
                    } else {
                        message = response.responseJSON.message;
                    }
                    uiAlert({
                        content: message
                    });

                }.bind(this)
            });
        }
    });
});
