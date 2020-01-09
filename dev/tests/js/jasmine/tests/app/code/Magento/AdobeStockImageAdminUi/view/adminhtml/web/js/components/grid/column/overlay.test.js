/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_AdobeStockImageAdminUi/js/components/grid/column/overlay'
], function ($, Overlay) {
    'use strict';

    describe('Magento_AdobeStockImageAdminUi/js/components/grid/column/overlay', function () {

        var overlayObj;

        beforeEach(function () {
            overlayObj = new Overlay({
                provider: 'providerName'
            });
        });

        describe('"initObservable" method', function () {

            it('Check for defined ', function () {
                expect(overlayObj.hasOwnProperty('initObservable')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.initObservable;

                expect(type).toEqual('function');
            });

            it('licensed will be defined', function () {
                expect(overlayObj.licensed).toBeDefined();
            });
        });

        describe('"updateLicensed" method', function () {
            it('Check for defined', function () {
                expect(overlayObj.hasOwnProperty('updateLicensed')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.updateLicensed;
                expect(type).toEqual('function');
            });

            it("Check Ajax Success request", function() {
                var login = {
                    user: function () {
                        return {
                            isAuthorized: true
                        };
                    }
                }, ids = [1,2,3];
                spyOn(overlayObj, 'login').and.callFake(function(e) {
                    return login;
                });
                spyOn(overlayObj, 'getIds').and.callFake(function(e) {
                    return ids;
                });
                spyOn($, 'ajax').and.callFake(function(e) {
                    return $.Deferred().resolve({
                        'response': 'success'
                    }).promise();
                });
                overlayObj.updateLicensed();
                expect(overlayObj.updateLicensed()).toBeUndefined();
                expect($.ajax).toHaveBeenCalled();
                expect(overlayObj.getIds).toHaveBeenCalled();
            });

            it('Check Ajax failure request', function() {
                var login = {
                    user: function () {
                        return {
                            isAuthorized: false
                        };
                    }
                }, ids = [1,2,3];
                spyOn(overlayObj, 'login').and.callFake(function(e) {
                    return login;
                });
                spyOn(overlayObj, 'getIds').and.callFake(function(e) {
                    return ids;
                });
                spyOn($, 'ajax');
                overlayObj.updateLicensed();
                expect(overlayObj.updateLicensed()).toBeUndefined();
                expect($.ajax).not.toHaveBeenCalled();
                expect(overlayObj.getIds).not.toHaveBeenCalled();
            });
        });

        describe('"getIds" method', function () {

            it('Check for defined ', function () {
                expect(overlayObj.hasOwnProperty('getIds')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.getIds;
                expect(type).toEqual('function');
            });

        });

        describe('"getStyles" method', function () {

            it('Check for defined ', function () {
                expect(overlayObj.hasOwnProperty('getStyles')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.getStyles;
                expect(type).toEqual('function');
            });

        });

        describe('"isVisible" method', function () {

            it('Check for defined', function () {
                expect(overlayObj.hasOwnProperty('isVisible')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.isVisible;
                expect(type).toEqual('function');
            });
        });

        describe('"getLabel" method', function () {

            it('Check for defined ', function () {
                expect(overlayObj.hasOwnProperty('getLabel')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.getLabel;
                expect(type).toEqual('function');
            });

        });
    });
});
