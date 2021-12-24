<?php
/**
 * HTML Blocks Prestashop Module
 * 
 * @author    Prestaddons <contact@prestaddons.fr>
 * @copyright 2016 Prestaddons
 * @license
 * @link      http://www.prestaddons.fr
 */

class Block extends ObjectModel
{
    /** @var string Name */
    public $date_from;
    public $date_to;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $id_hook;
    public $excluded_categories;
    public $new_window;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;

    public $title;
    public $content;
    public $link;
    public $link_title;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'html_block',
        'primary' => 'id_block',
        'multilang' => true,
        'fields' => array(
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'id_currency' => array('type' => self::TYPE_INT),
            'id_country' => array('type' => self::TYPE_INT),
            'id_group' => array('type' => self::TYPE_INT),
            'id_hook' => array('type' => self::TYPE_INT, 'required' => true),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'excluded_categories' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'new_window' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),

            /* Lang fields */
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => false,
                'size' => 128),
            'content' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isString',
                'size' => 3999999999999),
            'link' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isUrl',
                'required' => false,
                'size' => 256),
            'link_title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'required' => false,
                'size' => 128),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (version_compare(_PS_VERSION_, '1.5.2.0', '>')) {
            Shop::addTableAssociation('html_block', array('type' => 'shop'));
        }
        return parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->position = self::getLastPosition();
        $null_values = true;
        return parent::add($autodate, $null_values);
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            return $this->cleanPositions();
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            return $this->cleanPositions();
        }
        return false;
    }

    public static function getLastPosition()
    {
        $sql = '
        SELECT MAX(position) + 1
        FROM `'._DB_PREFIX_.'html_block`';

        return (Db::getInstance()->getValue($sql));
    }

    public static function cleanPositions()
    {
        $sql = '
        SELECT `id_block`
        FROM `'._DB_PREFIX_.'html_block`
        ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'html_block`
                    SET `position` = '.(int)$i.'
                    WHERE `id_block` = '.(int)$result[$i]['id_block'];
            Db::getInstance()->execute($sql);
        }

        return true;
    }

    public static function updateBlockPosition($id_block, $position)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'html_block`
                SET `position` = '.(int)$position.'
                WHERE `id_block` = '.(int)$id_block;

        Db::getInstance()->execute($sql);
    }

    public static function getBlocks(
        $order_by = '',
        $order_way = '',
        $active = true,
        $total = false,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($total) {
            $sql = 'SELECT COUNT(*) AS total
                    FROM `'._DB_PREFIX_.'html_block` hb
                    WHERE 1'.
                    ($active ? ' AND hb.active = 1' : '').
                    (Shop::isFeatureActive() ? ' AND hb.`id_block` IN (
						SELECT hb.id_block
						FROM `'._DB_PREFIX_.'html_block_shop` hbs
						WHERE hbs.id_shop IN ('.(int)$context->shop->id.'))' : '');

            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        $sql = 'SELECT *, hbl.title AS title, hbl.content AS content, hbl.link AS link, hbl.link_title AS link_title
				FROM `'._DB_PREFIX_.'html_block` hb
				LEFT JOIN `'._DB_PREFIX_.'html_block_lang` hbl ON (hb.`id_block` = hbl.`id_block` AND hbl.`id_lang` = '.
                (int)$context->language->id.') 
				WHERE 1'.
                ($active ? ' AND hb.active = 1' : '').
                (Shop::isFeatureActive() ? ' AND hb.`id_block` IN (
					SELECT hb.id_block
					FROM `'._DB_PREFIX_.'html_block_shop` hbs
					WHERE hbs.id_shop IN ('.(int)$context->shop->id.'))' : '');

        if ($order_by != '') {
            $sql .= ' ORDER BY '.pSQL($order_by).' '.pSQL($order_way).' ';
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function getBlocksByIdHook($id_hook, $active = true, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $sql = 'SELECT *, hbl.title AS title, hbl.content AS content, hbl.link AS link, hbl.link_title AS link_title
			FROM `'._DB_PREFIX_.'html_block` hb
			LEFT JOIN `'._DB_PREFIX_.'html_block_lang` hbl ON (hb.`id_block` = hbl.`id_block` AND hbl.`id_lang` = '.
                (int)$context->language->id.') 
			WHERE hb.id_hook = '.(int)$id_hook.
                ($active ? ' AND hb.active = 1 ' : '').
                (Shop::isFeatureActive() ? 'AND hb.`id_block` IN (
				SELECT hb.id_block
				FROM `'._DB_PREFIX_.'html_block_shop` hbs
				WHERE hbs.id_shop IN ('.(int)$context->shop->id.'))' : '').
                ' ORDER BY hb.position ASC';

        return Db::getInstance()->executeS($sql);
    }
    public static function updateExceptionsList($id_block, $liste)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'html_block`
                SET `excluded_categories` = "'.(string)$liste.'"
                WHERE `id_block` = '.(int)$id_block;

        Db::getInstance()->execute($sql);
    }
    public static function getExceptionsList($id_block)
    {
        $sql = 'SELECT `excluded_categories`
                FROM `'._DB_PREFIX_.'html_block`
                WHERE `id_block` = '.(int)$id_block;
        return Db::getInstance()->executeS($sql);
    }
}
