<?php


class customMarinerProduct extends Module
{


    public function __construct()
    {
        $this->name = 'customMarinerProduct';
        $this->author = ' Nicolas';
        $this->version = '1.0';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = 'custom Mariner Product';
        $this->description = '';
        $this->templateFile = 'module:customMarinerProduct/views/templates/hook/custom.tpl';

    }

    public function install()
    {
        if (!parent::install() || !$this->_installSql()
            //Pour les hooks suivants regarder le fichier src\PrestaShopBundle\Resources\views\Admin\Product\form.html.twig
            || !$this->registerHook('displayAdminProductsExtra')
            || !$this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    protected function _installSql()
    {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
            . "ADD advanced_title_0 VARCHAR(255) NULL, ADD advanced_description_0 TEXT NULL, ADD advanced_image_0 TEXT NULL, ADD advanced_title_1 VARCHAR(255) NULL, ADD advanced_description_1 TEXT NULL, ADD advanced_image_1 TEXT NULL";

        $returnSql = Db::getInstance()->execute($sqlInstall);

        return $returnSql;
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function _unInstallSql()
    {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
            . "DROP advanced_title_0, DROP advanced_description_0, DROP advanced_image_0,DROP advanced_title_1, DROP advanced_description_1, DROP advanced_image_1";
        $returnSql = Db::getInstance()->execute($sqlInstall);

        return $returnSql;
    }

    public function hookDisplayAdminProductsExtra($params)
    {

    }

    /**
     * Affichage des informations supplémentaires sur la fiche produit
     * @param type $params
     * @return type
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $product = new Product($params['id_product']);
        $moduleLink = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $this->context->smarty->assign(array(
                'advanced_title_0' => $product->advanced_title_0,
                'advanced_description_0' => $product->advanced_description_0,
                'advanced_image_0' => $product->advanced_image_0,
                'advanced_title_1' => $product->advanced_title_1,
                'advanced_description_1' => $product->advanced_description_1,
                'advanced_image_1' => $product->advanced_image_1,
                'file_dir' => $this->context->link->getBaseLink() . '/modules/' . $this->name . '/views/img/p/',
                'moduleLink' => $moduleLink,
                'id_product' => $params['id_product'],
            )
        );
        /**
         * @Todo Faire marcher le champ langue
         */
        return $this->display(__FILE__, 'views/templates/hook/extrafields.tpl');
    }

    /**
     * Configuration admin du module
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getContent()
    {
        //Gestion de l'envoi des fichiers
        if (Tools::getValue('uploadProductImage')) {
            $id_product = (int)Tools::getValue('id_product');
            $field_name = Tools::getValue('field_name');
            if ($this->_updateProductImageField($id_product, $field_name)) {
                return $this->l('Image uploaded with success');
            }
        } elseif (Tools::getValue('deleteProductImage')) {
            $id_product = (int)Tools::getValue('id_product');
            $field_name = Tools::getValue('field_name');
            if ($this->_deleteProductImageField($id_product, $field_name)) {
                return $this->l('Image deleted with success');
            }
        } else {
            return 'No configuration needed for this module';
        }

    }

    /**
     * Upload de l'image d'un produit
     * @param $id_product
     * @param $field_name
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function _updateProductImageField($id_product, $field_name)
    {
        //Envoi de l'image
        //@TOdo gérer le cas ou l'image existe déjà
        $savePath = dirname(__FILE__) . '/views/img/p';
        $uploader = new Uploader('file');
        $file = $uploader->setSavePath($savePath)
            ->setAcceptTypes(['jpg', 'png', 'git', 'jpeg'])
            ->process();

        $fileName = ltrim(str_replace($savePath, '', $file[0]['save_path']), '/');
        try {
            //Sauvegarde la valeur de l'image pour le produit
            $product = new Product($id_product);
            $product->$field_name = $fileName;
            $product->save();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
            return false;
        }

        return true;
    }


    /**
     * Suppression de l'image d'un produit
     * @param $id_product
     * @param $field_name
     * @return bool
     */
    protected function _deleteProductImageField($id_product, $field_name)
    {
        try {
            //Sauvegarde d'une valeur vide pour le produit
            $product = new Product($id_product);


            //Si le produit a une image et qu'elle existe on la supprime
            if ($product->$field_name != "") {
                $imagePath = dirname(__FILE__) . '/views/img/p/' . $product->$field_name;
                if (is_file($imagePath)) {
                    unlink($imagePath);
                }
            }
            $product->$field_name = '';
            $product->save();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}