/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            standAloneTitle: 'Manage Gallery',
            slidePanelTitle: 'Media Gallery',
            defaultTitle: null,
            massactionModeTitle: 'Select Images to Delete'
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
            this.initEvents();
            this.selectedItems({});

            return this;
        },

        /**
         * Is massaction mod active.
         */
        isMassAction: function () {
            return this.massActionMode();
        },

        /**
         * Initilize massactions events for media gallery grid.
         */
        initEvents: function () {
            $(window).on('massAction.MediaGallery', function () {
                if (this.massActionMode()) {
                    return;
                }
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
          * Retirn total selected items.
          */
        getSelectedCount: function () {
            return Object.keys(this.selectedItems()).length;
        },

        /**
         * Switch massaction per current event.
         */
        switchMode: function () {
            this.changePageTitle();
            this.switchButtons();
        },

        /**
         * Hide or show buttons per active mode.
         */
        switchButtons: function () {
            var buttonsIds = [
                '#delete_folder',
                '#create_folder',
                '#upload_image',
                '#search_adobe_stock',
                '.three-dots'
            ],
                deleteButtonSelector = '#delete_selected';

            if (this.massActionMode()) {
                $.each(buttonsIds, function (key, value) {
                    $(value).addClass('no-display');
                });

                $(deleteButtonSelector).removeClass('no-display media-gallery-actions-buttons');
                $(deleteButtonSelector).addClass('primary');
            } else {
                $.each(buttonsIds, function (key, value) {
                    $(value).removeClass('no-display');
                });
                $(deleteButtonSelector).addClass('no-display media-gallery-actions-buttons');
                $(deleteButtonSelector).removeClass('primary');
            }

        },

        /**
         * Change page title per active mode.
         */
        changePageTitle: function () {
            var title = $('h1:contains(' + this.standAloneTitle + ')'),
                  titleSelector = title.length === 1 ? title : $('h1:contains(' + this.slidePanelTitle + ')');

            if (this.massActionMode()) {
                this.defaultTitle = titleSelector.text();
                titleSelector.text(this.massactionModeTitle);
            } else {
                titleSelector = $('h1:contains(' + this.massactionModeTitle + ')');
                titleSelector.text(this.defaultTitle);
            }
        }
    });
});
