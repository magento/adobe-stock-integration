/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        $('#' + config.testButtonId).bind('click', function() {
            var validationResultContainer = $('#' + config.validationResultContainerId),
                apiKey = document.getElementById(config.apiKeyInput).value,
                validationContainerClass;

            validationResultContainer.text('');
            validationResultContainer.removeAttr('class');
            $.ajax({
                       type: "POST",
                       url: config.requestValidationUrl,
                       dataType: 'json',
                       data: { 'api_key': apiKey },
                       success: function(response) {
                           validationContainerClass = (response.success === true) ?
                               'message-validation message message-success success'
                               : 'message-validation message message-error error';
                           validationResultContainer.addClass(validationContainerClass).text(response.message);
                       },
                       error: function(response) {
                           validationResultContainer.text(config.defaultMessage).
                           addClass('message message-error error');
                       }
            });
        });
    };
});
