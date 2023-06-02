/**
 * WT JoomShopping Favorites is an alternative wish list (favorite products) for JoomShopping based on coockies.
 * @package     WT JoomShopping Favorite
 * @version     1.3.0
 * @Author      Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2022 Sergey Tolkachyov
 * @license     GNU/GPL 3.0
 * @since       1.0.0
 * @link        https://web-tolk.ru/en/dev/joomshopping/wt-joomshopping-favorite.html
 */
jQuery(document).ready(function () {

    jQuery("[data-favorite]").click(function () {
        event.preventDefault();
        let product_id = jQuery(this).attr("data-favorite");
        jQuery.ajax({
            type: "POST", url: location.protocol + '//' + location.hostname, data: ({
                'option': 'com_ajax',
                'plugin': 'wtjshoppingfavorites',
                'group': 'jshoppingproducts',
                'format': 'raw',
                'product_id': product_id,
                'cookie': decodeURIComponent(getCookie('wtjshoppingfavorites'))
            }), success: function (favorite) {
                favorite = JSON.parse(favorite);
                let data = encodeURIComponent(favorite[0]);
                let wt_jshopping_favorites_script_potions = Joomla.getOptions('wt_jshopping_favorites_script_potions');
                let cookie_max_age = wt_jshopping_favorites_script_potions['cookie_period'];
                document.cookie = "wtjshoppingfavorites=" + data + "; path=/; domain=" + location.hostname + "; max-age=" + cookie_max_age;
                changeModuleDigit(favorite, product_id);
            }, fail: function (favorite, data) {
                console.log(favorite);
            }
        });
    });


    jQuery('#wt-jshopping-favorite-empty-list').click(function () {
        let data = encodeURIComponent("");

        document.cookie = "wtjshoppingfavorites=" + data + ";max-age=0; path=/; domain=" + location.hostname;
        if ('body.wtjshoppingfavoritesView') {
            jQuery("[data-wt-jshop-favorite]").detach();
        }
        jQuery(".wt_jshop_favorite_module .digit").html('').removeClass('active');

    });

});

function getCookie(cname) {
    let name = cname + "=";
    // let decodedCookie = document.cookie;
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/**
 *
 * @param favorite    json response from ajax request
 * @param product_id JoomShopping product id
 */
function changeModuleDigit(favorite, product_id) {
    if (favorite["added"] == true) {

        let digit = jQuery(".wt_jshop_favorite_module .digit").html();

        digit++;

        jQuery(".wt_jshop_favorite_module .digit").html(digit);

        if (digit > 0) {
            jQuery(".wt_jshop_favorite_module").addClass("active");

        }
        jQuery("#favorite_button" + product_id).addClass("selected");

    } else {

        let digit = jQuery(".wt_jshop_favorite_module .digit").html();

        digit--;

        jQuery(".wt_jshop_favorite_module .digit").html(digit);

        if (digit == 0) {
            jQuery(".wt_jshop_favorite_module.active").removeClass("active");
        }
        jQuery("#favorite_button" + product_id).removeClass("selected");
        if ('body.wtjshoppingfavoritesView') {
            jQuery("[data-wt-jshop-favorite=" + product_id + "]").detach();
        }
    }
}

