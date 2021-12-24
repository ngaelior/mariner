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

function upgrade_module_1_7_0($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }

    $files_to_remove = array(
        _PS_MODULE_DIR_.$module_obj->name.'/views/templates/admin/exceptions-settings-form.tpl',
    );
    foreach ($files_to_remove as $file_path) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // update db
    $has_general_settings = false;
    $columns = $module_obj->db->executeS('SHOW COLUMNS FROM '._DB_PREFIX_.'easycarousels');
    foreach ($columns as $c) {
        if ($c['Field'] == 'general_settings') {
            $has_general_settings = true;
        }
    }
    if ($has_general_settings) {
        $current_carousels = $module_obj->db->executeS('SELECT * FROM '._DB_PREFIX_.'easycarousels');
        foreach ($current_carousels as $c) {
            $general_settings = Tools::jsonDecode($c['general_settings'], true);
            $owl_settings = Tools::jsonDecode($c['owl_settings'], true);
            $current_settings = array();
            foreach ($owl_settings as $k => $s) {
                $current_settings['carousel'][$k] = $s;
            }
            if (isset($general_settings['rows'])) {
                $current_settings['carousel']['r'] = $general_settings['rows'];
            }
            if (isset($general_settings['items_in_carousel'])) {
                $current_settings['carousel']['total'] = $general_settings['items_in_carousel'];
            }
            foreach ($general_settings as $k => $s) {
                if (strpos($k, 'show_') !== false) {
                    $current_settings['tpl'][str_replace('show_', '', $k)] = $s;
                }
            }
            if (isset($general_settings['image_type'])) {
                $current_settings['tpl']['image_type'] = $general_settings['image_type'];
            }
            if (isset($general_settings['custom_class'])) {
                $current_settings['tpl']['custom_class'] = $general_settings['custom_class'];
            }

            $special_fields = array(
                'id_feature' => 'id_feature',
                'cat_ids' => 'cat_ids',
                'id_m' => 'id_manufacturer',
                'id_s' => 'id_supplier',
            );
            foreach ($special_fields as $prev_key => $new_key) {
                if (isset($general_settings[$prev_key])) {
                    $current_settings['special'][$new_key] = $general_settings[$prev_key];
                }
            }

            foreach (array('php', 'tpl', 'carousel', 'special') as $type) {
                foreach ($module_obj->getFields($type) as $name => $field) {
                    if (!isset($current_settings[$type][$name])) {
                        $current_settings[$type][$name] = $field['value'];
                    }
                }
            }
            $current_settings = Tools::jsonEncode($current_settings);
            $module_obj->db->execute('
                UPDATE '._DB_PREFIX_.'easycarousels
                SET general_settings = \''.pSQL($current_settings).'\'
                WHERE id_carousel = '.(int)$c['id_carousel'].' AND id_shop =  '.(int)$c['id_shop'].'
            ');
        }
    }

    $new_columns = array(
        'group_in_tabs' => 'in_tabs tinyint(1) NOT NULL DEFAULT 1',
        'carousel_type' => 'type varchar(128) NOT NULL',
        'general_settings' => 'settings text NOT NULL',
    );
    foreach ($columns as $c) {
        if (isset($new_columns[$c['Field']])) {
            $module_obj->db->execute('
                ALTER TABLE '._DB_PREFIX_.'easycarousels
                CHANGE '.pSQL($c['Field']).' '.pSQL($new_columns[$c['Field']]).'
            ');
        } elseif ($c['Field'] == 'owl_settings') {
            $module_obj->db->execute('ALTER TABLE '._DB_PREFIX_.'easycarousels DROP COLUMN owl_settings');
        }
    }
    $module_obj->db->execute('ALTER TABLE '._DB_PREFIX_.'easycarousels ADD INDEX (in_tabs)');
    $module_obj->db->execute('DROP INDEX group_in_tabs ON '._DB_PREFIX_.'easycarousels');

    $module_obj->addOverride('Product');
    Tools::generateIndex();

    return true;
}
