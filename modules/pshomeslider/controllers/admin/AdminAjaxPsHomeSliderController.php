<?php
/**
* 2007-2016 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*/

class AdminAjaxPsHomeSliderController extends ModuleAdminController
{
    /**
     * Ajax : Get all configuration fields (tab configuration)
     *
     * @return json all configuration fields
     */
    public function ajaxProcessGetConfiguration()
    {
        $configuration = array(
            'formGeneralSettings' => array(
                'transitionEffect' => Configuration::get('PS_SLIDER_TRANSITION'),
                'transitionSpeed' => Configuration::get('PS_SLIDER_SPEED'),
                'pauseOnHover' => (bool) Configuration::get('PS_SLIDER_PAUSE_HOVER'),
                'loopForever' => (bool) Configuration::get('PS_SLIDER_LOOP'),
                'navigationType' => Configuration::get('PS_SLIDER_NAVIGATION_TYPE'),
            ),
            'formVisualSettings' => array(
                'titleTypography' => Configuration::get('PS_SLIDER_TITLE_FONT'),
                'titleSize' => (int) Configuration::get('PS_SLIDER_TITLE_TEXT_SIZE'),
                'paragraphTypography' => Configuration::get('PS_SLIDER_PARAGRAPH_FONT'),
                'paragraphSize' => (int) Configuration::get('PS_SLIDER_PARAGRAPH_TEXT_SIZE'),
                'navigationElementsColor' => Configuration::get('PS_SLIDER_NAVIGATION_COLOR'),
            )
        );

        $this->ajaxDie(json_encode($configuration));
    }

    /**
     * Ajax : Save configuration
     *
     * @param json formGeneralSettings
     * @param json formVisualSettings
     *
     * @return string success or message error if errors occured
     */
    public function ajaxProcessSaveConfiguration()
    {
        $generalSettingsForm = json_decode(Tools::getValue('formGeneralSettings'));
        $visualSettingsForm = json_decode(Tools::getValue('formVisualSettings'));

        $this->saveGeneralSettings($generalSettingsForm);
        $this->saveVisualSettings($visualSettingsForm);

        $this->ajaxDie(json_encode('success'));
    }

    /**
     * Save general settings configuration
     *
     * @param array $form formGeneralSettings
     *
     * @return void
     */
    public function saveGeneralSettings($form)
    {
        try {
            Configuration::updateValue('PS_SLIDER_TRANSITION', $form->transitionEffect);
            Configuration::updateValue('PS_SLIDER_SPEED', $form->transitionSpeed);
            Configuration::updateValue('PS_SLIDER_PAUSE_HOVER', $form->pauseOnHover);
            Configuration::updateValue('PS_SLIDER_LOOP', $form->loopForever);
            Configuration::updateValue('PS_SLIDER_NAVIGATION_TYPE', $form->navigationType);
        } catch (Exception $e) {
            $this->ajaxDie($e->getMessage());
        }
    }

    /**
     * Save visual settings configuration
     *
     * @param array $form formVisualSettings
     *
     * @return void
     */
    public function saveVisualSettings($form)
    {
        try {
            Configuration::updateValue('PS_SLIDER_TITLE_FONT', $form->titleTypography);
            Configuration::updateValue('PS_SLIDER_TITLE_TEXT_SIZE', $form->titleSize);
            Configuration::updateValue('PS_SLIDER_PARAGRAPH_FONT', $form->paragraphTypography);
            Configuration::updateValue('PS_SLIDER_PARAGRAPH_TEXT_SIZE', $form->paragraphSize);
            Configuration::updateValue('PS_SLIDER_NAVIGATION_COLOR', $form->navigationElementsColor);
        } catch (\Throwable $e) {
            $this->ajaxDie($e->getMessage());
        }
    }

    /**
     * Enable or disabled slide
     *
     * @param int id_slide
     *
     * @return string success or message error if errors occured
     */
    public function ajaxProcessToggleState()
    {
        $id_slide = (int) Tools::getValue('id_slide');

        if (empty($id_slide)) {
            throw new PrestaShopException('Parameter id_slide is missing');
        }

        try {
            $slide = new PsHomeSlide($id_slide);
            $slide->active = (bool) $slide->active ? 0 : 1;
            $slide->save();
        } catch (\Throwable $e) {
            $this->ajaxDie($e->getMessage());
        }

        $this->ajaxDie(json_encode('success'));
    }

    /**
     * Delete a slide
     *
     * @param int id_slide
     *
     * @return string success or message error if errors occured
     */
    public function ajaxProcessDeleteSlide()
    {
        $id_slide = (int) Tools::getValue('id_slide');

        if (empty($id_slide)) {
            throw new PrestaShopException('Parameter id_slide is missing');
        }

        try {
            $slide = new PsHomeSlide($id_slide);
            $slide->delete();
        } catch (\Throwable $e) {
            $this->ajaxDie($e->getMessage());
        }

        $this->ajaxDie(json_encode('success'));
    }

    /**
     * Update slide position
     *
     * @param json slide with new position
     *
     * @return string success or message error if errors occured
     */
    public function ajaxProcessUpdatePosition()
    {
        $slidesPosition = json_decode(Tools::getValue('position'));

        foreach ($slidesPosition as $position => $slide) {
            Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'pshomeslider` SET `position` = '.(int)$position.'
                WHERE `id_slide` = '.(int)$slide->id_slide
            );
        }

        $this->ajaxDie(json_encode('success'));
    }

    /**
     * Create slide
     *
     * @param json form
     *
     * @return bool true or message error if errors occured
     */
    public function ajaxProcessCreateSlide()
    {
        $slideForm = (array) json_decode(Tools::getValue('formSlide'));

        $this->ajaxDie(json_encode($this->saveSlide($slideForm)));
    }

    /**
     * Update slide
     *
     * @param json form
     *
     * @return bool true or message error if errors occured
     */
    public function ajaxProcessUpdateSlide()
    {
        $slideForm = (array) json_decode(Tools::getValue('formSlide'));

        $this->ajaxDie(json_encode($this->saveSlide($slideForm, 'update')));
    }

    /**
     * Add or update a slide + form validation + upload file
     *
     * @param array form
     * @param string new or update
     *
     * @return bool true or message error if errors occured
     */
    public function saveSlide($slideForm, $action = 'new')
    {
        $errors = array();

        $languages = Language::getLanguages(true);

        if ($action === 'new') {
            $slide = new PsHomeSlide();
        } else {
            $slide = new PsHomeSlide((int) Tools::getValue('idSlide'));
        }

        $slide->timer = $slideForm['timer'];
        if ($slide->timer) {
            if (empty($slideForm['availableDate'][0]) || empty($slideForm['availableDate'][1])) {
                $errors[] = $this->l('You need to select a date range');
            } else {
                $slide->date_start = date("Y-m-d H:i:s", strtotime($slideForm['availableDate'][0]));
                $slide->date_end = date("Y-m-d H:i:s", strtotime($slideForm['availableDate'][1]));
            }
        }

        $oneImageRequired = false;
        foreach ($languages as $lang => $value) {
            $isoCode = $value['iso_code'];
            $idLang = $value['id_lang'];

            if ($slideForm[$isoCode]->callToAction) {
                if (empty($slideForm[$isoCode]->callToActionText) || empty($slideForm[$isoCode]->callToActionText)) {
                    $errors[] = $this->l('Call to action text is empty').' ('.$isoCode.')';
                }
            }

            if (empty($slideForm[$isoCode]->file->name)) {
                if (isset($_FILES['file_'.$isoCode])) {
                    $file = $_FILES['file_'.$isoCode];
                    $fileTmpName = $file['tmp_name'];
                    $filename = trim(str_replace(" ","_", $file['name']));

                    // validateUpload return false if no error (false -> OK)
                    if (!ImageManager::validateUpload($file)) {
                        move_uploaded_file($fileTmpName, $this->module->slides_path.$filename);
                        $slide->image[$idLang] = '/modules/pshomeslider/slides/'.$filename;
                        $oneImageRequired = true;
                    } else {
                        $errors[] = ImageManager::validateUpload($file).' ('.$isoCode.')';
                        continue;
                    }
                } else {
                    $slide->image[$idLang] = '';
                }
            } else {
                $slide->image[$idLang] = '/modules/pshomeslider/slides/'.$slideForm[$isoCode]->file->name;
                $oneImageRequired = true;
            }

            $slide->title[$idLang] = $slideForm[$isoCode]->slideTitle;
            $slide->description[$idLang] = $slideForm[$isoCode]->slideText;
            $slide->text_position[$idLang] = $slideForm[$isoCode]->textPosition;
            $slide->text_background[$idLang] = $slideForm[$isoCode]->textBackground;
            $slide->url[$idLang] = $slideForm[$isoCode]->redirectUrl;
            $slide->open_new_tab[$idLang] = $slideForm[$isoCode]->openInNewTab;
            $slide->call_to_action[$idLang] = $slideForm[$isoCode]->callToAction;
            $slide->call_to_action_text[$idLang] = $slideForm[$isoCode]->callToActionText;
        }

        if ($oneImageRequired === false) {
            $errors[] = $this->l('You need to upload at least one image in one language');
        }

        if (count($errors) > 0) {
            return $errors;
        } else {
            $slide->active = 1;
            return $slide->save();
        }
    }

    /**
     * Ajax : get slide
     *
     * @param int idSlide
     *
     * @return json slide
     */
    public function ajaxProcessGetSlide()
    {
        $idSlide = (int) Tools::getValue('idSlide');

        $this->ajaxDie(json_encode(PsHomeSlide::getSlide($idSlide)));
    }

    /**
     * Ajax : get slides
     *
     * @return json slides
     */
    public function ajaxProcessGetSlides()
    {
        $slides = PsHomeSlide::getAllSlide(Context::getContext()->language->id);

        $slideList = array();
        foreach ($slides as $slide) {
            $slide['position'] = (int)$slide['position'];
            $slide['active'] = (bool)$slide['active'];
            $slide['description'] = strip_tags($slide['description']);
            $slide['image'] = $slide['image'];
            $slideList[] = $slide;
        }

        $this->ajaxDie(json_encode($slideList));
    }

    /**
     * Check if /slides/ folder have necessary access right in order to upload slide images
     *
     * @return bool
     */
    public function ajaxProcessCheckImageUploadRights()
    {
        $isWritable = is_writable($this->module->slides_path);

        $this->ajaxDie(json_encode($isWritable));
    }
}
