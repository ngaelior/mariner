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

include(_PS_MODULE_DIR_.'/pshomeslider/classes/PsHomeSlide.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

class Pshomeslider extends Module
{
    public $configurationFields = array(
        'PS_SLIDER_TRANSITION' => '1',
        'PS_SLIDER_SPEED' => '5000',
        'PS_SLIDER_PAUSE_HOVER' => 1,
        'PS_SLIDER_LOOP' => 1,
        'PS_SLIDER_NAVIGATION_TYPE' => '3',
        'PS_SLIDER_TITLE_FONT' => 'Lato',
        'PS_SLIDER_TITLE_TEXT_SIZE' => 50,
        'PS_SLIDER_PARAGRAPH_FONT' => 'Noto Sans',
        'PS_SLIDER_PARAGRAPH_TEXT_SIZE' => 26,
        'PS_SLIDER_NAVIGATION_COLOR' => '#f2f2f2',
    );

    public $hook = array(
        'displayNavFullWidth',
        'actionFrontControllerSetMedia'
    );

    private $fonts = array(
        1 => 'Roboto',
        2 => 'Montserrat',
        3 => 'Rubik',
        4 => 'Arvo',
        5 => 'Oswald',
        6 => 'Oxygen',
        7 => 'Abril Fatface',
        8 => 'Concert One',
        9 => 'Spectral',
        10 => 'Ubuntu',
        11 => 'Noto Sans',
        12 => 'Lato'
    );

    public function __construct()
    {
        $this->name = 'pshomeslider';
        $this->tab = 'front_office_features';
        $this->version = '1.1.3';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->module_key = '95646b26789fa27cde178690e033f9ef';
        $this->author_address = '0x64aa3c1e4034d07015f639b0e171b0d7b27d01aa';

        $this->controllerAdmin = 'AdminAjaxPsHomeSlider';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Homepage Slider');
        $this->description = $this->l('This module allows you to customize and display a full screen slider and manage your sliders');

        $this->css_path = $this->_path.'views/css/';
        $this->img_path = $this->_path.'views/img/';
        $this->logo_path = $this->_path.'logo.png';
        $this->docs_path = $this->_path.'docs/';
        $this->slides_path = dirname(__FILE__).'/slides/';
        $this->slides_url = 'modules/'.$this->name.'/slides/';
        $this->module_path = $this->_path;

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * This method is trigger at the installation of the module
     * - install all module tables
     * - set some configuration value
     * - register hook used by the module
     *
     * @return bool
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        foreach ($this->configurationFields as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        return parent::install() && $this->installTab() && $this->registerHook($this->hook);
    }

    /**
     * Triggered at the uninstall of the module
     * - erase tables
     * - erase configuration value
     * - unregister hook
     *
     * @return bool
     */
    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        foreach ($this->configurationFields as $key => $value) {
            Configuration::deleteByName($key);
        }

        return parent::uninstall() && $this->uninstallTab();
    }

    /**
     * Register admin ajax controler
     *
     * @param none
     *
     * @return bool
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $this->controllerAdmin;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->name;
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Unregister admin ajax controler
     *
     * @param none
     *
     * @return bool
     */
    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName($this->controllerAdmin);
        $tab = new Tab($id_tab);
        if (Validate::isLoadedObject($tab)) {
            return $tab->delete();
        }
    }

    /**
     * Load back dependencies
     *
     * @param none
     *
     * @return bool
     */
    public function loadAsset()
    {
        $css = array(
            $this->css_path.'app.css',
            $this->css_path.'chunk-vendors.css',
            'https://unpkg.com/element-ui/lib/theme-chalk/index.css',
            $this->css_path.'override-element-ui.css',
            $this->css_path.'back.css',
            'https://fonts.googleapis.com/css?family=Roboto',
            'https://fonts.googleapis.com/css?family=Montserrat',
            'https://fonts.googleapis.com/css?family=Rubik',
            'https://fonts.googleapis.com/css?family=Arvo',
            'https://fonts.googleapis.com/css?family=Oswald',
            'https://fonts.googleapis.com/css?family=Oxygen',
            'https://fonts.googleapis.com/css?family=Abril+Fatface',
            'https://fonts.googleapis.com/css?family=Concert+One',
            'https://fonts.googleapis.com/css?family=Spectral',
            'https://fonts.googleapis.com/css?family=Ubuntu',
            'https://fonts.googleapis.com/css?family=Noto+Sans',
            'https://fonts.googleapis.com/css?family=Lato',
        );

        return $this->context->controller->addCSS($css, 'all');
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->loadAsset();

        $id_lang = $this->context->language->id;
        $languages = Language::getLanguages(true);

        //get readme
        $iso_lang = Language::getIsoById($id_lang);
        $doc = $this->docs_path.'readme_'.$iso_lang.'.pdf';

        $adminAjaxController = $this->context->link->getAdminLink($this->controllerAdmin);

        $translationList = array(
            'menuConfiguration' => $this->l('Configuration'),
            'menuSliderManager' => $this->l('Slides manager'),
            'menuHelp' => $this->l('Help'),
            'titleConfiguration' => $this->l('Configure your slider settings'),
            'titleSliderManager' => $this->l('Set up your slides'),
            'noSlides' => $this->l('No slide has been created yet.'),
            'loadingSlides' => $this->l('Loading ...'),
            'createNewSlide' => $this->l('Create new slide'),
            'updateSlide' => $this->l('Update slide'),
            'createSlide' => $this->l('Create slide'),
            'saveForm' => $this->l('Save'),
            'cancel' => $this->l('Cancel'),
            'titleGeneralSettings' => $this->l('General settings'),
            'transitionEffect' => $this->l('Transition effect'),
            'slide' => $this->l('Slide'),
            'fade' => $this->l('Fade'),
            'easeOut' => $this->l('Ease out'),
            'transitionSpeed' => $this->l('Transition speed'),
            'slow' => $this->l('Slow'),
            'normal' => $this->l('Normal'),
            'speed' => $this->l('Fast'),
            'pauseOnHover' => $this->l('Pause on mouse hover'),
            'loopForever' => $this->l('Loop forever'),
            'navigationStyle' => $this->l('Navigation elements type'),
            'arrows' => $this->l('Arrows'),
            'dots' => $this->l('Dots'),
            'both' => $this->l('Both'),
            'titleVisualSettings' => $this->l('Visual settings'),
            'titles' => $this->l('Titles'),
            'titleTypography' => $this->l('Title typography'),
            'titleSize' => $this->l('Title size'),
            'paragraphs' => $this->l('Paragraphs'),
            'paragraphTypography' => $this->l('Paragraph typography'),
            'paragraphSize' => $this->l('Paragraph size'),
            'navigation' => $this->l('Navigation'),
            'navigationElementsColor' => $this->l('Navigation elements color'),
            'titleNewSlide' => $this->l('New slide'),
            'titleEditSlide' => $this->l('Edit slide'),
            'uploadImage' => $this->l('Upload an image'),
            'dropsFileHere' => $this->l('Drop files here or'),
            'clickToUpload' => $this->l('click to upload'),
            'recommandedImage' => $this->l('jpg/png files - recommanded size: 600 x 2000px'),
            'timerSettings' => $this->l('Date settings'),
            'enableTimer' => $this->l('Add specific dates'),
            'rangeDate' => $this->l('Select starting and ending dates'),
            'textSettings' => $this->l('Text settings'),
            'slideTitle' => $this->l('Slide title'),
            'addYourText' => $this->l('Add your text'),
            'enabled' => $this->l('Enabled'),
            'disabled' => $this->l('Disabled'),
            'textPosition' => $this->l('Text position'),
            'left' => $this->l('Left'),
            'centered' => $this->l('Centered'),
            'right' => $this->l('Right'),
            'textBackground' => $this->l('Text background'),
            'linkSettings' => $this->l('Link settings'),
            'redirectUrl' => $this->l('Redirect Url'),
            'openInNewTab' => $this->l('Open in new tab'),
            'addCallToAction' => $this->l('Add a call to action button'),
            'callToActionText' => $this->l('Button text'),
            'tipTimer' => $this->l('This setting will be applied on all languages'),
            'exitDialogOk' => $this->l('Confirm'),
            'exitDialogCancel' => $this->l('Cancel'),
            'exitDialogTitle' => $this->l('Warning'),
            'exitDialogText' => $this->l('Are you sure you want to leave this page ? The settings of this slide won\'t be saved.'),
            'dialogHelp' => $this->l('Why is my image cropped on some devices?'),
            'dialogResponsiveDisplay' => $this->l('Responsive display'),
            'dialogResponsiveDisplayText' => $this->l('Optimal image ratio on every device'),
            'dialogStaticDisplay' => $this->l('Static display'),
            'dialogStaticDisplayText1' => $this->l('Image ratio is too big or too small on some devices'),
            'dialogStaticDisplayText2' => $this->l('Not enough space for text area on mobile'),
            'helpTip1' => $this->l('Display a full-screen and responsive slider on your homepage for your marketing animations!'),
            'helpTip2' => $this->l('Customize your slider: adjust its transition, its speed or its navigation elements to stick to your shop’s design.'),
            'helpTip3' => $this->l('Configure your texts and their appearance: choice of the font, the size and the color of the elements.'),
            'helpTip4' => $this->l('Manage the order of appearance of your slides, edit them, disable them or delete them.'),
            'helpTip5' => $this->l('Edit your slides with the image of your choice, add a title, a description or a redirection.'),
            'helpTip6' => $this->l('Choose to display your slides for a specific commercial operation or just update it whenever you want!'),
            'downloadPDF' => $this->l('Download PDF'),
            'moduleAllowsYou' => $this->l('This module allows you'),
            'needHelp' => $this->l('Need help? Find here the documentation of this module'),
            'cannotFindAnswer' => $this->l('Couldn\'t find any answer to your question?'),
            'contactUs' => $this->l('Contact us on PrestaShop Addons'),
            'notificationSuccessCreate' => $this->l('Your slide has been successfuly created!'),
            'notificationErrorSave' => $this->l('Error! Please fill up all necessary fields.'),
            'notificationSuccessUpdate' => $this->l('Your slide has been successfuly updated!'),
            'notificationSuccess' => $this->l('Success'),
            'notificationError' => $this->l('Error'),
            'notificationErrorAjax' => $this->l('Fatal error. Please contact the developer.'),
            'notificationSaveConfigurationSuccess' => $this->l('Configuration has been updated successfully!'),
            'notificationSaveConfigurationError' => $this->l('An error occured. Please try later or contact the developer of the module.'),
            'notificationDeleteSuccess' => $this->l('The slide has been deleted successfully!'),
            'notificationUpdateSuccess' => $this->l('The slide has been updated successfully!'),
            'notificationUpdatePositionSuccess' => $this->l('Slides positions have been updated successfully!'),
            'notificationUpdatePositionError' => $this->l('An error occured. Cannot update slides positions. Please try later or contact the developer of the module.'),
            'imageFolderIsNotWritable' => $this->l('The image folder of this module does not have the rights to upload images. Please ask your webmaster or host provider to give the write access to /pshomeslider/slides/.')
        );

        Media::addJsDef(array(
            'translationList' => json_encode($translationList),
            'contextLanguage' => json_encode(Context::getContext()->language),
            'adminAjaxController' => $adminAjaxController,
            'languages' => json_encode($languages),
            'fonts' => json_encode($this->fonts),
            'baseUrl' => Tools::getShopDomain(true).__PS_BASE_URI__,
            'documentation' => $doc,
            'imgPath' => $this->img_path,
            'module_key' => $this->module_key,
            'iso_code' => Language::getIsoById($this->context->language->id),
            'ps_version' => _PS_VERSION_
        ));

        $this->context->smarty->assign(array(
            'appLink' => Tools::getShopDomainSsl(true).__PS_BASE_URI__.'/modules/'.$this->name.'/views/js/app.js',
            'appLinkVendors' => Tools::getShopDomainSsl(true).__PS_BASE_URI__.'/modules/'.$this->name.'/views/js/chunk-vendors.js',
        ));

        return $this->display(__FILE__, '/views/templates/admin/app.tpl');
    }

    public function hookDisplayNavFullWidth($params)
    {
        $current_page = $this->context->controller->php_self;

        if ($current_page !== 'index') {
            return false;
        }

        $id_lang = $this->context->language->id;

        $slides = PsHomeSlide::getAllSlide($id_lang, true);

        $slideList = array();
        foreach ($slides as $key => $value) {
            switch ($value['text_position']) {
                case '1':
                    $textPosition = 'left';
                    break;
                case '2':
                    $textPosition = 'center';
                    break;
                case '3':
                    $textPosition = 'right';
                    break;
            }
            $slideList[$key] = $value;
            $slideList[$key]['text_position'] = $textPosition;
        }

        $sliderPauseHover = 'false';
        if (Configuration::get('PS_SLIDER_PAUSE_HOVER') == true) {
            $sliderPauseHover = 'hover';
        }

        $sliderLoop = 'false';
        if (Configuration::get('PS_SLIDER_LOOP') == true) {
            $sliderLoop = 'true';
        }

        $this->context->smarty->assign(array(
            'baseUrl' => Tools::getHttpHost(true) . __PS_BASE_URI__,
            'slides' => $slideList,
            'slidesUrl' => $this->slides_url,
            'current_language' => $id_lang,
            'sliderTransition' => Configuration::get('PS_SLIDER_TRANSITION'),
            'sliderSpeed' => Configuration::get('PS_SLIDER_SPEED'),
            'sliderPauseHover' => $sliderPauseHover,
            'sliderLoop' => $sliderLoop,
            'sliderNavigationType' => Configuration::get('PS_SLIDER_NAVIGATION_TYPE'),
            'sliderTitleFont' => Configuration::get('PS_SLIDER_TITLE_FONT'),
            'sliderTitleSize' => Configuration::get('PS_SLIDER_TITLE_TEXT_SIZE'),
            'sliderParagraphFont' => Configuration::get('PS_SLIDER_PARAGRAPH_FONT'),
            'sliderParagraphSize' => Configuration::get('PS_SLIDER_PARAGRAPH_TEXT_SIZE'),
            'sliderNavigationColor' => Configuration::get('PS_SLIDER_NAVIGATION_COLOR'),
        ));

        return $this->display(__FILE__, 'views/templates/hook/slider.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $current_page = $this->context->controller->php_self;

        if ($current_page != 'index') {
            return false;
        }

        $this->context->controller->registerStylesheet(
            'pshomeslider-css',
            'modules/'.$this->name.'/views/css/pshomeslider.css'
        );
    }
}
