var config = {
    map: {
        '*': {
            'Magento_Checkout/template/sidebar.html':
                'Magento_Checkout/template/sidebar.html'
        }
    },
     config: {
        mixins: {
            'Magento_Checkout/js/model/step-navigator': {
                'Magento_Checkout/js/mixins/step-navigator-mixin': true
            },
             'Magento_Checkout/js/view/summary/abstract-total': {
                'Magento_Checkout/js/view/summary/abstract-total-mixin': true
            },
            'Magento_Checkout/js/view/summary': {
                'Magento_Checkout/js/view/summary/summary-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Magento_Checkout/js/mixins/shipping-mixin': true
            },
            'Magento_Checkout/js/layoutProcessor': {
                'Magento_Checkout/js/mixins/layout-processor-mixin': true
            }
        }
    }
};