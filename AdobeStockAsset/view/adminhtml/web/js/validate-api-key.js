/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
      let elements = $.extend({
           validationContainer: document.getElementById(config.validationResultContainerId),
           successMessageClass: 'message-validation message message-success success',
           errorMessageClass: 'message-validation message message-error error',
      });

      /**
       * Show message
       * @param message
       * @param messageClass
       */
      function showMessage(message, messageClass)
      {
        initializeValidationContainer();
        $(elements.validationContainer).addClass(messageClass).text(message);
      }

      /**
       * Initialize validation container
       */
      function initializeValidationContainer()
      {
        $(elements.validationContainer).text('');
        $(elements.validationContainer).removeAttr('class');
      }

      /**
       * Connection is checked during the settings load. If it is invalid show error message.
       */
      function isCurrentConnectionValid()
      {
        if (!config.isConnectionValid) {
          showMessage(config.defaultMessage, elements.errorMessageClass);
        }
      }

      /**
       * Bind events to the test connection button.
       */
      function bindTestConnectionButton()
      {
        $('#' + config.testButtonId).bind('click', function() {
          initializeValidationContainer();
          let apiKey = document.getElementById(config.apiKeyInput).value;
          $.ajax({
                   type: "POST",
                   url: config.requestValidationUrl,
                   dataType: 'json',
                   data: { 'api_key': apiKey },
                   success: function(response) {
                     (response.success === true) ?
                       showMessage(response.message, elements.successMessageClass)
                       : showMessage(response.message, elements.errorMessageClass)
                   },
                   error: function(response) {
                     showMessage(config.defaultMessage, elements.errorMessageClass);
                   }
                 });
        });
      }

      isCurrentConnectionValid();
      bindTestConnectionButton();
    };
});
