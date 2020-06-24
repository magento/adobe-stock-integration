/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_MediaGalleryUi/js/action/deleteImages',
    'uiLayout',
    'underscore'
], function ($, Component, DeleteImages, Layout, _) {
    'use strict';

    return Component.extend({
        defaults: {
            modules: {
                massactionView: '${ $.name }_view',
                imageModel: '${ $.imageModelName }'
            },
            viewConfig: [
                {
                    component: 'Magento_MediaGalleryUi/js/grid/massaction/massactionView',
                    name: '${ $.name }_view'
                }
            ],
            exports: {
                massActionMode: '${ $.name }_view:massActionMode'
            }
        },

        /**
         * Initializes media gallery massaction component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super().observe([
                'massActionMode'
            ]);
            this.initView();
            this.initEvents();

            return this;
        },

        /**
         * Initialize child components
         *
         * @returns {Object}
         */
        initView: function () {
            Layout(this.viewConfig);

            return this;
        },

        /**
         * Initilize massactions events for media gallery grid.
         */
        initEvents: function () {
            $(window).on('massAction.MediaGallery', function () {
                if (this.massActionMode()) {
                    return;
                }
                this.imageModel().selected(null);
                this.massActionMode(true);
                this.switchMode();
            }.bind(this));

            $(window).on('terminateMassAction.MediaGallery', function () {
                if (!this.massActionMode()) {
                    return;
                }

                this.massActionMode(false);
                this.switchMode();
            }.bind(this));
        },

        /**
         * Return total selected items.
         */
        getSelectedCount: function () {
            if (this.massActionMode() && !_.isNull(this.imageModel().selected())) {
                return Object.keys(this.imageModel().selected()).length;
            }

            return 0;
        },

        /**
         * Switch massaction per current event.
         */
        switchMode: function () {
            this.massactionView().switchView();
            this.handleDeleteAction();
        },

        /**
         * Change Default  behavior of delete image to bulk deletion.
         */
        handleDeleteAction: function () {
            if (this.massActionMode()) {
                $(this.massactionView().deleteButtonSelector).on('massDelete', function () {
                    DeleteImages(
                        this.imageModel().selected(),
                        this.imageModel().deleteImageUrl,
                        $.mage.__('Are you sure you want to delete "%2" images?').replace('%2', this.getSelectedCount())
                    ).then(function () {
                        this.imageModel().selected({});
                        this.massActionMode(false);
                        this.switchMode();
                    }.bind(this));
                }.bind(this));
            }
        }
    });
});
