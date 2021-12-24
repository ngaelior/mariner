<?php
/**
* 2007-2019 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2019 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

function upgrade_module_2_5_5($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $all = $module_obj->db->executeS('SELECT * FROM '._DB_PREFIX_.'easycarousels');
    $rows = array();
    foreach ($all as $a) {
        $settings = Tools::jsonDecode($a['settings'], true);
        $settings['php']['order_by'] = !empty($settings['php']['randomize']) ? 'random' : 'default';
        $settings = Tools::jsonEncode($settings);
        $rows[] = '('.(int)$a['id_carousel'].', '.(int)$a['id_shop'].', \''.pSQL($settings).'\')';
    }
    $module_obj->db->execute('
        INSERT INTO '._DB_PREFIX_.'easycarousels (id_carousel, id_shop, settings)
        VALUES '.implode(', ', $rows).'
        ON DUPLICATE KEY UPDATE
        settings = VALUES(settings)
    ');
    return true;
}
