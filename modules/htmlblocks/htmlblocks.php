<?php
/**
 * HTML Blocks Prestashop Module
 * 
 * @author    Prestaddons <contact@prestaddons.fr>
 * @copyright 2016 Prestaddons
 * @license
 * @link      http://www.prestaddons.fr
 */

class HtmlBlocks extends Module
{
    /*
     * Pattern for matching config values
     */
    protected $pattern = '/^([A-Z_]*)[0-9]+/';

    /** @var string html Output */
    protected $html = '';

    /** @var array post_errors Errors on forms */
    protected $post_errors = array();

    /** @var array $spacer_size Spacer for tree files */
    protected $spacer_size = '5';

    /** @var bool $is_version15 Prestashop is under 1.5 version */
    public $is_version15;

    /** @var bool $is_version16 Prestashop is under 1.6 version */
    public $is_version16;

    /** @var bool $is_version16 Prestashop is under 1.7 version */
    public $is_version17;

    /**
     * Constructeur de la classe HtmlBlocks
     */
    public function __construct()
    {
        require_once (dirname(__file__).'/classes/Block.php');

        $this->name = 'htmlblocks';
        $this->short_name = 'hb';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.8');
        $this->bootstrap = true;
        $this->module_key = '560802b70a00b002069c1cee07f66280';

        parent::__construct();

        $this->displayName = $this->l('HTML Blocks - Customizable content blocks');
        $this->description = $this->l('Add multiple customizable blocks for your content on your shop.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the HTML Blocks module?');
        $this->author = $this->l('Prestaddons');
        $this->contact = 'contact@prestaddons.fr';
        $this->addons_url = 'https://addons.prestashop.com/contact-form.php?id_product=23640';
        $this->is_version15 = $this->checkPSVersion();
        $this->is_version16 = $this->checkPSVersion('1.6.0.0');
        $this->is_version17 = $this->checkPSVersion('1.7.0.0');
    }

    /**
     * Méthode install()
     * 
     * Gère l'installation du module
     * 
     * @return bool True si l'installation a fonctionné, false dans le cas contraire
     */
    public function install()
    {
        //$params = $this->initFixtures();

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
                || !$this->registerHook('displayHeader')
                || !$this->registerHook('displayBackOfficeHeader')) {
            return false;
        }

        $sql = array();
        include (dirname(__file__).'/init/install_sql.php');
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return false;
            }
        }

        $this->generateCSS();

        return true;
    }

    /**
     * Méthode initFixtures()
     * 
     * Initialise tous les paramètres nécessaires à l'installation du module
     * 
     * @return array $params Tableau contenant les paramètres nécessaires à l'installation
     */
    private function initFixtures()
    {
        //$id_lang_en = Language::getIdByIso('en');
        //$id_lang_fr = Language::getIdByIso('fr');

        $params = array();

        return $params;
    }

    /**
     * Méthode uninstall()
     * 
     * Gère la désinstallation du module
     * 
     * @return bool True si la désinstallation a fonctionné, false dans le cas contraire
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        $sql = array();
        include (dirname(__file__).'/init/uninstall_sql.php');
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Méthode postValidation()
     * 
     * Contrôle les variables saisies dans le backoffice et définit les éventuelles erreurs à afficher
     * 
     * @return void
     */
    private function postValidation()
    {
        if (Tools::isSubmit('submitadd'.$this->name) || Tools::isSubmit('submitupdate'.$this->name)) {
            if (!Validate::isDateFormat(Tools::getValue('date_from'))) {
                $this->post_errors[] = $this->l('Beginning date must be a valid date');
            }
            if (!Validate::isDateFormat(Tools::getValue('date_to'))) {
                $this->post_errors[] = $this->l('Ending date must be a valid date');
            }
        }
    }

    /**
     * Méthode postProcess()
     * 
     * Traitement des informations saisies dans le backoffice
     * Traitements divers, mise à jour la base de données, définition des messages d'erreur ou de confirmation...
     * 
     * @return void
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submitadd'.$this->name) || Tools::isSubmit('submitupdate'.$this->name)) {
            $id_block = Tools::getvalue('id_block');
            $date_from = Tools::getvalue('date_from');
            $date_to = Tools::getvalue('date_to');
            $id_currency = Tools::getvalue('id_currency');
            $id_country = Tools::getvalue('id_country');
            $id_group = Tools::getvalue('id_group');
            $new_window = Tools::getvalue('new_window');
            $id_hook = Tools::getvalue('id_hook');
            $active = Tools::getvalue('active');

            if (Tools::isSubmit('submitadd'.$this->name)) {
                $block = new Block();
                $block->excluded_categories = '';
            } elseif (Tools::isSubmit('submitupdate'.$this->name)) {
                $block = new Block($id_block);
                $old_id_hook = $block->id_hook;
            }

            if ($date_from != '') {
                $block->date_from = $date_from;
            }
            if ($date_to != '') {
                $block->date_to = $date_to;
            }

            $block->id_currency = $id_currency;
            $block->id_country = $id_country;
            $block->id_group = $id_group;
            $block->new_window = $new_window;
            $block->id_hook = $id_hook;
            $block->active = $active;

            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $block->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
                $block->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
                $block->link[$language['id_lang']] = Tools::getValue('link_'.$language['id_lang']);
                $block->link_title[$language['id_lang']] = Tools::getValue('link_title_'.$language['id_lang']);
            }

            if (Tools::isSubmit('submitadd'.$this->name)) {
                $block->add();
                $this->addCssFile("#html_block_".$block->id."{"."\n\n"."}", $block->id);
                if ($this->is_version15) {
                    $this->registerHook(htmlblocks::getNameById($id_hook));
                } else {
                    $this->registerHook(Hook::getNameById($id_hook));
                }
                $this->html .= $this->displayConfirmation($this->l('HTML Block has been added'));
            } elseif (Tools::isSubmit('submitupdate'.$this->name)) {
                if ($old_id_hook != $id_hook) {
                    $this->unregisterHook($old_id_hook);
                    if ($this->is_version15) {
                        $this->registerHook(htmlblocks::getNameById($id_hook));
                    } else {
                        $this->registerHook(Hook::getNameById($id_hook));
                    }
                }
                $block->update();
                $this->html .= $this->displayConfirmation($this->l('HTML Block has been updated'));
            }
        } elseif (Tools::isSubmit('updatePositions')) {
            $this->updatePositionsDnd();
        }
    }

    /**
     * Méthode getContent()
     * 
     * Gère l'administration du module dans le backoffice
     * Dispatch vers les différentes méthodes en fonctions des cas 
     * (affichage des formulaires, des erreurs, des confirmations, ...)
     * 
     * @return string HTML de la partie backoffice du module
     */
    public function getContent()
    {

        $this->postValidation();

        if (!count($this->post_errors)) {
            $this->postProcess();
        } else {
            foreach ($this->post_errors as $err) {
                $this->html .= $this->displayError($err);
            }
        }

        /* if (Tools::isSubmit('submit'.$this->name))
          $this->html .= $this->renderList();
          else */
        if (Tools::isSubmit('exceptionsmanagement')) {
            $this->html .= $this->renderExceptionsForm();
        } elseif (Tools::isSubmit('configuratecss')) {
            $this->html .= $this->renderCssConfigForm();
        } elseif (Tools::isSubmit('add'.$this->name)
                || Tools::isSubmit('update'.$this->name)
                || (count($this->post_errors))) {
            $this->html .= $this->renderForm();
        } elseif (Tools::isSubmit('support'.$this->name)) {
            $this->html .= $this->renderSupportForm();
        } else {
            if (Tools::isSubmit('deletehtmlblocks')) {
                $this->html .= $this->deleteBlock();
            }
            if (Tools::isSubmit('statushtmlblocks')) {
                $this->changeStatus();
            }

            $this->html .= $this->renderList();
        }

        $this->html = $this->getButtonsTpl().$this->html;
        return $this->html;
    }

    public function updatePositionsDnd()
    {
        if (Tools::getValue('module-htmlblocks')) {
            $positions = Tools::getValue('module-htmlblocks');
        } else {
            $positions = array();
        }
        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2])) {
                Block::updateBlockPosition($pos[2], $position);
            }
        }
    }

    /**
     * Méthode renderList()
     * 
     * Affiche la liste principale d'éléments du module dans le backoffice
     * 
     * @return object helper
     */
    public function renderList()
    {
        $helper = $this->initList();
        return $helper->generateList($this->getListContent(), $this->fields_list);
    }

    /**
     * Méthode initList()
     * 
     * Initialise la liste principale d'éléments du module dans le backoffice
     * 
     * @return object helper
     */
    protected function initList()
    {
        $this->fields_list = array();

        if (Shop::isFeatureActive()) {
            $this->fields_list['id_shop'] = array(
                'title' => $this->l('ID Shop'),
                'align' => 'center',
                'type' => 'int',
                'search' => false
            );
        }

        $this->fields_list['id_block'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'search' => true
        );
        $this->fields_list['position'] = array(
            'title' => $this->l('Position'),
            'width' => 40,
            'align' => 'center',
            'position' => 'position',
            'search' => false)
        ;
        $this->fields_list['title'] = array(
            'title' => $this->l('Title'),
            'orderby' => false,
            'search' => false
        );
        $this->fields_list['hook_name'] = array(
            'title' => $this->l('Hook'),
            'orderby' => true,
            'search' => true
        );
        $this->fields_list['date_from'] = array(
            'title' => $this->l('From'),
            'type' => 'datetime',
            'align' => 'center',
            'search' => true
        );
        $this->fields_list['date_to'] = array(
            'title' => $this->l('To'),
            'type' => 'datetime',
            'align' => 'center',
            'search' => true
        );
        $this->fields_list['active'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => true,
            'search' => false
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_block';

        // Enable drag & drop on position field
        $helper->table_id = 'module-'.$this->name;
        $helper->position_identifier = 'position';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';

        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        //$helper->imageType = 'jpg';
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='
            .$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );

        $helper->toolbar_btn['edit'] = array(
            'href' => AdminController::$currentIndex.'&configure='
            .$this->name.'&configuratecss&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Configure CSS')
        );

        if ($this->is_version16) {
            $helper->toolbar_btn['anchor'] = array(
                'href' => AdminController::$currentIndex.'&configure='
                .$this->name.'&exceptionsmanagement&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Exeptions management')
            );
        } else {

            $helper->toolbar_btn['new-url'] = array(
                'href' => AdminController::$currentIndex.'&configure='
                .$this->name.'&exceptionsmanagement&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Exeptions management')
            );
        }

        $helper->toolbar_btn['help-new'] = array(
            'href' => AdminController::$currentIndex.'&configure='
            .$this->name.'&support&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Support')
        );

        $helper->tpl_vars['icon'] = 'icon-file-code-o';
        $helper->title = $this->displayName;
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->listTotal = count($this->getListContent($this->context->cookie->id_lang));

        return $helper;
    }

    public function renderChoicesSelect()
    {
        $spacer = str_repeat('&nbsp;', $this->spacer_size);
        $items = $this->getMenuItems();

        $html = '<optgroup label="'.$this->l('CMS').'">';
        $html .= $this->getCMSOptions(0, 1, $this->context->language->id, $items);
        $html .= '</optgroup>';

        // BEGIN SUPPLIER
        $html .= '<optgroup label="'.$this->l('Supplier').'">';
        // Option to show all Suppliers
        $html .= '<option value="ALLSUP0">'.$this->l('All suppliers').'</option>';
        $suppliers = Supplier::getSuppliers(false, $this->context->language->id);
        foreach ($suppliers as $supplier) {
            if (!in_array('SUP'.$supplier['id_supplier'], $items)) {
                $html .= '<option value="SUP'.$supplier['id_supplier'].'">'.$spacer.$supplier['name'].'</option>';
            }
        }
        $html .= '</optgroup>';

        // BEGIN Manufacturer
        $html .= '<optgroup label="'.$this->l('Manufacturer').'">';
        // Option to show all Manufacturers
        $html .= '<option value="ALLMAN0">'.$this->l('All manufacturers').'</option>';
        $manufacturers = Manufacturer::getManufacturers(false, $this->context->language->id);
        foreach ($manufacturers as $manufacturer) {
            if (!in_array('MAN'.$manufacturer['id_manufacturer'], $items)) {
                $html .= '<option value="MAN'.$manufacturer['id_manufacturer'].'">'
                    .$spacer
                    .$manufacturer['name']
                    .'</option>';
            }
        }
        $html .= '</optgroup>';

        // BEGIN Categories

        $html .= '<optgroup label="'.$this->l('Categories').'">';

        $shops_to_get = Shop::getContextListShopID();

        foreach ($shops_to_get as $shop_id) {
            $html .= $this->generateCategoriesOption(
                $this->customGetNestedCategories($shop_id, null, (int)$this->context->language->id, false),
                $items
            );
        }
        $html .= '</optgroup>';



        // BEGIN Products
        $html .= '<optgroup label="'.$this->l('Products').'">';
        $html .= '<option value="PRODUCT">'.$spacer.$this->l('Choose product ID').'</option>';
        $html .= '</optgroup>';


        $controllers = DispatcherCore::getControllers(_PS_FRONT_CONTROLLER_DIR_);
        ksort($controllers);

        $html .= '<optgroup label="'.$this->l('Core').'">';

        $i = 0;
        foreach ($controllers as $k => $v) {
            if ($k == 'auth') {
                $k = 'authentication';
                $v = $v;
            }
            $html .= '<option value="CORE'.(int)$i.'" >'.$k.'</option>';
            $i ++;
        }

        if ($this->checkPSVersion('1.6.0.5') && method_exists('Dispatcher', 'getModuleControllers')) {
            $modules_controllers_type = array('front' => $this->l('Front modules controller'));
            foreach ($modules_controllers_type as $type => $label) {
                $html .= '<optgroup label="'.$this->l($label).'">';
                $all_modules_controllers = Dispatcher::getModuleControllers($type);
                foreach ($all_modules_controllers as $module => $modules_controllers) {
                    $j = 0;
                    foreach ($modules_controllers as $cont) {
                        $code_module = 1000 * ModuleCore::getModuleIdByName($module);
                        $code_controller = $code_module + $j;
                        $html .= '<option value="MODULE'.$code_controller.'">'.$module.'-'.$cont.'</option>';
                        $j++;
                    }
                }
            }
        }
        $html .= '</optgroup>';
        $html .= '</select>';
        return $html;
    }

    protected function makeMenuOption($id_block)
    {
        //$menu_item = array();
        $id_lang = (int)$this->context->language->id;
        $selected_categories = Block::getExceptionsList($id_block);
        $selected_categories = explode('_', $selected_categories[0]['excluded_categories']);
        $html = '';
        foreach ($selected_categories as $item) {
            if (!$item) {
                continue;
            }

            preg_match($this->pattern, $item, $values);


            $id = (int)Tools::substr($item, Tools::strlen($values[1]), Tools::strlen($item));

            switch (Tools::substr($item, 0, Tools::strlen($values[1]))) {
                case 'CAT':
                    $category = new Category((int)$id, (int)$id_lang);
                    if (Validate::isLoadedObject($category)) {
                        $html .= '<option selected="selected" value="CAT'.$id.'">'.$category->name.'</option>'.PHP_EOL;
                    }
                    break;

                case 'PRD':
                    $product = new Product((int)$id, true, (int)$id_lang);
                    if (Validate::isLoadedObject($product)) {
                        $html .= '<option selected="selected" value="PRD'.$id.'">'.$product->name.'</option>'.PHP_EOL;
                    }
                    break;

                case 'CMS':
                    $cms = new CMS((int)$id, (int)$id_lang);
                    if (Validate::isLoadedObject($cms)) {
                        $html .= '<option selected="selected" value="CMS'.$id.'">'.$cms->meta_title.'</option>'.PHP_EOL;
                    }
                    break;

                case 'CMSCAT':
                    $category = new CMSCategory((int)$id, (int)$id_lang);
                    if (Validate::isLoadedObject($category)) {
                        $html .= '<option selected="selected" value="CMSCAT'
                            .$id
                            .'">'
                            .$category->name
                            .'</option>'
                            .PHP_EOL;
                    }
                    break;

                // Case to handle the option to show all Manufacturers
                case 'ALLMAN':
                    $html .= '<option selected="selected" value="ALLMAN0">'
                        .$this->l('All manufacturers')
                        .'</option>'
                        .PHP_EOL;
                    break;

                case 'MAN':
                    $manufacturer = new Manufacturer((int)$id, (int)$id_lang);
                    if (Validate::isLoadedObject($manufacturer)) {
                        $html .= '<option selected="selected" value="MAN'
                            .$id
                            .'">'
                            .$manufacturer->name
                            .'</option>'
                            .PHP_EOL;
                    }
                    break;

                // Case to handle the option to show all Suppliers
                case 'ALLSUP':
                    $html .= '<option selected="selected" value="ALLSUP0">'
                        .$this->l('All suppliers')
                        .'</option>'
                        .PHP_EOL;
                    break;

                case 'SUP':
                    $supplier = new Supplier((int)$id, (int)$id_lang);
                    if (Validate::isLoadedObject($supplier)) {
                        $html .= '<option selected="selected" value="SUP'
                            .$id
                            .'">'
                            .$supplier->name
                            .'</option>'
                            .PHP_EOL;
                    }
                    break;


                case 'CORE':
                    $controllers = DispatcherCore::getControllers(_PS_FRONT_CONTROLLER_DIR_);
                    ksort($controllers);

                    $i = 0;
                    foreach ($controllers as $k => $v) {
                        if ($k == 'auth') {
                            $k = 'authentification';
                            $v = $v;
                        }
                        if ($i == $id) {
                            $html .= '<option selected="selected" value="CORE'.(int)$i.'">'.$k.'</option>'.PHP_EOL;
                        }
                        $i ++;
                    }
                    break;

                case 'MODULE':
                    if ($this->checkPSVersion('1.6.0.5') && method_exists('Dispatcher', 'getModuleControllers')) {
                        $modules_controllers_type = array('front' => $this->l('Front modules controller'));
                        foreach ($modules_controllers_type as $type => $label) {
                            $all_modules_controllers = Dispatcher::getModuleControllers($type);
                            foreach ($all_modules_controllers as $module => $modules_controllers) {
                                $j = 0;
                                $code_module = 1000 * ModuleCore::getModuleIdByName($module);
                                foreach ($modules_controllers as $cont) {
                                    $code_controller = $j + $code_module;
                                    if ($code_controller == $id) {
                                        $html .= '<option selected="selected" value="MODULE'
                                            .(int)$code_controller
                                            .'">'
                                            .$module
                                            .'-'
                                            .$cont
                                            .'</option>'
                                            .PHP_EOL;
                                        $label = $label;
                                    }
                                    $j++;
                                }
                            }
                        }
                    }
                    break;
            }
        }
        /* foreach ($menu_item as $item) {
          if (!$item) {
          continue;
          }

          preg_match($this->pattern, $item, $values);
          $id = (int)substr($item, strlen($values[1]), strlen($item));

          switch (substr($item, 0, strlen($values[1]))) {
          case 'CAT':
          $category = new Category((int)$id, (int)$id_lang);
          if (Validate::isLoadedObject($category)) {
          $html .= '<option selected="selected" value="CAT'.$id.'">'.$category->name.'</option>'.PHP_EOL;
          }
          break;
          }
          } */

        return $html.'</select>';
    }

    protected function getCMSOptions($parent = 0, $depth = 1, $id_lang = false, $items_to_skip = null, $id_shop = false)
    {
        $html = '';
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;
        $categories = $this->getCMSCategories(false, (int)$parent, (int)$id_lang, (int)$id_shop);
        $pages = $this->getCMSPages((int)$parent, (int)$id_shop, (int)$id_lang);

        $spacer = str_repeat('&nbsp;', $this->spacer_size * (int)$depth);

        foreach ($categories as $category) {
            if (isset($items_to_skip) && !in_array('CMS_CAT'.$category['id_cms_category'], $items_to_skip)) {
                $html .= '<option value="CMSCAT'
                    .$category['id_cms_category']
                    .'" style="font-weight: bold;">'
                    .$spacer
                    .$category['name']
                    .'</option>';
            }
            $html .= $this->getCMSOptions(
                $category['id_cms_category'],
                (int)$depth + 1,
                (int)$id_lang,
                $items_to_skip
            );
        }

        foreach ($pages as $page) {
            if (isset($items_to_skip) && !in_array('CMS'.$page['id_cms'], $items_to_skip)) {
                $html .= '<option value="CMS'.$page['id_cms'].'">'.$spacer.$page['meta_title'].'</option>';
            }
        }

        return $html;
    }

    protected function getCMSCategories($recursive = false, $parent = 1, $id_lang = false, $id_shop = false)
    {
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $id_shop = ($id_shop !== false) ? $id_shop : Context::getContext()->shop->id;
        $join_shop = '';
        $where_shop = '';
        $categories = array();

        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true) {
            $join_shop = ' INNER JOIN `'._DB_PREFIX_.'cms_category_shop` cs
			ON (bcp.`id_cms_category` = cs.`id_cms_category`)';
            $where_shop = ' AND cs.`id_shop` = '.(int)$id_shop.' AND cl.`id_shop` = '.(int)$id_shop;
        }

        if ($recursive === false) {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`,
                bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp'.
                    $join_shop.'
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent.
                    $where_shop;

            return Db::getInstance()->executeS($sql);
        } else {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`,
                bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `'._DB_PREFIX_.'cms_category` bcp'.
                    $join_shop.'
				INNER JOIN `'._DB_PREFIX_.'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = '.(int)$id_lang.'
				AND bcp.`id_parent` = '.(int)$parent.
                    $where_shop;

            $results = Db::getInstance()->executeS($sql);
            foreach ($results as $result) {
                $sub_categories = $this->getCMSCategories(true, $result['id_cms_category'], (int)$id_lang);
                if ($sub_categories && count($sub_categories) > 0) {
                    $result['sub_categories'] = $sub_categories;
                }
                $categories[] = $result;
            }

            return isset($categories) ? $categories : false;
        }
    }

    protected function getCMSPages($id_cms_category, $id_shop = false, $id_lang = false)
    {
        $id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;

        $where_shop = '';
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true) {
            $where_shop = ' AND cl.`id_shop` = '.(int)$id_shop;
        }

        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `'._DB_PREFIX_.'cms` c
			INNER JOIN `'._DB_PREFIX_.'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `'._DB_PREFIX_.'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.`id_cms_category` = '.(int)$id_cms_category.'
			AND cs.`id_shop` = '.(int)$id_shop.'
			AND cl.`id_lang` = '.(int)$id_lang.
                $where_shop.'
			AND c.`active` = 1
			ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Méthode getListContent()
     * 
     * Récupère les éléments de la liste principale du module dans le backoffice
     * 
     * @return object helper
     */
    public function getListContent()
    {
        //$order_by = 'date_to';
        $order_by = 'position';
        $order_way = 'ASC';

        if (Tools::isSubmit($this->name.'Orderby')) {
            $order_by = Tools::getValue($this->name.'Orderby');
            $order_way = Tools::getValue($this->name.'Orderway');
        }

        $block_list = Block::getBlocks($order_by, $order_way, false);

        foreach ($block_list as &$block) {
            if ($this->is_version15) {
                $block['hook_name'] = ($block['id_hook'] == 0) ? $this->l('All') : HtmlBlocks::getNameById(
                    $block['id_hook']
                );
            } else {
                $block['hook_name'] = ($block['id_hook'] == 0) ? $this->l('All') : Hook::getNameById($block['id_hook']);
            }
        }
        return $block_list;
    }

    /**
     * Méthode renderForm()
     * 
     * Affiche le formulaire principale du module dans le backoffice
     * 
     * @return string HTML du backoffice du module
     */
    private function renderForm()
    {
        $helper = $this->initForm();
        $helper->fields_value = $this->getAddFieldsValues();

        $this->html .= $helper->generateForm($this->fields_form);
    }

    /**
     * Méthode renderCssConfigForm()
     * 
     * Affiche le formulaire de configuration du css dans le backoffice
     * 
     * @return string HTML du backoffice du module
     */
    private function renderCssConfigForm()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin/colorpicker.css', 'all');
        $this->context->controller->addJS($this->_path.'views/js/admin/colorpicker.js', 'all');
        $this->context->controller->addJS($this->_path.'views/js/admin/edit_area_full.js', 'all');
        $this->context->controller->addJS($this->_path.'views/js/admin.js', 'all');

        $msg = $this->handleForms();

        $blocks = Block::getBlocks('', '', false);

        $csss = $this->addCssContent($blocks);

        $id_block_active = null != Tools::getValue('id_block_active') ? Tools::getValue('id_block_active') : 1;

        $this->context->smarty->assign(array(
            'id_block_active' => $id_block_active,
            'blocks' => $blocks,
            'csss' => $csss,
            'alert' => $msg,
            'url_back' => AdminController::$currentIndex.'&configure='
            .$this->name.'&token='.Tools::getAdminTokenLite('AdminModules')
        ));

        if (!$this->is_version16) {
            return $this->display(__FILE__, 'views/templates/admin/configurationCSS15.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/configurationCSS.tpl');
        }
    }

    /**
     * Méthode renderExceptionsForm()
     * 
     * Affiche le formulaire de gestion des exeptions dans le backoffice
     * 
     * @return string HTML du backoffice du module
     */
    private function renderExceptionsForm()
    {
        $msg = $this->handleForms();
        $blocks = Block::getBlocks('', '', false);

        $selected_pages = array();
        foreach ($blocks as $key => $block) {
            $selected_pages[$key] = $this->makeMenuOption($block['id_block']);
        }

        $id_block = (null != Tools::getValue('idblock') ? Tools::getValue('idblock') : 1);

        $this->context->smarty->assign(array(
            'idblock' => $id_block,
            'tab' => $id_block,
            'blocks' => $blocks,
            'alert' => $msg,
            'url_back' => AdminController::$currentIndex.'&configure='
            .$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'choices' => $this->renderChoicesSelect(),
            'selected_pages' => $selected_pages,
        ));

        if (!$this->is_version16) {
            return $this->display(__FILE__, 'views/templates/admin/exceptionsmanagement15.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/exceptionsmanagement.tpl');
        }
    }

    /**
     * Méthode initToolbar()
     * 
     * Initialise la barre d'outils du formulaire principale du module dans le backoffice
     * 
     * @return array $this->toolbar_btn
     */
    private function initToolbar()
    {
        if (!$this->is_version16) {
            $this->toolbar_btn['save'] = array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='
                .$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules')
            );
            $this->toolbar_btn['back'] = array(
                'href' => AdminController::$currentIndex.'&configure='
                .$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list', null, null, false)
            );
        }

        $this->toolbar_btn['help-new'] = array(
            'href' => AdminController::$currentIndex.'&configure='
            .$this->name.'&support&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Support')
        );

        return $this->toolbar_btn;
    }

    /**
     * Méthode initForm()
     * 
     * Initialise le formulaire principale du module dans le backoffice
     * 
     * @return object helper
     */
    private function initForm()
    {
        //$this->context->controller->addCSS($this->_path.'views/css/mp-admin.css');
        $this->context->controller->addJS($this->_path.'views/js/admin.js');

        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $hooks = Hook::getHooks();
        $hooks_list = array();
        foreach ($hooks as $hook) {
            if ($this->is_version16) {
                if (Tools::strpos($hook['name'], 'display') !== false
                        && Tools::strpos($hook['name'], 'Admin') === false
                        && Tools::strpos($hook['name'], 'BackOffice') === false) {
                    $hooks_list[] = array(
                        'id' => $hook['id_hook'],
                        'name' => $hook['name'],
                    );
                }
            } else {
                if (strpos($hook['name'], 'display') !== false
                        && strpos($hook['name'], 'Admin') === false
                        && strpos($hook['name'], 'BackOffice') === false) {
                    $hooks_list[] = array(
                        'id' => $hook['id_hook'],
                        'name' => $hook['name'],
                    );
                }
            }
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        if (Tools::getIsset('addhtmlblocks')) {
            $helper->submit_action = 'submitadd'.$this->name;
        } elseif (Tools::getIsset('updatehtmlblocks')) {
            $helper->submit_action = 'submitupdate'.$this->name;
        }

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;  // false -> remove toolbar. Only 1.5
        $helper->toolbar_scroll = true;   // true -> Toolbar is always visible on the top of the screen. Only 1.5
        $helper->toolbar_btn = $this->initToolbar();

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->displayName,
                'icon' => 'icon-file-code-o',
            ),
            'submit' => array(
                'name' => 'submit'.$this->name,
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                array(
                    'icon' => 'process-icon-back',
                    'title' => $this->l('Back to list'),
                    'href' => AdminController::$currentIndex.'&configure='
                    .$this->name.'&token='.Tools::getAdminTokenLite('AdminModules')
                )
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_block',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Block title'),
                    'name' => 'title',
                    'desc' => $this->l('HTML block title'),
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML Content'),
                    'desc' => $this->l('HTML block content'),
                    'name' => 'content',
                    'autoload_rte' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Block link'),
                    'name' => 'link',
                    'desc' => $this->l('HTML block link'),
                    'lang' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Block link title'),
                    'name' => 'link_title',
                    'desc' => $this->l('HTML block link title'),
                    'lang' => true,
                ),
                array(
                    'type' => ($this->is_version16) ? 'switch' : 'radio',
                    'label' => $this->l('New window'),
                    'name' => 'new_window',
                    'desc' => $this->l('Open the link on a new window or not'),
                    'class' => 't', //only 1.5
                    'required' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'new_window_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'new_window_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ),
                    ),
                ),
            ),
        );

        if (Tools::isSubmit('updatehtmlblocks')) {
            if ($this->is_version16) {
                array_push(
                    $this->fields_form[0]['form']['input'],
                    array(
                        'name' => $this->l('Configure CSS'),
                        'type' => 'button',
                        'icon' => 'edit',
                        'title' => $this->l('Configure CSS'),
                        'href' => AdminController::$currentIndex.'&configure='.$this->name
                        .'&configuratecss&token='.Tools::getAdminTokenLite('AdminModules')
                        .'&idblock='.Tools::getValue('id_block'),
                        'nameA' => $this->l('Exceptions management'),
                        'hrefA' => AdminController::$currentIndex.'&configure='.$this->name
                        .'&exceptionsmanagement&token='.Tools::getAdminTokenLite('AdminModules')
                        .'&idblock='.Tools::getValue('id_block')
                    )
                );
            } else {
                array_push(
                    $this->fields_form[0]['form']['input'],
                    array(
                        'name' => $this->l('Configure CSS'),
                        'type' => 'button15',
                        'icon' => 'process-icon-edit',
                        'title' => $this->l('Configure CSS'),
                        'href' => AdminController::$currentIndex.'&configure='.$this->name
                        .'&configuratecss&token='.Tools::getAdminTokenLite('AdminModules')
                        .'&idblock='.Tools::getValue('id_block'),
                        'nameA' => $this->l('Exceptions management'),
                        'hrefA' => AdminController::$currentIndex.'&configure='.$this->name
                        .'&exceptionsmanagement&token='.Tools::getAdminTokenLite('AdminModules')
                        .'&idblock='.Tools::getValue('id_block')
                    )
                );
            }
        }
        array_push(
            $this->fields_form[0]['form']['input'],
            array(
                'type' => 'select',
                'label' => $this->l('Hook'),
                'desc' => $this->l('HTML block will be displayed on this hook'),
                'name' => 'id_hook',
                'required' => true,
                'options' => array(
                    'query' => $hooks_list,
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => ($this->is_version16) ? 'switch' : 'radio',
                'label' => $this->l('Active'),
                'name' => 'active',
                'desc' => $this->l('Enable or disable the HTML block'),
                'class' => 't', //only 1.5
                'required' => true,
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ),
                ),
            ),
            array(
                'type' => ($this->is_version16) ? 'text' : 'date',
                'label' => $this->l('From'),
                'desc' => $this->l('Set the beginning date'),
                'name' => 'date_from',
                'suffix' => '<i class="icon-calendar"></i>', //only 1.6
                'required' => false,
                'size' => 20,
                'class' => 'fixed-width-lg datepicker' //only 1.6
            ),
            array(
                'type' => ($this->is_version16) ? 'text' : 'date',
                'label' => $this->l('To'),
                'desc' => $this->l('Set the ending date'),
                'name' => 'date_to',
                'suffix' => '<i class="icon-calendar"></i>', //only 1.6
                'required' => false,
                'size' => 20,
                'class' => 'fixed-width-lg datepicker' //only 1.6
            ),
            array(
                'type' => 'select',
                'label' => $this->l('For'),
                'desc' => $this->l('HTML block will be only available for this currency'),
                'name' => 'id_currency',
                'class' => 'fixed-width-lg', //only 1.6
                'options' => array(
                    'query' =>
                    array_merge(
                        array(0 => array('id_currency' => 0, 'name' => $this->l('All currencies'))),
                        Currency::getCurrenciesByIdShop((int)$this->context->shop->id)
                    ),
                    'id' => 'id_currency',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('For'),
                'desc' => $this->l('HTML block will be only available for this country'),
                'name' => 'id_country',
                'class' => 'fixed-width-lg', //only 1.6
                'options' => array(
                'query' =>
                    array_merge(
                        array(0 => array('id_country' => 0, 'name' => $this->l('All countries'))),
                        Country::getCountriesByIdShop((int)$this->context->shop->id, (int)$this->context->language->id)
                    ),
                    'id' => 'id_country',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('For'),
                'desc' => $this->l('HTML block will be only available for this group'),
                'name' => 'id_group',
                'class' => 'fixed-width-lg', //only 1.6
                'options' => array(
                    'query' =>
                        array_merge(
                            array(0 => array('id_group' => 0, 'name' => $this->l('All groups'))),
                            Group::getGroups((int)$this->context->language->id, (int)$this->context->shop->id)
                        ),
                    'id' => 'id_group',
                    'name' => 'name'
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form[0]['form']['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        // Needed for WYSIWYG
        $language = new Language((int)$default_lang);
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path.'images/',
            'name_controller' => 'HtmlBlocks'
        );

        return $helper;
    }

    /**
     * Méthode getAddFieldsValues()
     * 
     * Récupère les valeurs des champs du formulaire en base de données
     * 
     * @return array $fields Valeurs des champs du formulaire
     */
    public function getAddFieldsValues()
    {
        $fields = array();
        $languages = Language::getLanguages(false);

        $block = new Block(Tools::getValue('id_block'));
        $fields['id_block'] = (Tools::getIsset('id_block')) ? Tools::getValue('id_block') : 0;
        $fields['id_hook'] = Tools::getValue('id_hook', $block->id_hook);
        $fields['new_window'] = Tools::getValue('new_window', $block->new_window);
        $fields['active'] = Tools::getValue('active', $block->active);
        $fields['date_from'] = Tools::getValue(
            'date_from',
            isset($block->date_from) ? $block->date_from : date('Y-m-d H:i:s')
        );
        $fields['date_to'] = Tools::getValue(
            'date_to',
            isset($block->date_to) ? $block->date_to : date('Y-m-d H:i:s', strtotime('+1 month'))
        );
        $fields['id_currency'] = Tools::getValue('id_currency', $block->id_currency);
        $fields['id_country'] = Tools::getValue('id_country', $block->id_country);
        $fields['id_group'] = Tools::getValue('id_group', $block->id_group);

        foreach ($languages as $lang) {
            $fields['title'][$lang['id_lang']] =
                isset($block->title[$lang['id_lang']]) ? $block->title[$lang['id_lang']] : '';
            $fields['content'][$lang['id_lang']] =
                isset($block->content[$lang['id_lang']]) ? $block->content[$lang['id_lang']] : '';
            $fields['link'][$lang['id_lang']] =
                isset($block->link[$lang['id_lang']]) ? $block->link[$lang['id_lang']] : '';
            $fields['link_title'][$lang['id_lang']] =
                isset($block->link_title[$lang['id_lang']]) ? $block->link_title[$lang['id_lang']] : '';
        }
        return $fields;
    }

    public function renderSupportForm()
    {
        // Envoi des paramètres au template
        $this->context->smarty->assign(array(
            'path' => _MODULE_DIR_.$this->name.'/',
            'iso' => Language::getIsoById($this->context->cookie->id_lang),
            'display_name' => $this->displayName,
            'version' => $this->version,
            'author' => $this->author,
            'contact' => $this->contact,
            'back_link' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.
                Tools::getAdminTokenLite('AdminModules'),
            'ps_version16' => $this->checkPSVersion('1.6.0.0')
        ));

        return $this->display(__FILE__, 'views/templates/admin/support.tpl');
    }

    private function deleteBlock()
    {
        $id_block = Tools::getValue('id_block');
        $block = new Block($id_block);
        $id_hook = $block->id_hook;
        $block->delete();
        if (file_exists(dirname(__FILE__).'/views/css/blockscss/htmlblocks-s-'.$id_block.'.css')) {
            unlink(dirname(__FILE__).'/views/css/blockscss/htmlblocks-s-'.$id_block.'.css');
        }

        $block_list = Block::getBlocksByIdHook($id_hook);
        if (count($block_list) == 0) {
            if ($this->is_version15) {
                $this->unregisterHook(htmlblocks::getNameById($id_hook));
            } else {
                $this->unregisterHook(Hook::getNameById($id_hook));
            }
        }

        return $this->displayConfirmation($this->l('HTML block has been deleted'));
    }

    private function changeStatus()
    {
        $id_block = Tools::getValue('id_block');
        $block = new Block($id_block);

        if ($block->active == 0) {
            $block->active = 1;
        } else {
            $block->active = 0;
        }

        $block->update();
    }

    /**
     * Méthode generateCSS()
     * 
     * Génère un fichier CSS en fonction des paramètres définits dans le backoffice du module
     */
    private function generateCSS()
    {
        foreach (Shop::getShops() as $shop) {
            // Récupération des paramètres
            //$banner_page = Configuration::get($this->short_name.'_banner_page', null, null, $shop['id_shop']);
            // Récupération du fichier CSS static
            $css_content = Tools::file_get_contents(dirname(__FILE__).'/views/css/static.css');

            // Génération du fichier CSS
            $css_content .= '';

            $filename = 'htmlblocks-s-'.$shop['id_shop'].'.css';

            file_put_contents(dirname(__FILE__).'/views/css/'.$filename, $css_content, LOCK_EX);
        }
    }

    /**
     * Méthode checkPSVersion()
     * 
     * Compare la version de Prestashop passée en paramètre avec la version courante
     * 
     * @param string $version Version à comparer
     * @param string $compare Sens de la comparaison
     * 
     * @return boolean True si la comparaison est vérifiée
     * 
     */
    public function checkPSVersion($version = '1.5.0.0', $compare = '>')
    {
        return version_compare(_PS_VERSION_, $version, $compare);
    }

    /**
     * Méthode checkPeriod()
     * 
     * Contrôle la période d'affichage d'un bloc HTML
     */
    private function checkPeriod($block)
    {
        if (time() < strtotime($block['date_from'])) {
            return false;
        }

        if (time() > strtotime($block['date_to'])) {
            return false;
        }

        return true;
    }

    private function checkException($block)
    {
        $list_excluded = explode('_', $block['excluded_categories']);
        $id_lang = (int)$this->context->language->id;
        foreach ($list_excluded as $item) {
            if (!$item) {
                continue;
            }

            preg_match($this->pattern, $item, $values);

            $id = (int)Tools::substr($item, Tools::strlen($values[1]), Tools::strlen($item));
            switch (Tools::substr($item, 0, Tools::strlen($values[1]))) {
                case 'CAT':
                    if ($this->context->controller->php_self == 'category') {
                        $cat_active = $this->context->controller->getCategory()->id;
                        if ($cat_active == $id) {
                            return true;
                        }
                    }
                    if ($this->context->controller->php_self == 'product') {
                        $cat_active = $this->context->controller->getProduct()->id_category_default;
                        if ($cat_active == $id) {
                            return true;
                        }
                    }
                    break;

                case 'PRD':
                    if ($this->context->controller->php_self == 'product') {
                        $prod_actif = $this->context->controller->getProduct()->id;
                        if ($prod_actif == $id) {
                            return true;
                        }
                    }
                    break;

                case 'CMS':
                    if ($this->context->controller->php_self == 'cms') {
                        $cms_actif = $this->context->controller->cms->id;
                        if ($cms_actif == $id) {
                            return true;
                        }
                    }
                    break;

                case 'CMSCAT':
                    if ($this->context->controller->php_self == 'cms') {
                        $cms_actif = $this->context->controller->cms->id;
                        $CMS_pages = CMSCore::getCMSPages($id_lang, $id);
                        foreach ($CMS_pages as $CMS_page) {
                            if ($CMS_page['id_cms'] == $cms_actif) {
                                return true;
                            }
                        }
                    }
                    break;

                // Case to handle the option to show all Manufacturers
                case 'ALLMAN':
                    if ($this->context->controller->php_self == 'manufacturer') {
                        $man_actif = $this->context->controller->getProduct()->id_manufacturer;
                        if (!Tools::isEmpty($man_actif)) {
                            return true;
                        }
                    }
                    break;

                case 'MAN':
                    if ($this->context->controller->php_self == 'manufacturer') {
                        $man_actif = $this->context->controller->getManufacturer()->id_manufacturer;
                        if ($man_actif == $id) {
                            return true;
                        }
                    }
                    break;

                // Case to handle the option to show all Suppliers
                case 'ALLSUP':
                    if ($this->context->controller->php_self == 'supplier') {
                        $sup_actif = $this->context->controller->getSupplier()->id_supplier;
                        if (!Tools::isEmpty($sup_actif)) {
                            return true;
                        }
                    }
                    break;

                case 'SUP':
                    if ($this->context->controller->php_self == 'supplier') {
                        $sup_actif = $this->context->controller->getSupplier()->id_supplier;
                        if ($sup_actif == $id) {
                            return true;
                        }
                    }
                    break;

                case 'CORE':
                    $controllers = DispatcherCore::getControllers(_PS_FRONT_CONTROLLER_DIR_);
                    ksort($controllers);

                    $i = 0;
                    $controller = null;
                    foreach ($controllers as $key => $class) {
                        if ($key == 'auth') {
                            $key = 'authentification';
                        }
                        if ($i == $id) {
                            $controller = new $class();
                        }
                        $i ++;
                    }
                    if (isset($controller->php_self) && $controller->php_self == $this->context->controller->php_self) {
                        return true;
                    }
                    break;

                case 'MODULE':
                    if ($this->checkPSVersion('1.6.0.5') && method_exists('Dispatcher', 'getModuleControllers')) {
                        $modules_controllers_type = array('front' => $this->l('Front modules controller'));
                        foreach ($modules_controllers_type as $type => $label) {
                            $all_modules_controllers = Dispatcher::getModuleControllers($type);
                            foreach ($all_modules_controllers as $module => $modules_controllers) {
                                $j = 0;
                                $code_module = 1000 * ModuleCore::getModuleIdByName($module);
                                foreach ($modules_controllers as $cont) {
                                    $code_controller = $j + $code_module;
                                    if ($code_controller == $id
                                        && Tools::isEmpty($this->context->controller->php_self)) {
                                        $mod_actif = $this->context->controller->page_name;
                                        if ($mod_actif == 'module-'.$module.'-'.$cont) {
                                            $label = $label;
                                            return true;
                                        }
                                    }
                                    $j++;
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Méthode checkUserAccess()
     * 
     * Contrôle si un utilisateur peut voir un bloc HTML
     */
    private function checkUserAccess($block)
    {
        // Get current user infos
        $id_currency = (int)$this->context->cookie->id_currency;
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);
        $id_country = (int)$id_customer ? Customer::getCurrentCountry($id_customer) :
            Configuration::get('PS_COUNTRY_DEFAULT');
        $customer_groups = Customer::getGroupsStatic($id_customer);

        // Check if user has good rights
        if ($block['id_currency'] != 0 && $block['id_currency'] != $id_currency) {
            return false;
        }

        if ($block['id_country'] != 0 && $block['id_country'] != $id_country) {
            return false;
        }

        if ($block['id_group'] != 0 && !in_array($block['id_group'], $customer_groups)) {
            return false;
        }

        return true;
    }

    private function getButtonsTpl()
    {
        $this->context->smarty->assign(array(
            'module_name' => $this->displayName,
            'add_url' => AdminController::$currentIndex.'&configure='.
                $this->name.'&addhtmlblocks&token='.Tools::getAdminTokenLite('AdminModules'),
            'list_url' => AdminController::$currentIndex.'&configure='.
                $this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'css_url' => AdminController::$currentIndex.'&configure='.
                $this->name.'&configuratecss&token='.Tools::getAdminTokenLite('AdminModules'),
            'exceptions_url' => AdminController::$currentIndex.'&configure='.
                $this->name.'&exceptionsmanagement&token='.Tools::getAdminTokenLite('AdminModules'),
            'support_url' => $this->addons_url,
            'documentation_url' => AdminController::$currentIndex.'&configure='.
                $this->name.'&support'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'ps_version16' => $this->checkPSVersion('1.6.0.0'),
        ));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/admin/buttons.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/css/admin/hb-admin.css', 'all');
        }
    }

    /**
     * Méthode hookDisplayHeader()
     * 
     * Ajoute des fichiers css et/ou js dans la balise <head> de la page Html
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS(
            $this->_path.'views/css/htmlblocks-s-'.$this->context->shop->id.'.css',
            'all'
        );

        $blocks = Block::getBlocks('', '', true);
        foreach ($blocks as $block) {
            if ($this->checkPeriod($block) && $this->checkUserAccess($block)) {
                $this->context->controller->addCSS(
                    $this->_path.'views/css/blockscss/htmlblocks-s-'.$block['id_block'].'.css',
                    'all'
                );
            }
        }
    }

    private function prepareHook($hook_name, $args = '')
    {
        $id_hook = Hook::getIdByName($hook_name);
        $block_list_tmp = Block::getBlocksByIdHook($id_hook);
        $block_list = array();
        foreach ($block_list_tmp as $block) {
            $block['id_html'] = Tools::strtolower(str_replace(' ', '_', $block['title']));
            if ($this->checkPeriod($block) && $this->checkUserAccess($block) && !$this->checkException($block)) {
                $block_list[] = $block;
            }
        }

        if (count($block_list) == 0) {
            return false;
        }

        $this->smarty->assign(array(
            'block_list' => $block_list,
            'args' => $args
        ));

        return $this->display(__FILE__, 'views/templates/hook/block.tpl');
    }

    private function addCssContent($blocks)
    {
        $contentsCss = array();
        foreach ($blocks as $block) {
            array_push(
                $contentsCss,
                Tools::file_get_contents(
                    dirname(__FILE__).'/views/css/blockscss/htmlblocks-s-'.$block['id_block'].'.css'
                )
            );
        }
        return $contentsCss;
    }

    private function handleForms()
    {
        if (!Tools::getIsset('submit')) {
            return '';
        }

        if (Tools::getIsset('submit_css') && Tools::getValue('submit_css') == 1) {
            $this->addCssFile(Tools::getValue('content'), Tools::getValue('id'));
            return $this->l('Your CSS has been modified');
        }

        if (Tools::getIsset('submit_exceptions') && Tools::getValue('submit_exceptions') == 1) {
            $this->updateExceptions(Tools::getValue('items'), Tools::getValue('id'));
            return $this->l('Your exceptions has been modified');
        }
    }

    private function updateExceptions($items, $id)
    {
        if ($items != '') {
            $list_exceptions = implode('_', $items);
        } else {
            $list_exceptions = '';
        }
        Block::updateExceptionsList($id, $list_exceptions);
    }

    private function addCssFile($file, $id)
    {
        $filename = 'htmlblocks-s-'.$id.'.css';
        file_put_contents(dirname(__FILE__).'/views/css/blockscss/'.$filename, $file, LOCK_EX);
    }

    public function __call($name, $args)
    {
        if (!Validate::isHookName($name)) {
            return false;
        }
        $hook_name = str_replace('hook', '', $name);

        return $this->prepareHook($hook_name, $args);
    }

    public static function getNameById($hook_id)
    {
        $cache_id = 'hook_namebyid_'.$hook_id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue('
							SELECT `name`
							FROM `'._DB_PREFIX_.'hook`
							WHERE `id_hook` = '.(int)$hook_id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    protected function getMenuItems()
    {
        $items = Tools::getValue('items');
        if (is_array($items) && count($items)) {
            return $items;
        } else {
            $shops = Shop::getContextListShopID();
            $conf = null;

            if (count($shops) > 1) {
                foreach ($shops as $key => $shop_id) {
                    $shop_group_id = Shop::getGroupFromShop($shop_id);
                    $conf .= (string)($key > 1 ? ',' : '').
                        Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop_group_id, $shop_id);
                }
            } else {
                $shop_id = (int)$shops[0];
                $shop_group_id = Shop::getGroupFromShop($shop_id);
                $conf = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop_group_id, $shop_id);
            }

            if (Tools::strlen($conf)) {
                return explode(',', $conf);
            } else {
                return array();
            }
        }
    }

    protected function generateCategoriesOption($categories, $items_to_skip = null)
    {
        $html = '';
        foreach ($categories as $category) {

            if (isset($items_to_skip)) {
                $shop = (object)Shop::getShop((int)$category['id_shop']);
                $this->context->smarty->assign(array(
                    'id_category' => (int)$category['id_category'],
                    'spacer' => str_repeat('&nbsp;', $this->spacer_size * (int)$category['level_depth']),
                    'category_name' => $category['name'],
                    'shop' => $shop,
                ));
                $html .= $this->context->smarty->fetch(
                    _PS_MODULE_DIR_.$this->name.'/views/templates/admin/options.tpl'
                );
            }

            if (isset($category['children']) && !empty($category['children'])) {
                $html .= $this->generateCategoriesOption($category['children'], $items_to_skip);
            }
        }

        return $html;
    }

    public function customGetNestedCategories(
        $shop_id,
        $root_category = null,
        $id_lang = false,
        $active = false,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array)$groups;
        }

        $cache_id = 'Category::getNestedCategories_'.md5(
            (int)$shop_id.(int)$root_category.(int)$id_lang.(int)$active.(int)$active
            .(isset($groups) && Group::isFeatureActive() ? implode('', $groups) : '')
        );

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                'SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				INNER JOIN `'._DB_PREFIX_.'category_shop` category_shop
                ON (category_shop.`id_category` = c.`id_category` AND category_shop.`id_shop` = "'.(int)$shop_id.'")
				LEFT JOIN `'._DB_PREFIX_.'category_lang`
                cl ON (c.`id_category` = cl.`id_category` AND cl.`id_shop` = "'.(int)$shop_id.'")
				WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND cl.`id_lang` = '.(int)$id_lang : '').'
				'.($active ? ' AND (c.`active` = 1 OR c.`is_root_category` = 1)' : '').'
				'.(isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN ('.implode(',', $groups).')' : '').'
				'.(!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '').'
				'.($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC').'
				'.($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '').'
				'.($sql_limit != '' ? $sql_limit : '')
            );

            $categories = array();
            $buff = array();

            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_parent'] == 0) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cache_id, $categories);
        }

        return Cache::retrieve($cache_id);
    }
}
