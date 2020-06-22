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
            imageItemSelector: '.media-gallery-image-block',
            gridSelector: '[data-id="media-gallery-masonry-grid"]',
            originDeleteSelector: null,
            deleteButtonSelector: '#delete_selected',
            standAloneTitle: 'Manage Gallery',
            slidePanelTitle: 'Media Gallery',
            defaultTitle: null,
            contextButtonSelector: '.three-dots',
            buttonsIds: [
                '#delete_folder',
                '#create_folder',
                '#upload_image',
                '#search_adobe_stock',
                '.three-dots',
                '#add_selected'
            ],
            massactionModeTitle: 'Select Images to Delete'
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

            return this;
        },

        /**
         * Initializes media gallery massaction per active view.
         */
        switchView: function () {
            var deferred = $.Deferred();

            this.changePageTitle().then(function () {
                this.switchButtons().then(function () {
                    this.handleItemsUpdates();
                    deferred.resolve();
                }.bind(this));
            }.bind(this));

            return deferred.promise();
        },

        /**
         * Hide or show buttons per active mode.
         */
        switchButtons: function () {
            var deferred = $.Deferred();

            if (this.massActionMode()) {
                this.originDeleteSelector = $(this.deleteButtonSelector).clone(true, true);

                $.each(this.buttonsIds, function (key, value) {
                    $(value).addClass('no-display');
                });

                $(this.imageItemSelector).css('pointer-events', 'none');
                $(this.deleteButtonSelector).removeClass('no-display media-gallery-actions-buttons');
                $(this.deleteButtonSelector).addClass('primary');

                $(this.deleteButtonSelector).off('click').on('click', function () {
                    $(this.deleteButtonSelector).trigger('massDelete');
                }.bind(this));
                deferred.resolve();
            } else {
                $(this.deleteButtonSelector).replaceWith(this.originDeleteSelector);
                $(this.imageItemSelector).css('pointer-events', '');

                $.each(this.buttonsIds, function (key, value) {
                    $(value).removeClass('no-display');
                });

                $(this.deleteButtonSelector).addClass('no-display media-gallery-actions-buttons');
                $(this.deleteButtonSelector).removeClass('primary');
                deferred.resolve();
            }

            return deferred.promise();
        },

        /**
         * Keep buttons hidden on massaction mod state when grid updated.
         */
        handleItemsUpdates: function () {
            document.querySelectorAll(this.gridSelector)[0].addEventListener('DOMSubtreeModified', function () {
                if (this.massActionMode()) {
                    $(this.imageItemSelector).css('pointer-events', 'none');
                    $(this.contextButtonSelector).addClass('no-display');
                }
            }.bind(this), false);

        },

        /**
         * Change page title per active mode.
         */
        changePageTitle: function () {
            var deferred = $.Deferred(),
                  title = $('h1:contains(' + this.standAloneTitle + ')'),
                  titleSelector = title.length === 1 ? title : $('h1:contains(' + this.slidePanelTitle + ')');

            if (this.massActionMode()) {
                this.defaultTitle = titleSelector.text();
                titleSelector.text(this.massactionModeTitle);
                deferred.resolve();
            } else {
                titleSelector = $('h1:contains(' + this.massactionModeTitle + ')');
                titleSelector.text(this.defaultTitle);
                deferred.resolve();
            }

            return deferred.promise();
        }
    });
});
