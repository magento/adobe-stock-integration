/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global FORM_KEY, tinyMceEditors */
define([
    'jquery',
    'wysiwygAdapter',
    'underscore'
], function ($, wysiwyg, _) {
    'use strict';

    return {

        /**
         * Insert provided image in wysiwyg if enabled, or widget
         *
         * @param {Object} record
         * @param {Object} config
         * @returns {Boolean}
         */
        insertImage: function (record, config) {
            var targetElement;

            if (record === null) {
                return false;
            }
            targetElement = this.getTargetElement(window.MediabrowserUtility.targetElementId);

            if (!targetElement.length) {
                window.MediabrowserUtility.closeDialog();
                throw 'Target element not found for content update';
            }

            if (targetElement.is('textarea')) {
                $.ajax({
                    url: config.onInsertUrl,
                    data: {
                        filename: record['encoded_id'],
                        'store_id': config.storeId,
                        'as_is': 1,
                        'force_static_path': targetElement.data('force_static_path') ? 1 : 0,
                        'form_key': FORM_KEY
                    },
                    context: this,
                    showLoader: true
                }).done($.proxy(function (data) {
                    $.mage.mediabrowser().insertAtCursor(targetElement.get(0), data);
                    targetElement.focus();
                    $(targetElement).change();
                }, this));
            } else {
                targetElement.val(record['thumbnail_url'])
                    .data('size', record.size)
                    .data('mime-type', record['content_type'])
                    .trigger('change');
            }
            window.MediabrowserUtility.closeDialog();
            targetElement.focus();
        },

        /**
         * Return opener Window object if it exists, not closed and editor is active
         *
         * @param {String} targetElementId
         * return {Object|null}
         */
        getMediaBrowserOpener: function (targetElementId) {
            if (!_.isUndefined(wysiwyg) && wysiwyg.get(targetElementId) && !_.isUndefined(tinyMceEditors) &&
                !tinyMceEditors.get(targetElementId).getMediaBrowserOpener().closed
            ) {
                return tinyMceEditors.get(targetElementId).getMediaBrowserOpener();
            }

            return null;
        },

        /**
         * Get target element
         *
         * @param {String} targetElementId
         * @returns {*|n.fn.init|jQuery|HTMLElement}
         */
        getTargetElement: function (targetElementId) {
            var opener;

            if (!_.isUndefined(wysiwyg) && wysiwyg.get(targetElementId)) {
                opener = this.getMediaBrowserOpener(targetElementId) || window;
                targetElementId = tinyMceEditors.get(targetElementId).getMediaBrowserTargetElementId();

                return $(opener.document.getElementById(targetElementId));
            }

            return $('#' + targetElementId);
        }
    };
});
