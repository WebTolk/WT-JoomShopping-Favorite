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
namespace Joomla\Component\Jshopping\Site\Controller;

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Component\Jshopping\Site\Model\WtproductsModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Profiler\Profiler;
use Joomla\Registry\Registry;

class WtjshoppingfavoritesController extends BaseController{

	public function display($cachable = false, $urlparams = false): void
    {
        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>WT JoomShopping Favorites</strong>: '.__CLASS__.' '.__FUNCTION__.' start');
        $this->view();
        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>WT JoomShopping Favorites</strong>: '.__CLASS__.' '.__FUNCTION__.' end');
    }

	public function view(): void
    {
        $jshopConfig = \JSFactory::getConfig();
        $lang        = Factory::getLanguage();
        $lang->load('plg_jshoppingproducts_wtjshoppingfavorites', JPATH_ADMINISTRATOR, null, true);
        PluginHelper::importPlugin('jshoppingproducts');
        Factory::getApplication()->triggerEvent('onBeforeDisplayWtjshoppingfavorites', array(&$this));
        $view_name   = "wtjshoppingfavorites";
        $view_config = array("template_path" => JPATH_COMPONENT . "/templates/" . $jshopConfig->template . "/" . $view_name);
        $view        = $this->getView($view_name, \JSHelper::getDocumentType(), '', $view_config);
        $view->setLayout("wtjshoppingfavorites");
        $view->config = $jshopConfig;

        $app = Factory::getApplication();

        $params     = $app->getParams();
        $menuParams = new Registry();
        $menu       = $app->getMenu()->getActive();
        if ($menu)
        {
            $menuParams->loadString($menu->getParams());
        }
        $mergedParams = clone $menuParams;
        $mergedParams->merge($params);

        $view->clear_favorites_btn_css_class = $mergedParams->get('clear_favorites_btn_css_class','btn btn-primary');
        $view->_tmp_list_products_html_start = '';
        $view->_tmp_list_products_html_end  = '';

        $product_ids = $app->input->cookie->get('wtjshoppingfavorites', null, 'string');
        if(!empty($product_ids))
        {
            $product_ids = unserialize($product_ids);
        }
        else
        {
            $product_ids = [];
        }

        $product_list = new \stdClass();
        if(!empty($product_ids))
        {
            $wt_products = new WtproductsModel;
            $wt_products->loadProductsByIds($product_ids);

            $view->rows = $wt_products->products;
            $product_list->products = $wt_products->products;
            $view->config->show_sort_product          = "0"; //Отключаем показ фильтров и сортировки
            $view->config->show_count_select_products = "0";
            $view->template_block_list_product = $wt_products->getTmplBlockListProduct();
            $view->template_no_list_product = $wt_products->getTmplNoListProduct();
            $view->template_block_form_filter = $wt_products->getTmplBlockFormFilter();
            $view->template_block_pagination = $wt_products->getTmplBlockPagination();
            $view->count_product_to_row = $jshopConfig->count_products_to_row;
            $view->image_category_path = $jshopConfig->image_category_live_path;
            $view->noimage = $jshopConfig->noimage;
            $view->shippinginfo = \JSHelper::SEFLink($jshopConfig->shippinginfourl, 1);
            $view->total = $wt_products->getTotal();
            $view->display_pagination = false;
            $review = \JSFactory::getTable('review');
            $view->allow_review = $review->getAllowReview();

        }
        else
        {
            $view->config = $jshopConfig;
            $view->rows = [];
            $product_list->products = [];

        }

        \JSHelper::setMetaData(Text::_('PLG_WTJSHOPPINGFAVORITES'), '', $mergedParams->get('menu-meta_description'), $mergedParams);
        Factory::getApplication()->triggerEvent('onBeforeDisplayProductListView',array(&$view, &$product_list));
        Factory::getApplication()->triggerEvent('onBeforeDisplaywtjshoppingfavoritesView',array(&$view, &$product_list));
        $view->display();
    }
}
?>