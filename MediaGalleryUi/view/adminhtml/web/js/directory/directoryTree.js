/**
 * Copyright © Magento, Inc. All rights reserved.g
 * See COPYING.txt for license details.
 */

/* global FORM_KEY */
define([
    'jquery',
    'uiComponent',
    'jquery/jstree/jquery.jstree'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            filterChipsProvider: 'componentType = filters, ns = ${ $.ns }',
            directoryTreeSelector: '#media-gallery-directory-tree',
            /* eslint-disable max-len */
            urlProvider: 'name = media_gallery_listing.media_gallery_listing.media_gallery_columns.thumbnail_url, ns = media_gallery_listing',
            options: {
                treeInitData: []
            },
            modules: {
                image: '${ $.urlProvider }',
                filterChips: '${ $.filterChipsProvider }'
            }
        },

        /**
         * Initializes media gallery directories component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super();

            // wait one second for template render
            setTimeout(function () {
                this.getJsonTree();
                this.createTree();
                this.initEvents();
            }.bind(this), 100);

            return this;
        },

        /**
         *  Hendle jstree events
         */
        initEvents: function () {
            $(this.directoryTreeSelector).on('select_node.jstree', function (element, data) {

                this.applyFilter($(data.rslt.obj).data('path'));

            }.bind(this));
        },

        /**
         * Apply folder filter by path
         *
         * @param {String} path
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
                url: this.image().getDirectoryTreeUrl,
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    'form_key': FORM_KEY
                },

                /**
                 * Succes handler for request
                 *
                 * @param {Object} data
                 */
                success: function (data) {
                    this.options.treeInitData = data;
                }.bind(this),

                /**
                 * Error handler for request
                 *
                 * @param {Object} jqXHR
                 * @param {String} textStatus
                 */
                error: function (jqXHR, textStatus) {
                    throw textStatus;
                }
            });
        },

        /**
         * Initialize directory tree.
         */
        createTree: function () {
            $(this.directoryTreeSelector).jstree({
                plugins: ['json_data', 'themes',  'ui', 'crrm', 'types', 'hotkeys'],
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
