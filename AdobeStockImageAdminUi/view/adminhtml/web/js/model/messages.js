/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'knockout'
], function (ko) {
    'use strict';

    return {
        messages: ko.observableArray(),

        /**
         * Get messages
         *
         * @return {Array}
         */
        get: function () {
            return this.messages();
        },

        /**
         * Add message
         *
         * @param {String} type
         * @param {String} message
         */
        add: function (type, message) {
            this.messages.push({
                code: type,
                message: message
            });
        },

        /**
         * Clear messages
         */
        clear: function () {
            this.messages.removeAll();
        },

        /**
         * Schedule message cleanup
         *
         * @param {Number} delay
         */
        scheduleCleanup: function (delay) {
            // eslint-disable-next-line no-unused-vars
            var timerId;

            delay = delay || 3;

            timerId = setTimeout(function () {
                clearTimeout(timerId);
                this.clear();
            }.bind(this), Number(delay) * 1000);
        }
    };
});
