define([], function () {
    'use strict';

    return function (LayoutProcessor) {

        return LayoutProcessor.extend({

            process: function (jsLayout) {

                console.log('✅ BeautyBop LayoutProcessor Loaded');

                return this._super(jsLayout);
            }

        });

    };
});