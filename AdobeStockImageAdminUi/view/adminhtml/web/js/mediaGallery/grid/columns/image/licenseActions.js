/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_MediaGalleryUi/js/grid/columns/image/actions',
    'Magento_MediaGalleryUi/js/action/getDetails',
    'mage/translate'
], function ($, _, Action, getDetails) {
    'use strict';

    return Action.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/mediaGallery/grid/columns/image/licenseActions',
            licenseAction: {
                name: 'license',
                title: $.mage.__('License'),
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
            this._super();
            this.actionsList.push(this.licenseAction);

            return this;
        },

        /**
         * Init observable variables
         *
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'visible'
                ]);

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
                if (_.isUndefined(record.overlay) || record.overlay === '') {
                    return false;
                }

                return true;
            }

            return true;
        },

        /**
         * Get image record and start license process
         *
         * @param {Number} imageId
         */
        getImageRecord: function (imageId) {
            getDetails(this.imageDetailsUrl, [imageId]).then(function (imageDetails) {
                var id = imageDetails[imageId]['adobe_stock'][0].value;

                this.image().actions().licenseProcess(
                    id,
                    imageDetails[imageId].title,
                    imageDetails[imageId].path,
                    imageDetails[imageId]['content_type'],
                    true
                ).then(function () {
                    this.image().actions().login().getUserQuota();
                    this.imageModel().reloadGrid();
                    this.imageModel().addMessage('success', $.mage.__('The image has been licensed.'));
                }.bind(this)).fail(function (error) {
                    if (error) {
                        this.imageModel().addMessage('error', error);
                    }
                }.bind(this));
            }.bind(this)).fail(function (message) {
                this.imageModel().addMessage('error', message);
            }.bind(this));
        }
    });
});
