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
            jsTreeReloaded: null,
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
                    this.renderDirectoryTree().then(function () {
                        this.initEvents();
                    }.bind(this));
                }.bind(this)
            );

            return this;
        },

        /**
         * Render directory tree component.
         */
        renderDirectoryTree: function () {
            var deferred = $.Deferred();

            this.getJsonTree().then(function (data) {
                this.createFolderIfNotExists(data).then(function (isFolderCreated) {
                    if (isFolderCreated) {
                        this.getJsonTree().then(function (newData) {
                            this.createTree(newData);
                            deferred.resolve();
                        }.bind(this));
                    } else {
                        this.createTree(data);
                        deferred.resolve();
                    }
                }.bind(this));
            }.bind(this));

            return deferred.promise();
        },

        /**
         * Set jstree reloaded
         *
         * @param {Boolean} value
         */
        setJsTreeReloaded: function (value) {
            this.jsTreeReloaded = value;
        },

        /**
         * Create folder by provided current_tree_path param
         *
         * @param {Array} directories
         */
        createFolderIfNotExists: function (directories) {
            var isMediaBrowser = !_.isUndefined(window.MediabrowserUtility),
                currentTreePath = isMediaBrowser ? window.MediabrowserUtility.pathId : null,
                deferred = $.Deferred(),
                decodedPath,
                pathArray;

            if (currentTreePath) {
                decodedPath = Base64.idDecode(currentTreePath);

                if (!this.isDirectoryExist(directories[0], decodedPath)) {
                    pathArray = this.convertPathToPathsArray(decodedPath);

                    $.each(pathArray, function (i, val) {
                        if (this.isDirectoryExist(directories[0], val)) {
                            pathArray.splice(i, 1);
                        }
                    }.bind(this));

                    createDirectory(
                        this.createDirectoryUrl,
                        pathArray
                    ).then(function () {
                        deferred.resolve(true);
                    });
                } else {
                    deferred.resolve(false);
                }
            } else {
                deferred.resolve(false);
            }

            return deferred.promise();
        },

        /**
         * Verify if directory exists in array
         *
         * @param {Array} directories
         * @param {String} directoryId
         */
        isDirectoryExist: function (directories, directoryId) {
            var found = false;

            /**
             * Recursive search in array
             *
             * @param {Array} data
             * @param {String} id
             */
            function recurse(data, id) {
                var i;

                for (i = 0; i < data.length; i++) {
                    if (data[i].attr.id === id) {
                        found = data[i];
                        break;
                    } else if (data[i].children && data[i].children.length) {
                        recurse(data[i].children, id);
                    }
                }
            }

            recurse(directories, directoryId);

            return found;
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
            this.firejsTreeEvents();
            this.overrideMultiselectBehavior();

            $(window).on('reload.MediaGallery', function () {
                this.getJsonTree().then(function (data) {
                    this.createFolderIfNotExists(data).then(function (isCreated) {
                        if (isCreated) {
                            this.renderDirectoryTree().then(function () {
                                this.setJsTreeReloaded(true);
                                this.firejsTreeEvents();
                            }.bind(this));
                        } else {
                            this.checkChipFiltersState();
                        }
                    }.bind(this));
                }.bind(this));
            }.bind(this));
        },

        /**
         * Fire event for jstree component
         */
        firejsTreeEvents: function () {
            $(this.directoryTreeSelector).on('select_node.jstree', function (element, data) {
                var path = $(data.rslt.obj).data('path');

                this.setActiveNodeFilter(path);
                this.setJsTreeReloaded(false);
            }.bind(this));

            $(this.directoryTreeSelector).on('loaded.jstree', function () {
                this.checkChipFiltersState();
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

            if (this.activeNode() === nodePath && !this.jsTreeReloaded) {
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
            var deferred = $.Deferred();

            this.getJsonTree().then(function (data) {
                this.createTree(data);
                this.setJsTreeReloaded(true);
                this.initEvents();
                deferred.resolve();
            }.bind(this));

            return deferred.promise();
        },

        /**
         * Get json data for jstree
         */
        getJsonTree: function () {
            var deferred = $.Deferred();

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
                    deferred.resolve(data);
                },

                /**
                 * Error handler for request
                 *
                 * @param {Object} jqXHR
                 * @param {String} textStatus
                 */
                error: function (jqXHR, textStatus) {
                    deferred.reject();
                    throw textStatus;
                }
            });

            return deferred.promise();
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
