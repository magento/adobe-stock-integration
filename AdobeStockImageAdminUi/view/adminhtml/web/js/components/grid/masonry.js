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
            template: 'Magento_AdobeStockImageAdminUi/grid/masonry',
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

            /**
             * Container styles
             * @param {Object}
             */
            containerStyles: {},
        },

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            this._super()
                .observe([
                    'rows',
                    'images',
                    'containerStyles'
                ]);

            return this;
        },

        /**
         * Init component handler
         * @param rows
         * @return {Object}
         */
        initComponent: function (rows) {
            if (!rows.length) {
                this.totalHeight = 0;
                this.setContainerHeight();
                return;
            }

            this.imageMargin = parseInt(this.imageMargin);
            this.container = $('[data-id="' + this.containerId + '"]')[0];

            this.setLayoutStyles();
            this.setContainerHeight();
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
                    this.setContainerHeight();
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
         * Set optimal container height
         */
        setContainerHeight: function() {
            var styles = this.containerStyles();

            styles['height'] = this.totalHeight + 'px';
            this.containerStyles(styles);
        },

        /**
         * Set layout styles inside the container
         */
        setLayoutStyles: function() {
            var containerWidth = parseInt(this.container.clientWidth),
                row = [],
                translateX = 0,
                translateY = 0,
                ratio = 0,
                imageWidth = 0,
                rowHeight = 0,
                calcHeight = 0;

            this.setMinRatio();

            this.rows().forEach(function(image, index) {
                ratio += parseFloat((image.width / image.height).toFixed(2));
                row.push(image);

                if (ratio >= this.minRatio || index + 1 === this.rows().length) {
                    ratio = Math.max(ratio, this.minRatio);
                    calcHeight = (containerWidth - this.imageMargin * (row.length - 1)) / ratio;
                    rowHeight = (calcHeight < this.maxImageHeight) ? calcHeight : this.maxImageHeight;

                    row.forEach(function(img) {
                        imageWidth = rowHeight * ((img.width / img.height).toFixed(2));
                        this.setImageStyles(img, imageWidth, rowHeight, translateX, translateY);
                        translateX += imageWidth + this.imageMargin;
                    }.bind(this));

                    row = [];
                    ratio = 0;
                    translateY += parseInt(rowHeight) + this.imageMargin;
                    translateX = 0;
                }
            }.bind(this));
            this.totalHeight = translateY - this.imageMargin;
        },

        /**
         * Set styles for every image in layout
         *
         * @param {Object} img
         * @param {Number} imageWidth
         * @param {Number} rowHeight
         * @param {Number} translateX
         * @param {Number} translateY
         */
        setImageStyles: function (img, imageWidth, rowHeight, translateX, translateY) {
            var styles = {
                width: parseInt(imageWidth) + 'px',
                height: parseInt(rowHeight) + 'px',
                transform: ('translate3d(' + translateX + 'px,' + translateY + 'px, 0)')
            };
            if (!img.styles) {
                img.styles = ko.observable();
            }
            img.styles(styles)
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
        }
    });
});
