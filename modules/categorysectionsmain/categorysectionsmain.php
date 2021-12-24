<?php
/**
* 2007-2018 PrestaShop
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
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Categorysectionsmain extends Module
{
    protected $config_form = false;
    private $currentProductId;
    public function __construct()
    {
        $this->name = 'categorysectionsmain';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'Pro Business';
        $this->module_key = '760202068a51566fac1bde9bb2ddc6fa';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Category sections on main page');
        $this->description = $this->l('Category sections on main page');

        $this->confirmUninstall = $this->l('All information will be deleted, are you sure?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->templateFile = 'module:categorysectionsmain/views/templates/hook/categorysectionsmain.tpl';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $this->_clearCache('*');


        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('displayHome') &&
            $this->installModuleTab();
    }

    public function uninstall()
    {
        $this->_clearCache('*');
        $this->uninstallModuleTab();
        include(dirname(__FILE__).'/sql/uninstall.php');
     
        return parent::uninstall();
    }

    protected function installModuleTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminCategorysectionsmain';
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentThemes');
        $tab->module = 'categorysectionsmain';
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int)$lang['id_lang']] = $this->l('Sections main page');
        }
        if (!$tab->save()) {
            return false;
        }
       
        return true;
    }
    
    protected function uninstallModuleTab()
    {
        if ($id_tab = Tab::getIdFromClassName('AdminCategorysectionsmain')) {
            $tab = new Tab((int) $id_tab);
            $tab->delete();
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $class = 'Admin'.get_class($this);
        if (!$this->context->link) {
            /* Server Params */
            $ssl = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'));
            $protocol = $ssl ? 'https://' : 'http://';

            $this->context->link = new Link($protocol, $protocol);
        }

        $url = $this->context->link->getAdminLink($class);
        Tools::redirectAdmin($url);
    }
 
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookdisplayHome()
    {
        $id_lang =(int)$this->context->language->id;
        $id_shop = (int)Shop::getContextShopID();
        $section_product = array();
        $sql_section = Db::getInstance()->executeS('SELECT l.id_category, l.count_products, ll.title, ll.products
                FROM '._DB_PREFIX_.'categorysectionsmain l
                LEFT JOIN '._DB_PREFIX_.'categorysectionsmain_lang ll ON (l.id_categorysectionsmain = ll.id_categorysectionsmain)
                LEFT JOIN '._DB_PREFIX_.'categorysectionsmain_shop shop ON (l.id_categorysectionsmain = shop.id_categorysectionsmain)
                WHERE l.status = 1
                AND ll.id_lang = '.(int)$id_lang.' AND shop.id_shop='.(int)$id_shop.' ORDER BY shop.position ASC');
     
        foreach ($sql_section as $key => $value) {
            if (empty(unserialize($value['products'])) && (int)$value['id_category']) {
                $section_product[$key]['title'] = $value['title'];
                $section_product[$key]['id_category'] = $value['id_category'];
                $section_product[$key]['products'] = $this->getProducts((int)$value['id_category'], (int)$value['count_products']);
            }
            if (!empty(unserialize($value['products']))) {
                $section_product[$key]['title'] = $value['title'];
                $section_product[$key]['id_category'] = $value['id_category'];
                $section_product[$key]['products'] = $this->getViewedProducts(unserialize($value['products']));
            }
        }
    
        if (!empty($section_product)) {
            $this->smarty->assign(
                array(
                    'section_product' => $section_product,
                )
            );
            return $this->fetch($this->templateFile, $this->getCacheId('categorysectionsmain'));
        }
    }

    public function hookdisplayFooterProduct($params)
    {
        $id_lang =(int)$this->context->language->id;
        $id_shop = (int)Shop::getContextShopID();
        $section_product = array();
        $sql_section = Db::getInstance()->executeS('SELECT l.id_category, l.count_products, ll.title, ll.products
                FROM '._DB_PREFIX_.'categorysectionsmain l
                LEFT JOIN '._DB_PREFIX_.'categorysectionsmain_lang ll ON (l.id_categorysectionsmain = ll.id_categorysectionsmain)
                LEFT JOIN '._DB_PREFIX_.'categorysectionsmain_shop shop ON (l.id_categorysectionsmain = shop.id_categorysectionsmain)
                WHERE l.status = 1
                AND ll.id_lang = '.(int)$id_lang.' AND l.id_category_view = '.(int)$params['product']['id_category_default'].' AND shop.id_shop='.(int)$id_shop.' ORDER BY shop.position ASC');
         
        foreach ($sql_section as $key => $value) {
            if (empty(unserialize($value['products'])) && (int)$value['id_category']) {
                $section_product[$key]['title'] = $value['title'];
                $section_product[$key]['id_category'] = $value['id_category'];
                $section_product[$key]['products'] = $this->getProducts((int)$value['id_category'], (int)$value['count_products']);
            }
            if (!empty(unserialize($value['products']))) {
                $section_product[$key]['title'] = $value['title'];
                $section_product[$key]['id_category'] = $value['id_category'];
                $section_product[$key]['products'] = $this->getViewedProducts(unserialize($value['products']));
            }
        }
        

        if (!empty($section_product)) {
            $this->smarty->assign(
                array(
                    'section_product' => $section_product,
                )
            );
            return $this->display($this->local_path, 'views/templates/hook/footerproduct.tpl');
        }
    }

    protected function getProducts($id_category, $number_products = 8)
    {
        $category = new Category((int)$id_category);

        $searchProvider = new CategoryProductSearchProvider(
            $this->context->getTranslator(),
            $category
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = (int)$number_products;
        if ($nProducts < 0) {
            $nProducts = 12;
        }

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        $query->setSortOrder(SortOrder::random());

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = array();

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }
  
        return $products_for_template;
    }

    protected function getViewedProducts($productIds)
    {
        $productIds = $productIds;
        if (!empty($productIds)) {
            $assembler = new ProductAssembler($this->context);
            $presenterFactory = new ProductPresenterFactory($this->context);
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter = new ProductListingPresenter(
                new ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );
            $products_for_template = array();
            if (is_array($productIds)) {
                foreach ($productIds as $productId) {
                    if ($this->currentProductId !== $productId) {
                        $products_for_template[] = $presenter->present(
                            $presentationSettings,
                            $assembler->assembleProduct(array('id_product' => $productId)),
                            $this->context->language
                        );
                    }
                }
            }
            return $products_for_template;
        }
        return false;
    }
}
