define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function ($,Component,quote,totals) {
        "use strict";
        return Component.extend({
            totals: quote.getTotals(),
            defaults: {
                template: 'Magelearnnew_CustomerDiscount/checkout/summary/customerDiscount'
            },
            isDisplayedCustomerDiscount: function () {
                return this.getValueCustomerDiscount() != 0;
            },
            getValueCustomerDiscount: function() {
                var price = 0;
                if (this.totals() && totals.getSegment('customer_discount')) {
                    price = totals.getSegment('customer_discount').value;
                }
                return price;
            }
        });
    }
);
