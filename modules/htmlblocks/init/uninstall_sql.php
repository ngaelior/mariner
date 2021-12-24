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

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'html_block`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'html_block_lang`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'html_block_shop`';
