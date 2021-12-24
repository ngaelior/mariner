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

function upgrade_module_2_0_3($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $all = $module_obj->db->executeS('SELECT * FROM '._DB_PREFIX_.'easycarousels');
    $rows = array();
    foreach ($all as $a) {
        $settings = Tools::jsonDecode($a['settings'], true);
        if (isset($settings['carousel']['activate_carousel'])) {
            $type = $settings['carousel']['activate_carousel'];
            unset($settings['carousel']['activate_carousel']);
        } else {
            $type = 1;
        }
        $settings['carousel']['type'] = $settings['carousel_m']['type'] = $type;
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
