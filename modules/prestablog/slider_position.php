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

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('prestablog.php');

$prestablog = new Prestablog();
$slides = array();

if (!Tools::getValue('action')) {
    die(1);
}

if (Tools::getValue('action') == 'updateSlidesPosition' && Tools::getValue('slides')) {
    $slides = Tools::getValue('slides');

    foreach ($slides as $position => $id_slide) {
        $res = Db::getInstance()->execute(
        '
      UPDATE `'._DB_PREFIX_.'prestablog_slide_lang` SET `position` = '.(int)$position.'
      WHERE `id_slide` = '.(int)$id_slide.' AND `id_lang` = '.Tools::getValue('languesup')
    );
    }

    $prestablog->clearCache();
}
