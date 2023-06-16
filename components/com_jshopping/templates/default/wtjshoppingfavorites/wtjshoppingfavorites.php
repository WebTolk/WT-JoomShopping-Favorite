<?php
/**
 * @package     WT JoomShopping Favorite
 * @version     1.0.0
 * WT JoomShopping Favorites is an alternative wish list (favorite products) for JoomShopping based on coockies.
 * @Author Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2020 Sergey Tolkachyov
 * @license     GNU/GPL 3.0
 * @since 1.0.0
 */
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
$doc = Factory::getDocument();

?>

<div class="jshop" id="comjshop">
    	<h1><?php print Text::_('PLG_WTJSHOPPINGFAVORITES') ?></h1>
		<button class="wt_jshop_favorite_btn_clean <?php echo $this->clear_favorites_btn_css_class;?>" id="wt-jshopping-favorite-empty-list" type="button"><?php print Text::_('PLG_WTJSHOPPINGFAVORITES_BTN_CLEAN_FAVORITES') ?></button>
	    <?php if ($this->rows) : ?>
        <div class="jshop_list_product">
            <?php
            if (count($this->rows)){
                include(dirname(__FILE__)."/../".$this->template_block_list_product);
            }else{
                include(dirname(__FILE__)."/../".$this->template_no_list_product);
            }
            if ($this->display_pagination){
                include(dirname(__FILE__)."/../".$this->template_block_pagination);
            }
            ?>
        </div>
    <?php else: ?>
	    <p><?php print Text::_('PLG_WTJSHOPPINGFAVORITES_NO_PRODUCTS') ?></p>
    <?php endif; ?>
</div>