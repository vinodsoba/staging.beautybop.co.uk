define([], function () {
    'use strict';

    return function (Shipping) {

        return Shipping.extend({

            initialize: function () {
                
                console.log('Shipping component', this);

                this._super();

                var self = this;

                 var interval = setInterval(function () {

                    var first = document.querySelector(
                        '[name="shippingAddress.firstname"]'
                    );

                    var last = document.querySelector(
                        '[name="shippingAddress.lastname"]'
                    );

                    var row = document.querySelector('.bb-form-row--name');

                    if (!first || !last || !row) {
                        return;
                    }

                    clearInterval(interval);

                    console.log('🚀 Moving Name Fields');

                    self.moveField('[name="shippingAddress.firstname"]', '.bb-form-row--name');
                    self.moveField('[name="shippingAddress.lastname"]', '.bb-form-row--name');

                    self.moveField('[name="shippingAddress.company"]', '.bb-company-row');

                    self.moveField('.field.street', '.bb-address-row');

                    self.moveField('[name="shippingAddress.country_id"]', '.bb-country-row');

                    self.moveField('[name="shippingAddress.city"]', '.bb-city-row');

                    self.moveField('[name="shippingAddress.postcode"]', '.bb-city-row');

                    self.moveField('[name="shippingAddress.telephone"]', '.bb-phone-row');


                }, 100);

                return this;
            },

             /**
             * Move a Magento field into a BB row
             */
            moveField: function (selector, rowSelector) {

                var field = document.querySelector(selector);
                var row = document.querySelector(rowSelector);

                if (field && row) {
                    row.appendChild(field);
                }

            },


            getCustomerEmailRegion: function () {
                return this.getRegion('customer-email');
            },

            getAddressListRegion: function () {
                return this.getRegion('address-list');
            },

            getBeforeFormRegion: function () {
                return this.getRegion('before-form');
            },

            getShippingAdditionalRegion: function () {
                return this.getRegion('shippingAdditional');
            },

            getShippingFormTemplate: function () {
                return this.shippingFormTemplate;
            },

        
        });

    };

});