/**
 * Copyright © Magento, Inc. All rights reserved.g
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'uiLayout',
    'underscore',
    'jquery/jstree/jquery.jstree'
], function ($, Component, layout, _) {
    'use strict';

    return Component.extend({
        defaults: {
            filterChipsProvider: 'componentType = filters, ns = ${ $.ns }',
            directoryTreeSelector: '#media-gallery-directory-tree',
            getDirectoryTreeUrl: 'media_gallery/directories/gettree',
            modules: {
                directories: '${ $.name }_directories',
                filterChips: '${ $.filterChipsProvider }'
            },
            listens: {
                '${ $.provider }:params.filters.path': 'clearFiltersHandle'
            },
            viewConfig: [{
                component: 'Magento_MediaGalleryUi/js/directory/directories',
                name: '${ $.name }_directories'
            }]
        },

        /**
         * Initializes media gallery directories component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super().observe(['activeNode']).initView();

            this.waitForContainer(function () {
                this.getJsonTree();
                this.initEvents();
                this.checkChipFiltersState();
            }.bind(this));

            return this;
        },

        /**
         * Initialize child components
         *
         * @returns {Object}
         */
        initView: function () {
            layout(this.viewConfig);

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
         * Remove ability to multiple select on nodes
         */
        overrideMultiselectBehavior: function () {
            $.jstree.defaults.ui['select_range_modifier'] = false;
            $.jstree.defaults.ui['select_multiple_modifier'] = false;
        },

        /**
         *  Handle jstree events
         */
        initEvents: function () {
            this.overrideMultiselectBehavior();

            $(this.directoryTreeSelector).on('select_node.jstree', function (element, data) {
                var path = $(data.rslt.obj).data('path');

                this.setActiveNodeFilter(path);
            }.bind(this));
        },

        /**
         * Verify directory filter on init event, select folder per directory filter state
         */
        checkChipFiltersState: function () {
            if (!_.isUndefined(this.filterChips().filters.path) && this.filterChips().filters.path !== '') {
                $(this.directoryTreeSelector).on('loaded.jstree', function () {
                    this.locateNode(this.filterChips().filters.path);
                }.bind(this));
            }
        },

        /**
         * Locate and higlight node in jstree by path id.
         *
         * @param {String} path
         */
        locateNode: function (path) {
            path = path.replace(/\//g, '\\/');
            $(this.directoryTreeSelector).jstree('open_node', '#' + path);
            $(this.directoryTreeSelector).jstree('select_node', '#' + path, true);

        },

        /**
         * Listener to clear filters event
         */
        clearFiltersHandle: function () {
            if (_.isUndefined(this.filterChips().filters.path)) {
                $(this.directoryTreeSelector).jstree('deselect_all');
                this.activeNode(null);
                this.directories().setInActive();
            }
        },

        /**
         * Set active node filter, or deselect if the same node clicked
         *
         * @param {String} nodePath
         */
        setActiveNodeFilter: function (nodePath) {
            if (this.activeNode() === nodePath) {
                this.selectStorageRoot();
            } else {
                this.selectFolder(nodePath);
            }
        },

        /**
         * Remove folders selection -> select storage root
         */
        selectStorageRoot: function () {
            var filters = {},
                applied = this.filterChips().get('applied');

            $(this.directoryTreeSelector).jstree('deselect_all');

            filters = $.extend(true, filters, applied);
            delete filters.path;
            this.filterChips().set('applied', filters);
            this.activeNode(null);
            this.directories().setInActive();
        },

        /**
         * Set selected folder
         *
         * @param {String} path
         */
        selectFolder: function (path) {
            this.activeNode(path);
            this.directories().setActive(path);
            this.applyFilter(path);
        },

        /**
          * Remove active node from directory tree, and select next
          */
        removeNode: function () {
            $(this.directoryTreeSelector).jstree('remove');
        },

        /**
         * Apply folder filter by path
         *
         * @param {String} path
         */
        applyFilter: function (path) {
            var filters = {},
                applied = this.filterChips().get('applied');

            filters = $.extend(true, filters, applied);
            filters.path = path;
            this.filterChips().set('applied', filters);

        },

        /**
         * Reload jstree and update jstree events
         */
        reloadJsTree: function () {
            $.ajaxSetup({
                async: false
            });

            this.getJsonTree();
            this.initEvents();

            $.ajaxSetup({
                async: true
            });
        },

        /**
         * Get json data for jstree
         */
        getJsonTree: function () {
            $.ajax({
                url: this.getDirectoryTreeUrl,
                type: 'GET',
                dataType: 'json',

                /**
                 * Success handler for request
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
                            'uncheck_node': true
                        }
                    }
                }
            });
        }
    });
});
