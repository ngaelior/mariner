<?php
/**
 * HTML Blocks Prestashop Module
 * 
 * @author    Prestaddons <contact@prestaddons.fr>
 * @copyright 2016 Prestaddons
 * @license
 * @link      http://www.prestaddons.fr
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'html_block` (
            `id_block` int(11) unsigned NOT NULL AUTO_INCREMENT ,
            `date_from` datetime NOT NULL,
			`date_to` datetime NOT NULL,
            `id_currency` int(10) unsigned NOT NULL,
			`id_country` int(10) unsigned NOT NULL,
			`id_group` int(10) unsigned NOT NULL,
			`id_hook` int(10) unsigned NOT NULL,
            `excluded_categories` text,
            `new_window` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			`position` int(11) unsigned NOT NULL DEFAULT \'0\',
            `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_block`)
        ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'html_block_lang` (
            `id_block` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
			`title` VARCHAR(128),
            `content` text,
            `link` VARCHAR(256),
            `link_title` VARCHAR(128),
            PRIMARY KEY (`id_block`,`id_lang`)
        ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'html_block_shop` (
            `id_block` int(11) unsigned NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
            PRIMARY KEY (`id_block`,`id_shop`)
        ) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
