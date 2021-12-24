<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Product extends ProductCore
{
    /*
    * Third argument $context is required for retro-compatibility
    */
    public function getAccessories($id_lang, $active = true, Context $context = null)
    {
        $context = $context ? $context : Context::getContext();
        $accessories_carousel = false;
        if (Tools::substr(_PS_VERSION_, 0, 3) === '1.7') {
            if (Tools::getValue('controller') == 'product' && Module::isEnabled('easycarousels')) {
                $accessories_carousel = (bool)Db::getInstance()->getValue('
                    SELECT * FROM '._DB_PREFIX_.'easycarousels WHERE type = \'accessories\'
                    AND id_shop = '.(int)$context->shop->id.' AND active = 1
                ');
            }
        } elseif (!empty($context->accessories_displayed)) {
            $accessories_carousel = true;
        }
        return $accessories_carousel ? array() : parent::getAccessories($id_lang, $active, $context);
    }
}
