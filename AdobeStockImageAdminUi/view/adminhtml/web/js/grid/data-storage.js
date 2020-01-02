/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/data-storage'
], function ($, _, dataStorage) {
    'use strict';

    return dataStorage.extend({

        /**
         * Forms data object associated with provided request.
         *
         * @param {Object} request - Request object.
         * @returns {jQueryPromise}
         */
        getRequestData: function (request) {
            var defer = $.Deferred(),
                resolve = defer.resolve.bind(defer),
                delay = this.cachedRequestDelay,
                result;

            result = {
                items: this.getByIds(request.ids),
                totalRecords: request.totalRecords,
                errorMessage: request.errorMessage
            };

            delay ?
                _.delay(resolve, delay, result) :
                resolve(result);

            return defer.promise();
        },

        /**
         * Caches requests object with provided parameters
         * and data object associated with it.
         *
         * @param {Object} data - Data associated with request.
         * @param {Object} params - Request parameters.
         * @returns {DataStorage} Chainable.
         */
        cacheRequest: function (data, params) {
            var cached = this.getRequest(params);

            if (cached) {
                this.removeRequest(cached);
            }

            this._requests.push({
                ids: this.getIds(data.items),
                params: params,
                totalRecords: data.totalRecords,
                errorMessage: data.errorMessage
            });

            return this;
        }
    });
});
