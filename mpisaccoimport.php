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
    private $debug;
    private $messages;
    private $url;
    private $user;
    private $password;
    private $json_string;
    private $filename;
    private $excel_filename;
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
        $this->url = '';
        $this->user = '';
        $this->password = '';
        $this->json_string = '';
        $this->messages = [];
        $this->debug = true;
        $this->filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'json.txt';
        $this->excel_filename = dirname(__FILE__) . DIRECTORY_SEPARATOR .'export.xls';
    }
  
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->installTab() ||
            !$this->extractClass()) {
            return false;
        }
            return true;
      }
    
    public function extractClass()
    {
        $zip = new ZipArchive();
        if ($zip->open(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PHPExcel-1.8.zip') === TRUE) {
            if ($zip->extractTo(_PS_CLASS_DIR_)) {
                $zip->close();
                return true;
            } else {
                $zip->close();
                return false;
            }
        } else {
            return false;
        }
    }
      
    public function uninstall()
    {
      if (!parent::uninstall() ||
          !$this->uninstallSQL()) {
        return false;
      }
      return true;
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
    
    public function getContent()
    {
        if (Tools::isSubmit('submit_form')) {
            $this->submit();
        } else {
            $this->url = ConfigurationCore::get('MP_ISACCO_IMPORT_URL');
            $this->user = ConfigurationCore::get('MP_ISACCO_IMPORT_USER');
            $this->password = ConfigurationCore::get('MP_ISACCO_IMPORT_PWD');
        }
        
        $smarty = Context::getContext()->smarty;
        $form =  $this->createForm();
        $script =  $smarty->fetch(_PS_MODULE_DIR_ . 'mpisaccoimport/views/templates/admin/mpisaccoimport_script.tpl');
        $this->debug_messages();
        
        return $form . $script . $this->messages;
    }
    
    private function createForm()
    {   
        Context::getContext()->smarty->assign('download_xls','');
        $fields_form = [];
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Import'),       
                'image' => '../modules/mpisaccoimport/views/img/update.png'   
            ],   
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Url:'),
                    'name' => 'input_url',
                    'id' => 'input_url',
                    'required' => true,
                    'desc' => $this->l('Insert url to get data from'),
                    'class' => 'input'
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('User:'),
                    'name' => 'input_user',
                    'id' => 'input_user',
                    'required' => true,
                    'desc' => $this->l('Insert username'),
                    'class' => 'input fixed-width-xxl'
                ],
                [
                    'type' => 'password',
                    'label' => $this->l('Password:'),
                    'name' => 'input_password',
                    'id' => 'input_password',
                    'required' => true,
                    'desc' => $this->l('Insert password'),
                    'class' => 'input fixed-width-xxl'
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('File dimension:'),
                    'name' => 'input_json',
                    'id' => 'input_json',
                    'required' => false,
                    'readonly' => true,
                    'desc' => $this->l('Show file dimension of json'),
                    'class' => 'input fixed-width-xxl',
                    'align' => 'right'
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Elements:'),
                    'name' => 'input_json_elements',
                    'id' => 'input_json_elements',
                    'required' => false,
                    'readonly' => true,
                    'desc' => $this->l('Show total elements of the imported file'),
                    'class' => 'input fixed-width-xxl',
                    'align' => 'right'
                ],
                [
                    'type' => 'hidden',
                    'name' => 'input_export',
                    'id'   => 'input_export',
                ],
            ],
            'submit' => [
                'title' => $this->l('GET'),       
                'class' => 'btn btn-default pull-right',
                'name'  => 'submit_form',
                'id'    => 'submit_form',
                'icon'  => 'process-icon-download'
            ],
            'buttons' => [
                    'cancelBlock' => [
                        'title' => $this->l('BACK'),
                        'href' => 'javascript:void(0);',
                        'icon' => 'process-icon-back'
                    ],
                    'cancelBlock' => [
                        'title' => $this->l('EXPORT XLS'),
                        'href' => 'javascript:export_excel();',
                        'icon' => 'process-icon-upload'
                    ],
                 ],
        ];
        
        if (Tools::isSubmit('submit_form')) {
            //get json from isacco server
            $username = $this->user;
            $password = $this->password;
            $url      = $this->url;
            
            if (Tools::getValue('input_export',0)==1) {
                //Get file from input field
                $json_purified = file_get_contents($this->filename); 
                $this->messages[]['export'] = [
                    'on' => true,
                    'call' => debug_backtrace()[1]['function'],
                    'export to excel' => 'yes',
                    'json' => strlen($this->json_string) . " Kb"
                ];
            } else {
                //Get file from server
                $context = stream_context_create(
                    [
                    'http' => ['header'  => "Authorization: Basic " . base64_encode("$username:$password")]
                    ]);
                $json_data_1 = file_get_contents($url, false, $context);
                $json_data_2 = substr($json_data_1,strpos($json_data_1,"{"));
                $pattern = '/,}$/';
                $replacement = '}';
                $json_purified = preg_replace($pattern, $replacement, $json_data_2);
                file_put_contents($this->filename, $json_purified);
                chmod($this->filename,0777);
            }
            $size = (int)(strlen($json_purified));
            $this->json_string = $json_purified;
            
            //Decode string into object
            $json_array  = Tools::jsonDecode($json_purified, true);
            
            /**********************
             * CREATE EXCEL SHEET *
             **********************/
            if (Tools::getValue('input_export',0)==1) {
                $excel = new PHPExcel();
                $sheet = &$excel->getSheet();
                $i_row = 1;
                foreach ($json_array as $row) {
                    if ($i_row==1) {
                        $i_col=0;
                        foreach ($row as $key=>$col) {
                            $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row, $key);
                            $i_col++;
                        }
                        $i_row++;
                    }
                    $i_col=0;
                    foreach ($row as $col) {
                        if (is_array($col)) {
                            $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row, implode(";",$col));
                        } else {
                            $sheet->setCellValueExplicitByColumnAndRow($i_col, $i_row, $col);
                        }
                        $i_col++;
                    }
                    $i_row++;
                }
                //$excel->removeSheetByIndex();
                //$excel->addSheet($sheet);
 
                $objWriter = new PHPExcel_Writer_Excel5($excel);
                try {
                    $objWriter->save($this->excel_filename); 
                    chmod($this->excel_filename, 0775);
                    Context::getContext()->smarty->assign('download_xls','../modules/mpisaccoimport/download.php?file=export.xls');
                    $this->messages[]['Excel Writer'] = [
                        'on' => true,
                        'call' => debug_backtrace()[1]['function'],
                        'create' => 'success',
                        'filename' => $this->excel_filename
                    ];
                } catch (Exception $exc) {
                    Context::getContext()->smarty->assign('download_xls','');
                    $this->messages[]['Excel Writer'] = [
                        'on' => true,
                        'call' => debug_backtrace()[1]['function'],
                        'error' => $exc->getMessage(),
                        'filename' => $this->excel_filename
                    ];
                }
            }
            
            $this->messages[]['createForm -> get json'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'url' => $this->url,
                'user' => $this->user,
                'password' => $this->password,
                'json' => count($json_array) . " elements",
            ];
        } else {
            $json_array = [];
            $size = 0;
        }
        
        $helper = new HelperFormCore();
        $helper->default_form_language = (int) ConfigurationCore::get('PS_LANG_DEFAULT');
        $helper->table = '';
        $helper->allow_employee_form_lang = (int) ConfigurationCore::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'submit_form';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->fields_value['input_url'] = $this->url;
        $helper->fields_value['input_user'] = $this->user;
        $helper->fields_value['input_password'] = $this->password;
        $helper->fields_value['input_json'] = number_format($size,0) . ' Kb';
        $helper->fields_value['input_json_elements'] = count($json_array) . " elements";
        if(Tools::isSubmit('submit_form')) {
            $helper->fields_value['input_export'] = '1';
        } else {
            $helper->fields_value['input_export'] = '0';
        }
        
        $this->messages[]['createForm'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            'return' => count($helper->fields_value) . ' elements',
            ];
        $form =  $helper->generateForm($fields_form);
        return $form;
    }
    
    private function debug_messages()
    {
        if ($this->debug) {
            $msg_display = '';
            foreach ($this->messages as $message) {
                foreach ($message as $key=>$msg)
                {
                    if ($msg['on']) {
                        unset($msg['on']);
                        $msg_display .= 'FUNCTION: ' 
                                .$key 
                                .PHP_EOL 
                                .print_r($msg, 1)
                                .PHP_EOL
                                .'===================================================================================='
                                .PHP_EOL
                                .'===================================================================================='
                                .PHP_EOL
                                .PHP_EOL;
                    }
                }
            }
            $this->messages = $this->displayConfirmation("<pre>" . $msg_display . "</pre>");
        } else {
            $this->messages = '';
        }
    }
    
    private function submit()
    {
        unlink($this->excel_filename);
        
        $url        = Tools::getValue('input_url','');
        $user       = Tools::getValue('input_user','');
        $password   = Tools::getValue('input_password','');
        
        ConfigurationCore::set('MP_ISACCO_IMPORT_URL', $url);
        ConfigurationCore::set('MP_ISACCO_IMPORT_USER', $user);
        ConfigurationCore::set('MP_ISACCO_IMPORT_PWD', $password);
        
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
        
        $this->messages[]['submit'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'url' => $this->url,
                'user' => $this->user,
                'password' => $this->password,
            ];
    }
    
    private function get($key,$default = '')
    {
        $value = ConfigurationCore::get($key);
        
        $this->messages[]['get'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            'key' => $key,
            'default' => $default,
            'value' => $value,
        ];
        
        if (empty($value)) { 
            return $default;
        } else {
            return $value;
        }
    }
}