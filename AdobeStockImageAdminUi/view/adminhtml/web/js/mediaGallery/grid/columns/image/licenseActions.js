/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_MediaGalleryUi/js/grid/columns/image/actions'
], function ($, Action) {
    'use strict';

    return Action.extend({
        defaults: {
            adobeStockModalSelector: '.adobe-search-images-modal',
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
            this._super().observe(['visible', 'licenseImage']);
            this.actionsList.push(this.licenseAction);

            return this;
        },

        /**
         * License image
         */
        licenseImageAction: function (record) {
            console.log(record.id);
            this.getImageRecord(record.id);
        },

        /**
         * Check if image licensed
         */
        isVisible: function (record, name) {

            if (name === this.licenseAction.name) {
                return record.licensed  ? true  : false;
            }

            return true;
        },

        /**
         * Return image record fo license action
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
                    this.image().actions().showLicenseConfirmation(response.imageDetails);

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
                    console.log(response);
                }.bind(this)
            });
        }
    });
});
