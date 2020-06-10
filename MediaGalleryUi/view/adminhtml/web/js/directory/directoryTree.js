/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global Base64 */
define([
    'jquery',
    'uiComponent',
    'uiLayout',
    'underscore',
    'Magento_MediaGalleryUi/js/directory/actions/createDirectory',
    'jquery/jstree/jquery.jstree'
], function ($, Component, layout, _, createDirectory) {
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

            this.waitForCondition(
                function () {
                    return $(this.directoryTreeSelector).length === 0;
                }.bind(this),
                function () {
                    this.createFolderIfNotExists().then(function () {
                        this.getJsonTree();
                        this.initEvents();
                    }.bind(this));
                }.bind(this)
            );

            return this;
        },

        /**
         * Create folder by provided current_tree_path param
         */
        createFolderIfNotExists: function () {
            var isMediaBrowser = !_.isUndefined(window.MediabrowserUtility),
                currentTreePath = isMediaBrowser ? window.MediabrowserUtility.pathId : null,
                deferred = $.Deferred();

            if (currentTreePath) {
                createDirectory(
                    this.createDirectoryUrl,
                    this.convertPathToPathsArray(Base64.idDecode(currentTreePath))
                ).always(function () {
                    deferred.resolve();
                });
            } else {
                deferred.resolve();
            }

            return deferred.promise();
        },

        /**
         * Convert path string to path array e.g 'path1/path2' -> ['path1', 'path1/path2']
         *
         * @param {String} path
         */
        convertPathToPathsArray: function (path) {
            var pathsArray = [],
                pathString = '',
                paths = path.split('/');

            $.each(paths, function (i, val) {
                pathString += i >= 1 ? val : val + '/';
                pathsArray.push(i >= 1 ? pathString : val);
            });

            return pathsArray;
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
         * Wait for condition then call provided callback
         */
        waitForCondition: function (condition, callback) {
            if (condition()) {
                setTimeout(function () {
                    this.waitForCondition(condition, callback);
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

            $(this.directoryTreeSelector).on('loaded.jstree', function () {
                this.checkChipFiltersState();
            }.bind(this));

            $(window).on('reload.MediaGallery', function () {
                this.checkChipFiltersState();
            }.bind(this));

            $(this.directoryTreeSelector).on('select_node.jstree', function (element, data) {
                var path = $(data.rslt.obj).data('path');

                this.setActiveNodeFilter(path);
            }.bind(this));
        },

        /**
         * Verify directory filter on init event, select folder per directory filter state
         */
        checkChipFiltersState: function () {
            var currentFilterPath = this.filterChips().filters.path,
                isMediaBrowser = !_.isUndefined(window.MediabrowserUtility),
                currentTreePath;

            currentTreePath = this.isFiltersApplied(currentFilterPath) || !isMediaBrowser ? currentFilterPath :
                Base64.idDecode(window.MediabrowserUtility.pathId);

            if (this.folderExistsInTree(currentTreePath)) {
                this.locateNode(currentTreePath);
            } else {
                this.selectStorageRoot();
            }
        },

        /**
         * Verify if directory exists in folder tree
         *
         * @param {String} path
         */
        folderExistsInTree: function (path) {
            if (!_.isUndefined(path)) {
                return $('#' + path.replace(/\//g, '\\/')).length === 1;
            }

            return false;
        },

        /**
         * Check if need to select directory by filters state
         *
         * @param {String} currentFilterPath
         */
        isFiltersApplied: function (currentFilterPath) {
            return !_.isUndefined(currentFilterPath) && currentFilterPath !== '' &&
                currentFilterPath !== 'wysiwyg' && currentFilterPath !== 'catalog/category';
        },

        /**
         * Locate and higlight node in jstree by path id.
         *
         * @param {String} path
         */
        locateNode: function (path) {
            var selectedId =  $(this.directoryTreeSelector).jstree('get_selected').attr('id');

            if (path === selectedId) {
                return;
            }
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
            this.waitForCondition(
              function () {
                return _.isUndefined(this.directories());
            }.bind(this),
                function () {
                this.directories().setInActive();
            }.bind(this)
          );

        },

        /**
         * Set selected folder
         *
         * @param {String} path
         */
        selectFolder: function (path) {
            this.activeNode(path);

            this.waitForCondition(
                function () {
                    return _.isUndefined(this.directories());
                }.bind(this),
                function () {
                    this.directories().setActive(path);
                }.bind(this)
            );

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
