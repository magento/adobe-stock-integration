/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/paging/sizes'
], function (Sizes) {
    'use strict';

    return Sizes.extend({
        defaults: {
            value: 32,
            minSize: 1,
            maxSize: 64
        },

        sizes: {
            '32': {
                value: 32,
                label: 32
            },
            '64': {
                value: 64,
                label: 64
            }
        },

        /**
         * @override
         */
        initialize: function () {
            this._super();
            this.options = this.sizes;
            this.updateArray();

            return this;
        }
    });
});
