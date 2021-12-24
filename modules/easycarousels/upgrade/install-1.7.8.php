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

function upgrade_module_1_7_8($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $carousels = $module_obj->db->executeS('SELECT * FROM '._DB_PREFIX_.'easycarousels');
    $multilang_rows = array();
    foreach ($carousels as $c) {
        $name_multilang = Tools::jsonDecode($c['name_multilang'], true);
        foreach ($name_multilang as $id_lang => $name) {
            $data = array('name' => $name, 'description' => '');
            $data = Tools::jsonEncode($data);
            $row = (int)$c['id_carousel'].', '.(int)$c['id_shop'].', '.(int)$id_lang.', \''.pSQL($data).'\'';
            $multilang_rows[] = '('.$row.')';
        }
    }
    $module_obj->prepareDatabaseTables();
    $module_obj->db->execute('REPLACE INTO '._DB_PREFIX_.'easycarousels_lang VALUES '.implode(', ', $multilang_rows));
    $module_obj->db->execute('ALTER TABLE '._DB_PREFIX_.'easycarousels DROP COLUMN name_multilang');

    return true;
}
