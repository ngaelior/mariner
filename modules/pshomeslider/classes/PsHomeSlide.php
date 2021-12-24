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

class PsHomeSlide extends ObjectModel
{
    public $id;
    public $image;
    public $title;
    public $description;
    public $text_position;
    public $text_background;
    public $url;
    public $open_new_tab;
    public $call_to_action;
    public $call_to_action_text;
    public $timer;
    public $date_start;
    public $date_end;
    public $active;
    public $position;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'pshomeslider',
        'primary' => 'id_slide',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            // Config fields
            'timer'      => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'date_start' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_end' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'active'   => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false),

            // Lang fields
            'image'               => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'title'               => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'description'         => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
            'text_position'       => array('type' => self::TYPE_INT, 'lang' => true, 'validate' => 'isInt', 'required' => false),
            'text_background'     => array('type' => self::TYPE_BOOL, 'lang' => true, 'validate' => 'isBool', 'required' => false),
            'url'                 => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'required' => false, 'size' => 255),
            'open_new_tab'        => array('type' => self::TYPE_BOOL, 'lang' => true, 'validate' => 'isBool', 'required' => false),
            'call_to_action'      => array('type' => self::TYPE_BOOL, 'lang' => true, 'validate' => 'isBool', 'required' => false),
            'call_to_action_text' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
        )
    );

    public function add($autoDate = true, $null_values = false)
    {
        $this->position = PsHomeSlide::getLastPosition();
        return parent::add($autoDate, $null_values);
    }

    public function update($nullValues = false)
    {
        if (parent::update($nullValues)) {
            return $this->cleanPositions((int) $this->id);
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            return $this->cleanPositions((int) $this->id);
        }
        return false;
    }

    /**
     * Get last position in order to add the newly added slide with the lastest position
     *
     * @return int position of the slide
     */
    public static function getLastPosition()
    {
        $sql = 'SELECT MAX(position) + 1 FROM `'._DB_PREFIX_.'pshomeslider`';

        return (Db::getInstance()->getValue($sql));
    }

    /**
     * Clean position of slides when deleting or update a slide.
     * It allow to avoid empty position like.
     *
     * @return bool true if success
     */
    public static function cleanPositions()
    {
        $sql = '
        SELECT `id_slide`
        FROM `'._DB_PREFIX_.'pshomeslider`
        ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'pshomeslider`
                    SET `position` = '.(int) $i.'
                    WHERE `id_slide` = '.(int) $result[$i]['id_slide'];
            Db::getInstance()->execute($sql);
        }

        return true;
    }

    /**
     * Get all slide detail in each language.
     * Construct a valid format to send it to vuejs
     *
     * @param int $idSlide
     *
     * @return array slide
     */
    public static function getSlide($idSlide)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'pshomeslider` pshs
                LEFT JOIN '._DB_PREFIX_.'pshomeslider_lang pshsl ON (pshs.id_slide = pshsl.id_slide)
                WHERE pshs.id_slide ='.(int)$idSlide;

        $result = Db::getInstance()->executeS($sql);

        $slideForm = array();
        foreach ($result as $key => $value) {
            $isoLang = Language::getIsoById($value['id_lang']);

            $slideForm['file'][$isoLang]['name'] = substr($value['image'], strrpos($value['image'], '/') + 1);
            $slideForm['filePreview'][$isoLang] = Tools::getShopDomain(true).__PS_BASE_URI__.$value['image'];
            $slideForm['slideTitle'][$isoLang] = $value['title'];
            $slideForm['slideText'][$isoLang] = $value['description'];
            $slideForm['textPosition'][$isoLang] = (int) $value['text_position'];
            $slideForm['textBackground'][$isoLang] = (bool) $value['text_background'];
            $slideForm['redirectUrl'][$isoLang] = $value['url'];
            $slideForm['openInNewTab'][$isoLang] = (bool) $value['open_new_tab'];
            $slideForm['callToAction'][$isoLang] = (bool) $value['call_to_action'];
            $slideForm['callToActionText'][$isoLang] = $value['call_to_action_text'];
            $slideForm['timer'] = (bool) $value['timer'];
            $slideForm['availableDate'] = null;
            if (!empty($value['date_start']) && !empty($value['date_end'])) {
                $slideForm['availableDate'] = array(
                    $value['date_start'],
                    $value['date_end']
                );
            }
        }

        return $slideForm;
    }

    /**
     * Get slide list in database
     *
     * @param int $id_lang
     * @param bool $active get only active slide or not
     *
     * @return array slides
     */
    public static function getAllSlide($id_lang, $active = false)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'pshomeslider` pshs
                LEFT JOIN '._DB_PREFIX_.'pshomeslider_lang pshsl ON (pshs.id_slide = pshsl.id_slide)
                WHERE pshsl.id_lang ='.(int)$id_lang;

        if ($active) {
            $sql .= ' AND pshs.active = 1';
        }

        $sql .= ' ORDER BY pshs.position';

        $result = Db::getInstance()->executeS($sql);

        if ($active) {
            $filteredResult = array();
            $i = 0;
            foreach ($result as $slide => $value) {
                $bool = true;
                if (empty($value['image'])) {
                    $bool = false;
                }

                if ($value['timer'] === '1') {
                    $checkDateStart = $value['date_start'] < date("Y-m-d H:i:s");
                    $checkDateEnd = $value['date_end'] > date("Y-m-d H:i:s");
                    if ($checkDateStart === false || $checkDateEnd === false) {
                        $bool = false;
                    }
                }

                if ($bool) {
                    $filteredResult[$i] = $value;
                    $i++;
                }
            }

            return $filteredResult;
        }

        return $result;
    }
}
