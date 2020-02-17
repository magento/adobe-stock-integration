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
            getDirectoryTreeUrl: 'media_gallery/directories/gettree',
            modules: {
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

            this.waitForContainer(function () {
                this.getJsonTree();
                this.initEvents();
            }.bind(this));

            return this;
        },

        /**
         * Wait for container to initialize
         */
        waitForContainer: function (callback) {
            if ($(this.directoryTreeSelector).length === 0) {
                setTimeout(function () {
                    this.waitForContainer(callback);
                }.bind(this), 100);
            } else {
                callback();
            }
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
                url: this.getDirectoryTreeUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    'form_key': FORM_KEY
                },

                /**
                 * Succes handler for request
                 *
                 * @param {Object} data
                 */
                success: function (data) {
                    this.createTree(data);
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
         * Initialize directory tree
         *
         * @param {Array} data
         */
        createTree: function (data) {
            $(this.directoryTreeSelector).jstree({
                plugins: ['json_data', 'themes',  'ui', 'crrm', 'types', 'hotkeys'],
                vcheckbox: {
                    'two_state': true,
                    'real_checkboxes': true
                },
                'json_data': {
                    data: data
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
