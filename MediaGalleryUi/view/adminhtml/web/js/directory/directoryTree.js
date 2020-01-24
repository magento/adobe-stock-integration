/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
	
define([
    'jquery',
    'uiElement',
    'Magento_Cms/js/folder-tree'
], function ($, Element) {
    'use strict';

    return Element.extend({ 
        defaults: {
            directoryListUrl: 'media_gallery/directories/getdirectorytreedata',
            directoryTreeSelector: '#media-gallery-directory-tree'
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
         * Initialize directory tree. 
         */
        createTree: function () {
            $(this.directoryTreeSelector).folderTree({
                rootName: 'Root',
                url: this.directoryListUrl,
                currentPath: ['root']
            });
            $(this.directoryTreeSelector).bind("loaded.jstree",
                function (event, data) {
                    $("a:contains('Root')").css("visibility","hidden")
                    $(".jstree-last .jstree-icon").first().hide()
                });
  
        },
    });


});
