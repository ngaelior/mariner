<?php
/**
 * 2008 - 2020 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */

class SliderClass extends ObjectModel
{
    public $id;
    public $id_shop = 1;
    public $title;
    public $position;
    public $url_associate;

    public $group = [];

    protected $table = 'prestablog_slide';
    protected $identifier = 'id_slide';

    protected static $table_static = 'prestablog_slide';
    protected static $identifier_static = 'id_slide';

    public static $definition = [
        'table' => 'prestablog_slide',
        'primary' => 'id_slide',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255],
            'url_associate' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
        ],
    ];

    public static function isTableInstalled()
    {
        $table = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SHOW TABLES LIKE \'' . bqSQL(_DB_PREFIX_ . self::$table_static) . '%\'
            ');

        if (count($table) > 0) {
            return true;
        }

        return false;
    }

    public static function updateDatas($id, $id_lang, $old_lang, $title, $url_associate, $position)
    {
        if ($old_lang == $id_lang) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            UPDATE `' . _DB_PREFIX_ . 'prestablog_slide_lang`
            SET `title`= \'' . $title . '\', `url_associate`= \'' . $url_associate . '\', `position`= \'' . $position . '\'
            WHERE `id_slide` = \'' . (int) $id . '\' AND `id_lang`= \'' . $old_lang . '\'');
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            UPDATE `' . _DB_PREFIX_ . 'prestablog_slide_lang`
            SET `title`= \'' . $title . '\', `url_associate`= \'' . $url_associate . '\', `position`= \'' . $position . '\',`id_lang`= \'' . $id_lang . '\'
        WHERE `id_slide` = \'' . (int) $id . '\' AND `id_lang`= \'' . $old_lang . '\'');
        }
    }

    public static function checkLang($id, $id_lang, $old_lang)
    {
        if ($old_lang != $id_lang) {
            $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT *
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang` WHERE `id_slide` = \'' . $id . '\' AND `id_lang` = \'' . $id_lang . '\'');
            if (isset($return1[0]) && $return1[0] != null) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public static function checkPosition($id_slide, $position, $id_lang)
    {
        $return = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `position`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang`
            WHERE `id_slide`= ' . (int) $id_slide . ' AND `id_lang`= ' . (int) $id_lang);

        if ($position != $return[0]['position']) {
            $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_slide`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang`
            WHERE `position`= ' . (int) $position . ' AND `id_lang`= ' . (int) $id_lang);
            if (isset($return1[0]) && $return1[0] != null) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public static function slideGetLang($id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_lang`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang`
            WHERE `id_slide`= ' . (int) $id);
    }

    public static function getIdLastSlide()
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_slide` FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` ORDER BY id_slide DESC LIMIT 1');

        return $return1[0]['id_slide'];
    }

    public function addTableSlide($id_shop)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            INSERT INTO `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide`
            (`id_shop`)
                VALUES
                (' . $id_shop . ')');
    }

    public function addTableSlideLang($title, $url_associate, $id_lang)
    {
        $returnId = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_slide` FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` ORDER BY id_slide DESC LIMIT 1');
        $returnId2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_slide` FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` ORDER BY id_slide DESC LIMIT 2');
        $lang_taken = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_lang` FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang` WHERE `id_slide` = \'' . $returnId[0]['id_slide'] . '\'');
        for ($i = 0; isset($lang_taken[$i]); $i++) {
            if ($lang_taken[$i] == $id_lang) {
                $taken = 1;
            }
        }
        $position_autoadd = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `position` FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang` WHERE `id_slide` = \'' . $returnId2[1]['id_slide'] . '\' AND `id_lang` = \'' . $id_lang . '\'');

        if (isset($returnId2[1]['id_slide']) && $returnId2[1]['id_slide'] != '') {
            $position = (int) $position_autoadd + 1;
        } else {
            $position = 1;
        }

        if (isset($taken) && $taken = 1) {
            return false;
        } else {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            INSERT INTO `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang`
            (`id_slide`, `title`, `url_associate`, `id_lang`, `position`)
                VALUES
                (\'' . $returnId[0]['id_slide'] . '\',\'' . $title . '\',\'' . $url_associate . '\',\'' . $id_lang . '\',\'' . $position . '\')');
        }
    }

    public function copyFromPost()
    {
        $object = $this;
        $table = $this->table;

        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $object) && $key != 'id_' . $table) {
                if ($key == 'passwd' && Tools::getValue('id_' . $table) && empty($value)) {
                    continue;
                }
                if ($key == 'passwd' && !empty($value)) {
                    $value = Tools::encrypt($value);
                }
                $object->{$key} = Tools::getValue($key);
            }
        }

        $rules = call_user_func([get_class($object), 'getValidationRules'], get_class($object));
        if (count($rules['validateLang'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($rules['validateLang']) as $field) {
                    if (Tools::getIsset($field . '_' . (int) $language['id_lang'])) {
                        $object->{$field}[(int) $language['id_lang']] = Tools::getValue(
                            $field . '_' . (int) $language['id_lang']
                        );
                    }
                }
            }
        }
    }

    public static function getListSlider($shopId, $id_lang = null)
    {
        if ($id_lang) {
            $txt = ' AND sl.`id_lang` = ' . (int) $id_lang;
        } else {
            $txt = '';
        }

        return Db::getInstance()->ExecuteS('
            SELECT sl.id_slide, sl.id_lang, sl.position, sl.title
            FROM `' . _DB_PREFIX_ . 'prestablog_slide_lang` sl
            INNER JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` s on s.id_slide = sl.id_slide
            WHERE s.id_shop = ' . $shopId . $txt . '
            ORDER BY position ASC');
    }

    public static function getTitle($id, $id_lang, $shopId)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT sl.`title`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang` sl
            INNER JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` s on s.id_slide = sl.id_slide
            WHERE sl.`id_slide`= ' . (int) $id . ' AND `id_lang` =' . (int) $id_lang . '  AND s.id_shop = ' . $shopId);

        if (isset($return1[0]['title'])) {
            return $return1[0]['title'];
        }
    }

    public static function getURL($id, $id_lang, $shopId)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT sl.`url_associate`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang` sl
            INNER JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` s on s.id_slide = sl.id_slide
            WHERE sl.`id_slide`= ' . (int) $id . ' AND sl.`id_lang` =' . (int) $id_lang . '  AND s.id_shop = ' . $shopId);

        if (isset($return1[0]['url_associate'])) {
            return $return1[0]['url_associate'];
        }
    }

    public static function getPosition($id, $id_lang, $shopId)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT sl.`position`
            FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide_lang` sl
            INNER JOIN `' . bqSQL(_DB_PREFIX_) . 'prestablog_slide` s on s.id_slide = sl.id_slide
            WHERE s.`id_slide` = ' . (int) $id . ' AND sl.`id_lang` = ' . (int) $id_lang . '  AND s.id_shop = ' . $shopId);

        if (isset($return1[0]['position'])) {
            return $return1[0]['position'];
        }
    }

    public static function getAllSlider($shopId, $id_lang = null)
    {
        $lang = '';
        if (empty($id_lang)) {
            $lang = 'sl.`id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT');
        } elseif (is_array($id_lang)) {
            if (count($id_lang) > 0) {
                foreach ($id_lang as $lang_id) {
                    $lang = 'sl.`id_lang` = ' . (int) $lang_id . ' ';
                }
            }
        } else {
            if ((int) $id_lang == 0) {
                $lang = '';
            } else {
                $lang = 'sl.`id_lang` = ' . (int) $id_lang;
            }
        }
        $test = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_slide
            FROM `' . _DB_PREFIX_ . 'prestablog_slide`');

        if (isset($test[0]) && $test[0] != '') {
            $return2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
                SELECT sl.id_slide, sl.title, sl.url_associate, sl.position
                FROM `' . _DB_PREFIX_ . 'prestablog_slide_lang` sl
                INNER JOIN `' . _DB_PREFIX_ . 'prestablog_slide` s on s.id_slide = sl.id_slide
                WHERE ' . $lang . ' AND s.id_shop = ' . $shopId . '
                ORDER BY position ASC');
        } else {
            $return2[0] = 0;
        }

        return $return2;
    }

    public function registerTablesBdd()
    {
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_slide_lang` (
            `' . bqSQL($this->identifier) . '` int(10) unsigned NOT null,
            `title` varchar(255),
            `url_associate` varchar(255),
            `id_lang` tinyint(1),
            `position` int(10),
            PRIMARY KEY (`' . bqSQL($this->identifier) . '`, `id_lang`))
            ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_slide` (
            `' . bqSQL($this->identifier) . '` int(10) unsigned NOT null auto_increment,
            `id_shop` tinyint(1),
            PRIMARY KEY (`' . bqSQL($this->identifier) . '`))
            ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')) {
            return false;
        }

        $langues = Language::getLanguages(true);
        if (count($langues) > 0) {
            $langue_use = [];
            foreach ($langues as $value) {
                $langue_use[] = (int) $value['id_lang'];
            }

            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
                INSERT INTO `' . _DB_PREFIX_ . 'prestablog_slide`
                (`' . bqSQL($this->identifier) . '`, `id_shop`)
                VALUES
                (1, 1)')) {
                return false;
            }

            $title = [
                1 => 'Curabitur venenatis ut elit quis tempus, sed eget sem pretium',
            ];

            $url_associate = [
                1 => 'http://curabitur.fr/fr/blog/curabitur-venenatis-ut-elit-quis-tempus-sed-eget-sem-pretium-n1',
            ];

            $sql_values = 'VALUES ';
            for ($i = 1; $i <= 1; $i++) {
                foreach ($langues as $value) {
                    $sql_values .= '
                    (
                    1,
                    \'' . $title[$i] . '\',
                    \'' . pSQL($url_associate[$i]) . '\',
                    ' . (int) $value['id_lang'] . ',
                    0
                ),';
                }
            }

            $sql_values = rtrim($sql_values, ',');
            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            INSERT INTO `' . _DB_PREFIX_ . 'prestablog_slide_lang`
            (
            `' . bqSQL($this->identifier) . '`,
            `title`,
            `url_associate`,
            `id_lang`,
            `position`
            )
            ' . $sql_values)) {
                return false;
            }
        }

        return true;
    }

    public function removeLang($id, $id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
        DELETE FROM `' . _DB_PREFIX_ . 'prestablog_slide_lang`
        WHERE `id_slide` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);
    }

    public function remove($id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
        DELETE FROM `' . _DB_PREFIX_ . 'prestablog_slide`
        WHERE `id_slide` = ' . (int) $id);
    }

    public function deleteTablesBdd()
    {
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
        DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_slide`
        ')) {
            return false;
        }
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
        DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_slide_lang`
        ')) {
            return false;
        }

        return true;
    }
}
