/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'Magento_Ui/js/grid/provider'
], function (dataProvider) {
    'use strict';

    return dataProvider.extend({
        defaults: {
            storageConfig: {
                component: 'Magento_AdobeStockImageAdminUi/js/grid/data-storage'
            }
        },
    });
});
