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
                provider: 'providerName',
                licensed: {}
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

            var handler = {
                success: function(data) {
                    return [1,2]
                },
                fail: function(data) {
                    return 'Error Message';
                }
            };
            var testObj = {
                ajaxFunction: function() {
                    var promise =
                        $.ajax({
                            url: 'http://example.com/admin/adobe_stock/license/getlist/?ids=1,2,3',
                            type: 'GET',
                            data: {
                                'form_key': 'MagentoFormkey'
                            },
                            dataType: 'json',
                            context: overlayObj
                        });
                    promise.done(function(data, status) {
                        handler.success(data);
                    });
                    promise.fail(function(status, error) {
                        handler.fail(error);
                    });
                }
            };

            it('Check for defined', function () {
                expect(overlayObj.hasOwnProperty('updateLicensed')).toBeDefined();
            });

            it('Check method type', function () {
                var type = typeof overlayObj.updateLicensed;
               expect(type).toEqual('function');
            });

            it("Check Ajax Success request", function() {

                spyOn(handler, 'success').and.callFake(function(e) {
                    return [1,2]
                });
                spyOn($, 'ajax').and.callFake(function(e) {
                    return $.Deferred().resolve({
                        'response': 'success'
                    }).promise();
                });
                testObj.ajaxFunction();
                expect(handler.success).toHaveBeenCalled();
                expect(typeof handler.success()).toBe('object');
                expect(handler.success().length).toBe(2);
            });

            it('Check Ajax failure request', function() {
                spyOn(handler, 'fail').and.callFake(function(e) {
                    return 'Error Message';
                });
                spyOn($, 'ajax').and.callFake(function(e) {
                    return $.Deferred().reject({
                        'response': 'failure'
                    }).promise();
                });
                testObj.ajaxFunction();
                expect(handler.fail).toHaveBeenCalled();
                expect(typeof handler.fail()).toBe('string');
                expect(handler.fail()).toBe('Error Message');
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

            it('Check returned value type if method called with object argument', function () {

                overlayObj.getIds = jasmine.createSpy().and.callFake(function () {
                    return [1,2,3];
                });

                expect(typeof overlayObj.getIds()).toEqual('object');
            });

            it('Check getIds and return array', function () {

                overlayObj.getIds = jasmine.createSpy().and.callFake(function () {
                    return [1,2,3];
                });

                expect(overlayObj.getIds().length).toBe(3);
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

            it('Check returned value type if method called with object argument', function () {

                overlayObj.getStyles = jasmine.createSpy().and.callFake(function () {
                    return {
                        top: '100px'
                    };
                });

                expect(typeof overlayObj.getStyles()).toEqual('object');
            });

            it('Check getStyles and return array', function () {

                overlayObj.getStyles = jasmine.createSpy().and.callFake(function () {
                    return {
                        top: '100px'
                    };
                });

                expect(overlayObj.getStyles().top).toBe('100px');
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

            it('Check returned value type if method called with object argument', function () {
                var row = {
                    id: 123213
                };

                overlayObj.isVisible = jasmine.createSpy().and.callFake(function () {
                    return true;
                });

                expect(typeof overlayObj.isVisible(row)).toEqual('boolean');
            });

            it('Check called "this.isVisible" method with object argument', function () {
                var row = {
                    id: 123213
                };
                overlayObj.isVisible = jasmine.createSpy().and.callFake(function () {
                    return true;
                });
                overlayObj.isVisible(row);
                expect(overlayObj.isVisible).toHaveBeenCalledWith(row);
            });

            it('Check isVisible and return false if id doesnt found', function () {
                var row = {
                    id: ''
                };
                overlayObj.isVisible = jasmine.createSpy().and.callFake(function () {
                    return false;
                });
                expect(overlayObj.isVisible(row)).toBe(false);
            });

            it('Check isVisible and return true if id found', function () {
                var row = {
                    id: 123213
                };
                overlayObj.isVisible = jasmine.createSpy().and.callFake(function () {
                    return true;
                });
                expect(overlayObj.isVisible(row)).toBe(true);
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

            it('Check returned value type if method called with object argument', function () {
                var row ={
                    id: 123213
                };
                expect(typeof overlayObj.getLabel(row)).toEqual('string');
            });

            it('Check called "this.getLabel" method with object argument', function () {
                var row ={
                    id: 123213
                };
                overlayObj.getLabel = jasmine.createSpy().and.callFake(function () {
                    return '';
                });
                overlayObj.getLabel(row);
                expect(overlayObj.getLabel).toHaveBeenCalledWith(row);
            });

            it('Check getLabel and return null if id doesnt found', function () {
                var row ={
                    id: ''
                };
                overlayObj.getLabel = jasmine.createSpy().and.callFake(function () {
                    return '';
                });
                expect(overlayObj.getLabel(row)).toBe('');
            });

            it('Check getLabel and return string if id found', function () {
                var row ={
                    id: 123213
                };
                overlayObj.getLabel = jasmine.createSpy().and.callFake(function () {
                    return 'Licensed';
                });
                expect(overlayObj.getLabel(row)).toBe('Licensed');
            });
        });
    });
});
