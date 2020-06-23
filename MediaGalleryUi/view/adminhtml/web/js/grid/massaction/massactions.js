/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_MediaGalleryUi/js/action/deleteImages',
    'uiLayout'
], function ($, Component, DeleteImages, Layout) {
    'use strict';

    return Component.extend({
        defaults: {
            modules: {
                massactionView: '${ $.name }_view',
                checkbox: '${ $.checkboxComponentName }',
                imageModel: '${ $.imageModelName }'
            },
            viewConfig: [
                {
                    component: 'Magento_MediaGalleryUi/js/grid/massaction/massactionView',
                    name: '${ $.name }_view',
                    provider: '${ $.provider }'
                }
            ],
            listens: {
                '${ $.checkboxComponentName }:selectedItems': 'setItems'
            },
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
                'selectedItems',
                'massActionMode'
            ]);
            this.selectedItems({});
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

                this.checkbox().selectedItems({});
                this.massActionMode(false);
                this.switchMode();
            }.bind(this));
        },

        /**
         * Set selected items. activete massaction if selected at least one item.
         */
        setItems: function () {
            this.selectedItems(this.checkbox().selectedItems());

            if (this.getSelectedCount() >= 1 && !this.massActionMode()) {
                $(window).trigger('massAction.MediaGallery');
            } else if (this.getSelectedCount() < 1 && this.massActionMode()) {
                this.massActionMode(false);
                this.switchMode();
            }
        },

        /**
         * Return total selected items.
         */
        getSelectedCount: function () {
            return Object.keys(this.selectedItems()).length;
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
                        this.selectedItems(),
                        this.imageModel().deleteImageUrl,
                        $.mage.__('Are you sure you want to delete "%2" images?').replace('%2', this.getSelectedCount())
                    ).then(function () {
                        this.checkbox().selectedItems({});
                        this.massActionMode(false);
                        this.switchMode();
                    }.bind(this));
                }.bind(this));
            }
        }
    });
});
