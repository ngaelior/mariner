<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * usefull when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_1_1_0($module)
{
    /**
     * Update psHomeSlider table
     */
    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'pshomeslider`
        ADD `timer` int(10) unsigned NOT NULL AFTER `active`,
        ADD `date_start` datetime NOT NULL AFTER `timer`,
        ADD `date_end` datetime NOT NULL AFTER `date_start`,
        DROP `action`,
        DROP `button_text_color`,
        DROP `button_background_color`,
        DROP `button_background_color_hover`,
        DROP `content_backgound`');

    /**
     * Update psHomeSlider Lang table
     */
    Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'pshomeslider_lang`
        CHANGE `image` `image` varchar(255) NOT NULL AFTER `id_shop`,
        CHANGE `title` `title` varchar(255) NOT NULL AFTER `image`,
        CHANGE `description` `description` varchar(4000) NOT NULL AFTER `title`,
        DROP `button_text`,
        DROP `text_horizontal_position`,
        DROP `text_vertical_position`,
        ADD `text_position` int(10) NOT NULL AFTER `description`,
        ADD `text_background` int(10) NOT NULL AFTER `text_position`,
        CHANGE `url` `url` varchar(255) NOT NULL AFTER `text_background`,
        ADD `open_new_tab` int(10) NOT NULL,
        ADD `call_to_action` int NOT NULL AFTER `open_new_tab`,
        ADD `call_to_action_text` varchar(255) NOT NULL AFTER `call_to_action`');

    /**
     * Add the configuration fiels in the configuration's table
     */
    foreach ($module->configurationFields as $key => $value) {
        Configuration::updateValue($key, $value);
    }

    /**
     * Update slides to be valid
     */
    $update = array(
        'text_position' => 1
    );

    Db::getInstance()->update('pshomeslider_lang', $update, '1');
 
    return true;
}
