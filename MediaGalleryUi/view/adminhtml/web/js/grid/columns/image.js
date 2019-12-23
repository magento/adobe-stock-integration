/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/grid/columns/image'
], function ($, image) {
    'use strict';

    return image.extend({
        defaults: {
            bodyTmpl: 'Magento_MediaGalleryUi/grid/columns/image',
        },

        /**
         * Return image name from file name.
         *
         * @param {Object} record
         * @return {String}
         */
        getName: function (record) {
            return this.extractImageName(record.thumbnail_url).replace(/\.[^/.]+$/, "");
        },

        /**
         * Return content type of image
         *
         * @param {Object} record
         * @return {String}
         */
        getContentType: function (record) {
            return record['content_type'].replace(/(\image\/)/, "").toUpperCase();;
        },

        /**
         * Display source if image was downloaded in adobe stock.
         *
         * @param {Object} record
         * @return {String}
         */
        getSource: function (record) {
            return record.source ? "AStock" : '';
        },

        /**
         * Returns dimensions to given record.
         *
         * @param {Object} record
         * @return {String}
         */
        getDimensions: function (record) {
            return record.width + 'x' + record.height;
        },

        /**
         * Extract image Name from Given url.
         *
         * @param {String} imageUrl
         * @return {String}
         */
        extractImageName: function (imageUrl) {
            return imageUrl.match(/([\w\d_-]*)\.?[^\\\/]*$/g)[0];
            
        },


    });
});
