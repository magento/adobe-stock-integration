/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiElement',
    'jquery/jstree/jquery.jstree'
], function ($, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            directoryListUrl: 'media_gallery/directories/getdirectorytreedata',
            directoryTreeSelector: '#media-gallery-directory-tree',
            options: {
                treeInitData: []
            }
        },

        /**
         * Initializes Media Gallery Directories Tree component
         */
        initialize: function () {
            this._super()

            this.createTree();

            return this;
        },

        /**
         * Get json data for jstree
         */
        getJsonTree: function () {
            $.ajax({
                url: this.directoryListUrl,
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    'form_key': FORM_KEY
                },
                success: function (data) {
                    this.options.treeInitData = data;
                }.bind(this),
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                }.bind(this)
            });
        },

        /**
         * Initialize directory tree.
         */
        createTree: function () {
            this.getJsonTree();
            $(this.directoryTreeSelector).jstree({
                plugins: ['themes', 'json_data', 'ui', 'crrm', 'types', 'hotkeys'],
                vcheckbox: {
                    'two_state': true,
                    'real_checkboxes': true
                },
                'json_data': {
                    data: this.options.treeInitData
                },
                ui: {
                    'select_limit': 0
                },
                hotkeys: {
                    space: this._changeState,
                    'return': this._changeState
                },
                types: {
                    'types': {
                        'disabled': {
                            'check_node': false,
                            'uncheck_node': false
                        }
                    }
                }
            });
        }
    });

});
