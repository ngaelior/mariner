<?php
class Product extends ProductCore
{
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public $advanced_title_0;
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public $advanced_description_0;
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public $advanced_image_0;
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public $advanced_title_1;
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public $advanced_description_1;
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public $advanced_image_1;
    /*
    * module: customMarinerProduct
    * date: 2022-01-04 10:51:40
    * version: 1.0
    */
    public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, \Context $context = null)
    {
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