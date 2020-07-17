/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/ui-select',
    'jquery',
    'underscore'
], function (Select, $, _) {
    'use strict';

    return Select.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/image/edit/keyword-ui-select',
            label: 'Keywords',
            image: {}
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            return this;
        },

        getKeywordsOp: function () {
            var imageTags = this.image.tags,
                option = [];
            imageTags.forEach(function (tag) {
                option['label'] = tag.value;
                option['value'] = tag.value;
                option.push(option);
            });

            this.options(option);
        },

        addLastElement: function (data) {

        },
    });
});
