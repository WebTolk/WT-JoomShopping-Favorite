<?php
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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;

if (!empty($product_ids))
{
	$active = ' active ';
	$digit = count($product_ids);
}
else
{
	$active = '';
	$digit = '0';

}
?>

<a class="wt_jshop_favorite_module btn position-relative <?php echo $active; ?>"
   href="<?php echo Route::_('index.php?option=com_jshopping&view=wtjshoppingfavorites' . $itemid); ?>">
	<i class="<?php echo $btn_icon_css_class; ?>" aria-hidden="true"></i>
	<span class="digit position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
		<?php echo $digit; ?>
	</span>
</a>