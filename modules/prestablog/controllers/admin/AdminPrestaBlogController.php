<?php
/**
 * 2008 - 2020 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */

class AdminPrestaBlogController extends ModuleAdminController
{
    /*public function __construct()
    {
        parent::__construct();

        if(!Tools::redirectAdmin('index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&configure=prestablog')) {
            return false;
        }
        return true;
    }*/

    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');
            return;
        }

        $id_tab = (int)Tab::getIdFromClassName('AdminModules');
        $id_employee = (int)$this->context->cookie->id_employee;
        $token = Tools::getAdminToken('AdminModules'.$id_tab.$id_employee);
        Tools::redirectAdmin('index.php?controller=AdminModules&configure=prestablog&token='.$token);
    }
}
