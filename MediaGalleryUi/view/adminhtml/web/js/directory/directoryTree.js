/**
 * Copyright © Magento, Inc. All rights reserved.g
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'jquery/jstree/jquery.jstree'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: "Magento_MediaGalleryUi/grid/directories/directoryTree",
            directoryListUrl: 'media_gallery/directories/getlist',
            filterChipsProvider: 'componentType = filters, ns = ${ $.ns }',
            directoryTreeSelector: '#media-gallery-directory-tree',
            urlProvider: 'name = media_gallery_listing.media_gallery_listing.media_gallery_columns.thumbnail_url, ns = media_gallery_listing',
            options: {
                treeInitData: [],
                selectedIds: [],
            },
            modules: {
                image: '${ $.urlProvider }',
                filterChips: '${ $.filterChipsProvider }'

            },
            listens: {
                '${ $.provider }:data.items': 'getJsonTree createTree initEvents',
            }
        },

        /**
         * Initializes Media Gallery Directories Tree component
         */
        initialize: function () {
            this._super()

            this.directoryListUrl = this.image().directoryListUrl;
            return this;
        },

        initEvents: function () {
            $(this.directoryTreeSelector).on('select_node.jstree', function(element, data) {
        
             this.applyFilter($(data.rslt.obj).data('path'));

            }.bind(this));
        },


        /**
         * Apply folder filter by path
         *
         * @param {string} path
         */
        applyFilter: function (path) {

            this.filterChips().set(
                'applied',
                {
                    'directory': path
                }
            );
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
                    console.log(errorThrown);
                }.bind(this)
            });
        },

        /**
         * Initialize directory tree.
         */
        createTree: function () {
            $(this.directoryTreeSelector).jstree({
                plugins: ['themes', 'json_data', 'ui', 'crrm', 'types', 'hotkeys'],
                vcheckbox: {
                    'two_state': true,
                    'real_checkboxes': true
                },
                'json_data': {
                    data: this.options.treeInitData
                },
                hotkeys: {
                    space: this._changeState,
                    'return': this._changeState
                },
                types: {
                    'types': {
                        'disabled': {
                            'check_node': true,
                            'uncheck_node': false
                        }
                    }
                }
            });
        }
    });

});
