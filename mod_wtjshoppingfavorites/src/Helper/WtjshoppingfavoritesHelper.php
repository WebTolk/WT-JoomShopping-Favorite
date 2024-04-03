<?php
/**
 * WT JoomShopping Favorites is an alternative wish list (favorite products) for JoomShopping based on cookies.
 * @package     WT JoomShopping Favorite
 * @version     2.0.2
 * @Author      Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2024 Sergey Tolkachyov
 * @license     GNU/GPL 3.0
 * @since       1.0.0
 * @link        https://web-tolk.ru/en/dev/joomshopping/wt-joomshopping-favorite
 */

namespace Joomla\Module\Wtjshoppingfavorites\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class WtjshoppingfavoritesHelper
{
	/**
	 * Method to get last seen products
	 *
	 * @param   Registry &      $params  Module parameters
	 * @param   CMSApplication  $app     Application object
	 *
	 * @return  array  Last seen products
	 * @since 1.0.0
	 */
	public static function getProductList(Registry &$params, CMSApplication $app): array
	{
		$cookie = $app->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string');
		if (!empty($cookie))
		{
			$product_ids = (array) unserialize($cookie);
		}
		else
		{
			$product_ids = [];
		}

		return $product_ids;
	}


	/**
	 * Return menu item id for WT JoomShopping favorites
	 *
	 * @throws \Exception
	 * @since 2.0.0
	 */
	public function getItemid(): int
	{
		/* @var $menu \Joomla\CMS\Menu\AbstractMenu */
		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getItems('link', 'index.php?option=com_jshopping&view=wtjshoppingfavorites', true);

		return $item->id;
	}
}