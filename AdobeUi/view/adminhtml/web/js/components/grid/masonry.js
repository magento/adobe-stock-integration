/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/listing',
    'jquery',
    'ko'
], function (Element, $, ko) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magento_AdobeUi/grid/masonry',
            imports: {
                rows: '${ $.provider }:data.items'
            },
            listens: {
                'rows': 'initComponent'
            },

            /**
             * Images container id
             * @param string
             */
            containerId: null,

            /**
             * Minimum aspect ratio for each image
             * @param int
             */
            minRatio: null,

            /**
             * Container width
             * @param int
             */
            containerWidth: window.innerWidth,

            /**
             * Margin between images
             * @param int
             */
            imageMargin: 20,

            /**
             * Maximum image height value
             * @param int
             */
            maxImageHeight: 240,
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'rows'
                ]);
            return this;
        },

        /**
         * Init component handler
         * @param rows
         * @return {Object}
         */
        initComponent: function (rows) {

            if (!rows || !rows.length) {
                return;
            }

            this.imageMargin = parseInt(this.imageMargin, 10);
            this.container = $('[data-id="' + this.containerId + '"]')[0];

            this.setLayoutStyles();
            this.setEventListener();
            return this;
        },

        /**
         * Set event listener to track resize event
         */
        setEventListener: function () {
            var running = false,
                handler = function() {
                    this.containerWidth = window.innerWidth;
                    this.setLayoutStyles();
                }.bind(this);

            window.addEventListener('resize', function () {
                if (!running) {
                    running = true;
                    if (window.requestAnimationFrame) {
                        window.requestAnimationFrame(function () {
                            handler();
                            running = false;
                        });
                    } else {
                        setTimeout(function () {
                            handler();
                            running = false;
                        }, 66);
                    }
                }
            });
        },

        /**
         * Set layout styles inside the container
         */
        setLayoutStyles: function() {
            var containerWidth = parseInt(this.container.clientWidth, 10) - this.imageMargin,
                row = [],
                ratio = 0,
                imageWidth = 0,
                rowHeight = 0,
                calcHeight = 0,
                isBottom = false,
                imageRowNumber = 1;

            this.setMinRatio();

            this.rows().forEach(function(image, index) {
                ratio += parseFloat((image.width / image.height).toFixed(2));
                row.push(image);

                if (ratio >= this.minRatio || index + 1 === this.rows().length) {
                    ratio = Math.max(ratio, this.minRatio);
                    calcHeight = (containerWidth - this.imageMargin * (row.length - 1)) / ratio;
                    rowHeight = calcHeight < this.maxImageHeight ? calcHeight : this.maxImageHeight;
                    isBottom = index + 1 === this.rows().length;

                    row.forEach(function(img) {
                        imageWidth = rowHeight * (img.width / img.height).toFixed(2);
                        this.setImageStyles(img, imageWidth, rowHeight);
                        this.setImageClass(img, {
                            bottom: isBottom
                        });
                        img.rowNumber = imageRowNumber;
                    }.bind(this));

                    row[0].firstInRow = true;
                    row[row.length - 1].lastInRow = true;
                    row = [];
                    ratio = 0;
                    imageRowNumber++;
                }
            }.bind(this));
        },

        /**
         * Set styles for every image in layout
         *
         * @param {Object} img
         * @param {Number} imageWidth
         * @param {Number} rowHeight
         */
        setImageStyles: function (img, imageWidth, rowHeight) {
            if (!img.styles) {
                img.styles = ko.observable();
            }
            img.styles({
                width: parseInt(imageWidth, 10) + 'px',
                height: parseInt(rowHeight, 10) + 'px'
            });
        },

        /**
         *
         * @param {Object} img
         * @param {Object} classes
         */
        setImageClass: function(img, classes){
            if (!img.css) {
                img.css = ko.observable(classes);
            }
            img.css(classes);
        },

        /**
         * Set min ratio for images in layout
         */
        setMinRatio: function() {
            if (this.containerWidth <= 640) {
                this.minRatio = 3;
            } else if (this.containerWidth <= 1280) {
                this.minRatio = 5;
            } else if (this.containerWidth <= 1920) {
                this.minRatio = 8;
            } else {
                this.minRatio = 10;
            }
        },

        /**
         * Checks if grid has data.
         *
         * @returns {Boolean}
         */
        hasData: function () {
            return !!this.rows() && !!this.rows().length;
        },
    });
});
