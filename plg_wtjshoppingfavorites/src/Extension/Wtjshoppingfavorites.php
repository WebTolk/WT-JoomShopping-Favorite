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

namespace Joomla\Plugin\Jshoppingproducts\Wtjshoppingfavorites\Extension;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class Wtjshoppingfavorites extends CMSPlugin implements SubscriberInterface
{
	protected $autoloadlanguage = true;

	protected $allowLegacyListeners = false;

	public static function getSubscribedEvents(): array
	{
		return [
			'onAjaxWtjshoppingfavorites'              => 'onAjaxWtjshoppingfavorites',
			'onBeforeDisplayProductListView'          => 'onBeforeDisplayProductListView',
			'onBeforeDisplayProductView'              => 'onBeforeDisplayProductView',
			'onBeforeDisplaywtjshoppingfavoritesView' => 'onBeforeDisplaywtjshoppingfavoritesView'
		];
	}

	/**
	 * AJAX event
	 *
	 * @param $event Event ajax event
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function onAjaxWtjshoppingfavorites(Event $event): void
	{
		$action = $this->getApplication()->getInput()->get('action', '', 'string');
		if (!empty($action) && $action == 'clearproducts')
		{
			$this->getApplication()->getInput()->cookie->set(
				'wtjshoppingfavorites',
				'',
				'-1',
				$this->getApplication()->get('cookie_path', '/'),
				$this->getApplication()->get('cookie_domain'),
				$this->getApplication()->isSSLConnection()
			);

			return;
		}

		$product_id = $this->getApplication()->getInput()->get('product_id', '', 'int');

		$product_ids = $this->getApplication()->getInput()->cookie->get('wtjshoppingfavorites', '', 'raw');
		if (!empty($product_ids))
		{
			$product_ids = (array) unserialize($product_ids);
		}
		else
		{
			$product_ids = [];
		}

		$response = [];

		/**
		 * Если product_id уже есть - удаляем.
		 * Если нет - добавляем.
		 */
		if (empty($product_ids))
		{
			// Куки нет вообще. В первый раз добавляет.
			$product_ids = [$product_id];

			$response['added'] = true;
		}
		else
		{
			// Куки уже есть. Когда-то что-то добавлял.
			if (($key = array_search($product_id, $product_ids)) !== false)
			{
				//Товар уже есть в массиве. Удаляем.
				unset($product_ids[$key]);

				$response['added'] = false;
			}
			else
			{
				// Товара нет в массиве. Добавляем.
				$product_ids[]     = $product_id;
				$response['added'] = true;
			}
		}

		$this->getApplication()->getInput()->cookie->set(
			'wtjshoppingfavorites',
			serialize(array_unique($product_ids)),
			$this->getCookiePeriod(),
			$this->getApplication()->get('cookie_path', '/'),
			$this->getApplication()->get('cookie_domain'),
			$this->getApplication()->isSSLConnection()
		);

		$event->setArgument('result', $response);
	}

	/**
	 * Returns cookie lifetime
	 *
	 * @return int
	 *
	 * @since 2.0.0
	 */
	private function getCookiePeriod(): int
	{
		return time() + (int) $this->params->get('cookie_period', '1') * 86400;
	}

	/**
	 * @param $event Event event object
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function onBeforeDisplayProductListView(Event $event): void
	{
		/* @var $view object JoomShopping Product List View object */
		$view        = $event->getArgument(0);
		$productlist = $event->getArgument(1);

		$this->addJs();
		$link_css_class       = $this->params->get('link_css_class');
		$product_list_tmp_var = $this->params->get('product_list_tmp_var');

		$rows   = $view->rows;
		$cookie = $this->getApplication()->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string');
		if (!empty($cookie))
		{
			$cookie = (array) unserialize($cookie);
		}
		else
		{
			$cookie = [];
		}
		foreach ($rows as $row)
		{
			if (in_array($row->product_id, $cookie))
			{
				$selected = 'selected';
			}
			else
			{
				$selected = '';
			}

			$button = '<button type="button" class="' . $link_css_class . ' ' . $selected . '" id="favorite_button' . $row->product_id . '" data-favorite="' . $row->product_id . '"><i class="' . $this->params->get('btn_icon_css_class') . '"></i></button>';
			if (!empty($row->$product_list_tmp_var))
			{
				$row->$product_list_tmp_var .= $button;
			}
			else
			{
				$row->$product_list_tmp_var = $button;
			}
		}

		$event->setArgument(0, $view);
	}

	/**
	 * Include extension's javascript into page
	 *
	 * @throws \Exception
	 * @since 1.3.1
	 */
	public function addJs(): void
	{
		$wa = $this->getApplication()->getDocument()->getWebAssetManager();
		$wa->registerAndUseScript('wt_jshopping_favorites_script',
			'plg_jshoppingproducts_wtjshoppingfavorites/wtjshoppingfavorites.js',
			[
				'version'  => 'auto',
				'relative' => true,
				'defer'    => true
			]
		);
	}

	/**
	 * @param $event Event event object
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function onBeforeDisplayProductView(Event $event): void
	{
		/* @var $view object JoomShopping Product View object */
		$view = $event->getArgument(0);

		$this->addJs();
		$link_css_class       = $this->params->get('in_product_link_css_class');
		$product_list_tmp_var = $this->params->get('product_tmp_var');

		$cookie = $this->getApplication()->getInput()->cookie->get('wtjshoppingfavorites', null, $filter = 'string');
		if (!empty($cookie))
		{
			$cookie = (array) unserialize($cookie);
		}
		else
		{
			$cookie = [];
		}

		$product_id = $view->product->product_id;

		if (in_array($product_id, $cookie))
		{
			$selected = 'selected';
		}
		else
		{
			$selected = '';
		}

		$button = '<button type="button" class="' . $link_css_class . ' ' . $selected . '" id="favorite_button' . $product_id . '" data-favorite="' . $product_id . '"><i class="' . $this->params->get('btn_icon_css_class') . '"></i></button>';
		if (!empty($view->$product_list_tmp_var))
		{
			$view->$product_list_tmp_var .= $button;
		}
		else
		{
			$view->$product_list_tmp_var = $button;
		}
		$event->setArgument(0, $view);
	}

	/**
	 * Добавляет атрибут с id товара блоку block_product через js-скрипт,
	 * чтобы достучаться до нужного товара
	 *
	 * @param $event Event event object
	 *
	 * @throws Exception
	 * @since version
	 */
	public function onBeforeDisplaywtjshoppingfavoritesView(Event $event): void
	{
		/* @var $view object JoomShopping Product View object */
		$view = $event->getArgument(0);

		$this->addJs();
		$wa = $this->getApplication()->getDocument()->getWebAssetManager();
		$js = "
			document.addEventListener('DOMContentLoaded', () => {
			    let body = document.querySelector('body');
			    body.classList.add('wtjshoppingfavoritesView');
			    let elements = document.querySelectorAll('.wtjshoppingfavoritesView .block_product');

				Array.prototype.forEach.call(elements, function (el, i) {
					let product_id_tag = el.querySelectorAll('[data-favorite]');
					let product_id = product_id_tag[0].getAttribute('data-favorite');
					el.setAttribute('data-wt-jshop-favorite', product_id);
				});
			});
		";
		$wa->addInlineScript($js);
	}
}