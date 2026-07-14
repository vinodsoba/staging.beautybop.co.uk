define([], function () {
    'use strict';

    return function (stepNavigator) {

        var originalRegisterStep = stepNavigator.registerStep;

        stepNavigator.registerStep = function () {

            originalRegisterStep.apply(stepNavigator, arguments);

            setTimeout(function () {

                stepNavigator.steps().forEach(function (step) {
                    step.isVisible(true);
                });

            }, 100);

        };

        return stepNavigator;
    };
});