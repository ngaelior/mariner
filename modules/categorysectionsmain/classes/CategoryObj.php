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
 *  @author    Pro Business <tim9898@ya.ru>
 *  @copyright 2007-2019 Pro Business
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CategoryObj extends ObjectModel
{
    public $id_categorysectionsmain;

    public $id_category = 0;
    
    public $status = 1;

    public $title;
    
    public $count_products;

    public $products;

    public $id_category_view = 0;
    
    public static $definition = array(
        'table' => 'categorysectionsmain',
        'primary' => 'id_categorysectionsmain',
        'multilang' => true,
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedInt','required' => false),
            'id_category_view' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedInt','required' => false),
            'status' => array('type' => self::TYPE_BOOL,'validate' => 'isBool','required' => true),
            'count_products' => array('type' => self::TYPE_INT,'validate' => 'isPhoneNumber','required' => false),
            /* Lang fields */
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'products' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            )
        );
    
    public function add($autodate = true, $null_values = true)
    {
        $this->products = serialize(Tools::getValue('products'));
        $success = parent::add($autodate, $null_values);
        if (Tools::isSubmit('checkBoxShopAsso_categorysectionsmain')) {
            foreach (Tools::getValue('checkBoxShopAsso_categorysectionsmain') as $idShop) {
                $position = (int) CategoryObj::getLastPosition((int)$idShop);
                $this->addPosition($position, $idShop);
            }
        } else {
            foreach (Shop::getShops(true) as $shop) {
                $position = (int) CategoryObj::getLastPosition($shop['id_shop']);
                $this->addPosition($position, $shop['id_shop']);
            }
        }
        return $success;
    }
    

    public static function getLastPosition($idShop)
    {
        $results = Db::getInstance()->executeS('
                SELECT 1
                FROM `' . _DB_PREFIX_ . 'categorysectionsmain` c
                 JOIN `' . _DB_PREFIX_ . 'categorysectionsmain_shop` cs
                ON (c.`id_categorysectionsmain` = cs.`id_categorysectionsmain` AND cs.`id_shop` = ' . (int) $idShop . ')');

        if (count($results) === 1) {
            return 0;
        } else {
            $maxPosition = (int) Db::getInstance()->getValue('
                SELECT MAX(cs.`position`)
                FROM `' . _DB_PREFIX_ . 'categorysectionsmain` c
                LEFT JOIN `' . _DB_PREFIX_ . 'categorysectionsmain_shop` cs
                ON (c.`id_categorysectionsmain` = cs.`id_categorysectionsmain` AND cs.`id_shop` = ' . (int) $idShop . ')');

            return 1 + $maxPosition;
        }
    }

    public function addPosition($position, $idShop = null)
    {
        $return = true;
        if (is_null($idShop)) {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                foreach (Shop::getContextListShopID() as $idShop) {
                    $return &= Db::getInstance()->execute('
                        INSERT INTO `' . _DB_PREFIX_ . 'categorysectionsmain_shop` (`id_categorysectionsmain`, `id_shop`, `position`) VALUES
                        (' . (int) $this->id . ', ' . (int) $idShop . ', ' . (int) $position . ')
                        ON DUPLICATE KEY UPDATE `position` = ' . (int) $position);
                }
            } else {
                $id = Context::getContext()->shop->id;
                $idShop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');
                $return &= Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'categorysectionsmain_shop` (`id_categorysectionsmain`, `id_shop`, `position`) VALUES
                    (' . (int) $this->id . ', ' . (int) $idShop . ', ' . (int) $position . ')
                    ON DUPLICATE KEY UPDATE `position` = ' . (int) $position);
            }
        } else {
            $return &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'categorysectionsmain_shop` (`id_categorysectionsmain`, `id_shop`, `position`) VALUES
            (' . (int) $this->id . ', ' . (int) $idShop . ', ' . (int) $position . ')
            ON DUPLICATE KEY UPDATE `position` = ' . (int) $position);
        }

        return $return;
    }

    public function update($null_values = true)
    {
        $success = parent::update($null_values);
       
        if (Tools::isSubmit('checkBoxShopAsso_categorysectionsmain')) {
            Db::getInstance()->delete('categorysectionsmain_shop', 'id_categorysectionsmain = ' . (int) $this->id);
          
            foreach (Tools::getValue('checkBoxShopAsso_categorysectionsmain') as $idShop) {
                $position = (int) CategoryObj::getLastPosition((int)$idShop);
                $this->addPosition($position, $idShop);
            }
        } else {
            if (Shop::isFeatureActive()) {
                throw new PrestaShopException('Select a store');
            }
        }

        
        Db::getInstance()->update('categorysectionsmain_lang', array(
                'products' => pSQL(serialize(Tools::getValue('products'))),
            ), 'id_categorysectionsmain = ' . (int)$this->id);
        return $success;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT cp.`id_categorysectionsmain`, categorysectionsmain_shop.`position`
            FROM `' . _DB_PREFIX_ . 'categorysectionsmain` cp
            INNER JOIN `' . _DB_PREFIX_ . 'categorysectionsmain_shop` categorysectionsmain_shop 
            ON (categorysectionsmain_shop.id_categorysectionsmain = cp.id_categorysectionsmain 
            AND categorysectionsmain_shop.id_shop = '.(int) Shop::getContextShopID().')
            ORDER BY categorysectionsmain_shop.`position` ASC'
        )) {
            return false;
        }


        $movedCategory = false;
        foreach ($res as $category) {
            if ((int) $category['id_categorysectionsmain'] == (int) $this->id) {
                $movedCategory = $category;
            }
        }

        if ($movedCategory === false) {
            return false;
        }

        return Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'categorysectionsmain` c  
            INNER JOIN `' . _DB_PREFIX_ . 'categorysectionsmain_shop` categorysectionsmain_shop 
            ON (categorysectionsmain_shop.id_categorysectionsmain = c.id_categorysectionsmain 
            AND categorysectionsmain_shop.id_shop = '.(int) Shop::getContextShopID().')
            SET c.`position`= c.`position` ' . ($way ? '- 1' : '+ 1') . ',
            categorysectionsmain_shop.`position`= categorysectionsmain_shop.`position` ' . ($way ? '- 1' : '+ 1') . '
            WHERE categorysectionsmain_shop.`position`
            ' . ($way
                ? '> ' . (int) $movedCategory['position'] . ' AND categorysectionsmain_shop.`position` <= ' . (int) $position
                : '< ' . (int) $movedCategory['position'] . ' AND categorysectionsmain_shop.`position` >= ' . (int) $position))
        && Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'categorysectionsmain` c 
            INNER JOIN `' . _DB_PREFIX_ . 'categorysectionsmain_shop` categorysectionsmain_shop 
            ON (categorysectionsmain_shop.id_categorysectionsmain = c.id_categorysectionsmain 
            AND categorysectionsmain_shop.id_shop = '.(int) Shop::getContextShopID().')
            SET c.`position` = ' . (int) $position . ',
            categorysectionsmain_shop.`position` = ' . (int) $position . '
            WHERE c.`id_categorysectionsmain` = ' . (int) $movedCategory['id_categorysectionsmain']);
    }

    public static function getTemplateLang($id_categorysectionsmain)
    {
        $ret = Db::getInstance()->getValue('
            SELECT id_category
            FROM '._DB_PREFIX_.'categorysectionsmain
            WHERE id_categorysectionsmain = '.(int)$id_categorysectionsmain);
        return $ret;
    }


    public static function getTemplateLangView($id_categorysectionsmain)
    {
        $ret = Db::getInstance()->getValue('
            SELECT id_category_view
            FROM '._DB_PREFIX_.'categorysectionsmain
            WHERE id_categorysectionsmain = '.(int)$id_categorysectionsmain);
        return $ret;
    }

    public function delete()
    {
        if ((int) $this->id === 0) {
            return false;
        }
        return Db::getInstance()->delete('categorysectionsmain_shop', 'id_categorysectionsmain = ' . (int) $this->id);
    }
}
