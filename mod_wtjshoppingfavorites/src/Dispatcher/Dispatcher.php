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
namespace Joomla\Module\Wtjshoppingfavorites\Site\Dispatcher;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Extension\ModuleInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Input\Input;
use Joomla\Registry\Registry;

class Dispatcher extends AbstractModuleDispatcher
{
    /**
     * The module extension. Used to fetch the module helper.
     *
     * @var   ModuleInterface|null
     * @since 1.0.0
     */
    private $moduleHelper;

    public function __construct(\stdClass $module, CMSApplicationInterface $app, Input $input)
    {
        parent::__construct($module, $app, $input);
        $this->moduleHelper = $this->app->bootModule('mod_wtjshoppingfavorites', 'site')->getHelper('WtjshoppingfavoritesHelper');
    }

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        $app = $this->getApplication();

        $data['moduleId'] = $data['module']->id;
        if (!empty($data['params']->get('moduleclass_sfx','')))
        {
            $data['moduleclass_sfx'] = htmlspecialchars($data['params']->get('moduleclass_sfx',''), ENT_COMPAT, 'UTF-8');
        }
        $data['product_ids'] = $this->moduleHelper->getProductList($data['params'], $app);


        $itemid = $this->moduleHelper->getItemid('com_jshopping', 'wtjshoppingfavorites');
        if (empty($itemid))
        {
            $itemid = \JSHelper::getDefaultItemid();
        }
        $data['itemid'] = '&Itemid=' . $itemid;

        $btn_icon_css_class = $data['params']->get('btn_icon_css_class', '');

        if (empty($btn_icon_css_class))
        {
            $plugin = PluginHelper::getPlugin('jshoppingproducts', 'wtjshoppingfavorites');
            if ($plugin)
            {
                $pluginParams = new Registry($plugin->params);
                $btn_icon_css_class = $pluginParams->get('btn_icon_css_class');
            }
        }
        $data['btn_icon_css_class'] = $btn_icon_css_class;

        /**
         * Take a css file for tmpl with the same name form media folder
         */

        $tmpl_css = explode(':', $data['params']->get('layout'));
        $tmpl_css_file = $tmpl_css[1];
        /* @var $wa WebAssetManager */
        $wa = $app->getDocument()->getWebAssetManager();
        if (file_exists('media/mod_wtjshoppingfavorites/css/' . $tmpl_css_file . '.css'))
        {
            $wa->registerAndUseStyle($data['module']->module . ':' . $tmpl_css_file, $data['module']->module . '/' . $tmpl_css_file . '.css');
        }
        else
        {
            $wa->registerAndUseStyle($data['module']->module . '.default', $data['module']->module . '/default.css');
        }
        
        return $data;
    }
}