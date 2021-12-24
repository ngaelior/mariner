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

function upgrade_module_2_5_0($module_obj)
{
    // add columns for new exceptions mechanism
    $module_obj->db->execute('
        ALTER TABLE '._DB_PREFIX_.'ec_hook_settings
        ADD exc_type tinyint(1) NOT NULL DEFAULT 1 AFTER display,
        ADD exc_controllers text NOT NULL AFTER exc_type
    ');
    // prepare exceptions data
    $exc_data = $module_obj->db->executeS('
        SELECT hme.*, h.name AS hook_name
        FROM '._DB_PREFIX_.'hook_module_exceptions hme
        LEFT JOIN '._DB_PREFIX_.'hook h ON h.id_hook = hme.id_hook
        WHERE id_module = '.(int)$module_obj->id.'
    ');
    $hook_exceptions = array();
    foreach ($exc_data as $d) {
        $id_shop = $d['id_shop'];
        $hook_name = $d['hook_name'];
        $hook_exceptions[$id_shop][$hook_name][] = $d['file_name'];
    }
    foreach ($hook_exceptions as $id_shop => $exceptions) {
        foreach ($exceptions as $hook_name => $exc_controllers) {
            $module_obj->saveExceptions($hook_name, 1, $exc_controllers, array($id_shop));
        }
    }
    return true;
}
