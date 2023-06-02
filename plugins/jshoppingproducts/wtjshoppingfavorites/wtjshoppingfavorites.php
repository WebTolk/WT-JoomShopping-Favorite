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
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;

class plgJshoppingproductsWtjshoppingfavorites extends CMSPlugin
{
	protected $autoloadlanguage = true;

	public function addJs()
	{
		$cookie_period = 60 * 60 * 24 * (int) $this->params->get('cookie_period', '1');
		$doc           = Factory::getApplication()->getDocument();
		HTMLHelper::script('plg_jshoppingproducts_wtjshoppingfavorites/wtjshoppingfavorites.js',
			[
				'version'  => 'auto',
				'relative' => true
			]
		);
		$doc->addScriptOptions('wt_jshopping_favorites_script_potions', array('cookie_period' => $cookie_period));
	}

	public function onBeforeDisplayProductListView(&$view)
	{
		$this->addJs();
		$link_css_class       = $this->params->get("link_css_class");
		$product_list_tmp_var = $this->params->get("product_list_tmp_var");

		$rows   = $view->rows;
		$app    = Factory::getApplication();
		$cookie = unserialize($app->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string'));

		foreach ($rows as $row)
		{
			$product_id = $row->product_id;
			if (!empty($cookie))
			{

				if (($key = array_search($product_id, (array) $cookie)) !== false)
				{
					$selected = "selected";
				}
				else
				{
					$selected = "";
				}
			}
			else
			{
				$selected = "";
			}

			$row->$product_list_tmp_var .= "<button type='button' class='" . $link_css_class . " " . $selected . "' id='favorite_button" . $product_id . "' data-favorite='" . $product_id . "'><i class=\"" . $this->params->get("btn_icon_css_class") . "\"></i></button>";
		}

	}

	public function onBeforeDisplayProductView(&$view)
	{
		$this->addJs();
		$link_css_class       = $this->params->get("in_product_link_css_class");
		$product_list_tmp_var = $this->params->get("product_tmp_var");
		$app                  = Factory::getApplication();
		$cookie               = unserialize($app->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string'));
		$product_id           = $view->product->product_id;
		if (!empty($cookie))
		{

			if (($key = array_search($product_id, (array) $cookie)) !== false)
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}
		}
		else
		{
			$selected = "";
		}

		$view->$product_list_tmp_var .= "<button type='button' class='" . $link_css_class . " " . $selected . "' id='favorite_button" . $product_id . "' data-favorite='" . $product_id . "'><i class=\"" . $this->params->get("btn_icon_css_class") . "\"></i></button>";

	}

	/*
	 * Добавляет атрибут с id товара блоку block_product через js-скрипт,
	 * чтобы достучаться до нужного товара
	 */
	public function onBeforeDisplaywtjshoppingfavoritesView(&$view)
	{
		$this->addJs();
		$doc = Factory::getApplication()->getDocument();


		$js  = "
			if (document.readyState != 'loading') {
				wtJshoppingFavoriteAttributeHandler();
			} else {
				document.addEventListener('DOMContentLoaded', wtJshoppingFavoriteAttributeHandler);
			}

			function wtJshoppingFavoriteAttributeHandler(){
			    let body = document.querySelector('body');
			    body.classList.add('wtjshoppingfavoritesView');
			    let elements = document.querySelectorAll('.wtjshoppingfavoritesView .block_product');

				Array.prototype.forEach.call(elements, function (el, i) {
					let product_id_tag = el.querySelectorAll('[data-favorite]');
					let product_id = product_id_tag[0].getAttribute('data-favorite');
					el.setAttribute('data-wt-jshop-favorite', product_id);
				});
			}
		";
		$wa = $doc->getWebAssetManager();
		$wa->addInlineScript($js);
	}

	public function onAjaxWtjshoppingfavorites()
	{
		$app        = Factory::getApplication();
		$product_id = $app->getInput()->post->get("product_id", "", "int");
		$cookie     = $app->getInput()->post->get("cookie", "", "raw");

		//$app->input->cookie->set('wt_jshopping_last_seen_products', $cookie, $cookie_period,'/');
		$product_decode = unserialize(stripcslashes($cookie));
		if (!isset($cookie))
		{
			if (($key = array_search($product_id, (array) $product_decode)) === false)
			{
				$product_info   = array($product_id);
				$product_encode = serialize($product_info);
				$output         = json_encode(array($product_encode, "added" => true));

				echo $output;

			}
			else
			{
				echo null;
			}
		}
		else
		{
			if (($key = array_search($product_id, (array) $product_decode)) !== false)
			{
				unset($product_decode[$key]);
				$product_encode = serialize($product_decode);
				$output         = json_encode(array($product_encode, "added" => false));
				echo $output;
			}
			else
			{
				$product_decode[] = $product_id;
				$product_encode   = serialize($product_decode);
				$output           = json_encode(array($product_encode, "added" => true));
				echo $output;
			}
		}
	}
}
