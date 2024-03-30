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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if(!empty($product_ids))
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

<div class="wt_jshop_favorite_module <?php echo $params->get('moduleclass_sfx'); ?>  <?php echo $active; ?>">
	<a title="В избранном" href="<?php echo Route::_('index.php?option=com_jshopping&view=wtjshoppingfavorites'.$itemid);?>">
		<span class="num">
            <i class="<?php echo $btn_icon_css_class; ?>" aria-hidden="true"></i>
            <span class="digit"><?php echo $digit; ?></span>
        </span> <span class="text"><?php echo Text::_('MOD_WTJSHOPPINGFAVORITES_TEXT'); ?></span>
	</a>
</div>