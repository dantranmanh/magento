jQuery(window).load(function () {
    if (Modernizr.mq('screen and (min-width:960px)')) {
        // action for screen widths including and above 960 pixels
        jQuery('#header-nav .first .nav-submenu.level0').masonry({
            itemSelector: '.nav-item.level1',
            percentPosition: true
        });
        window.masonryIsActive = true;
    }
    if (Modernizr.mq('screen and (max-width:959px)')) {
        jQuery('#header-nav .first .nav-submenu.level0').masonry().masonry('destroy');
        window.masonryIsActive = false;
    }
});

jQuery(document).ready(function () {
    function doneResizing() {
        if (Modernizr.mq('screen and (min-width:960px)')) {
            // action for screen widths including and above 960 pixels
            jQuery('#header-nav .first .nav-submenu.level0').masonry({
                itemSelector: '.nav-item.level1',
                percentPosition: true
            });
            window.masonryIsActive = true;
        } else if (Modernizr.mq('screen and (max-width:959px)') && window.masonryIsActive == true) {
            // action for screen widths below 959 pixels
            jQuery('#header-nav .first .nav-submenu.level0').masonry().masonry('destroy');
            window.masonryIsActive = false;
        }
    }

    var id;
    jQuery(window).resize(function () {
        clearTimeout(id);
        id = setTimeout(doneResizing, 0);
    });

    doneResizing();

    if (jQuery('.block-layered-nav').length && jQuery('.category-products').length) {
        jQuery('#gan-block-content-left .gan-attribute dt').removeClass('filter-content-show');
        jQuery('#gan-block-content-left .gan-attribute dt').addClass('filter-content-hide');
        jQuery('#gan-block-content-left .gan-attribute dd').hide();
    }

    /**
     * update the store locator link
     */
    jQuery('.cms-menu a').each(function (index, value) {
        var _url =jQuery(this).attr('href');
        var _page=_url.split("/");
        var _locator_key= _page.slice(-1).pop();
        if(_locator_key == "locator"){
            _url= _url.replace("/company/locator", "/locator/search");
            jQuery(this).attr('href',_url);
        };
    });
});

function _option_set_low_stock() {
    if (_enabled_low_stock != 1) return;
    $$('p.availability').each(function (el) {
        var el = $(el);
        _option_reset_class_label(el);
        el.addClassName('low-stock').removeClassName('out-of-stock');
        el.select('span').invoke('update', Translator.translate('Low Stock'));
    });
}

function _option_set_in_stock() {
    if (_enabled_low_stock != 1) return;
    $$('p.availability').each(function (el) {
        var el = $(el);
        _option_reset_class_label(el);
        el.addClassName('in-stock').removeClassName('out-of-stock');
        el.select('span').invoke('update', Translator.translate('In Stock'));
    });
}

function _option_set_label(label) {
    if (_enabled_low_stock != 1) return;
    $$('p.availability').each(function (el) {
        var el = $(el);
        if (label == 'Low Stock') _option_set_low_stock();
        if (label == 'In Stock') _option_set_in_stock();
    });
}

function _option_reset_class_label(el) {
    el.removeClassName('in-stock');
    el.removeClassName('low-stock');
}