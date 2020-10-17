/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Instagram
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2020 SapientPro (https://sapient.pro)
 */
 
define(['jquery', 'ko', 'uiComponent', 'Magento_Customer/js/model/customer', 'underscore', 'domReady!'],
    function($, ko, Component, customer, _) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'SapientPro_Core/checkout/step/shipping/step-config-hook',
            },

            /**
             * Is login form enabled for current customer.
             *
             * @return {Boolean}
             */
            isCustomerLoggedIn: function () {
                return customer.isLoggedIn();
            },

            /**
             *
             * @returns {any[]}
             */
            getHookElements: function() {
              return Object.values(this.hookElements);
            },

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                return this;
            },
        });
    }
);
