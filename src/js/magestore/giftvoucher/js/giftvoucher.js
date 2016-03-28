/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var templates;
var customer_name;
var recipient_name;
var recipient_email;
var recipient_ship;
var message;
var day_to_send;
var email_sender;
var current_image;
function hideTemplateImages() {
    if (jQuery('select-gift')[0].selected == true) {
        jQuery('gift-image-carosel').hide();
    }
    else {
        jQuery('gift-image-carosel').show();
    }
}
//if (customer_name && customer_name.value) {
//    jQuery('customer_name').value = customer_name.value;
//}
//if (recipient_name && recipient_name.value) {
//    jQuery('recipient_name').value = recipient_name.value;
//}
//if (recipient_email && recipient_email.value) {
//    jQuery('recipient_email').value = recipient_email.value;
//}
//if (recipient_ship && recipient_ship.value) {
//    jQuery('recipient_ship').checked = true;
//}
//if (message && message.value) {
//    jQuery('message').value = message.value;
//}
//if (day_to_send && day_to_send.value) {
//    jQuery('recipient_email').value = recipient_email.value;
//}
//if (email_sender && email_sender.value) {
//    jQuery('email_sender').checked = true;
//}
function loadGiftCard(templates) {
    if (jQuery('select-gift') && jQuery('select-gift').value)
        changeTemplate(jQuery('select-gift'), templates);
}
function sendFriend(el) {
    if (!el)
        return;
    var receiver = jQuery('giftvoucher-receiver');
    if (el.checked) {
        if (receiver) {
            receiver.show();
            if (jQuery('recipient_name'))
                jQuery('recipient_name').addClassName('required-entry');
            if (jQuery('recipient_email')) {
                jQuery('recipient_email').addClassName('required-entry');
                jQuery('recipient_email').addClassName('validate-email');
                jQuery('recipient_email').addClassName('validate-same-email');
            }
            if (jQuery('day_to_send')) {
                jQuery('day_to_send').addClassName('required-entry');
                jQuery('day_to_send').addClassName('validate-date');
                jQuery('day_to_send').addClassName('validate-date-giftcard');
            }
        }
    } else {
        if (receiver) {
            if (jQuery('recipient_email')) {
                jQuery('recipient_email').removeClassName('required-entry');
                jQuery('recipient_email').removeClassName('validate-email');
                jQuery('recipient_email').removeClassName('validate-same-email');
            }
            receiver.hide();
            if (jQuery('recipient_name'))
                jQuery('recipient_name').removeClassName('required-entry');

            if (jQuery('day_to_send')) {
                jQuery('day_to_send').removeClassName('required-entry');
                jQuery('day_to_send').removeClassName('validate-date');
                jQuery('day_to_send').removeClassName('validate-date-giftcard');
            }
        }
    }
}

var image_old;
var image_count;
var template_show_id;
var template_id;
var giftcard_prev = 0;
var giftcard_next = 4;
//var image_form_data;
function changeTemplate(el) {
    template_id = getTemplateById(el.value, templates);
    if (typeof image_for_old !== 'undefined')
        jQuery(image_for_old).hide();
    if (typeof image_form_data === 'undefined') {
        image_for_old = 'div-bound-' + templates[template_id].giftcard_template_id + '-0';
        if (jQuery(image_for_old))
            jQuery(image_for_old).show();
        giftcard_prev = 0;
        giftcard_next = 4;
    } else
        image_for_old = 'div-bound-' + templates[template_id].giftcard_template_id + '-' + (image_form_data - image_form_data % 4);
    if (jQuery(image_for_old))
        jQuery(image_for_old).show();
    if (templates[template_id].images)
        count_next_fix = templates[template_id].images.split(',').length;
    else
        count_next_fix = 0;

    if (giftcard_next >= count_next_fix)
        jQuery('giftcard-template-next').hide();
    else
        jQuery('giftcard-template-next').show();
    if (giftcard_prev <= 0)
        jQuery('giftcard-template-prev').hide();
    else
        jQuery('giftcard-template-prev').show();

    if (typeof image_form_data !== 'undefined') {
        changeSelectImages(image_form_data);
        delete image_form_data;
    } else {
        changeSelectImages(0);
    }
}
function getTemplateById(id, templates) {
    for (i = 0; i < templates.length; i++) {
        if (templates[i].giftcard_template_id === id)
            return i;
    }
    return 0;
}
function changeSelectImages(image_id) {
    if (typeof image_old != 'undefined') {
        jQuery('div-' + image_old).removeClassName('gift-active');
        jQuery('div-' + image_old).down('.egcSwatch-arrow').hide();
    }
    if (jQuery('image-for-' + templates[template_id].giftcard_template_id + '-' + image_id)) {
        image_old = 'image-for-' + templates[template_id].giftcard_template_id + '-' + image_id;
        jQuery('div-' + image_old).addClassName('gift-active');

        jQuery('div-image-for-' + templates[template_id].giftcard_template_id + '-' + image_id).down('.egcSwatch-arrow').show();
        image = jQuery(image_old).src;

        images_tmp = templates[template_id].images;
        if (images_tmp != null) {
            images_tmp = images_tmp.split(',');
            jQuery('giftcard-template-images').value = images_tmp[image_id];
        }
    }
}
function giftcardPrevImage() {
    if (giftcard_prev === 0)
        return;
    if (typeof image_for_old !== 'undefined')
        jQuery(image_for_old).hide();
    giftcard_prev = giftcard_prev - 4;
    giftcard_next = giftcard_next - 4;
    image_for_old = 'div-bound-' + templates[template_id].giftcard_template_id + '-' + giftcard_prev;
    jQuery(image_for_old).show();
    if (giftcard_prev === 0)
        jQuery('giftcard-template-prev').hide();
    if (giftcard_next < templates[template_id].images.split(',').length)
        jQuery('giftcard-template-next').show();
}
function giftcardNextImage() {
    if (giftcard_next >= templates[template_id].images.split(',').length)
        return;
    if (typeof image_for_old !== 'undefined')
        jQuery(image_for_old).hide();
    giftcard_next = giftcard_next + 4;
    giftcard_prev = giftcard_prev + 4;
    image_for_old = 'div-bound-' + templates[template_id].giftcard_template_id + '-' + giftcard_prev;
    jQuery(image_for_old).show();
    if (giftcard_next >= templates[template_id].images.split(',').length)
        jQuery('giftcard-template-next').hide();
    if (giftcard_prev > 0)
        jQuery('giftcard-template-prev').show();
}
function changeRemaining(el, remaining_max) {
    if (el.value.length > remaining_max) {
        el.value = el.value.substring(0, remaining_max);
    }
    jQuery('giftvoucher_char_remaining').innerHTML = remaining_max - el.value.length;
}
day_to_send_error = 'We cannot send a Gift Card on a date in the past. Please choose the sending date again.';
Validation.add('validate-date-giftcard', day_to_send_error, function (v) {
    if (Validation.get('validate-date').test(v)) {
        var test = new Date(v);
        var today = getTodayDate();
        if (test < today)
            return false;
    }
    return true;
});
function getTodayDate() {
    todayDate = new Date();
    todayDate.setDate(todayDate.getDate() - 1);
    todayDate.setHours(0);
    todayDate.setMinutes(0);
    todayDate.setSeconds(0);
    todayDate.setMilliseconds(0);
    return todayDate;
}
function shipToFriend(el, check) {
    if (el.checked) {
        if (jQuery('recipient_email'))
            jQuery('recipient_email').removeClassName('required-entry');
        if (jQuery('recipient_ship_desc'))
            jQuery('recipient_ship_desc').show();
    } else {

        if (jQuery('recipient_ship_desc'))
            jQuery('recipient_ship_desc').hide();
    }
}
function validateInputRange(el, from, to, priceFormat) {
    var result = [];
    price = priceFormat.match('1.000.00')[0];
    result['decimalSymbol'] = price.charAt(5);
    result['groupSymbol'] = price.charAt(1);

    var gift_amount_min = from;
    var gift_amount_max = to;

    validateValue = el.value.replace(/\s/g, '');
    if (validateValue.search(result.groupSymbol) != -1)
        validateValue = validateValue.replace(result.groupSymbol, '');
    el.value = validateValue.replace(result.decimalSymbol, '.');
    jQuery('amount_range').value = el.value;

    if (el.value < gift_amount_min)
        el.value = gift_amount_min;
    if (el.value > gift_amount_max)
        el.value = gift_amount_max;
}
function setAmountRange(amount) {
    if (!jQuery('amount_range').value)
        jQuery('amount_range').value = amount;
}