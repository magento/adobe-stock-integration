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
            options: []
        },

        /** @inheritdoc */
        initialize: function () {
            this._super()
                .observe([
                'options'
                ]);
            return this;
        },

        getKeywordsOp: function (keywords) {
            var i,
                keyword,
                option = [];

            for (i = 0; i < keywords.length; i++) {
                keyword = keywords[i];
                option['label'] = keyword;
                option['value'] = keyword;
                this.options.push(option);
            }
        },

        addLastElement: function (data) {

        },
    });
});
