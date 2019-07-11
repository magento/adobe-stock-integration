/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiElement',
    'jquery',
    'knockout',
    'Magento_AdobeStockImageAdminUi/js/components/preview'
], function (Element, $, ko, preview) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/masonry',
            imports: {
              rows: '${ $.provider }:data.items'
            },
            listens: {
                'rows': 'initComponent'
            },

            /**
             * Images array
             * @param array
             */
            images: [],

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

            /**
             * Show preview image
             * @param bool
             */
            showPreviewImage: true,
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
            this.images([]);
            if (!rows.length) {
                return;
            }

            this.imageMargin = parseInt(this.imageMargin);
            this.container = $('[data-id="' + this.containerId + '"]')[0];

            this.prepareImages(rows);
            this.setLayoutStyles();
            this.setEventListener();
            return this;
        },

        /**
         * Prepare and assign images to observable var
         * @param rows
         */
        prepareImages: function (rows) {
            this.images(rows.map( (asset) => {
                return {
                    src: asset.url,
                    ratio: (asset.width / asset.height).toFixed(2),
                    id: asset.id,
                    width: asset.width,
                    height: asset.height,
                    preview_url: asset.preview_url || asset.url,
                    title: asset.title,
                    firstInARow: ko.observable(false),
                    lastInARow: ko.observable(false),
                    firstRow: ko.observable(false),
                    lastRow: ko.observable(false),
                };
            }));
            if(this.showPreviewImage) {
                preview(this.images);
            }
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
            var containerWidth = parseInt(this.container.clientWidth),
                row = [],
                ratio = 0,
                imageWidth = 0,
                rowHeight = 0,
                calcHeight = 0,
                isFirstRow = true;

            this.setMinRatio();

            this.images().forEach(function(image, index) {
                ratio += parseFloat(image.ratio);
                row.push(image);
                image.firstRow(isFirstRow);

                if (ratio >= this.minRatio || index + 1 === this.images().length) {
                    ratio = Math.max(ratio, this.minRatio);
                    calcHeight = (containerWidth - this.imageMargin * (row.length - 1)) / ratio;
                    rowHeight = (calcHeight < this.maxImageHeight) ? calcHeight : this.maxImageHeight;

                    row.forEach(function(img, _index) {
                        imageWidth = rowHeight * img.ratio;
                        img.firstInARow(_index === 0);
                        img.lastInARow(_index === row.length - 1);
                        img.lastRow(!this.images()[index + 1]);
                        this.setImageStyles(img.id, imageWidth, rowHeight);
                    }.bind(this));

                    row = [];
                    ratio = 0;
                    isFirstRow = false;
                }
            }.bind(this));
        },

        /**
         * Set styles for every image in layout
         *
         * @param {Number} imageId
         * @param {Number} imageWidth
         * @param {Number} rowHeight
         * @param {Number} marginBottom
         */
        setImageStyles: function (imageId, imageWidth, rowHeight) {
            $('[data-id="' + imageId + '"]').css({
                width: parseInt(imageWidth) + 'px',
                height: parseInt(rowHeight) + 'px',
            });
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

        togglePreview: function(item, event){
            preview.image(item);

            var $target = $(event.currentTarget),
                $lastInARow = $target.is('.last')? $target: $target.find('~ .last:eq(0)');
            $target.preview(item, $last);

            if(!$target.is('.last')) {
                $target = $target.find('~ .last:eq(0)');
            }

            var $template = $('<div class="expand-preview"><img src="' + item.preview_url + '" /></div>').hide(),
                $preview = $target.parent().find('.expand-preview');
            if($preview.length) {
                $preview.slideUp(400, function(){
                    $preview.remove();
                });
                if($preview.find('img').attr('src') != item.preview_url) {
                    $template.insertAfter($target).slideDown();
                }
            } else {
                $template.insertAfter($target).slideDown();
            }
        }
    });
});
