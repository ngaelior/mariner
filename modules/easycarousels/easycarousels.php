<?php
/**
* 2007-2019 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2019 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class EasyCarousels extends Module
{
    public function __construct()
    {
        if (!defined('_PS_VERSION_')) {
            exit;
        }
        $this->name = 'easycarousels';
        $this->tab = 'front_office_features';
        $this->version = '2.6.2';
        $this->author = 'Amazzing';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = 'b277f11ccef2f6ec16aaac88af76573e';

        parent::__construct();

        $this->displayName = $this->l('Easy carousels');
        $this->description = $this->l('Create custom carousels in just a few clicks');

        $this->db = Db::getInstance();
        $this->image_sizes = array();
        $this->is_17 = Tools::substr(_PS_VERSION_, 0, 3) === '1.7';
        $this->custom_overrides_dir = $this->local_path.'override_files/';
        $this->is_mobile = $this->isMobile();
    }

    public function isMobile()
    {
        if (is_callable(array($this->context, 'isMobile'))) {
            $is_mobile = $this->context->isMobile();
        } else {
            $is_mobile = $this->context->getMobileDetect()->isMobile();
        }
        return $is_mobile;
    }

    public function getTypeNames($grouped = true)
    {
        $related_products_label = $this->is_17 ? $this->l('Carousels for product page / checkout page') :
        $this->l('Carousels for product page');
        $type_names = array(
            $this->l('Carousels for any page') => array (
                'newproducts' => $this->l('New products'),
                'bestsellers' => $this->l('Bestsellers'),
                'featuredproducts' => $this->l('Featured products'),
                'pricesdrop' => $this->l('On sale'),
                'catproducts' => $this->l('Products from selected categories'),
                'products' => $this->l('Selected products'),
                'viewedproducts' => $this->l('Viewed products'),
                'bymanufacturer' => $this->l('Products by manufacturer'),
                'bysupplier' => $this->l('Products by supplier'),
                'categories' => $this->l('Selected categories'),
                'subcategories' => $this->l('Subcategories'),
                'manufacturers' => $this->l('Manufacturers'),
                'suppliers' => $this->l('Suppliers'),
            ),
            $related_products_label => array(
                'samecategory' => $this->l('Other products from same category'),
                'samefeature' => $this->l('Other products with same features'),
                'sametag' => $this->l('Other products with same tags'),
                'accessories' => $this->l('Product accessories'),
            ),
        );
        return $grouped ? $type_names : call_user_func_array('array_merge', $type_names);
    }

    public function getFields($type, $advanced_options = array())
    {
        $fields = array();
        $int_options = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10);
        switch ($type) {
            case 'carousel':
                $fields = array (
                    'type' => array(
                        'name'  => $this->l('Display type'),
                        'value' => 1,
                        'type'  => 'select',
                        'select' => array(
                            0 => $this->l('Simple grid'),
                            1 => $this->l('Carousel'),
                            2 => $this->l('Native horizontal scroll'),
                        ),
                    ),
                    'p' => array(
                        'name'  => $this->l('Pagination'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'c-opt',
                    ),
                    'n' => array(
                        'name'  => $this->l('Navigation arrows'),
                        'value' => 1,
                        'type'  => 'switcher',
                        'class' => 'c-opt',
                    ),
                    'a' => array(
                        'name'  => $this->l('Enable autoplay'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'c-opt',
                    ),
                    'ah' => array(
                        'name'  => $this->l('Stop autoplay on hover'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'c-opt',
                    ),
                    'ps' => array(
                        'name'  => $this->l('Autoplay interval'),
                        'tooltip'  => $this->l('Time interval between each auto transition (in ms)'),
                        'value' => 4000,
                        'type'  => 'text',
                        'class' => 'c-opt',
                    ),
                    'l' => array(
                        'name'  => $this->l('Loop'),
                        'value' => 1,
                        'type'  => 'switcher',
                        'class' => 'c-opt',
                    ),
                    's' => array(
                        'name'  => $this->l('Animation speed (ms)'),
                        'value' => 100,
                        'type'  => 'text',
                        'class' => 'c-opt',
                    ),
                    'm' => array(
                        'name'    => $this->l('Slides to move'),
                        'tooltip' => $this->l('Number of slides moved per transition. Set 0 to move all visible slides'),
                        'value'   => 1,
                        'type'    => 'text',
                        'class'   => 'c-opt',
                    ),
                    'min_width'   => array(
                        'name'    => $this->l('Min item width (px)'),
                        'tooltip' => $this->l('If item width gets less than this, number of columns will be decreased.'),
                        'value'   => 150,
                        'type'    => 'text',
                        'class'   => 'c-opt',
                    ),
                    'normalize_h' => array(
                        'name'    => $this->l('Normalize heights'),
                        'tooltip' => $this->l('Force same height for all elements'),
                        'value'   => 0,
                        'type'    => 'switcher',
                    ),
                    'total' => array(
                        'name'  => $this->l('Total items'),
                        'value' => 10,
                        'type'  => 'text'
                    ),
                    'r' => array(
                        'name'    => $this->l('Visible rows'),
                        'tooltip' => $this->l('You can rotate several rows at once'),
                        'value'   => 1,
                        'type'    => 'select',
                        'select'  => $int_options,
                        'class'   => 'c-opt',
                    ),
                    'i' => array(
                        'name'    => $this->l('Visible columns'),
                        'tooltip' => $this->l('Number of visible items in a row'),
                        'value'   => 4,
                        'type'    => 'select',
                        'select'  => $int_options,
                    ),
                    'i_1200' => array(
                        'name'    => $this->l('Visible columns on displays < 1200px'),
                        'tooltip' => $this->l('Number of visible items in a row for displays smaller than 1200px.'),
                        'value'   => 4,
                        'type'    => 'select',
                        'select'  => $int_options,
                    ),
                    'i_992' => array(
                        'name'   => $this->l('Visible columns on displays < 992px'),
                        'value'  => 3,
                        'type'   => 'select',
                        'select' => $int_options,
                    ),
                    'i_768' => array(
                        'name'   => $this->l('Visible columns on displays < 768px'),
                        'value'  => 2,
                        'type'   => 'select',
                        'select' => $int_options,
                    ),
                    'i_480' => array(
                        'name'   => $this->l('Visible columns on displays < 480px'),
                        'value'  => 1,
                        'type'   => 'select',
                        'select' => $int_options,
                    ),
                );
                break;
            case 'exceptions':
                $fields = array (
                    'display' => array(
                        'name' => $this->l('Display carousel'),
                        'type' => 'custom',
                        'value' => array(),
                        'selectors' => array(
                            'page' => $this->getPageExceptionsOptions(),
                            'customer' => array(
                                '0' => $this->l('For all customers'),
                                'group' => $this->l('Only for selected customer groups'),
                                'customer' => $this->l('Only for selected customers'),
                            ),
                        ),
                    ),
                );
                break;
            case 'special':
                $sorted_manufacturers = $sorted_suppliers = array(0 => '-');
                foreach (Manufacturer::getManufacturers() as $m) {
                    $sorted_manufacturers[$m['id_manufacturer']] = $m['name'];
                }
                foreach (Supplier::getSuppliers() as $s) {
                    $sorted_suppliers[$s['id_supplier']] = $s['name'];
                }

                $fields = array(
                    'id_feature' => array(
                        'name'   => $this->l('Feature group IDs'),
                        'tooltip' => $this->l('Leave it empty to display products, matching by all feature groups'),
                        'value'  => '',
                        'type'   => 'text',
                        'class' => 'special_option samefeature',
                    ),
                    'min_matches' => array(
                        'name'   => $this->l('Minimum matches'),
                        'tooltip' => $this->l('Display products, having at least this number of matching properties'),
                        'value'  => '0',
                        'type'   => 'select',
                        'select' => $int_options + array('0' => $this->l('All available')),
                        'class' => 'special_option samefeature sametag',
                    ),
                    'product_ids' => array(
                        'name'    => $this->l('Product ids'),
                        'tooltip' => $this->l('Separated by comma (1,2,3 ...)'),
                        'value'   => '',
                        'type'    => 'text',
                        'class' => 'special_option products',
                    ),
                    'cat_ids' => array(
                        'name'    => $this->l('Category ids'),
                        'tooltip' => $this->l('Separated by comma (1,2,3 ...)'),
                        'value'   => '',
                        'type'    => 'text',
                        'class' => 'special_option catproducts categories',
                    ),
                    'parent_ids' => array(
                        'name'    => $this->l('Category parents'),
                        'tooltip' => $this->l('Leave empty if you want to display subcategories of current category'),
                        'value'   => '',
                        'type'    => 'text',
                        'class' => 'special_option subcategories',
                    ),
                    'id_manufacturer' => array(
                        'name'   => $this->l('Manufacturer'),
                        'value'  => 0,
                        'type'   => 'select',
                        'select' => $sorted_manufacturers,
                        'class' => 'special_option bymanufacturer',
                    ),
                    'id_supplier' => array(
                        'name'   => $this->l('Supplier'),
                        'value'  => 0,
                        'type'   => 'select',
                        'select' => $sorted_suppliers,
                        'class' => 'special_option bysupplier',
                    ),
                );
                break;
            case 'php':
                $fields = array(
                    'order_by' => array(
                        'name'   => $this->l('Order by'),
                        'value'  => 'default',
                        'type'   => 'select',
                        'select' => array(
                            'default' => $this->l('Default way for this carousel'),
                            'random' => $this->l('Randomly'),
                            'date_add' => $this->l('Date added'),
                            'date_upd' => $this->l('Date updated'),
                            'name' => $this->l('Name'),
                            // 'reference' => $this->l('Product reference'),
                            // 'position' => $this->l('Position in category'),
                            // 'price' => $this->l('Price'),
                        ),
                        'class'   => 'p-option',
                    ),
                    'consider_cat' => array(
                        'name'    => $this->l('Consider category'),
                        'tooltip' => $this->l('Show products only from current category, if carousel is displayed on category page'),
                        'value'   => 0,
                        'type'    => 'switcher',
                        'class'   => 'p-option not-for-some not-for-accessories not-for-samecategory
                        not-for-samefeature not-for-sametag',
                    ),
                );
                break;
            case 'tpl':
                $product_manufacturer_options = array(
                    '0' => $this->l('Don\'t display'),
                    '1' => $this->l('Title'),
                );
                $image_types = array('--' => 'not-for-p');
                $required_types = array('products', 'categories', 'manufacturers', 'suppliers');
                foreach (ImageType::getImagesTypes() as $t) {
                    $img_type = $t['name'];
                    $cls = array();
                    $include = false;
                    foreach ($required_types as $rt) {
                        if (!$t[$rt]) {
                            $cls[] = 'not-for-'.Tools::substr($rt, 0, 1);
                        } else {
                            $include = true;
                            if ($rt == 'manufacturers') {
                                $product_manufacturer_options[$img_type] = $this->l('Logo').': '.$img_type;
                            }
                        }
                    }
                    if ($include) {
                        $image_types[$img_type] = implode(' ', $cls);
                    }
                }
                $image_types['original'] = '';
                $product_manufacturer_options['original'] = $this->l('Logo').': original';
                $home_type = $this->is_17 ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home');
                $fields = array (
                    'custom_class' => array(
                        'name'    => $this->l('Container class'),
                        'tooltip' => $this->l('It will be applied to container of items'),
                        'value'   => '',
                        'type'    => 'text',
                        'class'   => 'custom-class',
                    ),
                    'external_tpl' => array(
                        'name'   => $this->l('Product item template'),
                        'value'  => 0,
                        'type'   => 'select',
                        'select' => array(
                            0 => $this->l('Built-in (configurable)'),
                            1 => $this->l('External template file'),
                        ),
                        'class' => 'p-option external-tpl',
                    ),
                    'image_type' => array(
                        'name'   => $this->l('Image'),
                        'value'  => $home_type,
                        'type'   => 'select',
                        'select' => $image_types,
                    ),
                    'second_image' => array(
                        'name'   => $this->l('Second image on hover'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'title_one_line' => array(
                        'name'  => $this->l('Title in one line'),
                        'tooltip' => $this->l('Truncate title if its length overlaps first line'),
                        'value' => 1,
                        'type'  => 'switcher',
                    ),
                    'title' => array(
                        'name'  => $this->l('Title length (symbols)'),
                        'tooltip' => $this->l('Set 0 if you don\'t want to display title'),
                        'value' => 45,
                        'type'  => 'text',
                    ),
                    'reference' => array(
                        'name'  => $this->l('Product reference'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'description' => array(
                        'name'  => $this->l('Description length'),
                        'value' => 0,
                        'type'  => 'text',
                    ),
                    'product_cat' => array(
                        'name'  => $this->l('Product category'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'product_man' => array(
                        'name'  => $this->l('Product manufacturer'),
                        'value' => 0,
                        'class' => 'p-option',
                        'type' => 'select',
                        'select' => $product_manufacturer_options,
                    ),
                    'price' => array(
                        'name'  => $this->l('Price'),
                        'value' => 1,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'add_to_cart' => array(
                        'name'  => $this->l('Add to cart button'),
                        'value' => 1,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'view_more' => array(
                        'name'  => $this->l('View more'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'quick_view' => array(
                        'name'  => $this->l('Quick view'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'stock' => array(
                        'name'  => $this->l('Stock data'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'stickers' => array(
                        'name'  => $this->l('Stickers'),
                        'value' => 1,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    ),
                    'view_all' => array(
                        'name'  => $this->l('Link to all items'),
                        'value' => 0,
                        'type'  => 'select',
                        'select' => array(
                            0 => $this->l('None'),
                            1 => $this->l('Displayed below carousel'),
                            2 => $this->l('Bound to carousel title'),
                        ),
                        'class' => 'special_option newproducts bestsellers pricesdrop bymanufacturer bysupplier',
                    ),
                    'matches' => array(
                        'name'  => $this->l('Number of matches'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'special_option manufacturers suppliers',
                    ),
                    'c_matches' => array(
                        'name'  => $this->l('Show number of matches'),
                        'value' => 0,
                        'type'  => 'select',
                        'select' => array(
                            0 => $this->l('None'),
                            1 => $this->l('Products associated directly'),
                            2 => $this->l('All products from subcategories'),
                        ),
                        'class' => 'special_option categories subcategories',
                    ),
                    'external_tpl_path' =>  array(
                        'name'    => $this->l('External template path'),
                        'tooltip' => $this->l('Relative path to existing tpl file in theme directory'),
                        'value'   => $this->is_17 ? 'templates/catalog/_partials/miniatures/product.tpl' : '',
                        'type'    => 'text',
                        'class'   => 'p-option external-tpl-path',
                    ),
                );
                if (Module::isInstalled('productlistthumbnails')) {
                    $fields['thumbnails'] = array(
                        'name'  => $this->l('Product thumbnails'),
                        'value' => 0,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    );
                }
                $available_hooks = array('ProductPriceBlock', 'ProductListReviews');
                if ($this->is_17) {
                    $available_hooks[] = 'ProductListFunctionalButtons';
                } else {
                    $available_hooks[] = 'ProductDeliveryTime';
                }
                foreach ($available_hooks as $k => $hook) {
                    $full_hook_name = 'display'.$hook;
                    $fields[$full_hook_name] = array(
                        'name'  => $hook,
                        'tooltip' => sprintf($this->l('All data hooked to %s'), $full_hook_name),
                        'value' => 1,
                        'type'  => 'switcher',
                        'class' => 'p-option',
                    );
                    if (!$k) {
                        $fields[$full_hook_name]['separator'] = $this->l('Displayed hooks');
                    }
                }
                break;
        }
        if (isset($advanced_options[$type])) {
            foreach ($advanced_options[$type] as $a_name => $visible) {
                if (isset($fields[$a_name])) {
                    $fields[$a_name]['class'] = isset($fields[$a_name]['class']) ? $fields[$a_name]['class'].' ' : '';
                    $fields[$a_name]['class'] .= 'advanced-option';
                    if (!$visible) {
                        $opt = $fields[$a_name];
                        $opt['class'] .= ' force-hidden';
                        unset($fields[$a_name]); // move to the end
                        $fields[$a_name] = $opt;
                    }
                }
            }
        }
        return $fields;
    }

    public function getPageExceptionsOptions()
    {
        $pages = array(
            'product' => $this->l('product'),
            'category' => $this->l('category'),
            'manufacturer' => $this->l('manufacturer'),
            'supplier' => $this->l('supplier'),
            'cms' => $this->l('cms'),
        );
        $options = array('0' => $this->l('On all available pages'));
        foreach ($pages as $k => $page) {
            $options[$k.'_all'] = sprintf($this->l('Only on %s pages'), $page);
            $options[$k] = sprintf($this->l('Only on selected %s pages'), $page);
            if ($k == 'product') {
                $options['product_category'] = $this->l('On product pages inside selected categories');
            } elseif ($k == 'category') {
                $options['subcategory'] = $this->l('On all subcategories of selected categories');
            }
        }
        return $options;
    }

    public function install()
    {
        $installed = true;
        if (!$this->prepareDatabaseTables()
            || !parent::install()
            || !$this->registerHook('displayHeader')) {
            $installed = false;
        }
        if ($installed) {
            $this->prepareDemoContent();
        }
        return $installed;
    }

    public function prepareDatabaseTables()
    {
        $sql = array();
        $sql[] = '
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'easycarousels (
            id_carousel int(10) unsigned NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            hook_name varchar(128) NOT NULL,
            id_wrapper int(10) unsigned NOT NULL,
            in_tabs tinyint(1) NOT NULL DEFAULT 1,
            active tinyint(1) NOT NULL DEFAULT 1,
            position int(10) NOT NULL,
            type varchar(128) NOT NULL,
            settings text NOT NULL,
            PRIMARY KEY (id_carousel, id_shop),
            KEY hook_name (hook_name),
            KEY position (position),
            KEY active (active),
            KEY in_tabs (in_tabs)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'easycarousels_lang (
            id_carousel int(10) unsigned NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            id_lang int(10) unsigned NOT NULL,
            data text NOT NULL,
            PRIMARY KEY (id_carousel, id_shop, id_lang)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'ec_wrapper (
            id_wrapper int(10) unsigned NOT NULL AUTO_INCREMENT,
            settings text NOT NULL,
            PRIMARY KEY (id_wrapper)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        $sql[] = '
            CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'ec_hook_settings (
            hook_name varchar(64) NOT NULL,
            id_shop int(10) unsigned NOT NULL,
            display text NOT NULL,
            exc_type tinyint(1) NOT NULL DEFAULT 1,
            exc_controllers text NOT NULL,
            PRIMARY KEY (hook_name, id_shop)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

        if (!$created = $this->runSql($sql)) {
            $this->context->controller->errors[] = $this->l('Database table was not installed properly');
        }
        return $created;
    }

    public function prepareDemoContent()
    {
        if ($demo_file_path = $this->getDemoFilePath()) {
            $this->importCarousels($demo_file_path);
        }
    }

    public function getDemoFilePath()
    {
        $demo_file_path = $this->local_path.'democontent/carousels-custom.txt';
        if (!file_exists($demo_file_path)) {
            $demo_file_path = $this->local_path.'democontent/carousels-'.($this->is_17 ? '17' : '16').'.txt';
        }
        return file_exists($demo_file_path) ? $demo_file_path : false;
    }

    public function uninstall()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'easycarousels';
        $sql[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'easycarousels_lang';
        $sql[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_wrapper';
        $sql[] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_hook_settings';

        if (!$this->runSql($sql) || !parent::uninstall()) {
            return false;
        }
        $this->processOverride('removeOverride', $this->getOverridePath('Product'), false);
        return true;
    }

    public function runSql($sql)
    {
        foreach ($sql as $s) {
            if (!$this->db->execute($s)) {
                return false;
            }
        }
        return true;
    }

    /**
    * easycarousels table has a composite KEY that cannot be autoincremented
    **/
    public function getNewCarouselId()
    {
        $max_id = $this->db->getValue('SELECT MAX(id_carousel) FROM '._DB_PREFIX_.'easycarousels');
        return (int)$max_id + 1;
    }

    public function getNextCarouselPosition($hook_name)
    {
        $max_position = $this->db->getValue('
            SELECT MAX(position) FROM '._DB_PREFIX_.'easycarousels WHERE hook_name = \''.pSQL($hook_name).'\'
        ');
        return (int)$max_position + 1;
    }

    public function prepaceMCEContentCSS()
    {
        $mce_content_css = !$this->is_17 ? _THEME_CSS_DIR_.'global.css' :
        _THEME_CSS_DIR_.'theme.css, '._THEME_CSS_DIR_.'custom.css';
        $mce_content_css .= ', '.$this->_path.'views/css/mce_custom.css';
        return $mce_content_css;
    }

    public function getContent()
    {
        $this->failed_txt = $this->l('Failed');
        $this->saved_txt = $this->l('Saved');

        if ($action = Tools::getValue('action')) {
            if (Tools::getValue('ajax')) {
                $action_method = 'ajax'.$action;
                if (method_exists($this, $action_method) && is_callable(array($this, $action_method))) {
                    $this->$action_method();
                }
            } elseif ($action == 'exportCarousels') {
                return $this->exportCarousels();
            }
            return;
        }

        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->css_files[$this->_path.'views/css/back.css?'.$this->version] = 'all';
        $this->context->controller->js_files[] = $this->_path.'views/js/back.js?'.$this->version;
        $this->context->controller->addJS(__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js');
        if (file_exists(_PS_ROOT_DIR_.'/js/admin/tinymce.inc.js')) {
            $this->context->controller->addJS(__PS_BASE_URI__.'js/admin/tinymce.inc.js');
        } else { // retro-compatibility
            $this->context->controller->addJS(__PS_BASE_URI__.'js/tinymce.inc.js');
        }
        $iso = $this->context->language->iso_code;
        $js = '
            <script type="text/javascript">
                var iso = \''.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\';
                var mce_content_css = \''.$this->prepaceMCEContentCSS().'\';
                var ad = \''.dirname($_SERVER['PHP_SELF']).'\';
                var failedTxt = \''.$this->escapeApostrophe($this->failed_txt).'\';
                var savedTxt = \''.$this->escapeApostrophe($this->saved_txt).'\';
                var areYouSureTxt = \''.$this->escapeApostrophe($this->l('Are you sure?')).'\';
            </script>
        ';
        $html = $this->displayForm();
        return $js.$html;
    }

    private function displayForm()
    {
        $carousels = $this->getAllCarousels();
        $hooks = $this->getAvailableHooks();

        $sorted_hooks = array();
        foreach (array_keys($carousels) as $hook_name) {
            if (!$hook_name) {
                continue;
            }
            $total = 0;
            foreach ($carousels[$hook_name] as $carousels_in_wrapper) {
                $total += count($carousels_in_wrapper);
            }
            $sorted_hooks[$hook_name] = $total;
        }
        arsort($sorted_hooks);

        foreach ($hooks as $hook_name => $count) {
            if (!isset($sorted_hooks[$hook_name])) {
                $sorted_hooks[$hook_name] = $count;
            }
        }

        $this->context->smarty->assign(array(
            'hooks' => $sorted_hooks,
            'carousels' => $carousels,
            'custom_code' => $this->getCustomCode(),
            'type_names' => $this->getTypeNames(),
            'id_lang_current' => $this->context->language->id,
            'iso_lang_current' => $this->context->language->iso_code,
            'overrides_data' => $this->getOverridesData(),
            'ec' => $this,
            'howto_tpl_path' => $this->getTemplatePath('views/templates/admin/importer-how-to.tpl'),
            'info_links' => array(
                'changelog' => $this->_path.'Readme.md?v='.$this->version,
                'documentation' => $this->_path.'readme_en.pdf?v='.$this->version,
                'contact' => 'https://addons.prestashop.com/contact-form.php?id_product=18853',
                'modules' => 'https://addons.prestashop.com/en/2_community-developer?contributor=64815',
            ),
            'module_obj' => $this,
            'is_17' => $this->is_17,
        ));
        $html = $this->display(__FILE__, 'views/templates/admin/configure.tpl');
        return $html;
    }

    public function getCustomCode()
    {
        $custom_code = array('css' => '', 'js' => '');
        foreach ($custom_code as $type => &$code) {
            $path = $this->getCustomCodeFilePath($type);
            if (file_exists($path)) {
                $code = Tools::file_get_contents($path);
            }
        }
        return $custom_code;
    }

    public function getOverridesData()
    {
        $overrides = array();
        foreach (Tools::scandir($this->custom_overrides_dir, 'php', '', true) as $file) {
            $class_name = basename($file, '.php');
            if ($class_name != 'index') {
                $path = $this->getOverridePath($class_name);
                $installed = $this->isOverrideInstalled($path);
                $overrides[$class_name] = array(
                    'note' => $this->getOverrideNote($class_name, $installed),
                    'path' => $path,
                    'installed' => $installed,
                );
            }
        }
        return $overrides;
    }

    public function getOverridePath($class_name)
    {
        if (empty($this->ps_autoload)) {
            $this->ps_autoload = PrestaShopAutoload::getInstance();
        }
        return $this->ps_autoload->getClassPath($class_name.'Core');
    }

    public function getOverrideNote($class_name, $installed)
    {
        $note = '';
        switch ($class_name) {
            case 'Product':
                if ($this->accessoriesDisplayed()) {
                    $note = $this->l('Required to replace native accessories carousel on product page');
                } else {
                    $note = $this->l('Not required');
                    if ($installed === true) {
                        $note .= '. '.$this->l('You can safely uninstall it');
                    }
                }
                break;
        }
        return $note;
    }

    public function isOverrideInstalled($path)
    {
        $shop_override_path = _PS_OVERRIDE_DIR_.$path;
        $module_override_path = $this->custom_overrides_dir.$path;
        $methods_to_override = $already_overriden = array();
        if (file_exists($module_override_path)) {
            $lines = file($module_override_path);
            foreach ($lines as $line) {
                // note: this check is available only for public functions
                if (Tools::substr(trim($line), 0, 6) == 'public') {
                    $key = trim(current(explode('(', $line)));
                    $methods_to_override[$key] = 0;
                }
            }
        }
        $name_length = Tools::strlen($this->name);
        if (file_exists($shop_override_path)) {
            $lines = file($shop_override_path);
            foreach ($lines as $i => $line) {
                if (Tools::substr(trim($line), 0, 6) == 'public') {
                    $key = trim(current(explode('(', $line)));
                    if (isset($methods_to_override[$key])) {
                        unset($methods_to_override[$key]);
                        // if there is no comment about installed override
                        if (!isset($lines[$i - 4]) ||
                            Tools::substr(trim($lines[$i - 4]), - $name_length) !== $this->name) {
                            $key = explode('function ', $key);
                            if (isset($key[1])) {
                                $already_overriden[] = $key[1].'()';
                            }
                        }
                    }
                }
            }
        }
        $installed = (bool)!$methods_to_override;
        if ($already_overriden) {
            $installed = implode(', ', $already_overriden);
        }
        return $installed;
    }

    public function processOverride($action, $override, $throw_error = true)
    {
        $processed = false;
        switch ($action) {
            case 'addOverride':
            case 'removeOverride':
                $file_path = $this->custom_overrides_dir.$override;
                $tmp_path = $this->local_path.'override/'.$override;
                if (file_exists($file_path)) {
                    if (is_writable(dirname($tmp_path))) {
                        try {
                            // temporarily copy file to /override/ folder for processing it natively
                            Tools::copy($file_path, $tmp_path);
                            $class_name = basename($override, '.php');
                            $processed = $this->$action($class_name);
                            unlink($tmp_path);
                        } catch (Exception $e) {
                            unlink($tmp_path);
                            if ($throw_error) {
                                $this->throwError($e->getMessage());
                            }
                        }
                    } elseif ($throw_error) {
                        $dir_name = str_replace(_PS_ROOT_DIR_, '', dirname($tmp_path)).'/';
                        $txt = $this->l('Make sure the following directory is writable: %s');
                        $this->throwError(sprintf($txt, $dir_name));
                    }
                }
                break;
        }
        return $processed;
    }

    public function ajaxProcessOverride()
    {
        $override = Tools::getValue('override');
        $override_action = Tools::getValue('override_action');
        $ret = array(
            'processed' => $this->processOverride($override_action, $override),
        );
        exit(Tools::jsonEncode($ret));
    }

    public function accessoriesDisplayed($id_shop = false, $active = false)
    {
        $displayed = $this->db->getValue('
            SELECT * FROM '._DB_PREFIX_.'easycarousels WHERE type = \'accessories\''
            .($id_shop ? ' AND id_shop = '.(int)$id_shop : '')
            .($active ? ' AND active = 1' : '').'
        ');
        return $displayed;
    }

    public function renderPossibleWarnings()
    {
        $html = '';
        $file_warnings = $customizable_layout_files = array();
        $locations = array(
            '/css/' => 'css',
            '/js/'  => 'js',
            '/templates/admin/' => 'tpl',
            '/templates/hook/' => 'tpl',
            '/templates/front/' => 'tpl',
        );
        foreach ($locations as $loc => $ext) {
            $loc = 'views'.$loc;
            $files = glob($this->local_path.$loc.'*.'.$ext);
            foreach ($files as $file) {
                $customizable_layout_files[] = '/'.$loc.basename($file);
            }
        }
        foreach ($customizable_layout_files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $customized_file_path = _PS_THEME_DIR_;
            if (!$this->is_17 && $ext != 'tpl') {
                $customized_file_path .= $ext.'/';
            }
            $customized_file_path .= 'modules/'.$this->name.$file;
            if (file_exists($customized_file_path)) {
                $original_file_path = $this->local_path.$file;
                $original_rows = file($original_file_path);
                $original_identifier = trim(array_pop($original_rows));
                $customized_rows = file($customized_file_path);
                $customized_identifier = trim(array_pop($customized_rows));
                if (Tools::substr($original_identifier, 1, 7) === '* since' &&
                    $original_identifier != $customized_identifier) {
                    $path = explode('/themes/', $customized_file_path);
                    $path = isset($path[1]) ? '/themes/'.$path[1] : $file;
                    $file_warnings[$path] = $original_identifier;
                }
            }
        }
        $this->context->smarty->assign(array(
            'file_warnings' => $file_warnings,
        ));
        $html .= $this->display(__FILE__, 'views/templates/admin/warnings.tpl');
        return $html;
    }

    public function getAvailableHooks()
    {
        $methods = get_class_methods(__CLASS__);
        $available_hooks = array();
        foreach ($methods as $m) {
            if (Tools::substr($m, 0, 11) === 'hookDisplay') {
                $available_hooks[] = str_replace('hookDisplay', 'display', $m);
            }
        }
        $to_exclude = array('displayHeader');
        if (!$this->is_17) {
            $to_exclude = array_merge(
                $to_exclude,
                array('displayFooterAfter', 'displayFooterBefore', 'displayOrderConfirmation2',
                'displayCrossSellingShoppingCart', 'displayReassurance', 'displayNavFullWidth',
                'displayNav1', 'displayNav2', 'displaySearch')
            );
        }
        $available_hooks = array_diff($available_hooks, $to_exclude);
        $available_hooks = array_fill_keys($available_hooks, 0);
        ksort($available_hooks);
        return $available_hooks;
    }

    public function exportCarousels()
    {
        $languages = Language::getLanguages(false);
        $lang_id_iso = array();
        foreach ($languages as $lang) {
            $lang_id_iso[$lang['id_lang']] = $lang['iso_code'];
        }

        $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $tables_to_export = array(
            'easycarousels',
            'easycarousels_lang',
            'ec_wrapper',
            'ec_hook_settings',
            'hook_module'
        );
        $export_data = array();
        foreach ($tables_to_export as $table_name) {
            $data_from_db = $this->db->executeS('SELECT * FROM '._DB_PREFIX_.pSQL($table_name));
            $ret = array();
            switch ($table_name) {
                case 'easycarousels':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $ret[$id_shop][$d['id_carousel']] = $d;
                    }
                    break;
                case 'easycarousels_lang':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $l_iso = $d['id_lang'] == $id_lang_default ? 'LANG_ISO_DEFAULT' : $lang_id_iso[$d['id_lang']];
                        $ret[$id_shop][$l_iso][$d['id_carousel']] = $d;
                    }
                    break;
                case 'ec_hook_settings':
                    foreach ($data_from_db as $d) {
                        $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                        $ret[$id_shop][$d['hook_name']] = $d;
                    }
                    break;
                case 'hook_module':
                    foreach ($data_from_db as $d) {
                        if ($d['id_module'] == $this->id) {
                            $id_shop = $d['id_shop'] == $id_shop_default ? 'ID_SHOP_DEFAULT' : $d['id_shop'];
                            $hook_name = Hook::getNameByid($d['id_hook']);
                            $ret[$id_shop][$hook_name] = $d['position'];
                        }
                    }
                    break;
                default:
                    $ret = $data_from_db;
                    break;
            }
            $export_data[$table_name] = $ret;
        }
        foreach (array('css', 'js') as $type) {
            $custom_file_path = $this->getCustomCodeFilePath($type);
            if (file_exists($custom_file_path)) {
                $export_data[$type] = Tools::file_get_contents($custom_file_path);
            }
        }
        $export_data = Tools::jsonEncode($export_data);
        $file_name = 'carousels-'.date('d-m-Y').'.txt';
        header('Content-disposition: attachment; filename='.$file_name);
        header('Content-type: text/plain');
        echo $export_data;
        exit();
    }

    public function ajaxImportCarousels()
    {
        if ($this->importCarousels()) {
            $ret = array('upd_html' => utf8_encode($this->import_response.$this->displayForm()));
        } else {
            $ret = array('errors' => $this->import_response);
        }
        exit(Tools::jsonEncode($ret));
    }

    public function getRequiredFields($advanced_options = array())
    {
        $keys = array('php', 'special', 'tpl', 'carousel');
        $required_fields = array();
        foreach ($keys as $key) {
            $required_fields[$key] = $this->getFields($key, $advanced_options);
        }
        return $required_fields;
    }

    public function importCarousels($file_path = false)
    {
        if (!$file_path) {
            if (!isset($_FILES['carousels_data_file'])
            || !is_uploaded_file($_FILES['carousels_data_file']['tmp_name'])) {
                return $this->displayError($this->l('File not uploaded'));
            }
            $file_path = $_FILES['carousels_data_file']['tmp_name'];
        }

        $imported_data = Tools::jsonDecode(Tools::file_get_contents($file_path), true);
        $shop_ids = Shop::getContextListShopID();
        $languages = Language::getLanguages(false);
        $lang_iso_id = array();
        foreach ($languages as $lang) {
            $lang_iso_id[$lang['iso_code']] = $lang['id_lang'];
        }

        $tables_to_fill = array();
        $hooks_data = array();

        $required_fields = $this->getRequiredFields();

        foreach ($shop_ids as $id_shop) {
            $key = isset($imported_data['easycarousels'][$id_shop]) ? $id_shop : 'ID_SHOP_DEFAULT';
            $carousels = $imported_data['easycarousels'][$key];
            foreach ($carousels as $c) {
                $c['id_shop'] = $id_shop;
                // make sure all settings are filled properly
                $settings = Tools::jsonDecode($c['settings'], true);
                if (!isset($settings['php']['order_by']) && isset($settings['php']['randomize'])) {
                    $settings['php']['order_by'] = 'random'; // retro compatibility
                }
                foreach ($required_fields as $key => $fields) {
                    foreach ($fields as $name => $f) {
                        if (!isset($settings[$key][$name])) {
                            $settings[$key][$name] = $f['value'];
                        }
                    }
                }
                $c['settings'] = Tools::jsonEncode($settings);
                if (!isset($c['id_wrapper'])) {
                    $c['id_wrapper'] = 0; // retro compatibility
                }
                $tables_to_fill['easycarousels'][] = $c;
            }

            $key = isset($imported_data['easycarousels_lang'][$id_shop]) ? $id_shop : 'ID_SHOP_DEFAULT';
            $carousels_lang = $imported_data['easycarousels_lang'][$key];
            foreach ($lang_iso_id as $iso => $id_lang) {
                $key = isset($carousels_lang[$iso]) ? $iso : 'LANG_ISO_DEFAULT';
                $rows = $carousels_lang[$key];
                foreach ($rows as $row) {
                    $row['id_shop'] = $id_shop;
                    $row['id_lang'] = $id_lang;
                    $tables_to_fill['easycarousels_lang'][] = $row;
                }
            }

            if (!empty($imported_data['ec_wrapper'])) {
                $tables_to_fill['ec_wrapper'] = $imported_data['ec_wrapper'];
            }

            // ec_hook_settings
            if ($imported_data['ec_hook_settings']) {
                if (isset($imported_data['ec_hook_settings'][$id_shop])) {
                    $settings_rows = $imported_data['ec_hook_settings'][$id_shop];
                } else {
                    $settings_rows = $imported_data['ec_hook_settings']['ID_SHOP_DEFAULT'];
                }
                foreach ($settings_rows as $row) {
                    $row['id_shop'] = $id_shop;
                    $tables_to_fill['ec_hook_settings'][] = $row;
                }
            }

            // hooks & positions
            if ($imported_data['hook_module']) {
                if (isset($imported_data['hook_module'][$id_shop])) {
                    $hooks_data[$id_shop] = $imported_data['hook_module'][$id_shop];
                } else {
                    $hooks_data[$id_shop] = $imported_data['hook_module']['ID_SHOP_DEFAULT'];
                }
            }
        }

        $sql = array();
        foreach ($tables_to_fill as $table_name => $rows_to_insert) {
            $db_columns = $this->db->executeS('SHOW COLUMNS FROM '._DB_PREFIX_.pSQL($table_name));
            foreach ($db_columns as &$col) {
                $col = $col['Field'];
            }
            $test_row_columns = array_keys(current($rows_to_insert));
            foreach ($test_row_columns as $col_name) {
                if (!in_array($col_name, $db_columns)) {
                    $err = $this->l('This file can not be used for import. Reason: Database tables don\'t match (%s).');
                    return $this->throwError(sprintf($err, _DB_PREFIX_.$table_name));
                }
            }

            $delete_query = 'DELETE FROM '._DB_PREFIX_.pSQL($table_name);
            if ($table_name != 'ec_wrapper') {
                $delete_query .= ' WHERE id_shop IN ('.implode(', ', array_map('intval', $shop_ids)).')';
            }
            $sql[] = $delete_query;

            $rows = array();
            $column_names = array();
            foreach ($rows_to_insert as $row) {
                if (!$column_names) {
                    $column_names = array_keys($row);
                }
                foreach ($row as $name => &$r) {
                    $allow_html = $name == 'data' ? true : false;
                    $r = pSQL($r, $allow_html);
                }
                $rows[] = '(\''.implode('\', \'', $row).'\')';
            }
            if (!$rows || !$column_names) {
                continue;
            }
            $sql[] = '
                INSERT INTO '._DB_PREFIX_.pSQL($table_name).' ('.implode(', ', array_map('pSQL', $column_names)).')
                VALUES '.implode(', ', $rows).'
            ';
        }
        if (!$sql) {
            $this->throwError($this->l('Nothing to import'));
        }

        if ($imported = $this->runSql($sql)) {
            // save original shop context, because it will be changed while setting up hooks & exceptions
            $original_shop_context = Shop::getContext();
            $original_shop_context_id = null;
            if ($original_shop_context == Shop::CONTEXT_GROUP) {
                $original_shop_context_id = $this->context->shop->id_shop_group;
            } elseif ($original_shop_context == Shop::CONTEXT_SHOP) {
                $original_shop_context_id = $this->context->shop->id;
            }
            foreach ($hooks_data as $id_shop => $hook_list) {
                foreach ($hook_list as $hook_name => $cb_position) {
                    if ($id_shop != $this->context->shop->id) {
                        Cache::clean('hook_module_list');
                        Shop::setContext(Shop::CONTEXT_SHOP, $id_shop);
                    }
                    $id_hook = Hook::getIdByName($hook_name);
                    $this->registerHook($hook_name, array($id_shop));
                    $this->updatePosition($id_hook, 0, $cb_position);
                }
            }
            Shop::setContext($original_shop_context, $original_shop_context_id);
            foreach (array('css', 'js') as $type) {
                if (!empty($imported_data[$type])) {
                    file_put_contents($this->getCustomCodeFilePath($type), $imported_data[$type]);
                }
            }
            $this->import_response = $this->displayConfirmation($this->l('Data was successfully  imported'));
        } else {
            $this->import_response = $this->displayError($this->l('An error occured while importing data'));
        }
        return $imported;
    }

    public function addJS($file, $custom_path = '')
    {
        $path = ($custom_path ? $custom_path : 'modules/'.$this->name.'/views/js/').$file;
        if ($this->is_17) {
            $params = array('server' => $custom_path ? 'remote' : 'local');
            $this->context->controller->registerJavascript(sha1($path), $path, $params);
        } else {
            $path = $custom_path ? $path : __PS_BASE_URI__.$path;
            $this->context->controller->addJS($path);
        }
    }

    public function addCSS($file, $custom_path = '', $media = 'all')
    {
        $path = ($custom_path ? $custom_path : 'modules/'.$this->name.'/views/css/').$file;
        if ($this->is_17) {
            $params = array('media' => $media, 'server' => $custom_path ? 'remote' : 'local');
            $this->context->controller->registerStylesheet(sha1($path), $path, $params);
        } else {
            $path = $custom_path ? $path : __PS_BASE_URI__.$path;
            $this->context->controller->addCSS($path, $media);
        }
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addJqueryPlugin('bxslider');
        $this->context->controller->addjqueryPlugin('fancybox');
        $this->addCSS('front.css');
        $this->addCSS('custom.css');
        $this->addCSS('bx-styles.css');
        $this->addJS('front.js');
        $this->addJS('custom.js');

        $js_def = array('isMobile' => $this->is_mobile, 'is_17' => $this->is_17);
        if (!$this->is_17) {
            $max_items = $this->context->smarty->tpl_vars['comparator_max_item']->value;
            $js_def['comparator_max_item'] = $max_items;
            $js_def['comparedProductsIds'] = $this->context->smarty->tpl_vars['compared_products']->value;
            $js_def['min_item'] = $this->escapeApostrophe($this->l('Please select at least one product'));
            $js_def['max_item'] = sprintf(
                $this->escapeApostrophe($this->l('You cannot add more than %d product(s) to the product comparison')),
                $max_items
            );
        }
        Media::addJsDef($js_def);
        if ($this->getFullControllerName() == 'product') {
            $this->addViewedProduct(Tools::getValue('id_product'));
            $this->context->accessories_displayed = $this->accessoriesDisplayed($this->context->shop->id, true);
        }
    }

    public function addViewedProduct($id_product)
    {
        $viewed = !empty($this->context->cookie->ec_viewed) ?
        $this->formatIDs($this->context->cookie->ec_viewed, false) : array();
        $viewed = array($id_product => $id_product) + $viewed; // last viewed comes first
        $this->context->cookie->__set('ec_viewed', implode(',', $viewed));
    }

    public function escapeApostrophe($string)
    {
        return str_replace("'", "\'", $string);
    }

    public function ajaxGetCarouselsInHook()
    {
        // $time_start = microtime(true);
        $hook_name = Tools::getValue('hook_name');
        $id_product = $this->formatIDs(Tools::getValue('id_product'), false);
        $id_category = Tools::getValue('id_category');
        $current_id = Tools::getValue('current_id');
        $current_controller = Tools::getValue('current_controller');
        $display_settings = $this->getHookDisplaySettings($hook_name);
        $html = $this->displayCarousels(
            $hook_name,
            $id_product,
            $id_category,
            $current_id,
            $current_controller,
            $display_settings
        );
        $ret = array(
            'carousels_html' => utf8_encode($html),
            // 'time_'.$hook_name => microtime(true) - $time_start,
        );
        exit(Tools::jsonEncode($ret));
    }

    public function displayCarousels(
        $hook_name,
        $id_product,
        $id_category,
        $current_id,
        $current_controller,
        $display_settings
    ) {
        $carousels = $this->getAllCarousels(
            'in_tabs',
            $hook_name,
            true,
            $id_product,
            $id_category,
            $current_id,
            $current_controller
        );

        // get all wrappers settings in one request
        $wrappers_settings = array();
        if ($carousels) {
            $wrapper_ids = array_keys($carousels);
            $wrapper_settings_data = $this->db->executeS('
                SELECT * FROM '._DB_PREFIX_.'ec_wrapper
                WHERE id_wrapper IN('.implode(', ', array_map('intval', $wrapper_ids)).')
            ');
            foreach ($wrapper_settings_data as $s) {
                $wrappers_settings[$s['id_wrapper']] = Tools::jsonDecode($s['settings'], true);
            }
        }
        $smarty_array = array(
            'carousels_in_hook' => $carousels,
            'wrappers_settings' => $wrappers_settings,
            'hook_name' => $hook_name,
            'display_settings' => $display_settings,
            'image_sizes' => $this->image_sizes,
            'carousel_tpl' => $this->getTemplatePath('carousel.tpl'),
            'currency_iso_code' => $this->context->currency->iso_code,
            'static_token' => Tools::getToken(false),
            'is_17' => $this->is_17,
        );
        if (!$this->is_17) {
            $smarty_array['add_to_cart_url'] = $this->context->link->getPageLink('cart', true, null, 'add=1');
        } elseif (empty($this->context->smarty->tpl_vars['urls'])) {
            $smarty_array['urls'] = array( // simplified version of $this->context->controller->getTemplateVarUrls()
                'pages' => array('cart' => $this->context->link->getPageLink('cart', true))
            );
            $smarty_array['page'] = array('page_name' => 'category'); // may be used in external templates
        }
        $this->context->smarty->assign($smarty_array);
        return $this->display(__FILE__, 'views/templates/hook/layout.tpl');
    }

    public function getFullControllerName()
    {
        if (!isset($this->full_controller_name)) {
            $controller = Tools::getValue('controller');
            if (Tools::getValue('fc') == 'module' && Tools::isSubmit('module')) {
                $controller = 'module-'.Tools::getValue('module').'-'.$controller;
            }
            $this->full_controller_name = $controller;
        }
        return $this->full_controller_name;
    }

    public function getCurrentProductIds($current_controller)
    {
        $ids = array();
        $cart_controllers = array('cart' => 1, 'order' => 1, 'orderopc' => 1);
        if ($id_product = Tools::getValue('id_product')) {
            $ids[$id_product] = $id_product;
        } elseif (isset($cart_controllers[$current_controller])) {
            foreach ($this->context->cart->getProducts(false, false, null, false) as $p) {
                $ids[$p['id_product']] = $p['id_product'];
            }
        }
        return $ids;
    }

    public function displayNativeHook($hook_name)
    {
        $current_controller = $this->getFullControllerName();
        $current_id = Tools::getValue('id_'.$current_controller);
        $hook_settings = $this->db->getRow('
            SELECT * FROM '._DB_PREFIX_.'ec_hook_settings
            WHERE hook_name = \''.pSQL($hook_name).'\'
        ');
        if (!empty($hook_settings['exc_type'])) {
            $type = $hook_settings['exc_type'];
            $controllers = array_flip(explode(',', $hook_settings['exc_controllers']));
            if (($type == 1 && isset($controllers[$current_controller])) ||
                ($type == 2 && !isset($controllers[$current_controller]))) {
                return;
            }
        }

        $display_settings = $this->getHookDisplaySettings($hook_name);
        $id_product = $this->getCurrentProductIds($current_controller);
        $id_category = Tools::getValue('id_category');

        if (empty($display_settings['instant_load']) && !Tools::getValue('ajax')) {
            $params = array(
                'ajax' => 1,
                'ajaxGetCarouselsInHook' => 1,
                'hook_name' => $hook_name,
                'id_product' => $this->formatIDs($id_product),
                'id_category' => $id_category,
                'current_id' => $current_id,
                'current_controller' => $current_controller,
            );
            $ajax_path = $this->getAjaxPath('ajax', $params);
            $ret = '<div class="easycarousels dynamic" data-ajaxpath="'.$ajax_path.'"></div>';
        } else {
            $ret = $this->displayCarousels(
                $hook_name,
                $id_product,
                $id_category,
                $current_id,
                $current_controller,
                $display_settings
            );
        }
        return $ret;
    }

    public function getAjaxPath($controller_name = 'ajax', $params = array())
    {
        $ssl = !empty($this->context->controller->ssl);
        return $this->context->link->getModuleLink($this->name, $controller_name, $params, $ssl);
    }

    public function hookDisplayHome()
    {
        return $this->displayNativeHook('displayHome');
    }

    public function hookDisplayTop()
    {
        return $this->displayNativeHook('displayTop');
    }

    public function hookDisplayTopColumn()
    {
        return $this->displayNativeHook('displayTopColumn');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->displayNativeHook('displayLeftColumn');
    }

    public function hookDisplayRightColumn()
    {
        return $this->displayNativeHook('displayRightColumn');
    }

    public function hookDisplayFooterProduct()
    {
        return $this->displayNativeHook('displayFooterProduct');
    }

    public function hookDisplayFooter()
    {
        return $this->displayNativeHook('displayFooter');
    }

    public function hookDisplayFooterAfter()
    {
        return $this->displayNativeHook('displayFooterAfter');
    }

    public function hookDisplayFooterBefore()
    {
        return $this->displayNativeHook('displayFooterBefore');
    }

    public function hookDisplayShoppingCart()
    {
        return $this->displayNativeHook('displayShoppingCart');
    }

    public function hookDisplayShoppingCartFooter()
    {
        return $this->displayNativeHook('displayShoppingCartFooter');
    }

    public function hookDisplayOrderConfirmation2()
    {
        return $this->displayNativeHook('displayOrderConfirmation2');
    }

    public function hookDisplayCrossSellingShoppingCart()
    {
        return $this->displayNativeHook('displayCrossSellingShoppingCart');
    }

    public function hookDisplaySearch()
    {
        return $this->displayNativeHook('displaySearch');
    }

    public function hookDisplayNavFullWidth()
    {
        return $this->displayNativeHook('displayNavFullWidth');
    }

    public function hookDisplayNav()
    {
        return $this->displayNativeHook('displayNav');
    }

    public function hookDisplayNav1()
    {
        return $this->displayNativeHook('displayNav1');
    }

    public function hookDisplayNav2()
    {
        return $this->displayNativeHook('displayNav2');
    }

    public function hookDisplayReassurance()
    {
        return $this->displayNativeHook('displayReassurance');
    }

    public function hookDisplayEasyCarousel1()
    {
        return $this->displayNativeHook('displayEasyCarousel1');
    }

    public function hookDisplayEasyCarousel2()
    {
        return $this->displayNativeHook('displayEasyCarousel2');
    }

    public function hookDisplayEasyCarousel3()
    {
        return $this->displayNativeHook('displayEasyCarousel3');
    }

    public function hookDisplayEasyCarousel4()
    {
        return $this->displayNativeHook('displayEasyCarousel4');
    }

    public function hookDisplayEasyCarousel5()
    {
        return $this->displayNativeHook('displayEasyCarousel5');
    }

    public function prepareIsNewQuery($alias = 'product_shop')
    {
        $nb_days_new = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $nb_days_new = $nb_days_new ? $nb_days_new : 20;
        return pSQL($alias).'.date_add > "'.pSQL(date('Y-m-d', strtotime('-'.(int)$nb_days_new.' DAY'))).'"';
    }

    public function prepareOrderBy($order_by, $default_value = 'product_shop.date_add DESC')
    {
        $queries = array(
            'default'  => $default_value,
            'date_add' => 'product_shop.date_add DESC',
            'date_upd' => 'product_shop.date_upd DESC',
            'name'     => 'pl.name ASC',
            'random'   => 'RAND()',
        );
        // secondary ordering is added because some columns can have replicating values
        return (isset($queries[$order_by]) ? $queries[$order_by].', ' : '').'product_shop.id_product DESC';
    }

    public function getMatchingFeatures($current_product_ids, $imploded_group_ids, $implode = true)
    {
        $ids = array();
        if ($current_product_ids) {
            $feature_values = $this->db->executeS('
                SELECT id_feature_value as id FROM '._DB_PREFIX_.'feature_product
                WHERE id_product IN ('.pSQL($current_product_ids).')
                '.($imploded_group_ids ? ' AND id_feature IN ('.pSQL($imploded_group_ids).')' : '').'
            ');
            foreach ($feature_values as $row) {
                $ids[$row['id']] = $row['id'];
            }
        }
        return $this->formatIDs($ids, $implode);
    }

    public function getMatchingTags($current_product_ids, $id_lang, $implode = true)
    {
        $ids = array();
        if ($current_product_ids) {
            $product_tags = $this->db->executeS('
                SELECT id_tag as id FROM '._DB_PREFIX_.'product_tag
                WHERE id_product IN ('.pSQL($current_product_ids).') AND id_lang = '.(int)$id_lang.'
            ');
            foreach ($product_tags as $row) {
                $ids[$row['id']] = $row['id'];
            }
        }
        return $this->formatIDs($ids, $implode);
    }

    public function getMatchingCategories($current_product_ids, $id_shop, $implode = true)
    {
        $ids = array();
        if ($current_product_ids) {
            // first goes current product category defined basing on user navigation, if available
            if ($this->getFullControllerName() == 'product' &&
                method_exists($this->context->controller, 'getCategory') &&
                $id_product_category = $this->context->controller->getCategory()->id) {
                $ids[$id_product_category] = $id_product_category;
            }
            // then goes default category. It will be used if there will be no matches by current category
            $cat_rows = $this->db->executeS('
                SELECT id_category_default as id FROM '._DB_PREFIX_.'product_shop
                WHERE id_product IN ('.pSQL($current_product_ids).') AND id_shop = '.(int)$id_shop.'
            ');
            foreach ($cat_rows as $row) {
                $ids[$row['id']] = $row['id'];
            }
        }
        return $this->formatIDs($ids, $implode);
    }

    public function getMatchingProducts($all_matches, $available_min, $settings_min, $limit)
    {
        $sorted_matches = $matching_products = array();
        foreach ($all_matches as $p) {
            $sorted_matches[$p['id_product']][$p['id_match']] = $p['id_match'];
        }
        $min_matches = $settings_min && $settings_min < $available_min ? $settings_min : $available_min;
        foreach ($sorted_matches as $id => $matching_values) {
            if (count($matching_values) >= $min_matches) {
                $matching_products[$id] = array('id_product' => $id); // same format as in other carousels
            }
            if (count($matching_products) >= $limit) {
                break;
            }
        }
        return $matching_products;
    }

    public function getCarouselProducts($type, $settings, $current_category, $current_products, $details = true)
    {
        $products = array();
        $current_product_ids = $this->formatIDs($current_products);
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $order_by = !empty($settings['php']['order_by']) ? $settings['php']['order_by'] : 'default';
        $default_order_by = 'product_shop.date_add DESC';

        $query = new DbQuery();
        $query->select('DISTINCT(p.id_product)');
        $query->from('product', 'p');
        $query->join(Shop::addSqlAssociation('product', 'p'));
        $query->where('product_shop.active = 1 AND product_shop.visibility IN ("both", "catalog")');
        if ($current_category && $settings['php']['consider_cat']) {
            $query->innerJoin('category_product', 'cat_p', 'cat_p.id_product = p.id_product');
            $query->where('cat_p.id_category = '.(int)$current_category);
        }
        if ($order_by == 'name') {
            $query->leftJoin('product_lang', 'pl', 'pl.id_product = p.id_product');
            $query->where('pl.id_lang = '.(int)$id_lang.' AND pl.id_shop = '.(int)$id_shop);
        }

        switch ($type) {
            case 'newproducts':
                $query->where($this->prepareIsNewQuery());
                break;
            case 'featuredproducts':
                $query->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product');
                $query->where('cp.id_category = '.(int)$this->context->shop->getCategory());
                $default_order_by = 'cp.position ASC';
                break;
            case 'catproducts':
                if (!empty($settings['special']['cat_ids'])) {
                    $query->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product');
                    $query->where('cp.id_category IN ('.$this->formatIDs($settings['special']['cat_ids']).')');
                    $default_order_by = 'cp.position ASC';
                } else {
                    $query = false;
                }
                break;
            case 'pricesdrop':
                $today = date('Y-m-d G:i:s');
                $query->innerJoin('specific_price', 'sp', 'sp.id_product = p.id_product');
                $query->where('sp.id_customer IN (0,'.(int)$this->context->customer->id.')');
                $query->where('sp.id_group IN (0,'.pSQL($this->getCustomerGroups(true)).')');
                $query->where('sp.id_shop IN (0,'.(int)$id_shop.')');
                $query->where('sp.from = "0000-00-00 00:00:00" OR sp.from <= "'.pSQL($today).'"');
                $query->where('sp.to = "0000-00-00 00:00:00" OR sp.to >= "'.pSQL($today).'"');
                $query->where('sp.reduction > 0');
                // $default_order_by = 'product_shop.price ASC';
                break;
            case 'bestsellers':
                $query->innerJoin('product_sale', 'ps', 'ps.id_product = p.id_product');
                $default_order_by = 'ps.quantity DESC';
                break;
            case 'viewedproducts':
            case 'products':
                $ids = array();
                if ($type == 'viewedproducts' && !empty($this->context->cookie->ec_viewed)) {
                    $ids = $this->context->cookie->ec_viewed;
                } elseif ($type == 'products' && !empty($settings['special']['product_ids'])) {
                    $ids = $settings['special']['product_ids'];
                }
                if ($imploded_ids = $this->formatIDs($ids)) {
                    $query->where('p.id_product IN ('.pSQL($imploded_ids).')');
                    if ($current_product_ids) {
                        $query->where('p.id_product NOT IN ('.pSQL($current_product_ids).')');
                    }
                    $default_order_by = 'FIELD(product_shop.id_product, '.pSQL($imploded_ids).')';
                } else {
                    $query = false;
                }
                break;
            case 'bymanufacturer':
                if (!empty($settings['special']['id_manufacturer'])) {
                    $query->where('p.id_manufacturer = '.(int)$settings['special']['id_manufacturer']);
                } else {
                    $query = false;
                }
                break;
            case 'bysupplier':
                if (!empty($settings['special']['id_supplier'])) {
                    $query->innerJoin('product_supplier', 'psup', 'psup.id_product = p.id_product');
                    $query->where('psup.id_product_attribute = 0');
                    $query->where('psup.id_supplier = '.(int)$settings['special']['id_supplier']);
                } else {
                    $query = false;
                }
                break;
            case 'accessories':
                if ($current_product_ids) {
                    $query->innerJoin('accessory', 'a', 'a.id_product_2 = p.id_product');
                    $query->where('a.id_product_1 IN ('.pSQL($current_product_ids).')');
                    $query->where('a.id_product_2 NOT IN ('.pSQL($current_product_ids).')');
                    $default_order_by = '1';
                } else {
                    $query = false;
                }
                break;
            case 'samecategory':
                if ($imploded_cat_ids = $this->getMatchingCategories($current_product_ids, $id_shop)) {
                    $query->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product');
                    $query->where('cp.id_category IN ('.pSQL($imploded_cat_ids).')');
                    $query->where('p.id_product NOT IN ('.pSQL($current_product_ids).')');
                    // first go matches by current category, then by default category
                    $query->orderBy('FIELD(cp.id_category, '.pSQL($imploded_cat_ids).')');
                    $default_order_by = 'cp.position ASC';
                } else {
                    $query = false;
                }
                break;
            case 'samefeature':
            case 'sametag':
                $limit = $settings['carousel']['total'];
                foreach ($current_products as $id_product) { // define min matches for each product individually
                    if ($type == 'samefeature') {
                        $table = 'feature_product';
                        $match_identifier = 'id_feature_value';
                        $imploded_group_ids = $this->formatIDs($settings['special']['id_feature']);
                        $imploded_matching_properties = $this->getMatchingFeatures($id_product, $imploded_group_ids);
                    } else {
                        $table = 'product_tag';
                        $match_identifier = 'id_tag';
                        $imploded_matching_properties = $this->getMatchingTags($id_product, $id_lang);
                    }
                    if ($imploded_matching_properties) {
                        $query->select($table.'.'.$match_identifier.' as id_match');
                        $query->innerJoin($table, $table, $table.'.id_product = p.id_product');
                        $query->where($table.'.'.$match_identifier.' IN ('.pSQL($imploded_matching_properties).')');
                        $query->where('p.id_product NOT IN ('.pSQL($current_product_ids).')');
                        $query->orderBy($this->prepareOrderBy($order_by));
                        $all_matches = $this->db->executeS($query);

                        $available_min = count(explode(',', $imploded_matching_properties));
                        $settings_min = $settings['special']['min_matches'];
                        // each matching product will have key as id_product, so it is OK to use +=
                        $products += $this->getMatchingProducts($all_matches, $available_min, $settings_min, $limit);
                    }
                    $limit -= count($products);
                    if ($limit < 1) {
                        break;
                    }
                }
                $query = false;
                break;
        }
        if ($query) {
            $query->orderBy($this->prepareOrderBy($order_by, $default_order_by));
            $query->limit((int)$settings['carousel']['total']);
            $products = $this->db->executeS($query);
        }
        return $details ? $this->getProductsInfos($products, $id_lang, $id_shop, $settings['tpl']) : $products;
    }

    public function getCarouselItems($type, $item_type, $settings, $current_category)
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $items = array();
        $identifier = 'id_'.$item_type;
        $query = new DbQuery();
        if ($type == 'subcategories') {
            $imploded_parent_ids = !empty($settings['special']['parent_ids']) ?
            $this->formatIDs($settings['special']['parent_ids']) : (int)$current_category;
            if ($imploded_parent_ids) {
                $query->where('main.id_parent IN ('.pSQL($imploded_parent_ids).')');
                $query->orderBy('category_shop.position ASC');
            } else {
                $query = false;
            }
        } elseif ($type == 'categories') {
            if ($imploded_ids = $this->formatIDs($settings['special']['cat_ids'])) {
                $query->where('main.id_category IN ('.pSQL($imploded_ids).')');
                $query->orderBy('FIELD(main.id_category, '.pSQL($imploded_ids).')');
            } else {
                $query = false;
            }
        } else {
            $query->orderBy('main.name ASC');
        }
        if ($query) {
            $query->select('DISTINCT(main.'.pSQL($identifier).') AS id, name, description');
            $query->from(pSQL($item_type), 'main');
            $query->join(Shop::addSqlAssociation($item_type, 'main'));
            $query->innerJoin(pSQL($item_type).'_lang', 'lang', 'lang.'.pSQL($identifier).' = main.'.pSQL($identifier));
            $query->where('main.active = 1 AND lang.id_lang = '.(int)$id_lang);
            if ($item_type == 'category') {
                $query->select('link_rewrite');
                $query->innerJoin('category_group', 'cg', 'cg.id_category = main.id_category');
                $query->where('cg.id_group IN ('.pSQL($this->getCustomerGroups(true)).')');
                $query->where('main.id_parent > 0 AND main.id_category <> '.(int)$this->context->shop->getCategory());
                $query->where('lang.id_shop = '.(int)$id_shop);
            }
            $query->limit((int)$settings['carousel']['total']);
            $items = $this->db->ExecuteS($query);
        }
        return $items;
    }

    public function getStructuredCarouselItems($type, $item_type, $settings, $current_category, $current_products)
    {
        if (empty($settings['carousel']['total'])) {
            return array();
        }
        if ($item_type == 'product') {
            $items = $this->getCarouselProducts($type, $settings, $current_category, $current_products);
        } else {
            $items = $this->getCarouselItems($type, $item_type, $settings, $current_category);
            $m_key = $item_type == 'category' ? 'c_matches' : 'matches';
            $show_matches = !empty($settings['tpl'][$m_key]) ? $settings['tpl'][$m_key] : 0;
            $consider_subcategories = $show_matches == 2;
        }

        if ($settings['carousel']['type'] != 1) {
            $settings['carousel']['r'] = 1;
        }

        $structured_items = array();
        $current_row = 1;
        $current_col = 0;
        foreach ($items as $item) {
            if ($current_col >= ceil(count($items) / $settings['carousel']['r'])) {
                $current_row++;
                $current_col = 0;
            }
            $current_col++;
            if ($item_type != 'product') {
                $item['img_src'] = $this->getImageUrl($item_type, $item['id'], $settings['tpl']['image_type']);
                $alias = isset($item['link_rewrite']) ? $item['link_rewrite'] : Tools::str2url($item['name']);
                $item['url'] = $this->getItemUrl($item_type, $item['id'], $alias);
                if ($show_matches) {
                    $item['matches'] = $this->getMatchesNum($item_type, $item['id'], $consider_subcategories);
                }
            }
            $structured_items[$current_col][$current_row] = $item;
        }
        return $structured_items;
    }

    public function getCarouselItemType($type)
    {
        $other_types = array(
            'manufacturers' => 'manufacturer',
            'suppliers' => 'supplier',
            'categories' => 'category',
            'subcategories' => 'category',
        );
        return isset($other_types[$type]) ? $other_types[$type] : 'product';
    }

    public function getFullExternalTplPath($tpl_path)
    {
        $external_tpl_path = _PS_THEME_DIR_.$tpl_path;
        return is_file($external_tpl_path) ? $external_tpl_path : '';
    }

    public function getCarouselItemTpl($item_type, $settings)
    {
        if ($item_type == 'product') {
            if (empty($settings['external_tpl']) ||
                !$tpl_path = $this->getFullExternalTplPath($settings['external_tpl_path'])) {
                $tpl_path = $this->getTemplatePath('product-item-'.($this->is_17 ? '17' : '16').'.tpl');
            }
        } else {
            $tpl_path = $this->getTemplatePath('item.tpl');
        }
        return $tpl_path;
    }

    public function getImageUrl($resource_type, $id, $image_type)
    {
        if ($image_type == '--') {
            return false;
        }
        $first_char = Tools::substr($resource_type, 0, 1);
        $local_dir = _PS_IMG_DIR_.$first_char.'/';
        $dir = _PS_IMG_.$first_char.'/';
        $img = $id.($image_type != 'original' ? '-'.$image_type : '').'.jpg';
        $default_img = $this->context->language->iso_code.'-default-'.$image_type.'.jpg';
        return file_exists($local_dir.$img) ? $dir.$img : $dir.$default_img;
    }

    public function getItemUrl($item_type, $id, $alias = null)
    {
        $url = '#';
        $method = 'get'.Tools::ucfirst($item_type).'Link';
        if (is_callable(array($this->context->link, $method))) {
            $url = $this->context->link->$method($id, $alias);
        }
        return $url;
    }

    public function getMatchesNum($item_type, $id, $all = false)
    {
        $num = 0;
        $sql_data = array(
            'category' => array('category_product', ''),
            'manufacturer' => array('product', ''),
            'supplier' => array('product_supplier', ' AND main.id_product_attribute = 0'),
        );
        $imploded_ids = $item_type == 'category' && $all ? $this->getSubcategoryIDs($id) : (int)$id;
        if (isset($sql_data[$item_type]) && $imploded_ids) {
            $num = $this->db->getValue('
                SELECT COUNT(DISTINCT(main.id_product)) FROM '._DB_PREFIX_.pSQL($sql_data[$item_type][0]).' main
                '.Shop::addSqlAssociation('product', 'main').'
                WHERE product_shop.active = 1 AND product_shop.visibility IN ("both", "catalog")
                AND main.id_'.pSQL($item_type).' IN ('.pSQL($imploded_ids).')'.pSQL($sql_data[$item_type][1]).'
            ');
        }
        return $num;
    }

    public function getSubcategoryIDs($id_parent, $include_self = true, $implode = true)
    {
        $ids = array();
        if ($include_self) {
            $ids[$id_parent] = $id_parent;
        }
        $parent = $this->db->getRow('SELECT * FROM '._DB_PREFIX_.'category WHERE id_category = '.(int)$id_parent);
        $subcategories = $this->db->executeS('
            SELECT DISTINCT(c.id_category) AS id FROM '._DB_PREFIX_.'category c
            '.Shop::addSqlAssociation('category', 'c').' WHERE c.active = 1
            AND c.nleft > '.(int)$parent['nleft'].' AND c.nright < '.(int)$parent['nright'].'
        ');
        foreach ($subcategories as $row) {
            $ids[$row['id']] = $row['id'];
        }
        return $this->formatIDs($ids, $implode);
    }

    public function getProductsInfos($items, $id_lang, $id_shop, $settings)
    {
        $ids = array();
        foreach ($items as $i) {
            $ids[] = $i['id_product'];
        }
        if (!$ids) {
            return array();
        }
        $show_cat = $settings['product_cat'];
        $show_man = $settings['product_man'];
        $products_infos = array();
        $imploded_ids = implode(', ', $ids);

        $products_data = $this->db->executeS('
            SELECT p.*, product_shop.*, pl.*, image.id_image, il.legend,
            pas.id_product_attribute, pas.minimal_quantity AS pa_minimal_quantity,
            '.($show_cat ? 'cl.name AS cat_name, cl.link_rewrite as cat_link_rewrite, ' : '').'
            '.($show_man ? 'm.name AS man_name, ' : '').'
            '.$this->prepareIsNewQuery().' AS new
            FROM '._DB_PREFIX_.'product p
            '.Shop::addSqlAssociation('product', 'p').'
            INNER JOIN '._DB_PREFIX_.'product_lang pl
                ON (pl.id_product = p.id_product
                AND pl.id_shop = '.(int)$id_shop.' AND pl.id_lang = '.(int)$id_lang.')
            LEFT JOIN '._DB_PREFIX_.'product_attribute_shop pas
                ON pas.id_product_attribute = product_shop.cache_default_attribute
                AND pas.id_shop = product_shop.id_shop
            '.($show_cat ? '
                LEFT JOIN '._DB_PREFIX_.'category_lang cl
                    ON (cl.id_category = product_shop.id_category_default
                    AND cl.id_shop = '.(int)$id_shop.' AND cl.id_lang = '.(int)$id_lang.')
            ' : '').'
            '.($show_man ? '
                LEFT JOIN '._DB_PREFIX_.'manufacturer m
                    ON m.id_manufacturer = p.id_manufacturer AND m.active = 1
            ' : '').'
            LEFT JOIN '._DB_PREFIX_.'image image
                ON (image.id_product = p.id_product AND image.cover = 1)
            LEFT JOIN '._DB_PREFIX_.'image_lang il
                ON (il.id_image = image.id_image AND il.id_lang = '.(int)$id_lang.')
            WHERE p.id_product IN ('.pSQL($imploded_ids).')
        ');

        $second_images = array();
        if (!empty($settings['second_image'])) {
            $second_images_data = $this->db->executeS('
                SELECT i.id_product, i.id_image
                FROM '._DB_PREFIX_.'image i
                '.Shop::addSqlAssociation('image', 'i').'
                WHERE i.id_product IN ('.pSQL($imploded_ids).')
                AND i.cover IS NULL
                GROUP BY i.id_product
            ');
            foreach ($second_images_data as $d) {
                $second_images[$d['id_product']] = $d['id_image'];
            }
        }

        $positions = array_flip($ids);
        foreach ($products_data as $pd) {
            $id = $pd['id_product'];

            if (!empty($pd['pa_minimal_quantity'])) {
                $pd['minimal_quantity'] = $pd['pa_minimal_quantity'];
            }
            // out_of_stock is required to avoid extra queries in getProductProperties
            $pd['out_of_stock'] = StockAvailable::outOfStock($id, $id_shop);
            $pd = Product::getProductProperties($id_lang, $pd);

            $image_type = $settings['image_type'] != 'original' ? $settings['image_type'] : null;
            $link_rewrite = $pd['link_rewrite'];

            if ($this->is_17) {
                $pd = $this->presentProduct($pd);
                if (!$image_type) {
                    $original_img_src = $this->context->link->getImageLink(
                        $pd['link_rewrite'],
                        $pd['cover']['id_image']
                    );
                    $pd['cover']['bySize']['original']['url'] = $original_img_src;
                }
            } else {
                $pd['img_src'] = $this->context->link->getImageLink($link_rewrite, $pd['id_image'], $image_type);
                if ($settings['stock'] && $pd['available_for_order'] && !Configuration::get('PS_CATALOG_MODE') &&
                    Configuration::get('PS_STOCK_MANAGEMENT')) {
                    $pd = $this->addStockStatus($pd);
                }
            }
            if (!empty($second_images[$id])) {
                $src  = $this->context->link->getImageLink($link_rewrite, $second_images[$id], $image_type);
                $pd['second_img_src'] = $src;
            }
            if ($show_man && !empty($pd['id_manufacturer'])) {
                $alias = Tools::str2url($pd['man_name']);
                $pd['man_url'] = $this->getItemUrl('manufacturer', $pd['id_manufacturer'], $alias);
                if ($show_man != 1) {
                    $pd['man_img_src'] = $this->getImageUrl('manufacturer', $pd['id_manufacturer'], $show_man);
                }
            }
            if ($show_cat && !empty($pd['id_category_default'])) {
                $pd['cat_url'] = $this->getItemUrl('category', $pd['id_category_default'], $pd['cat_link_rewrite']);
            }
            $products_infos[$positions[$id]] = $pd;
        }
        ksort($products_infos);
        return $products_infos;
    }

    public function addStockStatus($product)
    {
        $availability_statuses = array(
            'available_now' => $this->l('In Stock'),
            'available_later' => $this->l('In Stock'),
            'available_different' => $this->l('Available with different options'),
            'not_available' => $this->l('Out of stock'),
        );
        if ($product['quantity'] > 0) {
            $status = 'available_now';
        } elseif ($product['allow_oosp']) {
            $status = 'available_later';
        } elseif (isset($product['quantity_all_versions']) &&  $product['quantity_all_versions'] > 0) {
            $status = 'available_different';
        } else {
            $status = 'not_available';
        }
        $product['stock_status'] = $status;
        $product['stock_txt'] = !empty($product[$status]) ? $product[$status] : $availability_statuses[$status];
        return $product;
    }

    public function presentProduct($product_data)
    {
        if (!isset($this->factory_presenter)) {
            $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
            $this->factory_presenter = $factory->getPresenter();
            $this->factory_settings = $factory->getPresentationSettings();
        }
        return $this->factory_presenter->present($this->factory_settings, $product_data, $this->context->language);
    }

    public function ajaxCallCarouselForm()
    {
        $id_carousel = (int)Tools::getValue('id_carousel');
        $hook_name = Tools::getValue('hook_name');
        $id_wrapper = Tools::getValue('id_wrapper');
        $utf8_encoded_form = $this->renderCarouselForm($id_carousel, $hook_name, $id_wrapper);
        exit(Tools::jsonEncode($utf8_encoded_form));
    }

    public function ajaxCallSettingsForm()
    {
        $hook_name = Tools::getValue('hook_name');
        $settings_type = Tools::getValue('settings_type');
        $method = 'getHook'.Tools::ucfirst($settings_type).'Settings';
        if (!is_callable(array($this, $method))) {
            $this->throwError($this->l('This type of settings is not supported'));
        }

        $this->context->smarty->assign(array(
            'settings' => $this->$method($hook_name),
            'settings_type' => $settings_type,
            'hook_name' => $hook_name,
        ));
        $form_html = $this->display($this->local_path, 'views/templates/admin/hook-'.$settings_type.'-form.tpl');
        $ret = array(
            'form_html' => utf8_encode($form_html),
        );
        exit(Tools::jsonEncode($ret));
    }

    public function ajaxCallWrapperSettingsForm()
    {
        $id_wrapper = Tools::getValue('id_wrapper');
        $this->context->smarty->assign(array(
            'id_wrapper' => $id_wrapper,
            'wrapper_fields' => $this->getWrapperFields($id_wrapper),
        ));
        $form_html = $this->display($this->local_path, 'views/templates/admin/wrapper-settings-form.tpl');
        $ret = array(
            'form_html' => utf8_encode($form_html),
        );
        exit(Tools::jsonEncode($ret));
    }

    public function getWrapperFields($id_wrapper = false)
    {
        $fields = array(
            'custom_class' => array(
                'display_name'  => $this->l('Wrapper class'),
                'value' => '',
                'type'  => 'text',
                'validate' => 'isLabel',
            ),
        );
        if ($id_wrapper) {
            $saved_data = $this->db->getValue('
                SELECT settings FROM '._DB_PREFIX_.'ec_wrapper
                WHERE id_wrapper = '.(int)$id_wrapper.'
            ');
            $saved_data = Tools::jsonDecode($saved_data, true);
            foreach (array_keys($fields) as $name) {
                if (isset($saved_data[$name])) {
                    $fields[$name]['value'] = $saved_data[$name];
                }
            }
        }
        return $fields;
    }

    public function ajaxSaveWrapperSettings()
    {
        $fields = $this->getWrapperFields();
        $form_data = Tools::getValue('form_data');
        parse_str($form_data, $form_data);

        $data_to_save = array();
        foreach ($fields as $name => $field) {
            if (isset($form_data[$name])) {
                $validate = $field['validate'];
                if (Validate::$validate($form_data[$name])) {
                    $field['value'] = $form_data[$name];
                } else {
                    $txt = sprintf($this->l('Incorrect value for "%s"'), $field['display_name']);
                    $this->throwError($txt);
                }
            }
            $data_to_save[$name] = $field['value'];
        }
        $id_wrapper = $form_data['id_wrapper'];
        $id_wrapper_new = false;
        if (!$id_wrapper) {
            $id_wrapper_new = $id_wrapper = $this->addWrapper();
            if ($ids_in_wrapper = Tools::getValue('ids_in_wrapper')) {
                $id_carousel_first = current($ids_in_wrapper);
                $this->updateCarouselWrapper($id_carousel_first, $id_wrapper_new, $ids_in_wrapper);
            }
        }
        $ret = array(
            'saved' => $this->saveWrapperSettings($id_wrapper, $data_to_save),
            'id_wrapper_new' => $id_wrapper_new,
        );
        exit(Tools::jsonEncode($ret));
    }

    public function saveWrapperSettings($id_wrapper, $settings)
    {
        if (!Validate::isString($settings)) {
            $settings = Tools::jsonEncode($settings);
        }
        $saved = $this->db->execute('
            INSERT INTO '._DB_PREFIX_.'ec_wrapper (id_wrapper, settings)
            VALUES ('.(int)$id_wrapper.', \''.pSQL($settings).'\')
            ON DUPLICATE KEY UPDATE settings = VALUES(settings)
        ');
        return $saved;
    }

    public function getHookExceptionsSettings($hook_name)
    {
        $shop_ids = Shop::getContextListShopID();
        $exc_data = $this->db->executeS('
            SELECT exc_type, exc_controllers
            FROM '._DB_PREFIX_.'ec_hook_settings
            WHERE hook_name = \''.pSQL($hook_name).'\'
            AND id_shop IN ('.pSQL(implode(', ', $shop_ids)).')
        ');
        $type = 0;
        $current_exceptions = array();
        foreach ($exc_data as $row) {
            if (!$type || $row['id_shop'] == $this->context->controller->id_shop) {
                $type = $row['exc_type'];
            }
            $current_exceptions += explode(',', $row['exc_controllers']);
        }
        $current_exceptions = array_flip($current_exceptions);

        $sorted_exceptions = array(
            'core' => array(
                'group_name' => $this->l('Core pages'),
                'values' => array(),
            ),
            'modules' => array(
                'group_name' => $this->l('Module pages'),
                'values' => array(),
            ),
        );

        $front_controllers = array_keys(Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_));
        $retro_compatibility = array(
            'auth' => 'authentication',
            'compare' => 'productscomparison'
        );
        foreach ($front_controllers as $fc) {
            $fc = isset($retro_compatibility[$fc]) ? $retro_compatibility[$fc] : $fc;
            $sorted_exceptions['core']['values'][$fc] = (int)isset($current_exceptions[$fc]);
        }

        $module_front_controllers = Dispatcher::getModuleControllers('front');
        foreach ($module_front_controllers as $module_name => $controllers) {
            foreach ($controllers as $controller_name) {
                $key = 'module-'.$module_name.'-'.$controller_name;
                $sorted_exceptions['modules']['values'][$key] = (int)isset($current_exceptions[$key]);
            }
        }
        $settings = array(
            'type' => $type,
            'exceptions' => $sorted_exceptions,
        );
        return $settings;
    }

    public function getHookPositionsSettings($hook_name)
    {
        $hook_modules = Hook::getModulesFromHook(Hook::getIdByName($hook_name));
        $sorted = array();
        foreach ($hook_modules as $m) {
            if ($instance = Module::getInstanceByName($m['name'])) {
                $logo_src = false;
                if (file_exists(_PS_MODULE_DIR_.$instance->name.'/logo.png')) {
                    $logo_src = _MODULE_DIR_.$instance->name.'/logo.png';
                }
                $sorted[$m['id_module']] = array(
                    'name' => $instance->name,
                    'position' => $m['m.position'],
                    'enabled' => $instance->isEnabledForShopContext(),
                    'display_name' => $instance->displayName,
                    'description' => $instance->description,
                    'logo_src' => $logo_src,
                );
                if ($m['id_module'] == $this->id) {
                    $sorted[$m['id_module']]['current'] = 1;
                }
            }
        }
        return $sorted;
    }

    public function getHookDisplaySettings($hook_name)
    {
        $hook_settings = array('custom_class' => 'row', 'compact_tabs' => 1, 'instant_load' => 1);
        $saved_settings = $this->db->getValue('
            SELECT display FROM '._DB_PREFIX_.'ec_hook_settings
            WHERE hook_name = \''.pSQL($hook_name).'\' AND id_shop = '.(int)$this->context->shop->id.'
        ');
        if ($saved_settings = Tools::jsonDecode($saved_settings, true)) {
            foreach ($hook_settings as $name => $val) {
                $hook_settings[$name] = isset($saved_settings[$name]) ? $saved_settings[$name] : $val;
            }
        }
        return $hook_settings;
    }

    public function ajaxSaveHookSettings()
    {
        $hook_name = Tools::getValue('hook_name');
        $id_hook = Hook::getIdByName($hook_name);
        $settings_type = Tools::getValue('settings_type');
        $saved = false;
        if ($settings_type == 'display') {
            $display_settings = Tools::jsonEncode(Tools::getValue('settings'));
            $rows = array();
            foreach (Shop::getContextListShopID() as $id_shop) {
                $rows[] = '(\''.pSQL($hook_name).'\', '.(int)$id_shop.', \''.pSQL($display_settings).'\')';
            }
            $saved = $this->db->execute('
                INSERT INTO '._DB_PREFIX_.'ec_hook_settings
                (hook_name, id_shop, display)
                VALUES '.implode(', ', $rows).'
                ON DUPLICATE KEY UPDATE display = VALUES(display)
            ');
        } elseif ($settings_type == 'exceptions') {
            $exc_type = Tools::getValue('exceptions_type');
            $exc_controllers = Tools::getValue('exceptions');
            $shop_ids = Shop::getContextListShopID();
            $saved = $this->saveExceptions($hook_name, $exc_type, $exc_controllers, $shop_ids);
        } elseif ($settings_type == 'position') {
            $id_module = Tools::getValue('id_module');
            $new_position = Tools::getValue('new_position');
            $way = Tools::getValue('way');
            if ($module = Module::getInstanceById($id_module)) {
                $saved = $module->updatePosition($id_hook, $way, $new_position);
            }
        }
        $ret = array('saved' => $saved);
        exit(Tools::jsonEncode($ret));
    }

    public function saveExceptions($hook_name, $exc_type, $exc_controllers, $shop_ids)
    {
        $exc_controllers = is_array($exc_controllers) ? implode(',', $exc_controllers) : $exc_controllers;
        $rows = array();
        foreach ($shop_ids as $id_shop) {
            $row = '\''.pSQL($hook_name).'\', '.(int)$id_shop.', '.(int)$exc_type.', \''.pSQL($exc_controllers).'\'';
            $rows[] = '('.$row.')';
        }
        $saved = true;
        if ($rows) {
            $saved &= $this->db->execute('
                INSERT INTO '._DB_PREFIX_.'ec_hook_settings
                (hook_name, id_shop, exc_type, exc_controllers)
                VALUES '.implode(', ', $rows).'
                ON DUPLICATE KEY UPDATE
                exc_type = VALUES(exc_type),
                exc_controllers = VALUES(exc_controllers)
            ');
        }
        // make sure native exceptions are not used
        $saved = $this->unregisterExceptions(Hook::getIdByName($hook_name), $shop_ids);
        return $saved;
    }

    public function ajaxProcessModule()
    {
        $id_module = Tools::getValue('id_module');
        $hook_name = Tools::getValue('hook_name');
        $act = Tools::getValue('act');
        $module = Module::getInstanceById($id_module);

        $saved = false;
        if (Validate::isLoadedObject($module)) {
            switch ($act) {
                case 'disable':
                    $module->disable();
                    $saved = !$module->isEnabledForShopContext();
                    break;
                case 'unhook':
                    $saved = $module->unregisterHook(Hook::getIdByName($hook_name));
                    break;
                case 'uninstall':
                    if ($id_module != $this->id) {
                        $saved = $module->uninstall();
                    }
                    break;
                case 'enable':
                    $saved = $module->enable();
                    break;
            }
        }
        $ret = array ('saved' => $saved);
        exit(Tools::jsonEncode($ret));
    }

    public function renderCarouselForm($id_carousel, $hook_name, $id_wrapper, $full = true)
    {
        $carousel_info = $this->db->getRow('
            SELECT * FROM '._DB_PREFIX_.'easycarousels
            WHERE id_carousel = '.(int)$id_carousel.' AND '.$this->shopAssoc('where').'
            ORDER BY '.$this->shopAssoc('order_by').'
        ');

        if ($carousel_info) {
            $multilang = $this->db->executeS('
                SELECT data, id_lang FROM '._DB_PREFIX_.'easycarousels_lang
                WHERE id_carousel = '.(int)$id_carousel.' AND '.$this->shopAssoc('where').'
                GROUP BY id_lang
                ORDER BY '.$this->shopAssoc('order_by').'
            ');
            $carousel_info['multilang'] = array();
            foreach ($multilang as $ml) {
                $carousel_info['multilang'][$ml['id_lang']] = Tools::jsonDecode($ml['data'], true);
                if (!isset($carousel_info['name']) || $ml['id_lang'] == $this->context->language->id) {
                    $carousel_info['name'] = $carousel_info['multilang'][$ml['id_lang']]['name'];
                }
            }
            $carousel_info['settings'] = Tools::jsonDecode($carousel_info['settings'], true);
            if ($exc_note = $this->getExceptionsNote($carousel_info['settings'])) {
                $carousel_info['exc_note'] = $exc_note;
            }
        } else {
            $carousel_info = array ( // default carousel data
                'id_carousel' => (int)$id_carousel,
                'active' => 1,
                'type' => 'newproducts',
                'in_tabs' => 0,
                'hook_name' => $hook_name,
                'id_wrapper' => $id_wrapper,
                'settings' => array(),
            );
        }

        $fields = $this->getRequiredFields($this->prepareAdvancedOptions($carousel_info['settings']));
        $fields['exceptions'] = $this->getFields('exceptions');
        $this->context->smarty->assign(array(
            'carousel' => $carousel_info,
            'type_names' => $this->getTypeNames(),
            'fields' => $fields,
            'languages' => Language::getLanguages(false),
            'id_lang_current' => $this->context->language->id,
            'device_types' => $this->getDeviceTypes(),
            'multidevice_settings' => $this->getMultiDeviceSettings(),
            'full' => $full,
            'ec' => $this,
            'multishop_warning' => count(Shop::getContextListShopID()) > 1 ? true : false,
        ));
        $form = $this->display(__FILE__, 'views/templates/admin/carousel-form.tpl');
        return utf8_encode($form);
    }

    public function prepareAdvancedOptions($saved_settings)
    {
        $show_advanced = Tools::getValue('advanced');
        $advanced_options = array(
            'tpl' => array('external_tpl', 'external_tpl_path'),
        );
        foreach ($advanced_options as $type => $fields) {
            $advanced_options[$type] = array_fill_keys($fields, $show_advanced);
        }
        if (!$advanced_options['tpl']['external_tpl']) { // external_tpl should be visible if it was activated before
            foreach (array_keys($this->getDeviceTypes()) as $d_key) {
                if (!empty($saved_settings['tpl'.($d_key == 'desktop' ? '' : '_'.$d_key)]['external_tpl'])) {
                    $advanced_options['tpl']['external_tpl'] = $advanced_options['tpl']['external_tpl_path'] = true;
                    break;
                }
            }
        }
        return $advanced_options;
    }

    public function getDeviceTypes()
    {
        return array('desktop' => $this->l('Desktop version'), 'mobile' => $this->l('Mobile version'));
    }

    public function getMultiDeviceSettings()
    {
        return array('carousel' => 'carousel', 'tpl' => 'tpl');
    }

    public function getExceptionsNote($settings)
    {
        $exc_note = '';
        if (isset($settings['exceptions'])) {
            $exceptions = array();
            if (!empty($settings['exceptions']['page']['type'])) {
                $exceptions[] = $this->l('on selected pages');
            }
            if (!empty($settings['exceptions']['customer']['type'])) {
                $exceptions[] = $this->l('for selected customers');
            }
            if ($exceptions) {
                $exc_note = sprintf($this->l('Displayed %s'), implode('/', $exceptions));
            }
        }
        return $exc_note;
    }

    public function isColumnHook($hook_name)
    {
        $column_hooks = array('displayLeftColumn', 'displayRightColumn');
        return in_array($hook_name, $column_hooks);
    }

    public function ajaxBulkAction()
    {
        $action = Tools::getValue('act');
        $carousel_ids = Tools::getValue('ids');
        if (!$carousel_ids) {
            $this->throwError($this->l('Please make a selection'));
        }
        $shop_ids = Shop::getContextListShopID();
        $success = true;
        $this->response_text = $this->saved_txt;

        switch ($action) {
            case 'enable':
            case 'disable':
                $active = $action == 'enable';
                $success &= $this->db->execute('
                    UPDATE '._DB_PREFIX_.'easycarousels SET active = '.(int)$active.'
                    WHERE id_carousel IN ('.implode(', ', array_map('intval', $carousel_ids)).')
                    AND id_shop IN ('.implode(', ', array_map('intval', $shop_ids)).')
                ');
                break;
            case 'group_in_tabs':
            case 'ungroup':
                $in_tabs = $action == 'group_in_tabs';
                $success &= $this->db->execute('
                    UPDATE '._DB_PREFIX_.'easycarousels SET in_tabs = '.(int)$in_tabs.'
                    WHERE id_carousel IN ('.implode(', ', array_map('intval', $carousel_ids)).')
                    AND id_shop IN ('.implode(', ', array_map('intval', $shop_ids)).')
                ');
                break;
            case 'delete':
                foreach ($carousel_ids as $id_carousel) {
                    $success &= $this->deleteCarousel($id_carousel);
                }
                break;
        }
        $ret = array(
            'success' => $success,
            'reponseText' => $this->response_text,
        );
        exit(Tools::jsonEncode($ret));
    }

    public function ajaxSaveCarousel()
    {
        $id_carousel = $keep_positions = Tools::getValue('id_carousel');
        if ($id_carousel == 0) {
            $id_carousel = $this->getNewCarouselId();
        }
        $params_string = Tools::getValue('carousel_data');
        parse_str($params_string, $params);
        $hook_name = $params['hook_name'];

        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        if (!trim($params['multilang'][$id_lang_default]['name']) && isset($params['in_tabs'])) {
            $this->errors[] = $this->l('Please fill carousel name at least for the following language: ').
            $this->db->getValue('SELECT name FROM '._DB_PREFIX_.'lang WHERE id_lang = '.(int)$id_lang_default);
        }

        if (empty($this->errors) && !$this->saveCarousel($id_carousel, $hook_name, $params)) {
            $this->errors[] = $this->l('Carousel not saved');
        }

        if (!$keep_positions && $ordered_ids_in_hook = Tools::getValue('ids_in_hook')) {
            foreach ($ordered_ids_in_hook as $k => $id) {
                if ($id == 0) {
                    // back.js is responsible for having only one carousel with id=0
                    $ordered_ids_in_hook[$k] = $id_carousel;
                }
            }
            $this->updatePositionsInHook($ordered_ids_in_hook);
        }

        // save wrapper if it was not saved before
        $id_wrapper_new = false;
        if (empty($params['id_wrapper'])) {
            $ids_in_wrapper = Tools::getValue('ids_in_wrapper');
            $id_wrapper_new = $this->updateCarouselWrapper($id_carousel, $id_wrapper_new, $ids_in_wrapper);
        }

        if (!empty($this->errors)) {
            $this->throwError($this->errors);
        }

        $result = array(
            'updated_form_header' => $this->renderCarouselForm($id_carousel, false, false),
            'responseText' => $this->l('Saved'),
            'id_wrapper_new' => $id_wrapper_new,
        );

        exit(Tools::jsonEncode($result));
    }

    public function getCustomCodeFilePath($type)
    {
        return $this->local_path.'views/'.$type.'/custom.'.$type;
    }

    public function ajaxSaveCustomCode()
    {
        $available_types = array('css' => 1, 'js' => 1);
        $type = Tools::getValue('type');
        if (isset($available_types[$type]) && $file_path = $this->getCustomCodeFilePath($type)) {
            if ($code = rtrim(Tools::getValue('code'))) {
                file_put_contents($file_path, $code.PHP_EOL);  // add last empty line to r-trimmed code
            } else {
                unlink($file_path);
            }
            exit(Tools::jsonEncode(array('successText' => $this->l('Saved'))));
        }
    }

    public function ajaxGetOriginalCustomCode()
    {
        $ret = array('original_code' => '');
        $type = Tools::getValue('type');
        if ($demo_file_path = $this->getDemoFilePath()) {
            $demo_data = Tools::jsonDecode(Tools::file_get_contents($demo_file_path), true);
            if (!empty($demo_data[$type])) {
                $ret['original_code'] = utf8_encode($demo_data[$type]);
            }
        }
        exit(Tools::jsonEncode($ret));
    }

    public function formatIDs($ids, $return_string = true)
    {
        if ($ids = is_array($ids) ? $ids : explode(',', $ids)) {
            $ids = array_map('intval', $ids);
            $ids = array_combine($ids, $ids);
            unset($ids[0]);
        }
        return $return_string ? implode(',', $ids) : $ids;
    }

    public function validateSettings($settings, $carousel_type)
    {
        $errors = array();

        if (isset($settings['special'])) {
            foreach ($settings['special'] as $name => &$ids) {
                if ($name != 'min_matches') {
                    $ids = $this->formatIDs($ids);
                }
            }
        }
        if (isset($settings['exceptions'])) {
            foreach ($settings['exceptions'] as $key => &$exc) {
                if ($exc['type'] && Tools::substr($exc['type'], -4) != '_all') {
                    if (!$exc['ids'] = $this->formatIDs($exc['ids'])) {
                        $exc['type'] = ($key == 'page') ? $exc['type'].'_all' : '0';
                    }
                } else {
                    $exc['ids'] = '';
                }
            }
        }

        if (in_array($carousel_type, array('catproducts', 'categories')) && !$settings['special']['cat_ids']) {
            $errors[] = $this->l('Please add at least one category id');
        } elseif ($carousel_type == 'bymanufacturer' && !$settings['special']['id_manufacturer']) {
            $errors[] = $this->l('Please select a manufacturer');
        } elseif ($carousel_type == 'bysupplier' && !$settings['special']['id_supplier']) {
            $errors[] = $this->l('Please select a supplier');
        } elseif ($carousel_type == 'products' && !$settings['special']['product_ids']) {
            $errors[] = $this->l('Please add at least one product id');
        }

        foreach ($this->getDeviceTypes() as $d_type => $d_name) {
            $is_desktop = $d_type == 'desktop';
            $d_key = $is_desktop ? '' : '_'.$d_type;
            $d_txt = $is_desktop ? '' : ' | '.$d_name;

            if (isset($settings['tpl'.$d_key])) {
                if (isset($settings['tpl'.$d_key]['external_tpl_path'])) {
                    $path = trim($settings['tpl'.$d_key]['external_tpl_path'], '/');
                    $settings['tpl'.$d_key]['external_tpl_path'] = $this->getFullExternalTplPath($path) ? $path : '';
                }
                $t = array_merge($settings['tpl'], $settings['tpl'.$d_key]);
                if ($t['external_tpl'] && !$t['external_tpl_path']) {
                    $errors[] = $this->l('Specified tpl file does not exist in your theme directory').$d_txt;
                }
            }

            if (isset($settings['carousel'.$d_key])) {
                $c = array_merge($settings['carousel'], $settings['carousel'.$d_key]);
                if ($c['type'] == 1 && $c['r'] > 1 && $c['total'] % $c['r'] !== 0) {
                    $txt = $this->l('Make sure that number of items (%1$d) is divisible by number of rows (%2$d)');
                    $errors[] = sprintf($txt, $c['total'], $c['r']).$d_txt;
                }
            }
        }

        if ($errors && Tools::getValue('ajax')) {
            $this->throwError($errors);
        }

        return $settings;
    }

    /**
    * @return boolean saved
    **/
    public function saveCarousel($id_carousel, $hook_name, $params)
    {
        foreach (array('php', 'tpl', 'carousel', 'special') as $type) {
            foreach ($this->getFields($type) as $name => $field) {
                if (!isset($params['settings'][$type][$name])) {
                    $params['settings'][$type][$name] = $field['value'];
                }
            }
        }

        $settings = $this->validateSettings($params['settings'], $params['type']);
        $settings = Tools::jsonEncode($settings);

        $languages = Language::getLanguages(false);
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $shop_ids = Shop::getContextListShopID();
        $carousel_rows = $multilang_rows = array();
        $position = !empty($params['position']) ? $params['position'] : $this->getNextCarouselPosition($hook_name);

        foreach ($shop_ids as $id_shop) {
            $carousel_rows[$id_shop] = '(';
            $carousel_rows[$id_shop] .= (int)$id_carousel;
            $carousel_rows[$id_shop] .= ', '.(int)$id_shop;
            $carousel_rows[$id_shop] .= ', \''.pSQL($hook_name).'\'';
            $carousel_rows[$id_shop] .= ', '.(int)($params['id_wrapper']);
            $carousel_rows[$id_shop] .= ', '.(int)!empty($params['in_tabs']);
            $carousel_rows[$id_shop] .= ', '.(int)!empty($params['active']);
            $carousel_rows[$id_shop] .= ', '.(int)$position;
            $carousel_rows[$id_shop] .= ', \''.pSQL($params['type']).'\'';
            $carousel_rows[$id_shop] .= ', \''.pSQL($settings).'\'';
            $carousel_rows[$id_shop] .= ')';
            foreach ($languages as $lang) {
                if (isset($params['multilang'][$lang['id_lang']])) {
                    $data = $params['multilang'][$lang['id_lang']];
                } else {
                    $data = $params['multilang'][$id_lang_default];
                }
                $data = Tools::jsonEncode($data);
                $row = (int)$id_carousel.', '.(int)$id_shop.', '.(int)$lang['id_lang'].', \''.pSQL($data, true).'\'';
                $multilang_rows[] = '('.$row.')';
            }
        }
        $sql = array(
            'REPLACE INTO '._DB_PREFIX_.'easycarousels VALUES '.implode(', ', $carousel_rows),
            'REPLACE INTO '._DB_PREFIX_.'easycarousels_lang VALUES '.implode(', ', $multilang_rows),
        );

        $saved = $this->runSql($sql);

        if ($params['type'] == 'accessories') {
            $this->processOverride('addOverride', $this->getOverridePath('Product'), false);
        }

        // possibe duplicate hookRegistrations are handled in registerHook()
        $this->registerHook($hook_name);
        return $saved;
    }

    /**
    * @return int $id_wrapper
    **/
    public function updateCarouselWrapper($id_carousel, $id_wrapper, $ids_in_wrapper = array())
    {
        if (!is_array($ids_in_wrapper)) {
            return false;
        }
        if (!$id_wrapper) {
            $id_wrapper = $this->addWrapper();
        }
        // make sure id_carousel is included in wrapper
        $ids_in_wrapper = array_combine($ids_in_wrapper, $ids_in_wrapper);
        $ids_in_wrapper[$id_carousel] = $id_carousel;
        unset($ids_in_wrapper[0]);

        if ($ids_in_wrapper) {
            $updated = $this->db->execute('
                UPDATE '._DB_PREFIX_.'easycarousels
                SET id_wrapper = '.(int)$id_wrapper.'
                WHERE id_carousel IN ('.implode(', ', array_map('intval', $ids_in_wrapper)).')
            ');
            if ($updated) {
                // delete unused wrappers
                $wrappers_data = $this->db->executeS('
                    SELECT c.id_carousel, w.id_wrapper
                    FROM '._DB_PREFIX_.'ec_wrapper w
                    LEFT JOIN '._DB_PREFIX_.'easycarousels c
                        ON c.id_wrapper = w.id_wrapper
                ');
                $to_delete = array();
                foreach ($wrappers_data as $w) {
                    if (!$w['id_carousel']) {
                        $to_delete[] = $w['id_wrapper'];
                    }
                }
                if ($to_delete) {
                    $this->db->execute('
                        DELETE FROM '._DB_PREFIX_.'ec_wrapper
                        WHERE id_wrapper IN ('.implode(', ', array_map('intval', $to_delete)).')
                    ');
                }
            }
        }

        return $id_wrapper;
    }

    public function addWrapper()
    {
        $added = $this->db->execute('
            INSERT INTO '._DB_PREFIX_.'ec_wrapper VALUES (0, "[]")
        ');
        return $added ? $this->db->insert_ID() : false;
    }

    public function ajaxToggleParam()
    {
        $id_carousel = Tools::getValue('id_carousel');
        $param_name = Tools::getValue('param_name');
        $param_value = Tools::getValue('param_value');
        if (!$param_name) {
            $this->throwError($this->l('Parameters not provided correctly'));
        }
        $shop_ids = Shop::getContextListShopID();
        $update_query = '
            UPDATE '._DB_PREFIX_.'easycarousels
            SET `'.bqSQL($param_name).'` = '.(int)$param_value.'
            WHERE id_carousel = '.(int)$id_carousel.'
            AND id_shop IN ('.implode(', ', array_map('intval', $shop_ids)).')
        ';
        $ret = array('success' => $this->db->execute($update_query));
        exit(Tools::jsonEncode($ret));
    }

    public function ajaxDeleteCarousel()
    {
        $id_carousel = Tools::getValue('id_carousel');
        $result = array(
            'deleted' => $this->deleteCarousel($id_carousel),
        );
        die(Tools::jsonEncode($result));
    }

    public function deleteCarousel($id_carousel)
    {
        $shop_ids = Shop::getContextListShopID();
        $delete_query = array();
        $delete_query[] = '
            DELETE FROM '._DB_PREFIX_.'easycarousels
            WHERE id_carousel = '.(int)$id_carousel.'
            AND id_shop IN ('.implode(', ', array_map('intval', $shop_ids)).')';
        $delete_query[] = '
            DELETE FROM '._DB_PREFIX_.'easycarousels_lang
            WHERE id_carousel = '.(int)$id_carousel.'
            AND id_shop IN ('.implode(', ', array_map('intval', $shop_ids)).')';
        return $this->runSql($delete_query);
    }

    public function ajaxUpdatePositionsInHook()
    {
        $ordered_ids = Tools::getValue('ordered_ids');
        if (!$ordered_ids) {
            $this->throwError($this->l('Ordering failed'));
        }
        $moved_element_wrapper_id = $moved_element_id = $id_wrapper_new = false;
        if (Tools::getValue('moved_element_is_carousel')) {
            $moved_element_wrapper_id = Tools::getValue('moved_element_wrapper_id');
            $moved_element_id = Tools::getValue('moved_element_id');
            if (!$moved_element_wrapper_id) {
                $id_wrapper_new = $this->updateCarouselWrapper($moved_element_id);
                $moved_element_wrapper_id = $id_wrapper_new;
            }
        }
        if ($this->updatePositionsInHook($ordered_ids, $moved_element_wrapper_id, $moved_element_id)) {
            $ret = array(
                'id_wrapper_new' => $id_wrapper_new,
                'successText' => $this->l('Saved'),
            );
            exit(Tools::jsonEncode($ret));
        } else {
            $this->throwError($this->l('Ordering failed'));
        }
    }

    public function updatePositionsInHook($ordered_ids, $moved_element_wrapper_id = false, $moved_element_id = false)
    {
        if (!$ordered_ids) {
            return true;
        }
        $update_rows = array();
        $shop_ids = Shop::getContextListShopID();
        foreach ($shop_ids as $id_shop) {
            foreach ($ordered_ids as $k => $id_carousel) {
                if ($id_carousel > 0) {
                    $pos = $k + 1;
                    $update_rows[] = '('.(int)$id_carousel.', '.(int)$id_shop.', '.(int)$pos.')';
                }
            }
        }
        $sql = array();
        $sql[] = '
            INSERT INTO '._DB_PREFIX_.'easycarousels (id_carousel, id_shop, position)
            VALUES '.implode(', ', $update_rows).'
            ON DUPLICATE KEY UPDATE
            position = VALUES(position)
        ';
        if ($moved_element_wrapper_id) {
            $sql[] = '
                UPDATE '._DB_PREFIX_.'easycarousels
                SET id_wrapper = '.(int)$moved_element_wrapper_id.'
                WHERE id_carousel = '.(int)$moved_element_id.'
            ';
        }
        return $this->runSql($sql);
    }

    public function getCarouselName($type)
    {
        $type_names = $this->getTypeNames(false);
        $name = isset($type_names[$type]) ? $type_names[$type] : $type;
        return $name;
    }

    public function shopAssoc($type = 'where', $alias = '')
    {
        if (!isset($this->shop_assoc)) {
            $id_shop = $this->context->shop->id;
            $shop_ids = $this->formatIDs(Shop::getContextListShopID(), false);
            if (isset($shop_ids[$id_shop])) { // move current shop to beginning
                $shop_ids = array($id_shop => $id_shop) + $shop_ids;
            }
            $imploded_shop_ids = implode(',', $shop_ids);
            $field = ($alias ? $alias.'.' : '').'id_shop';
            $this->shop_assoc = array(
                'where' => pSQL($field).' IN ('.pSQL($imploded_shop_ids).')',
                'order_by' => 'FIELD('.pSQL($field).', '.pSQL($imploded_shop_ids).')',
            );
        }
        return isset($this->shop_assoc[$type]) ? $this->shop_assoc[$type] : '';
    }

    public function getAllCarousels(
        $sort_by = 'hook_name',
        $hook_name = false,
        $front = false,
        $id_product = 0,
        $id_category = 0,
        $current_id = 0,
        $current_controller = ''
    ) {
        $where = 'WHERE '.$this->shopAssoc('where', 'c');
        if ($hook_name) {
            $where .= ' AND c.hook_name = \''.pSQL($hook_name).'\'';
        }
        if ($front) {
            $where .= ' AND c.active = 1';
        }

        $carousels = $this->db->ExecuteS('
            SELECT c.*, cl.data AS multilang_data
            FROM '._DB_PREFIX_.'easycarousels c
            LEFT JOIN '._DB_PREFIX_.'easycarousels_lang cl
                ON c.id_carousel = cl.id_carousel
                AND cl.id_lang = '.(int)$this->context->language->id.'
                AND cl.id_shop = c.id_shop
            '.$where.'
            GROUP BY id_carousel
            ORDER BY '.$this->shopAssoc('order_by').', position
        ');

        if ($sort_by) {
            $sorted_carousels = array();
            foreach ($carousels as $k => $c) {
                $carousel = array();
                // id_carousel, id_shop, in_tabs etc...
                foreach ($c as $name => $value) {
                    $carousel[$name] = $value;
                }

                $settings = Tools::jsonDecode($c['settings'], true);
                $multilang = Tools::jsonDecode($c['multilang_data'], true);
                $carousel['name'] = $multilang['name'];

                if (!$front) {
                    if ($exc_note = $this->getExceptionsNote($settings)) {
                        $carousel['exc_note'] = $exc_note;
                    }
                } else {
                    if (isset($settings['exceptions'])) {
                        $type = $settings['exceptions']['page']['type'];
                        $allowed_ids = $this->formatIDs($settings['exceptions']['page']['ids'], false);
                        if ($type == 'product_category') {
                            if ($current_controller != 'product' || !$allowed_ids ||
                                !$this->isInCategory($current_id, $allowed_ids)) {
                                continue;
                            }
                        } elseif ($type == 'subcategory') {
                            if ($current_controller != 'category' || !$allowed_ids ||
                                !$this->isSubcategory($this->context->controller->getCategory(), $allowed_ids)) {
                                continue;
                            }
                        } elseif ($allowed_controller = str_replace('_all', '', $type)) {
                            if ($allowed_controller != $current_controller ||
                                $allowed_ids && !isset($allowed_ids[$current_id])) {
                                continue;
                            }
                        }
                        if ($customer_exceptions = $settings['exceptions']['customer']['type']) {
                            $allowed_ids = $this->formatIDs($settings['exceptions']['customer']['ids'], false);
                            if ($customer_exceptions == 'customer' &&
                                !isset($allowed_ids[$this->context->customer->id])) {
                                continue;
                            } elseif ($customer_exceptions == 'group' &&
                                !array_intersect($this->getCustomerGroups(), $allowed_ids)) {
                                continue;
                            }
                        }
                    }
                    if ($this->is_mobile) {
                        foreach ($this->getMultiDeviceSettings() as $settings_type) {
                            if (isset($settings[$settings_type.'_mobile'])) {
                                $settings[$settings_type] = array_merge(
                                    $settings[$settings_type],
                                    $settings[$settings_type.'_mobile']
                                );
                            }
                        }
                    }
                    $carousel['item_type'] = $this->getCarouselItemType($carousel['type']);
                    $carousel['items'] = $this->getStructuredCarouselItems(
                        $carousel['type'],
                        $carousel['item_type'],
                        $settings,
                        $id_category,
                        $id_product
                    );
                    if (!$carousel['items']) {
                        continue;
                    }
                    $carousel['identifier'] = $carousel['type'].'_'.$carousel['id_carousel'];
                    $carousel['description'] = $multilang['description'];
                    $carousel['item_tpl'] = $this->getCarouselItemTpl($carousel['item_type'], $settings['tpl']);
                    $carousel['settings'] = $settings;
                    if (!empty($settings['tpl']['view_all'])) {
                        $link = $this->getLinkToAllItems($c['type'], $settings);
                        $carousel['view_all_link'] = $link;
                    }
                    if (!$carousel['name'] && $carousel['in_tabs']) {
                        $carousel['name'] = $this->getCarouselName($carousel['type']);
                    }
                    // prepare image sizes to be used later in tpls
                    foreach (array('image_type', 'product_man') as $i) {
                        if (!empty($settings['tpl'][$i])) {
                            $img_type = $settings['tpl'][$i];
                            if ($img_type && $img_type != 1 && empty($this->image_sizes[$img_type])) {
                                $this->image_sizes[$img_type] = Image::getSize($img_type);
                            }
                        }
                    }
                }
                if (!empty($carousel)) {
                    $sorting_key = $c[$sort_by];
                    if ($sort_by == 'hook_name') {
                        $sorted_carousels[$sorting_key][$c['id_wrapper']][$k] = $carousel;
                    } else {
                        if ($sort_by == 'in_tabs') {
                            $sorting_key = $sorting_key ? 'in_tabs' : 'one_by_one';
                        }
                        $sorted_carousels[$c['id_wrapper']][$sorting_key][$k] = $carousel;
                    }
                }
            }
            $carousels = $sorted_carousels;
        }
        return $carousels;
    }

    public function isInCategory($id_product, $cat_ids)
    {
        // detect only direct associations
        return (bool)$this->db->getValue('
            SELECT id_product FROM '._DB_PREFIX_.'category_product
            WHERE id_product = '.(int)$id_product.' AND id_category IN ('.pSQL($this->formatIDs($cat_ids)).')
        ');
    }

    public function isSubCategory($category_obj, $parent_ids)
    {
        // detect parents of all levels
        return (bool)$this->db->getValue('
            SELECT id_category FROM '._DB_PREFIX_.'category
            WHERE nleft < '.(int)$category_obj->nleft.' AND nright > '.$category_obj->nright.'
            AND id_category IN ('.pSQL($this->formatIDs($parent_ids)).')
        ');
    }

    public function getCustomerGroups($implode = false)
    {
        if (!isset($this->customer_groups)) {
            $this->customer_groups = $this->context->customer->getGroups();
        }
        return $implode ? $this->formatIDs($this->customer_groups) : $this->customer_groups;
    }

    public function getLinkToAllItems($carousel_type, $settings)
    {
        $link = '';
        switch ($carousel_type) {
            case 'newproducts':
                $link = $this->context->link->getPageLink('new-products');
                break;
            case 'bestsellers':
                $link = $this->context->link->getPageLink('best-sales');
                break;
            case 'pricesdrop':
                $link = $this->context->link->getPageLink('prices-drop');
                break;
            case 'bymanufacturer':
                $link = $this->context->link->getManufacturerLink($settings['special']['id_manufacturer']);
                break;
            case 'bysupplier':
                $link = $this->context->link->getSupplierLink($settings['special']['id_supplier']);
                break;
        }
        return $link;
    }

    public function throwError($errors)
    {
        if (!is_array($errors)) {
            $errors = array($errors);
        }
        $errors_html = $this->displayError(implode('<br>', $errors));
        if (Tools::isSubmit('ajax')) {
            die(Tools::jsonEncode(array('errors' => utf8_encode($errors_html))));
        }
        return $errors_html;
    }
}
