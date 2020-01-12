/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/grid/data-storage'
], function (dataStorage) {
    'use strict';

    describe('Magento_AdobeStockImageAdminUi/js/grid/data-storage', function () {
        var obj;

        beforeEach(function () {
            obj = new dataStorage({
                dataScope: ''
            });
        });

        describe('"getRequestData" method', function () {

            it('Check for defined ', function () {
                expect(obj.hasOwnProperty('getRequestData')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof obj.getRequestData;

                expect(type).toEqual('function');
            });

            it('check "getRequestData" has been executed', function () {
                var request = {
                    ids: [1,2,3]
                };
                expect(obj.getRequestData(request)).toBeTruthy();
            });
        });

        describe('"cacheRequest" method', function () {

            it('Check for defined ', function () {
                expect(obj.hasOwnProperty('cacheRequest')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof obj.cacheRequest;

                expect(type).toEqual('function');
            });

            it('check "cacheRequest" has been executed', function () {
                var data = {
                        items: [1,2,3],
                        totalRecords: 3,
                        errorMessage: ''
                    },
                    params = {
                        namespace: 'magento',
                        search: '',
                        sorting: {},
                        paging: {}
                    };
                expect(obj.cacheRequest(data, params)).toBeTruthy();
            });
        });

    });
});
