<?php
/**
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = ' CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pshomeslider` (
        `id_slide` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `timer` int(10) NOT NULL,
        `date_start` datetime,
        `date_end` datetime,
        `position` int(10) unsigned NOT NULL,
        `active` int(10) unsigned NOT NULL,
        PRIMARY KEY (`id_slide`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'pshomeslider_lang` (
        `id_slide` int(10) unsigned NOT NULL,
        `id_lang` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        `image` varchar(255) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` varchar(4000) NOT NULL,
        `text_position` int(10),
        `text_background` int(10),
        `url` varchar(255) NOT NULL,
        `open_new_tab` int(10) NOT NULL,
        `call_to_action` int(10) NOT NULL,
        `call_to_action_text` varchar(255),
        PRIMARY KEY (`id_slide`,`id_shop`,`id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

$sql[] = "INSERT INTO "._DB_PREFIX_."pshomeslider (id_slide, position, active)
        VALUES (1, 0, 1)";
$sql[] = "INSERT INTO "._DB_PREFIX_."pshomeslider (id_slide, position, active)
        VALUES (2, 1, 1)";
$sql[] = "INSERT INTO "._DB_PREFIX_."pshomeslider (id_slide, position, active)
        VALUES (3, 2, 1)";

$languages = Language::getLanguages(true);

foreach ($languages as $lang) {
    $sql[] = "INSERT INTO "._DB_PREFIX_."pshomeslider_lang (id_slide, id_lang, id_shop, title, description, text_position, text_background, url, open_new_tab, call_to_action, call_to_action_text, image)
            VALUES (1, ".$lang['id_lang'].", 1, 'This is a title', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque.', 3, 0, 'http://prestashop.com', 1, 0, '', '/modules/pshomeslider/slides/slide1.jpg')";

    $sql[] = "INSERT INTO "._DB_PREFIX_."pshomeslider_lang (id_slide, id_lang, id_shop, title, description, text_position, text_background, url, open_new_tab, call_to_action, call_to_action_text, image)
            VALUES (2, ".$lang['id_lang'].", 1, 'This is a title', 'Nam libero tempore cum soluta nobis est eligendi optio cumque nihil impedit.', 2, 1, 'http://prestashop.com', 1, 1, 'Discover', '/modules/pshomeslider/slides/slide2.jpg')";

    $sql[] = "INSERT INTO "._DB_PREFIX_."pshomeslider_lang (id_slide, id_lang, id_shop, title, description, text_position, text_background, url, open_new_tab, call_to_action, call_to_action_text, image)
            VALUES (3, ".$lang['id_lang'].", 1, 'This is a title', 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas.', 1, 0, 'http://prestashop.com', 1, 0, '', '/modules/pshomeslider/slides/slide3.jpg')";
}

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
