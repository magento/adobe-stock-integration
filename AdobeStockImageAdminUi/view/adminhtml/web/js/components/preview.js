/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiElement',
    'underscore',
    'knockout'
], function (Element, _, ko) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/preview',

            /**
             * Images array
             * @param array
             */
            images: [],

            /**
             * Current image data
             * @param object
             */
            image: {},

            /**
             * Current image index
             * @param integer
             */
            currentImageIndex: 0,

            /**
             * Visible
             * @param bool
             */
            visible: false,

            /**
             * Show labels below image
             * @param bool
             */
            showLabels: false,
        },

        prevAvailable: false,
        nextAvailable: false,
        saveAvailable: false,

        /**
         * Init observable variables
         * @return {Object}
         */
        initObservable: function () {
            var _this = this;
            this._super().observe([
                'images',
                'image',
                'visible',
                'showLabels',
            ]);
            this.prevAvailable = ko.computed(function(){
                return this.images()[this.currentImageIndex - 1] !== 'undefined'
            }, this);
            this.nextAvailable = ko.computed(function(){
                return this.images()[this.currentImageIndex + 1] !== 'undefined'
            }, this);
            this.currentImageIndex.subscribe(function(){
                _this.image(_this.images()[_this.currentImageIndex()]);
            });
            return this;
        },

        /**
         * Init component handler
         * @return {Object}
         */
        initComponent: function (data) {
            if (!data.length) {
                return;
            }
            this.images(data);
            return this;
        },

        show: function(image){
            if(image) {
                this._goto(_.indexOf(this.images(), image));
            }
            this.visible(true);
        },
        hide: function(){
            this.visible(false);
        },

        _goto: function(index){
            this.currentImageIndex(index);
        },
        prev: function(){
            if(this.prevAvailable()) {
                this.currentImageIndex(this.currentImageIndex() - 1)
            }
        },
        next: function(){
            if(this.nextAvailable()) {
                this.currentImageIndex(this.currentImageIndex() + 1);
            }
        },
    });
});
