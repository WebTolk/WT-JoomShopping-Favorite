<?php
/**
 * @package     WT JoomShopping Favorite
 * @version     1.3.2
 * WT JoomShopping Favorites is an alternative wish list (favorite products) for JoomShopping based on coockies.
 * @Author Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2020 Sergey Tolkachyov
 * @license     GNU/GPL 3.0
 * @since 1.0.0
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Version;

class pkg_wt_jshopping_favoritesInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent)
    {
		$version = new Version;
			
		// only for Joomla 3.x

		if (version_compare($version->getShortVersion(), '4.0', '<')) {
			
			Factory::getApplication()->enqueueMessage('&#128546; <strong>WT JoomShopping Favorite</strong> package doesn\'t support Joomla versions <span class="alert-link">lower 4</span>. Your Joomla version is <span class="badge badge-important">'.$version->getShortVersion().'</span>','error');
			return false;

		} 	
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {
		
		
		
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {
		$version = new Version;
			
		// only for Joomla 3.x

		if (version_compare($version->getShortVersion(), '4.0', '<')) {
			
			Factory::getApplication()->enqueueMessage('&#128546; <strong>WT JoomShopping Favorite</strong> package doesn\'t support Joomla versions <span class="alert-link">lower 4</span>. Your Joomla version is <span class="badge badge-important">'.$version->getShortVersion().'</span>','error');
			return false;

		} 	
    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {

    }
	


    /**
     * Runs right after any installation action is performed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $installer) 
    {
		$smile = '';
		if($type != 'uninstall'){
			$smiles = ['&#9786;','&#128512;','&#128521;','&#128525;','&#128526;','&#128522;','&#128591;'];
			$smile_key = array_rand($smiles, 1);
			$smile = $smiles[$smile_key];
		}

		$object = new stdClass();
		$object->element = 'wtjshoppingfavorites';
		$object->type = 'plugin';
		$object->enabled = 1;
		$result = Factory::getDbo()->updateObject('#__extensions', $object, 'element');

				
		$element = strtoupper($installer->getElement());


		echo "
		<div class='row bg-white m-0' style='margin:25px auto; border:1px solid rgba(0,0,0,0.125); box-shadow:0px 0px 10px rgba(0,0,0,0.125); padding: 10px 20px;'>
		<div class='col-8' id='wt_download_id_form_wrapper'>
		<h2>".$smile." ".Text::_($element."_AFTER_".$type)." <br/>".Text::_($element)."</h2>
		".Text::_($element."_DESC");
		
		echo Text::_($element."_WHATS_NEW");

		echo "</div>
		<div class='col-4' style='display:flex; flex-direction:column; justify-content:center;'>
		<img width='200px' src='https://web-tolk.ru/web_tolk_logo_wide.png'>
		<p>Joomla Extensions</p>
		<p class='btn-group'>
			<a class='btn btn-sm btn-outline-primary' href='https://web-tolk.ru' target='_blank'> https://web-tolk.ru</a>
			<a class='btn btn-sm btn-outline-primary' href='mailto:info@web-tolk.ru'><i class='icon-envelope'></i> info@web-tolk.ru</a>
		</p>
		<p><a class='btn btn-primary' href='https://t.me/joomlaru' target='_blank'>Joomla Russian Community in Telegram</a></p>
		".Text::_($element."_MAYBE_INTERESTING")."
		</div>

		";	
		
    }
}