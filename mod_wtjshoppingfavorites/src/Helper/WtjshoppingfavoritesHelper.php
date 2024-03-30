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
	 * @param Registry &$params Module parameters
     * @param CMSApplication $app Application object
	 *
	 * @return  array  Last seen products
     * @since 1.0.0
	 */
	public static function getProductList(Registry &$params, CMSApplication $app): array
	{
        $cookie = $app->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string');
		if (!empty($cookie))
		{
			$product_ids = unserialize($app->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string'));
		}
        else
        {
			$product_ids = [];
		}
		return $product_ids;
	}

    /**
     * @param string $component
     * @param string $view
     *
     * @throws \Exception
     * @since 2.0.0
     */
    public function getItemid(string $component, string $view)
    {
        $items = Factory::getApplication()->getMenu('site')->getItems('component', $component);
        foreach ($items as $item)
        {
            if ($item->query['view'] === $view)
            {
                return $item->id;
            }
        }
    }
}
?>