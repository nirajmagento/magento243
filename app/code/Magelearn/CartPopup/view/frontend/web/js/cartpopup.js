define([
    'jquery',
    'uiElement',
    'ko',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal',
    'owlCarousel'
], function ($, Component, ko, customerData, modal) {

    'use strict';

    var modalPopupSelector = '[data-placeholder="cartpopup"]';

    return Component.extend({
        defaults: {
            template: 'Magelearn_CartPopup/popup'
        },
        isUpdated: ko.observable(false),
        products: ko.observable(false),
        successIcon: ko.observable(),
        cartTotalCount: ko.observable(),
        cartMessage: ko.observable(),
        shoppingCartUrl: '',

        /**
         * Update modal popup content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {
            this.products(updatedCart.products);
            this.applyCartMessageBinding();
            this.cartTotalCount(updatedCart.cartTotalCount);
            this.isUpdated(false);

            $(modalPopupSelector).modal("openModal");
        },

        /**
         * Close Modal Popup Action
         */
        closeModal: function () {
            $(modalPopupSelector).modal("closeModal");
        },

        /**
         * Redirect to Shopping Cart
         */
        viewShoppingCart: function () {
            window.location.replace(this.shoppingCartUrl);
        },

        /**
         * Apply bindings to element loaded inside of cartMessage
         */
        applyCartMessageBinding: function () {
            ko.applyBindingsToNode(
                document.getElementById(
                    'cart-popup-total-count'),
                {text: this.cartTotalCount}
            );
        },

        /**
         * Initialize Component
         * @returns {*}
         */
        initialize: function (config) {
            var self = this,
            cartPopupData = customerData.get('cartpopup');
			var carousal_autoplay = config.carouselAutoPlay;
            /**
             * Update CartPopup only when an addToCart action is triggered
             */
            $(document).on('ajax:addToCart', function (sku, productIds, form, response) {
                self.isUpdated(true);
            });

            /**
             * Subscribe to changes on the CustomerData component
             */
            cartPopupData.subscribe(function (updatedCart) {
                if (self.isUpdated()) {
                    self.update(updatedCart);
                }
            }, this);

            /**
             * Set Modal Popup Component options
             */
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
                modalClass: 'no-header-footer',
                buttons: [{
                    text: $.mage.__('Close'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            modal(options, $(modalPopupSelector));
			ko.bindingHandlers.owlCarouselInitiator = {
			    init: function() {
					console.log(carousal_autoplay);
					$('.owl-carousel').owlCarousel({
						loop:true,
				        nav:true,
				        navText:["<div class='nav-btn prev-slide'></div>","<div class='nav-btn next-slide'></div>"],
						autoplay:parseInt(carousal_autoplay),
						autoplayTimeout:2000,
						items : 4,
				      	itemsDesktop : [1000,4],
				      	itemsDesktopSmall : [900,4],
				      	itemsTablet: [600,2]
		        });
			    }
			};
            return self._super();
        }
    })
});