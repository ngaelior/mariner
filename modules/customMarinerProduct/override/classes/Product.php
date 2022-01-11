<?php


class Product extends ProductCore
{

    public $advanced_title_0;
    public $advanced_description_0;
    public $advanced_image_0;
    public $advanced_title_1;
    public $advanced_description_1;
    public $advanced_image_1;

    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null)
    {
        //Définition des nouveaux champs
        self::$definition['fields']['advanced_title_0'] = [
            'type' => self::TYPE_STRING,
            'required' => false, 'size' => 255
        ];
        self::$definition['fields']['advanced_image_0'] = [
            'type' => self::TYPE_STRING,
            'lang' => false,
            'required' => false,
            'validate' => 'isCleanHtml'
        ];
        self::$definition['fields']['advanced_description_0'] = [
            'type' => self::TYPE_HTML,
            'required' => false,
        ];       self::$definition['fields']['advanced_title_1'] = [
            'type' => self::TYPE_STRING,
            'required' => false, 'size' => 255
        ];
        self::$definition['fields']['advanced_image_1'] = [
            'type' => self::TYPE_STRING,
            'lang' => false,
            'required' => false,
            'validate' => 'isCleanHtml'
        ];
        self::$definition['fields']['advanced_description_1'] = [
            'type' => self::TYPE_HTML,
            'required' => false,
        ];
        parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
    }
}