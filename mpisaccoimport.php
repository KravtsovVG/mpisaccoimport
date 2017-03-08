<?php
/**
 * 2017 mpSOFT
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
 *  @author    mpSOFT <info@mpsoft.it>
 *  @copyright 2017 mpSOFT Massimiliano Palermo
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of mpSOFT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
if (!defined('_CLASSES_')) {
    define('_CLASSES_', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
}

class MpIsaccoImport extends Module
{
    private $id_carrier_import;
    private $carrier_name;
    private $carrier_import_type_id;
    private $column_separator;
    private $web_link;
    private $col_order_reference;
    private $col_tracking_id;
    private $col_delivered_date;
    private $order_state_id;
    private $tablename;
    private $where;
    private $debug;
    protected $_lang;
    
    public function __construct()
    {
        $this->name = 'mpisaccoimport';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'mpsoft';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MP Isacco Import');
        $this->description = $this->l('Imports Isacco products.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->_lang = ContextCore::getContext()->language->id;
        
        //field values
        $this->id_carrier_import = 0;
        $this->carrier_name = '';
        $this->carrier_import_type_id = 0;
        $this->column_separator = '';
        $this->web_link = '';
        $this->col_order_reference = '';
        $this->col_tracking_id = '';
        $this->col_delivered_date = '';
        $this->order_state_id = '';
        $this->tablename = 'mp_isacco_import';
        $this->where = [];
        $this->debug = true;
    }
  
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('displayAdminOrder') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->installSQL() ||
            !$this->installTab()) {
            return false;
        }
            return true;
      }
    
    public function uninstall()
    {
      if (!parent::uninstall() ||
          !$this->uninstallSQL()) {
        return false;
      }
      return true;
    }
    
    private function installSQL()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "install.sql";
        $sql = explode(";",file_get_contents($filename));
        if(empty($sql)){return FALSE;}
        foreach($sql as $query)
        {
            if(!empty($query))
            {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if(!$result){return FALSE;}
            }
        }
        return TRUE;
    }
    
    private function uninstallSQL()
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . "uninstall.sql";
        $sql = explode(";",file_get_contents($filename));
        if(empty($sql)){return FALSE;}
        foreach($sql as $query)
        {
            if(!empty($query))
            {
                $query = str_replace("{_DB_PREFIX_}", _DB_PREFIX_, $query);
                $db = Db::getInstance();
                $result = $db->execute($query);
                if(!$result){return FALSE;}
            }
        }
        return TRUE;
    }
    
    public function hookDisplayAdminOrder()
    {
            //Assign Smarty Variables
            //return $this->display(__FILE__, 'import_csv.tpl');
    }

    public function hookDisplayBackOfficeHeader()
    {
        /*
        $this->context->controller->addCSS($this->_path .'views/css/admin.css');
        $this->context->controller->addJS ($this->_path .'views/js/label.js');
        $this->context->controller->addCSS($this->_path.'views/css/config.css');
        $this->context->controller->addCSS($this->_path.'views/css/dialog.css');
         */
    }
    
    public function installTab()
    {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminMpIsaccoImport';
            $tab->name = array();
            foreach (Language::getLanguages(true) as $lang)
            {
                    $tab->name[$lang['id_lang']] = 'MP Isacco Import';
            }
            $tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
            $tab->module = $this->name;
            return $tab->add();
    }

    public function uninstallTab()
    {
            $id_tab = (int)Tab::getIdFromClassName('AdminMpIsaccoImport');
            if ($id_tab)
            {
                    $tab = new Tab($id_tab);
                    return $tab->delete();
            }
            else
            {
                    return false;
            }
    }
}