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

        /**
         * @inheritdoc
         */
        success: function (response) {
            var options = [];

            _.each(response.options, function (opt) {
                options.push(opt);
            });

            this.total = response.total;
            this.cacheOptions.plain = options;
            this.options(options);
        }
    });
});
