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
            pageActionsSelector: '.page-actions-buttons',
            gridSelector: '[data-id="media-gallery-masonry-grid"]',
            originDeleteSelector: null,
            originCancelEvent: null,
            cancelMassactionButton: '<button id="cancel_massaction" type="button" class="cancel">Cancel</button>',
            isCancelButtonInserted: false,
            deleteButtonSelector: '#delete_massaction',
            addSelectedButtonSelector: '#add_selected',
            cancelMassactionButtonSelector: '#cancel',
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
            massactionModeTitle: $.mage.__('Select Images to Delete')
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
         * Switch massaction view state per active mode.
         */
        switchView: function () {
            this.changePageTitle();
            this.switchButtons();
        },

        /**
         * Hide or show buttons per active mode.
         */
        switchButtons: function () {

            if (this.massActionMode()) {
                this.activateMassactionButtonView();
            } else {
                this.revertButtonsToDefaultView();
            }
        },

        /**
         * Sets buttons to default regular -mode view.
         */
        revertButtonsToDefaultView: function () {
            $(this.deleteButtonSelector).replaceWith(this.originDeleteSelector);

            if (!this.isCancelButtonInserted) {
                $('#cancel_massaction').replaceWith(this.originCancelEvent);
            } else {
                $(this.cancelMassactionButtonSelector).addClass('no-display');
                $('#cancel_massaction').remove();
            }

            $.each(this.buttonsIds, function (key, value) {
                $(value).removeClass('no-display');
            });

            $(this.addSelectedButtonSelector).addClass('no-display');
            $(this.deleteButtonSelector)
                .addClass('media-gallery-actions-buttons')
                .removeClass('primary');
        },

        /**
          * Activate mass action buttons view
          */
        activateMassactionButtonView: function () {
            this.originDeleteSelector = $(this.deleteButtonSelector).clone(true, true);
            this.originCancelEvent = $('#cancel').clone(true);

            $.each(this.buttonsIds, function (key, value) {
                $(value).addClass('no-display');
            });

            $(this.deleteButtonSelector)
                .removeClass('media-gallery-actions-buttons')
                .text($.mage.__('Delete Selected'))
                .addClass('primary');

            if (!$(this.cancelMassactionButtonSelector).length) {
                $(this.pageActionsSelector).append(this.cancelMassactionButton);
                this.isCancelButtonInserted = true;
            } else {
                $(this.cancelMassactionButtonSelector).replaceWith(this.cancelMassactionButton);
            }
            $('#cancel_massaction').on('click', function () {
                $(window).trigger('terminateMassAction.MediaGallery');
            });

            $(this.deleteButtonSelector).off('click').on('click', function () {
                $(this.deleteButtonSelector).trigger('massDelete');
            }.bind(this));

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
