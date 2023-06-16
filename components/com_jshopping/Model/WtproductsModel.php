<?php
/**
 * Модель для работы расширений [Web-tolk.ru](https://web-tolk.ru):
 * @version      1.1.0 Apr 2022
 * @author       Sergey Tolkachyov
 * @package      Jshopping
 * @copyright    Copyright (C) 2022 Sergey Tolkachyov. All rights reserved.
 * @license      GNU/GPL
 *
 */
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

class WtproductsModel
{

	/**
	 * Подсчитывает количество товаров в массиве
	 * @return int
	 *
	 * @since 1.0.0
	 */
	public function getTotal(): int
	{
		if (!empty($this->products))
		{
			return count($this->products);
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Функция выбирает товары JoomShopping из базы данных по списку id
	 *
	 * @param   array  $product_ids  Простой одномерный массив [1, 2, 3, ...]
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function loadProductsByIds(array $product_ids): void
	{

		$lang = \JSFactory::getLang();

		if (count($product_ids) > 0)
		{
			//@todo Вычистить запрос, оставить выборку только самого нужного
			$query = "SELECT * FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                  WHERE prod.product_id IN(" . implode(',', $product_ids) . ") AND prod.product_publish=1 ORDER BY FIELD(prod.product_id, ".implode(',', $product_ids).")";
			$db    = Factory::getDBO();
			$db->setQuery($query);

			$products = $db->loadObjectList();

			// Добавляем вендоров, производителей, пути к картинкам и т.д.
			// Второй параметр - setUrl - добавляет в объект товара ссылки на товар и на покупку (в корзину)
			$products = \JSHelper::listProductUpdateData($products, 1);

			Factory::getApplication()->triggerEvent('onBeforeDisplayProductList', array(&$products));

			foreach ($products as $product)
			{
				$name                       = $lang->get('name');
				$product->name              = $product->$name;
				$short_description          = $lang->get('short_description');
				$product->short_description = $product->$short_description;
				$description                = $lang->get('description');
				$product->description       = $product->$description;
			}
			// Готовый массив
			$this->products = $products;

		}

	}

	/**
	 * Функция добавляет кнопку избранного для избранных товаров.
	 * Для WT JoomShopping Favorites. Начиная с версии 1.2.0.
	 * Настройки берутся из плагина WT JoomShopping favorites группы jshoppingproducts
	 * @return void
	 * @since 1.0.0
	 */
	public function addWtFavoritesButtonToProducts(): void
	{

		// Если включен плагин добавления в избранное

		if (PluginHelper::isEnabled('jshoppingproducts', 'wtjshoppingfavorites') === true)
		{

			$plugin               = PluginHelper::getPlugin('jshoppingproducts', 'wtjshoppingfavorites');
			$params               = (!empty($plugin->params) ? json_decode($plugin->params) : '');
			$link_css_class       = $params->link_css_class;
			$product_list_tmp_var = $params->product_list_tmp_var;
			$btn_type             = $params->in_product_btn_type;
			if ($btn_type == "button")
			{
				$tag = "button";
			}
			else
			{
				$tag = "a";
			}
			if (count($this->products) > 0)
			{

				foreach ($this->products as $product)
				{
					$product->$product_list_tmp_var = "<" . $tag . " " . (($tag == "a") ? "href='#'" : '') . "  class='" . $link_css_class . " selected' id='favorite_button" . $product->product_id . "' data-favorite='" . $product->product_id . "'><i class=\"" . $params->btn_icon_css_class . "\"></i></" . $tag . ">";
				}
			}
		}
	}


	/**
	 * Функция добавляет кнопку добавления в сравнения для товаров.
	 * Для WT JoomShopping Compare. Начиная с версии 1.0.0.
	 * Настройки берутся из плагина WT JoomShopping compare группы jshoppingproducts
	 * @return void
	 * @since 1.0.0
	 */
	public function addWtCompareButtonToProducts(): void
	{

		// Если включен плагин добавления в избранное

		if (PluginHelper::isEnabled('jshoppingproducts', 'wtjshoppingcompare') === true)
		{

			$plugin               = PluginHelper::getPlugin('jshoppingproducts', 'wtjshoppingcompare');
			$params               = (!empty($plugin->params) ? json_decode($plugin->params) : '');
			$link_css_class       = $params->link_css_class;
			$product_list_tmp_var = $params->product_list_tmp_var;
			$btn_type             = $params->in_product_btn_type;
			if ($btn_type == "button")
			{
				$tag = "button";
			}
			else
			{
				$tag = "a";
			}
			if (count($this->products) > 0)
			{

				foreach ($this->products as $product)
				{
					$product->$product_list_tmp_var = "<" . $tag . " " . (($tag == "a") ? "href='#'" : '') . "  class='" . $link_css_class . " selected' id='compare_button" . $product->product_id . "' data-compare='" . $product->product_id . "'><i class=\"" . $params->btn_icon_css_class . "\"></i></" . $tag . ">";
				}
			}
		}
	}

	/**
	 * Функция загружает список характеристик для сравнения товаров.
	 * Берем показ характеристик для карточки товара и применяем её для списка товаров
	 * Присваивание характеристик идёт для конкретного товара. В контроллере метод дёргается циклом.
	 *
	 * @param $product_id   string|int  id товара
	 *
	 * @return array                    массив с характеристиками
	 */
	public function getProductExtraFields($product_id): array
	{
		$product = \JSFactory::getTable('product', 'jshop');
		$product->load($product_id);
		$extra_fields = $product->getExtraFields();
		return $extra_fields;
	}


	/**
	 * Функция пересобирает массив с характеристиками и товарами для более удобной работы в сравнеии товаров
	 * WT JShopping Compare. Для этого нужно пересобрать массив с другой иерархией:
	 * - Группа характеристик (если есть, если группы нет - приравниваем характеристику к группе)
	 *     + характеристика товара
	 *         - id товара = значение характеристики
	 *         - id товара = значение характеристики
	 *         - id товара = значение характеристики
	 *
	 *
	 * @param   $rows   array   products array
	 *
	 * @return  array   Массив вышеуказанной структуры
	 */
	public function rebuildProductRowsWithExtraFieldsForCompare($rows):array
	{
		$extra_fields = array();

		foreach ($rows as $row)
		{
			foreach ($row->extra_field as $extra_field_id => $extra_field_array){

				if(!empty($extra_field_array['groupname'])){
					$extra_fields[$extra_field_array['groupname']][$extra_field_id]['name'] = $extra_field_array['name'];
					$extra_fields[$extra_field_array['groupname']][$extra_field_id]['description'] = $extra_field_array['description'];
					$extra_fields[$extra_field_array['groupname']][$extra_field_id]['products_extra_fields_values'][$row->product_id] = $extra_field_array['value'];
				} else{
					$extra_fields[$extra_field_id]['name'] = $extra_field_array['name'];
					$extra_fields[$extra_field_id]['description'] = $extra_field_array['description'];
					$extra_fields[$extra_field_id]['products_extra_fields_values'][$row->product_id] = $extra_field_array['value'];
				}


			}
		}
		return $extra_fields;
	}

	/**
	 * Функция назначает layout в шаблоне для списка товаров
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getTmplBlockListProduct(): string
	{
		return \JSFactory::getConfig()->default_template_block_list_product;
	}

	/**
	 * Функция назначает layout в шаблоне для списка товаров, когда нет товаров в категории
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getTmplNoListProduct(): string
	{
		return \JSFactory::getConfig()->default_template_no_list_product;
	}

	/**
	 * Функция назначает layout для стандартных фильтров JoomShopping в шаблоне для списка товаров
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getTmplBlockFormFilter(): string
	{
		return \JSFactory::getConfig()->default_template_block_form_filter_product;
	}

	/**
	 * Функция назначает layout пагинации в шаблоне для списка товаров
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getTmplBlockPagination(): string
	{
		return \JSFactory::getConfig()->default_template_block_pagination_product;
	}

}