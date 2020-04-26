/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/alert',
    'Magento_MediaGalleryUi/js/grid/columns/image/actions',
    'Magento_MediaGalleryUi/js/action/getDetails',
    'Magento_AdobeStockImageAdminUi/js/action/getLicenseStatus'
], function ($, _, uiAlert, Action, getDetails, getLicenseStatus) {
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
                if (_.isNull(record['is_licensed'])) {
                    return false;
                }

                return !parseInt(record['is_licensed'], 16);
            }

            return true;
        },

        /**
         * Get image record and start license process
         *
         * @param {Number} imageId
         */
        getImageRecord: function (imageId) {
            this.image().actions().login().login().then(function () {
                getDetails(this.imageDetailsUrl, imageId).then(function (imageDetails) {
                    var id = imageDetails['adobe_stock'][0].value;

                    getLicenseStatus(
                        this.image().actions().overlay().getImagesUrl,
                        [id]
                    ).then(function (licensedInfo) {
                        var isLicensed = licensedInfo[id] || false;

                        this.image().actions().licenseProcess(
                            id,
                            imageDetails.title,
                            imageDetails.path,
                            imageDetails['content_type'],
                            isLicensed,
                            true
                        ).then(function () {
                            this.image().actions().login().getUserQuota();
                            this.imageModel().reloadGrid();
                        }.bind(this)).catch(function (error) {
                            if (error) {
                                uiAlert({
                                    content: error
                                });
                            }
                        });
                    }.bind(this));
                }.bind(this)).catch(function (message) {
                    uiAlert({
                        content: message
                    });
                });
            }.bind(this)).catch(function (error) {
                uiAlert({
                    content: error
                });
            });
        }
    });
});
