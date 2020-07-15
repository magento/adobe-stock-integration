/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* eslint-disable strict */
define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/editableMultiselect/js/jquery.editable',
    'jquery/editableMultiselect/js/jquery.multiselect'
], function ($, alert) {
    /**
     * Editable multiselect wrapper for multiselects
     * This class is defined in global scope ('var' is not needed).
     *
     *  @param {Object} settings - settings object.
     *  @param {String} settings.add_button_caption - caption of the 'Add New Value' button
     *  @param {String} settings.new_url - URL to which new request has to be submitted
     *  @param {String} settings.save_url - URL to which save request has to be submitted
     *  @param {String} settings.delete_url - URL to which delete request has to be submitted
     *  @param {String} settings.target_select_id - HTML ID of target select element
     *  @param {Hash} settings.submit_data - extra parameters to send with new/edit/delete requests
     *  @param {String} settings.entity_value_name - name of the request parameter that represents select option text
     *  @param {String} settings.entity_id_name - name of the request parameter that represents select option value
     *  @param {Boolean} settings.is_entry_editable - flag that shows if user can add/edit/remove data
     *
     * @constructor
     */
    window.EditableMultiselect = function (settings) {
        this.settings = settings || {};
        this.addButtonCaption = this.settings['add_button_caption'] || 'Add new value';
        this.newUrl = this.settings['new_url'];
        this.saveUrl = this.settings['save_url'];
        this.deleteUrl = this.settings['delete_url'];
        this.targetSelectId = this.settings['target_select_id'];
        this.submitData = this.settings['submit_data'] || {};
        this.entityIdName = this.settings['entity_id_name'] || 'entity_id';
        this.entityValueName = this.settings['entity_value_name'] || 'entity_value';
        this.isEntityEditable = this.settings['is_entity_editable'] || false;

        /**
         * Initialize editable multiselect (make it visible in UI)
         */
        EditableMultiselect.prototype.init = function () {
            var self = this,
                mselectOptions = {
                    addText: this.addButtonCaption,
                    item : '<div  class="%mselectListItemClass% %mselectDisabledClass% %iseditable% %isremovable%"><label><input type="checkbox" class="%mselectCheckedClass%" value="%value%" %checked% %disabled% /><span>%label%</span></label>' +
                        '<span class="mselect-delete" title="Delete">Delete</span> ' +
                        '</div>',

                    /**
                     * @param {*} value
                     * @param {*} options
                     */
                    mselectInputSubmitCallback: function (value, options) {
                        self.createEntity(value, options);
                    }
                },
                mselectList;

            if (!this.isEntityEditable) {
                // Override default layout of editable multiselect
                mselectOptions.layout = '<section class="block %mselectListClass%">' +
                    '<div class="block-content"><div class="%mselectItemsWrapperClass%">' +
                    '%items%' +
                    '</div></div>' +
                    '<div class="%mselectInputContainerClass%">' +
                    '<input type="text" class="%mselectInputClass%" title="%inputTitle%"/>' +
                    '<span class="%mselectButtonCancelClass%" title="%cancelText%"></span>' +
                    '<span class="%mselectButtonSaveClass%" title="Add"></span>' +
                    '</div>' +
                    '</section>';
            }

            $('#' + this.targetSelectId).multiselect(mselectOptions);

            // Make multiselect editable if needed
            if (this.isEntityEditable) {

                // Root element of HTML markup that represents select element in UI
                mselectList = $('#' + this.targetSelectId).next();
                this.attachEventsToControls(mselectList);
            }
        };

        /**
         * Attach required event handlers to control elements of editable multiselect
         *
         * @param {Object} mselectList
         */
        EditableMultiselect.prototype.attachEventsToControls = function (mselectList) {
            mselectList.on('click.mselect-delete', '.mselect-delete', {
                container: this
            }, function (event) {
                // Pass the clicked button to container
                event.data.container.deleteEntity({
                    'delete_button': this
                });
            });

            mselectList.on('click.mselect-checked', '.mselect-list-item input', {
                container: this
            }, function (event) {
                var el = $(this),
                    checkedClassName = 'mselect-checked';

                el[el.is(':checked') ? 'addClass' : 'removeClass'](checkedClassName);
            });
        };

        /**
         * Callback function that is called when admin adds new value to select
         *
         * @param {*} value
         * @param {Object} options - list of settings of multiselect
         */
        EditableMultiselect.prototype.createEntity = function (value, options) {
            var select, entityIdName, entityValueName, entityInfo;

            if (!value) {
                return;
            }

            select = $('#' + this.targetSelectId),
            entityIdName = this.entityIdName,
            entityValueName = this.entityValueName,
            entityInfo = {};
            entityInfo[entityIdName] = null;
            entityInfo[entityValueName] = value;

                var resultEntityValueName, mselectItemHtml, sectionBlock, itemsWrapper, inputSelector;

                    resultEntityValueName = '';

                    if (typeof entityInfo[entityValueName] === 'string') {
                        resultEntityValueName = entityInfo[entityValueName].escapeHTML();
                    } else {
                        resultEntityValueName = entityInfo[entityValueName];
                    }
                    // Add item to initial select element
                    select.append('<option value="' + resultEntityValueName + '" selected="selected">' +
                        resultEntityValueName + '</option>');
                    // Add editable multiselect item
                    mselectItemHtml = $(options.item.replace(/%value%|%label%/gi, resultEntityValueName)
                        .replace(/%mselectDisabledClass%|%iseditable%|%isremovable%/gi, '')
                        .replace(/%mselectListItemClass%/gi, options.mselectListItemClass))
                        .find('[type=checkbox]')
                        .attr('checked', true)
                        .addClass(options.mselectCheckedClass)
                        .end();
                    sectionBlock = select.nextAll('section.block:first');
                    itemsWrapper = sectionBlock.find('.' + options.mselectItemsWrapperClass + '');

                    if (itemsWrapper.children('.' + options.mselectListItemClass + '').length) {
                        itemsWrapper.children('.' + options.mselectListItemClass + ':last').after(mselectItemHtml);
                    } else {
                        itemsWrapper.prepend(mselectItemHtml);
                    }
                    // Trigger blur event on input field, that is used to add new value, to hide it
                    inputSelector = '.' + options.mselectInputContainerClass + ' [type=text].' +
                        options.mselectInputClass + '';
                    sectionBlock.find(inputSelector).trigger('blur');
        };

        /**
         * Callback function that is called when user tries to delete value from select
         *
         * @param {Object} options
         */
        EditableMultiselect.prototype.deleteEntity = function (options) {
            if (options['delete_button']) {
                var deleteButton = $(options['delete_button']),
                    index = deleteButton.parent().index(),
                    select = deleteButton.closest('.mselect-list').prev();
                deleteButton.parent().remove();
                select.find('option').eq(index).remove();
            }
        };
    };
});
