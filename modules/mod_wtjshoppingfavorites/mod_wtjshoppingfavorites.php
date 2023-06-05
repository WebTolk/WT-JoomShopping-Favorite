<?php
/**
 * WT JoomShopping Favorites is an alternative wish list (favorite products) for JoomShopping based on coockies.
 * @package     WT JoomShopping Favorite
 * @version     1.3.5
 * @Author      Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2020-2023 Sergey Tolkachyov
 * @license     GNU/GPL 3.0
 * @since       1.0.0
 * @link        https://web-tolk.ru/en/dev/joomshopping/wt-joomshopping-favorite.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

$moduleId = $module->id;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
$doc             = Factory::getApplication()->getDocument();
/*
 * Take a css file for tmpl with the same name form media folder
 */

$tmpl_css      = explode(':', $params->get('layout'));
$tmpl_css_file = $tmpl_css[1];

if($params->get('use_module_css',1) == 1){
	$wa            = $doc->getWebAssetManager();
	if (file_exists('media/mod_wtjshoppingfavorites/css/' . $tmpl_css_file . '.css'))
	{

		$wa->registerStyle($module->module . '.' . $tmpl_css_file, $module->module . '/' . $tmpl_css_file . '.css');
		$wa->useStyle($module->module . '.' . $tmpl_css_file);

	}
	else
	{
		$wa->registerStyle($module->module . '.default', $module->module . '/default.css');
		$wa->useStyle($module->module . '.default');
	}
}



$app         = Factory::getApplication();
$product_ids = unserialize($app->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string'));
if ($product_ids == false)
{
	unset($product_ids);
}


if (!function_exists('getItemid'))
{

	function getItemid($component, $view)
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


$itemid = getItemid('com_jshopping', 'wtjshoppingfavorites');

if (empty($itemid) || is_null($itemid))
{
	$itemid = \JSHelper::getDefaultItemid();
	$itemid = "&Itemid=" . $itemid;
}
else
{
	$itemid = "&Itemid=" . $itemid;
}

$btn_icon_css_class = $params->get('btn_icon_css_class','');

if(empty($btn_icon_css_class)){
	$plugin = PluginHelper::getPlugin('jshoppingproducts', 'wtjshoppingfavorites');
	if ($plugin)
	{
		$pluginParams       = new Registry($plugin->params);
		$btn_icon_css_class = $pluginParams->get("btn_icon_css_class");
	}
}



require ModuleHelper::getLayoutPath('mod_wtjshoppingfavorites', $params->get('layout', 'default'));