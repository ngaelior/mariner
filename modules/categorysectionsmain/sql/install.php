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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'categorysectionsmain` (
			`id_categorysectionsmain` int(11) NOT NULL AUTO_INCREMENT,
			`id_category` INT(11) UNSIGNED NOT NULL,
			`id_category_view` INT(11) UNSIGNED NOT NULL,
			`status` TINYINT( 1 ) NOT NULL,
			`count_products` INT(11) UNSIGNED NOT NULL,
			`position` INT(11) UNSIGNED NOT NULL,
			PRIMARY KEY (`id_categorysectionsmain`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'categorysectionsmain_lang` (
			`id_categorysectionsmain` INT(11) UNSIGNED NOT NULL,
			`id_lang` INT(11) UNSIGNED NOT NULL,	
			`title` VARCHAR( 255 ) NOT NULL,
			`products` longtext NOT NULL,
			PRIMARY KEY(`id_categorysectionsmain` , `id_lang`),
			INDEX ( `id_categorysectionsmain` , `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'categorysectionsmain_shop` (
			`id_categorysectionsmain` INT(11) UNSIGNED NOT NULL,
			`id_shop` INT(11) UNSIGNED NOT NULL,
			`position` INT(11) UNSIGNED NOT NULL,
			PRIMARY KEY(`id_categorysectionsmain` , `id_shop`),
			INDEX ( `id_categorysectionsmain` , `id_shop`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
