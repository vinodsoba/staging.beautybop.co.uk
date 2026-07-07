define([], function () {
    'use strict';

    return function (Shipping) {

        return Shipping.extend({

            initialize: function () {
                
                console.log('Shipping component', this);

                this._super();

                var self = this;

                self.waitForElements([

                    'input[name="firstname"]',
                    'input[name="lastname"]',
                    'input[name="street[0]"]',
                    'input[name="street[1]"]',
                    'input[name="city"]',
                    'input[name="postcode"]',
                    'input[name="telephone"]'

                ], function () {

                    self.buildCheckout();

                });

                return this;
            },



            waitForElements: function (selectors, callback) {

                var interval = setInterval(function () {

                    var ready = selectors.every(function (selector) {
                        return document.querySelector(selector);
                    });

                    if (!ready) {
                        return;
                    }

                    clearInterval(interval);

                    callback();

                }, 50);

            },


            buildCheckout: function () {

                this.moveFields();
                this.renameLabels();
                this.setPlaceholders();
                this.removeFields();

            },


            moveFields: function () {

                this.moveField(
                    '[name="shippingAddress.firstname"]',
                    '.bb-form-row--name'
                );

                this.moveField(
                    '[name="shippingAddress.lastname"]',
                    '.bb-form-row--name'
                );

                this.moveField(
                    '[name="shippingAddress.street.0"]',
                    '.bb-form-row--address1'
                );

                this.moveField(
                    '[name="shippingAddress.street.1"]',
                    '.bb-form-row--address2'
                );

                this.moveField(
                    '[name="shippingAddress.country_id"]',
                    '.bb-form-row--country'
                );

                this.moveField(
                    '[name="shippingAddress.city"]',
                    '.bb-form-row--city'
                );

                this.moveField(
                    '[name="shippingAddress.postcode"]',
                    '.bb-form-row--city'
                );

                this.moveField(
                    '[name="shippingAddress.telephone"]',
                    '.bb-form-row--phone'
                );

            },

            renameLabels: function () {

                var firstname = document.querySelector(
                    '[name="shippingAddress.firstname"] label span'
                );

                if(firstname) {
                    firstname.textContent = 'First Name *';
                }

                var lastname = document.querySelector(
                    '[name="shippingAddress.lastname"] label span'
                );

                if(lastname) {
                    lastname.textContent = 'Last Name *';
                }

                var address1 = document.querySelector(
                    '[name="shippingAddress.street.0"] label span'
                );

                if (address1) {
                    address1.textContent = 'Address Line 1 *';
                }

                var address2 = document.querySelector(
                    '[name="shippingAddress.street.1"] label span'
                );

                if (address2) {
                    address2.textContent = 'Address Line 2 ( Optional )';
                }

                var city = document.querySelector(
                    '[name="shippingAddress.city"] label span'
                );

                if (city) {
                    city.textContent = 'Town / City *';
                }

                var postcode = document.querySelector(
                    '[name="shippingAddress.postcode"] label span'
                );

                if (postcode) {
                    postcode.textContent = 'Postcode *';
                }

                 var telephone = document.querySelector(
                    '[name="shippingAddress.telephone"] label span'
                );

                if(telephone) {
                    telephone.textContent = 'Phone Number *';
                }



            },

            setPlaceholders: function () {

                // First Name
                document.querySelector('[name="shippingAddress.firstname"] input')
                    ?.setAttribute('placeholder', 'Enter your first name');

                // Last Name
                document.querySelector('[name="shippingAddress.lastname"] input')
                    ?.setAttribute('placeholder', 'Enter your last name');

                // Address 1
                document.querySelector('[name="street[0]"]')
                    ?.setAttribute('placeholder', 'House number and street');

                // Address 2
                document.querySelector('[name="street[1]"]')
                    ?.setAttribute('placeholder', 'Apartment, suite (optional)');

                // Town / City
                document.querySelector('input[name="city"]')
                    ?.setAttribute('placeholder', 'Town / City');

                // Postcode
                document.querySelector('input[name="postcode"]')
                    ?.setAttribute('placeholder', 'Postcode');

                // Phone
                document.querySelector('input[name="telephone"]')
                    ?.setAttribute('placeholder', 'For delivery updates');

            },

            removeFields: function () {

                // Remove Company
                var company = document.querySelector('[name="shippingAddress.company"]');
                
                if (company) {
                    company.closest('.field').style.display = 'none';
                }

                // Remove Address Line 3
                document
                .querySelector('[name="shippingAddress.street.2"]')
                ?.style.setProperty('display', 'none');

                var streetLegend = document.querySelector(
                    'fieldset.field.street legend'
                );

                if (streetLegend) {
                    streetLegend.style.display = 'none';
                }


            },


             /**
             * Move a Magento field into a BB row
             */
            moveField: function (selector, rowSelector) {

                var field = document.querySelector(selector);
                var row = document.querySelector(rowSelector);

                if (!field || !row) {
                    return;
                }

                row.appendChild(field);
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