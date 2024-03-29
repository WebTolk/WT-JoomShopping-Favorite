/**
 * WT JoomShopping Favorites is an alternative wish list (favorite products) for JoomShopping based on coockies.
 * @package     WT JoomShopping Favorite
 * @version     2.0.2
 * @Author      Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2024 Sergey Tolkachyov
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
                url: Joomla.getOptions('system.paths', '').root + '/index.php?option=com_ajax&plugin=wtjshoppingfavorites&group=jshoppingproducts&format=json&product_id=' + product_id,
                onSuccess: function (response, xhr) {
                    // Тут делаем что-то с результатами
                    // Проверяем, пришли ли ответы
                    if (response !== '') {
                        let favorite = JSON.parse(response);
                        changeModuleDigit(favorite.data.added, product_id);
                    } else {
                        console.error(response);
                    }
                }
            });
        }, false);//addEventListener
    });

    let btn_favorite_empty_list = document.getElementById('wt-jshopping-favorite-empty-list');
    if (btn_favorite_empty_list) {
        btn_favorite_empty_list.addEventListener('click', () => {
			Joomla.request({
                url: Joomla.getOptions('system.paths', '').root + '/index.php?option=com_ajax&plugin=wtjshoppingfavorites&group=jshoppingproducts&format=json&action=clearproducts',
                onSuccess: function (response, xhr) {
                    // Тут делаем что-то с результатами
                    // Проверяем, пришли ли ответы
                    if (response !== '') {
                        let body = document.querySelector('body');
						if (body.classList.contains('wtjshoppingfavoritesView')) {
							for (const el of document.querySelectorAll('[data-wt-jshop-favorite]')) {
								el.remove();
							}
						}
						let digit = document.querySelector('.wt_jshop_favorite_module .digit');
						digit.innerHTML = '';
						digit.classList.remove('active');
                    } else {
                        console.error(response);
                    }
                }
            });
        });
    }
});

/**
 *
 * @param favorite    json response from ajax request
 * @param product_id JoomShopping product id
 */
function changeModuleDigit(favorite, product_id) {
    let digits = document.querySelectorAll('.wt_jshop_favorite_module .digit');

    digits.forEach((digit) => {
        let digit_int = parseInt(digit.textContent);
        let favorite_module = document.querySelector('.wt_jshop_favorite_module');
        let favorite_button = document.getElementById('favorite_button' + product_id);

        if (favorite === true) {

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
            let body = document.querySelector('body');
            if (body.classList.contains('wtjshoppingfavoritesView')) {
                let el = document.querySelector("[data-wt-jshop-favorite='" + product_id + "']");
                el.remove();
            }
        }
    });
}

