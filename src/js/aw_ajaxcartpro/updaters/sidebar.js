var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('sidebar');
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: true,
    updateOnActionRequest: false,

    beforeUpdate: function (html) {
        // remove old sidebar
        jQuery('#mini-cart').remove();
        // end remove old sidebar

        return null;
    },

    afterUpdate: function (html, selectors) {
        var me = this;
        //call mage function
        if (typeof(truncateOptions) === 'function') {
            truncateOptions();
        }

        selectors.each(function (selector) {
            me._effect(selector);
        });

        // move sidebar again
        if (jQuery('#top').hasClass('header-mobile') === true) {
            var SmartHeader = {

                mobileHeaderThreshold: 770
                , rootContainer: jQuery('.header-container')

                , init: function () {
                    enquire.register('(max-width: ' + (SmartHeader.mobileHeaderThreshold - 1) + 'px)', {
                        match: SmartHeader.moveElementsToMobilePosition,
                        unmatch: SmartHeader.moveElementsToRegularPosition
                    });
                }

                , activateMobileHeader: function () {
                    SmartHeader.rootContainer.addClass('header-mobile').removeClass('header-regular');
                }

                , activateRegularHeader: function () {
                    SmartHeader.rootContainer.addClass('header-regular').removeClass('header-mobile');
                }

                , moveElementsToMobilePosition: function () {
                    SmartHeader.activateMobileHeader();

                    //Move cart
                    jQuery('#mini-cart-wrapper-mobile').prepend(jQuery('#mini-cart'));

                    //Reset active state
                    jQuery('.skip-active').removeClass('skip-active');

                    //Disable dropdowns
                    jQuery('#mini-cart').removeClass('dropdown');
                    jQuery('#mini-compare').removeClass('dropdown');

                    //Clean up after dropdowns: reset the "display" property
                    jQuery('#header-cart').css('display', '');
                    jQuery('#header-compare').css('display', '');

                }

            }; //end: SmartHeader

            SmartHeader.init();

            jQuery('.skip-link').click(function () {

                var self = jQuery(this);
                var target = self.attr('href');

                if (target == '#header-locations') {
                    window.location.href = locatorUrl;
                }

                //Get target element
                var elem = jQuery(target);

                //Check if stub is open
                var isSkipContentOpen = elem.hasClass('skip-active') ? 1 : 0;


                //Toggle stubs
                if (target == '#header-cart') {
                    //Hide all stubs
                    jQuery('.skip-link').removeClass('skip-active');
                    jQuery('.skip-content').removeClass('skip-active');

                    if (isSkipContentOpen) {
                        self.removeClass('skip-active');
                    } else {
                        self.addClass('skip-active');
                        elem.addClass('skip-active');
                        elem.show();
                    }
                }
                else {
                    jQuery('.skip-link.skip-cart').removeClass('skip-active');
                    jQuery('.mini-cart-content.skip-content').removeClass('skip-active');
                }
            });
        }
        else {
            if (jQuery('#mini-cart').parent().parent().hasClass('nav-holder') === false && jQuery('#top').hasClass('sticky-header') === true) {
                jQuery('#nav-holder1').prepend(jQuery('#mini-cart'));
            }
        }
        // move sidebar again
        return null;
    },

    _effect: function (obj) {
        var el = $$(obj)[0];
        if (typeof(el) == 'undefined') {
            return null;
        }
        switch (this.config.cartAnimation) {
            case 'opacity':
                el.hide();
                new Effect.Appear(el);
                break;
            case 'grow':
                el.hide();
                new Effect.BlindDown(el);
                break;
            case 'blink':
                new Effect.Pulsate(el);
                break;
            default:
        }
    }
});
AW_AjaxCartPro.registerUpdater(AW_AjaxCartProUpdaterObject);
delete AW_AjaxCartProUpdaterObject;