<?php
/**
 * 2008 - 2018 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */

class AuthorClass extends ObjectModel
{
    public $id_author;
    public $lastname;
    public $firstname;
    public $pseudo;
    public $date;
    public $bio;
    public $email;
    public $meta_title;
    public $meta_description;

    protected $table = 'prestablog_author';
    protected $identifier = 'id_author';

    public static $table_static = 'prestablog_author';
    public static $identifier_static = 'id_author';

    public static $definition = array(
        'table' => 'prestablog_author',
        'primary' => 'id_author',
        'multilang' => true,
        'fields' => array(
            'lastname' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'firstname' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'pseudo' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'bio' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
            'email' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 75),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
        )
    );

    public static function isTableInstalled()
    {
        $table = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SHOW TABLES LIKE \''.bqSQL(_DB_PREFIX_.self::$table_static).'%\'
            ');

        if (count($table) > 0) {
            return true;
        }
        return false;
    }



    public static function checkAuthor($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `id_author`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            WHERE `id_author`= '.(int)$id);

        return $return1;
    }
    public static function getPseudo($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `pseudo`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            WHERE `id_author`= '.(int)$id);
        if (isset($return1[0]['pseudo'])) {
            return $return1[0]['pseudo'];
        }
    }
    public static function getBio($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `bio`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            WHERE `id_author`= '.(int)$id);
        if (isset($return1[0]['bio'])) {
            return $return1[0]['bio'];
        }
    }

    public static function getEmail($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `email`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            WHERE `id_author`= '.(int)$id);

        if (isset($return1[0]['email'])) {
            return $return1[0]['email'];
        }
    }

    public static function getMetaTitle($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `meta_title`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            WHERE `id_author`= '.(int)$id);

        if (isset($return1[0]['meta_title'])) {
            return $return1[0]['meta_title'];
        }
    }

    public static function getMetaDescription($id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT `meta_description`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            WHERE `id_author`= '.(int)$id);

        if (isset($return1[0]['meta_description'])) {
            return $return1[0]['meta_description'];
        }
    }

    public static function getListeEmployee()
    {
        return Db::getInstance()->ExecuteS('
            SELECT p.`id_employee`, p.`lastname`, p.`firstname`, p.`email`
            FROM `'._DB_PREFIX_.'employee` p');
    }

    public static function addAuthor($id, $firstname, $lastname, $mail)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            INSERT INTO `'._DB_PREFIX_.'prestablog_author`
            (`id_author`, `firstname`, `lastname`, `date`, `email`)
            VALUES
            ('.(int)$id.', \''.$firstname.'\', \''.$lastname.'\', \''.date("Y-m-d").'\' , \''.$mail.'\')');
    }

    public static function editAuthor($author_id, $pseudo, $bio, $email, $meta_title, $meta_description)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            UPDATE `'._DB_PREFIX_.'prestablog_author`
            SET `pseudo`= \''.htmlspecialchars($pseudo, ENT_QUOTES).'\', `bio`= \''.htmlspecialchars($bio, ENT_QUOTES).'\', `email`= \''.$email.'\', `meta_title`= \''.htmlspecialchars($meta_title, ENT_QUOTES).'\', `meta_description`= \''.htmlspecialchars($meta_description, ENT_QUOTES).'\'
            WHERE `id_author` ='.(int)$author_id);
    }

    public static function getListeAuthor()
    {
        return Db::getInstance()->ExecuteS('
            SELECT p.`id_author`, p.`firstname`, p.`lastname`, p.`date`, p.`email`, p.`pseudo`
            FROM `'._DB_PREFIX_.'prestablog_author` p');
    }

    public static function delAuthor($id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
                DELETE FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author`
                WHERE `id_author` = '.(int)$id);
    }

    public static function getCountArticleCreated($author_id)
    {
        //Count du nombre d'articles créé par l'auteur
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
            SELECT count(DISTINCT n.id_prestablog_news) as `count`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news` n
            WHERE n.`author_id` =  '.$author_id);

        return $value['count'];
    }
    public static function getMostRedArticle($author_id)
    {
        //Recupération de l'article le plus lu créé par l'auteur
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT n.`id_prestablog_news`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news` n
            WHERE n.`author_id` =  '.$author_id);

        for ($i = 0; isset($value[$i]); $i++) {
            $value2[$i] = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                    SELECT n.`read`, n.`id_prestablog_news`, n.`title`
                    FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news_lang` n
                    WHERE n.`id_prestablog_news` =  '.$value[$i]['id_prestablog_news']);
        }
        if (isset($value2)) {
            $max = max($value2);
        } else {
            $max['title'] = "";
        }


        return $max['title'];
    }

    public static function getAuthorData($author_id)
    {
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                    SELECT n.`id_author`, n.`firstname`, n.`lastname`, n.`pseudo`, n.`email`, n.`bio`
                    FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author` n
                    WHERE n.`id_author` = '.$author_id);
        return $value;
    }


    public static function getArticleListe($author, $active = false, $limit_start = 0, $limit_stop = null)
    {
        $limit = '';
        if (!empty($limit_stop)) {
            $limit = ' LIMIT '.(int)$limit_start.', '.(int)$limit_stop;
        }
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT    `id_prestablog_news`
        FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news`
        WHERE `author_id` = '.(int)$author.''.$limit);

        $return2 = array();
        foreach ($return1 as $value) {
            $news = new NewsClass((int)$value['id_prestablog_news']);

            if ((int)$news->id) {
                if ($active) {
                    if ($news->actif) {
                        $return2[] = $value['id_prestablog_news'];
                    }
                } else {
                    $return2[] = $value['id_prestablog_news'];
                }
            }
        }

        return $return2;
    }

    public static function getAuthorID($news_id)
    {
        //Recupération de l'auteur'
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT n.`author_id`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news` n
            WHERE n.`id_prestablog_news` =  '.$news_id);

        if (isset($value[0]['author_id']) && $value[0]['author_id'] != null) {
            return $value;
        } else {
            return "";
        }
    }

    public static function getAuthorName($news_id)
    {
        //Recupération de l'auteur'
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT n.`author_id`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news` n
            WHERE n.`id_prestablog_news` =  '.$news_id);

        if (isset($value[0]['author_id']) && $value[0]['author_id'] != null) {
            $value2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                    SELECT n.`firstname`, n.`lastname`, n.`pseudo`, n.`bio`
                    FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author` n
                    WHERE n.`id_author` = '.$value[0]['author_id']);
        } else {
            $value2 = "";
        }
        return $value2;
    }

    public static function getAuthorPseudo($news_id)
    {
        //Recupération de l'auteur'
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT n.`author_id`
            FROM `'.bqSQL(_DB_PREFIX_).'prestablog_news` n
            WHERE n.`id_prestablog_news` =  '.$news_id);

        if (isset($value[0]['author_id']) && $value[0]['author_id'] != null) {
            $value2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
                    SELECT n.`pseudo`
                    FROM `'.bqSQL(_DB_PREFIX_).'prestablog_author` n
                    WHERE n.`id_author` = '.$value[0]['author_id']);
        } else {
            $value2 = "";
        }
        return $value2;
    }


    public static function verifyAuthorSet($author_id)
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT    `firstname`
            FROM `'.bqSQL(_DB_PREFIX_.'prestablog_author').'`
            WHERE `id_author` = '.(int)$author_id);
        if (isset($return1) && $return1 != null) {
            return true;
        } else {
            return false;
        }
    }

    public function registerTablesBdd()
    {
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'prestablog_author` (
            `'.bqSQL($this->identifier).'` int(10) unsigned NOT null,
            `lastname` varchar(255) NOT null,
            `firstname` varchar(255) NOT null,
            `pseudo` varchar(255) NOT null,
            `date` datetime NOT null,
            `bio` mediumtext,
            `meta_title` varchar(60), 
            `meta_description` varchar(160), 
            `email` varchar(255) NOT null,
            PRIMARY KEY (`'.bqSQL($this->identifier).'`))
            ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
            return false;
        }
        return true;
    }

    public function deleteTablesBdd()
    {
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
            DROP TABLE IF EXISTS `'.bqSQL(_DB_PREFIX_).'prestablog_author`
            ')) {
            return false;
        }

        return true;
    }
}
