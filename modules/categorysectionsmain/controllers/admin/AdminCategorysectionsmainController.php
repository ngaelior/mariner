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

require_once(_PS_MODULE_DIR_ . 'categorysectionsmain/classes/CategoryObj.php');
class AdminCategorysectionsmainController extends ModuleAdminController
{
    public $active = true;
    public $title;
    protected $position_identifier = 'id_categorysectionsmain';
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;
        $this->table = 'categorysectionsmain';
        $this->list_id = 'id_categorysectionsmain';
        $this->identifier = 'id_categorysectionsmain';
        $this->className = 'CategoryObj';
        $this->explicitSelect = true;
        $this->_defaultOrderBy = 'll.position';
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_where = Shop::getContextShopID() ? "AND ll.id_shop = ".Shop::getContextShopID().' GROUP BY ll.`id_categorysectionsmain`' : "GROUP BY ll.`id_categorysectionsmain`";
        $this->_select = 'll.id_shop AS shop';
        $this->_join = '
        LEFT JOIN `' . _DB_PREFIX_ . 'categorysectionsmain_shop` ll ON (ll.`id_categorysectionsmain` = a.`id_categorysectionsmain`)';
        $this->allow_export = true;
        $this->allow_add = false;
        $this->context = Context::getContext();
        $this->toolbar_title = $this->title;
        $this->_use_found_rows = true;
        $this->fields_list = array(
            'id_categorysectionsmain' => array(
                'title' => 'ID',
                'search' => false,
                'class' => 'fixed-width-xs',
            ),
            'id_category' => array(
                'title' => 'Category',
                'search' => false,
                'callback' => 'setCategory',
            ),
            'count_products' => array(
                'title' => 'Count products',
                'search' => false,
            ),
            
            'title' => array(
                'title' => 'Title',
                'search' => true
            ),

            'status' => array(
                'title' => 'Status',
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false
            ),
            'position' => array(
                'title' => 'Position',
                'filter_key' => 'll!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-md',
              
            ),
        );

        if (Shop::isFeatureActive()) {
            $this->fields_list = array_merge($this->fields_list, array(
                'shop' => array(
                    'title' => 'Shop',
                    'callback' => 'setShop',
                ),
            ));
        }
        
        parent::__construct();
        
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            unset($this->fields_list['position']);
        }
    }
    
    public function setShop($id_shop)
    {
        $shop = new Shop((int)$id_shop);
        return $shop->name;
    }

    public function setCategory($id)
    {
        if ((int)$id != 0) {
            $categor = new Category((int)$id);
            return $id.' | '.$categor->name[Configuration::get('PS_LANG_DEFAULT')];
        } else {
            return '-';
        }
    }
 

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) (Tools::getValue('way'));
        $id_categorysectionsmain = (int)(Tools::getValue('id'));
        $positions = Tools::getValue('categorysectionsmain');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] == $id_categorysectionsmain) {
                if ($categorysectionsmain = new CategoryObj((int)$pos[2])) {
                    if (isset($position) && $categorysectionsmain->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for carrier ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update carrier ' . (int) $id_categorysectionsmain . ' to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This carrier (' . (int) $id_categorysectionsmain . ') can t be loaded"}';
                }

                break;
            }
        }
        Tools::clearSmartyCache();
    }
    
    public function ajaxProcessSearchSectionsmain()
    {
        if (Tools::strlen(Tools::getValue('name')) >= 3) {
            $products = Product::searchByName($this->context->language->id, Tools::getValue('name'));
            die(json_encode($products));
        }
    }
    
    public function renderForm()
    {
        if (!($categorysectionsmain = $this->loadObject(true))) {
            return;
        }
        Tools::clearSmartyCache();
        $products_s_wiev = array();
        if (Tools::isSubmit('updatecategorysectionsmain')) {
            $products_s = Db::getInstance()->getValue('SELECT products
                FROM '._DB_PREFIX_.'categorysectionsmain_lang 
                WHERE id_categorysectionsmain = '.(int)$categorysectionsmain->id);
            $products_s = unserialize($products_s);
            if ($products_s) {
                foreach ($products_s as $key => $value) {
                    $prod = new Product($value);
                    $products_s_wiev[$key]['id'] = $value;
                    $products_s_wiev[$key]['name'] = $prod->name[$this->context->language->id];
                }
            }
        }
        $selected_categories = array();
        if (Tools::getIsset('updatecategorysectionsmain') && !Tools::getValue('updatecategorysectionsmain')) {
            $link = CategoryObj::getTemplateLang((int)Tools::getValue('id_categorysectionsmain'));
            $selected_categories = array('0' => $link );
        }
        $selected_categories_view = array();
        if (Tools::getIsset('updatecategorysectionsmain') && !Tools::getValue('updatecategorysectionsmain')) {
            $link = CategoryObj::getTemplateLangView((int)Tools::getValue('id_categorysectionsmain'));
            $selected_categories_view = array('0' => $link );
        }

        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('categories_col2');
        $tree->setUseCheckBox(false)
             ->setAttribute('is_category_filter', $root->id)
             ->setRootCategory($root->id)
             ->setSelectedCategories($selected_categories_view)
             ->setInputName('id_category_view');
        $categoryTreeCol2 = $tree->render();
        $this->context->smarty->assign(
            array(
                'products_s' => $products_s_wiev,
                'token' => $this->token,
            )
        );
        $product_field_tpl_s = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . 'categorysectionsmain/views/templates/admin/addproduct.tpl',
            $this->tpl_view_vars
        );
        $this->fields_value = array(
             'width' => 0
        );
        $this->warnings[] = $this->trans('If a category is selected, the selected products will not be displayed');
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Add section')
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'status',
                    'is_bool' => 1,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type'  => 'categories',
                    'label' => $this->l('Category'),
                    'name'  => 'id_category',
                    'required' => false,
                    'desc'    => $this->l('Show products of the selected category'),
                    'tree'  => array(
                        'id'                  => 'categories-tree',
                        'selected_categories' => $selected_categories,
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => 'Title section',
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'rows' => 5,
                    'cols' => 100,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('The number of items in section'),
                    'name' => 'count_products',
                    'class' => 'fixed-width-xs',
                ),
                array(
                    'type' => 'categories_select',
                    'label' => $this->l('Attach products'),
                    'category_tree' => $product_field_tpl_s->fetch(),
                    'name' => 'is_parent',
                    'desc' => $this->l('Show products in section'),
                   
                ),
                array(
                   'type'  => 'categories_select',
                   'label' => $this->l('Show on the item card'),
                   'desc'    => $this->l('Display in the product card of the selected category'),
                   'name'  => 'id_category_view',
                   'category_tree'  => $categoryTreeCol2
                ),

            ),

            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }
       
        return parent::renderForm();
    }





    public function ajaxProcessStatusCategorysectionsmain()
    {
        if (!$id_categorysectionsmain = (int) Tools::getValue('id_categorysectionsmain')) {
            die(json_encode(array(
                'success' => false,
                'error' => true,
                'text' => $this->trans('Failed to update the status', array(), 'Admin.Notifications.Error')
            )));
        } else {
            $categorysectionsmain = new CategoryObj((int)$id_categorysectionsmain);
            if (Validate::isLoadedObject($categorysectionsmain)) {
                $categorysectionsmain->status = $categorysectionsmain->status == 1 ? 0 : 1;
                $categorysectionsmain->save() ? die(json_encode(array(
                    'success' => true,
                    'text' => $this->trans('The status has been updated successfully', array(), 'Admin.Notifications.Success')
                ))) : die(json_encode(array(
                    'success' => false,
                    'error' => true,
                    'text' => $this->trans('Failed to update the status', array(), 'Admin.Notifications.Success')
                )));
            }
        }
        Tools::clearSmartyCache();
    }



   
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_banner'] = array(
                'href' => self::$currentIndex.'&addcategorysectionsmain&token='.$this->token,
                'desc' => $this->l('Add new section'),
                'icon' => 'process-icon-new'
            );
        }
    }
}
