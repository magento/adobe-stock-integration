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

            /**
             * @return {Object}
             */
            function getMasonary()  {
                return {
                    rows: function () {
                        return [{
                            id: 1
                        }]
                    }
                };
            }

            overlayObj.masonry = getMasonary
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
                spyOn(overlayObj, 'licensed').and.callFake(function() {
                    return [];
                });
                spyOn(overlayObj, 'login').and.callFake(function() {
                    return login;
                });
                spyOn(overlayObj, 'getIds').and.callFake(function() {
                    return ids;
                });
                spyOn($, 'ajax').and.callFake(function() {
                    return overlayObj.licensed([1,2]);
                });
                overlayObj.updateLicensed();
                expect(overlayObj.updateLicensed()).toBeUndefined();
                expect($.ajax).toHaveBeenCalled();
                expect(overlayObj.getIds).toHaveBeenCalled();
            });

            it("Check Ajax error request", function() {
                var handler = {
                        error: function() {}
                    },
                    login = {
                        user: function () {
                            return {
                                isAuthorized: true
                            };
                        }
                    }, ids = [];
                spyOn(overlayObj, 'login').and.callFake(function() {
                    return login;
                });
                spyOn(overlayObj, 'getIds').and.callFake(function() {
                    return ids;
                });
                spyOn($, 'ajax').and.callFake(function() {
                    return 'error';
                });
                overlayObj.updateLicensed();
                expect(overlayObj.updateLicensed()).toBeUndefined();
                expect($.ajax).toHaveBeenCalled();
                expect(overlayObj.getIds).toHaveBeenCalled();
            });

            it('If user is not authorized', function() {
                var login = {
                    user: function () {
                        return {
                            isAuthorized: false
                        };
                    }
                }, ids = [1,2,3];
                spyOn(overlayObj, 'login').and.callFake(function() {
                    return login;
                });
                spyOn(overlayObj, 'getIds').and.callFake(function() {
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

            it('Return array if getIds is called', function() {
                var returnValue = [1];
                expect(overlayObj.getIds()).toEqual(returnValue);
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

            it('Return object if getStyles is called', function() {
                var record = {
                        styles: function () {}
                    },
                    returnValue = {
                        top: '150px'
                    };
                spyOn(record, 'styles').and.callFake(function(e) {
                    return {
                        height: '200px'
                    };
                });
                overlayObj.getStyles(record);
                expect(overlayObj.getStyles(record)).toEqual(returnValue);
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

            it('Return true if licensed property is not empty', function () {
                var row = {
                    id: 1
                };
                spyOn(overlayObj, 'licensed').and.callFake(function(e) {
                    return [1,2,3];
                });
                overlayObj.licensed();
                expect(overlayObj.isVisible(row)).toBeTruthy();
            });

            it('Return false if licensed property is empty', function () {
                var row = {
                    id: ''
                };
                spyOn(overlayObj, 'licensed').and.callFake(function(e) {
                    return [];
                });
                overlayObj.licensed();
                expect(overlayObj.isVisible(row)).toBeFalsy();
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

            it('Return "Licensed" if licensed property is not empty', function () {
                var row = {
                    id: 1
                };
                spyOn(overlayObj, 'licensed').and.callFake(function(e) {
                    return [1,2,3];
                });
                overlayObj.licensed();
                expect(overlayObj.getLabel(row)).toEqual('Licensed');
            });

            it('Return "empty" string if licensed property is empty', function () {
                var row = {
                    id: 1
                };
                spyOn(overlayObj, 'licensed').and.callFake(function(e) {
                    return [];
                });
                overlayObj.licensed();
                expect(overlayObj.getLabel(row)).toEqual('');
            });
        });
    });
});
