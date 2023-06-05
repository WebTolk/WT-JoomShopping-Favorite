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

document.addEventListener('DOMContentLoaded', () => {
    let elements = document.querySelectorAll('[data-favorite]');
    Array.prototype.forEach.call(elements, function (el, i) {
        el.addEventListener('click', async function () {
            let product_id = el.getAttribute('data-favorite');

            Joomla.request({
                url: Joomla.getOptions('system.paths', '').root + '/index.php',
                method: 'POST',
                headers: {
                    'Cache-Control' : 'no-cache',
                    'Your-custom-header' : 'custom-header-value'
                },
                data: {
                    'option': 'com_ajax',
                    'plugin': 'wtjshoppingfavorites',
                    'group': 'jshoppingproducts',
                    'format': 'raw',
                    'product_id': product_id,
                    'cookie': decodeURIComponent(getCookie('wtjshoppingfavorites'))
                },
                onSuccess: function (response, xhr){
                    // Тут делаем что-то с результатами
                    // Проверяем пришли ли ответы
                    if (response !== ''){
                        let favorite = JSON.parse(response);
                        let data = encodeURIComponent(favorite[0]);
                        let wt_jshopping_favorites_script_potions = Joomla.getOptions('wt_jshopping_favorites_script_potions');
                        let cookie_max_age = wt_jshopping_favorites_script_potions['cookie_period'];
                        document.cookie = "wtjshoppingfavorites=" + data + "; path=/; domain=" + location.hostname + "; max-age=" + cookie_max_age;
                        changeModuleDigit(favorite, product_id);
                    } else {
                        console.log(response);
                    }
                }
            });


            let response  = await fetch(Joomla.getOptions('system.paths', '').root + '/index.php?option=com_ajax&plugin=wt_content_like&group=content&format=raw&article_id=' + articleId);

            if (response.ok){
                let json = await response.json();

                if (json !== '') {
                    if (json.success === 1) {
                        let rating_count_span = document.getElementById('wt_content_like_rating_count_' + articleId);
                        rating_count_span.removeAttribute('style');
                        rating_count_span.innerHTML = json.rating;

                        let rating_message_span = document.getElementById('wt_content_like_meesage_' + articleId);
                        rating_message_span.innerHTML = json.message;
                        rating_message_span.setAttribute('style','color:#008000;');

                    } else {
                        let rating_message_span = document.getElementById('wt_content_like_meesage_' + articleId);
                        rating_message_span.innerHTML = json.message;
                        rating_message_span.setAttribute('style','color:#FF0000;');
                    }
                }
            }
        }, false);//addEventListener
    });


    let btn_favorite_empty_list = document.getElementById('wt-jshopping-favorite-empty-list');
    if(btn_favorite_empty_list)
    {
        btn_favorite_empty_list.addEventListener('click', () => {
            let data = encodeURIComponent("");

            document.cookie = "wtjshoppingfavorites=" + data + ";max-age=0; path=/; domain=" + location.hostname;
            let body = document.querySelector('body');
            if (body.classList.contains('wtjshoppingfavoritesView')) {
                for (const el of document.querySelectorAll('[data-wt-jshop-favorite]')) {
                    el.remove();
                }
            }
            let digit = document.querySelector('.wt_jshop_favorite_module .digit');
            digit.innerHTML = '';
            digit.classList.remove('active');
        });
    }

});


// jQuery(document).ready(function () {
//
//     jQuery("[data-favorite]").click(function () {
//         event.preventDefault();
//         let product_id = jQuery(this).attr("data-favorite");
//         jQuery.ajax({
//             type: "POST", url: location.protocol + '//' + location.hostname, data: ({
//                 'option': 'com_ajax',
//                 'plugin': 'wtjshoppingfavorites',
//                 'group': 'jshoppingproducts',
//                 'format': 'raw',
//                 'product_id': product_id,
//                 'cookie': decodeURIComponent(getCookie('wtjshoppingfavorites'))
//             }), success: function (favorite) {
//                 favorite = JSON.parse(favorite);
//                 let data = encodeURIComponent(favorite[0]);
//                 let wt_jshopping_favorites_script_potions = Joomla.getOptions('wt_jshopping_favorites_script_potions');
//                 let cookie_max_age = wt_jshopping_favorites_script_potions['cookie_period'];
//                 document.cookie = "wtjshoppingfavorites=" + data + "; path=/; domain=" + location.hostname + "; max-age=" + cookie_max_age;
//                 changeModuleDigit(favorite, product_id);
//             }, fail: function (favorite, data) {
//                 console.log(favorite);
//             }
//         });
//     });
//
//
//     jQuery('#wt-jshopping-favorite-empty-list').click(function () {
//         let data = encodeURIComponent("");
//
//         document.cookie = "wtjshoppingfavorites=" + data + ";max-age=0; path=/; domain=" + location.hostname;
//         if ('body.wtjshoppingfavoritesView') {
//             jQuery("[data-wt-jshop-favorite]").detach();
//         }
//         jQuery(".wt_jshop_favorite_module .digit").html('').removeClass('active');
//
//     });
//
// });

function getCookie(cname) {
    let name = cname + "=";
    // let decodedCookie = document.cookie;
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
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
    let digit = document.querySelector('.wt_jshop_favorite_module .digit');
    let digit_int = parseInt(digit);
    let favorite_module = document.querySelector('.wt_jshop_favorite_module');
    let favorite_button = document.getElementById('favorite_button' + product_id);

    if (favorite["added"] === true) {

        digit_int++;
        digit.innerHTML = digit_int;

        if (digit_int > 0) {
            favorite_module.classList.add('active');
        }

        favorite_button.classList.add('selected');

    } else {

        digit_int--;
        digit.innerHTML = digit_int;

        if (digit === 0) {

            favorite_module.classList.remove('active');
        }
        favorite_button.classList.remove('selected');
        if (body.classList.contains('wtjshoppingfavoritesView')) {
            el = document.querySelector("[data-wt-jshop-favorite=" + product_id + "]");
            el.remove();
        }
    }
}

