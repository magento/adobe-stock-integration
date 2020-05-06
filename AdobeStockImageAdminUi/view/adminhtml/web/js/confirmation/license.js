/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/prompt',
    'text!Magento_AdobeStockImageAdminUi/template/modal/adobe-modal-prompt-content.html'
], function ($, prompt, adobePromptContentTmpl) {
    'use strict';

    return function (recordTitle, quotaMessage, isPreviewDownloaded, proposedfileName, fileExtension) {
        var confirmationContent = $.mage.__('License "' + recordTitle + '"'),
            deferred = $.Deferred(),
            displayFieldName = !isPreviewDownloaded ? '<b>' + $.mage.__('File Name') + '</b>' : '',
            content = '<p>' + confirmationContent + '</p><p><b>' + quotaMessage + '</b></p><br>' + displayFieldName,
            data = {
                'title': $.mage.__('License Adobe Stock Images?'),
                'content': content,
                'visible': !isPreviewDownloaded,
                'actions': {
                    /**
                     * Confirm action
                     *
                     * @param {String} fileName
                     */
                    confirm: function (fileName) {
                        deferred.resolve(fileName);
                    }
                },
                'buttons': [{
                    text: $.mage.__('Cancel'),
                    class: 'action-secondary action-dismiss',

                    /**
                     * Close modal
                     */
                    click: function () {
                        this.closeModal();
                        deferred.reject();
                    }
                }, {
                    text: $.mage.__('Confirm'),
                    class: 'action-primary action-accept'
                }]

            };

        prompt({
            title: data.title,
            content:  data.content,
            value: proposedfileName,
            imageExtension: fileExtension,
            visible: data.visible,
            promptContentTmpl: adobePromptContentTmpl,
            modalClass: 'adobe-stock-save-preview-prompt',
            validation: true,
            promptField: '[data-role="adobe-stock-image-name-field"]',
            validationRules: ['required-entry', 'validate-image-name'],
            attributesForm: {
                novalidate: 'novalidate',
                action: '',
                onkeydown: 'return event.key != \'Enter\';'
            },
            attributesField: {
                name: 'name',
                'data-validate': '{required:true}',
                maxlength: '128'
            },
            context: this,
            actions: data.actions,
            buttons: data.buttons
        });

        return deferred.promise();
    };
});
