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

class AdminMpIsaccoImportController extends ModuleAdminControllerCore {
    private $_lang;
    private $tablename;
    private $list;
    private $paginationList;
    private $filename;
    private $messages;
    private $tmp_file;
    private $json_import;
    private $json_file;
    private $current_page;
    private $current_pagination;
    private $session_list;
    private $list_manufacturers;
    private $list_suppliers;
    private $selected_manufacturer;
    private $selected_supplier;
    private $total_new_products;
    private $total_obs_products;
    private $reference_prefix;
    
    //debug
    private $debug;
    
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->_lang = $this->context->language->id;
        $this->name = 'adminmpcarrierimport'; 

        parent::__construct();

        $this->tablename = 'mp_isacco_import';
        $this->filename = '';
        $this->tmp_file = '';
        $this->json_file = 'import.json';
        $this->messages = [];
        $this->list = [];
        $this->paginationList = [];
        $this->debug = true;
        $this->smarty = Context::getContext()->smarty;
        $this->current_page = Tools::getValue('page',0);
        $this->current_pagination = Tools::getValue('pagination',10);
        $this->session_list = 'isacco_list';
        $this->list_manufacturers = [];
        $this->list_suppliers = [];
        $this->getInputValues();
    }
    
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
    
    public function createForm()
    {   
        $token = Tools::getValue('token');
        $fields_form = [];
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Import'),       
                'image' => '../modules/mpisaccoimport/views/img/update.png'   
            ],   
            'input' => [
                [
                    'type' => 'file',
                    'label' => $this->l('Import file:'),
                    'name' => 'input_import_file',
                    'id' => 'input_import_file',
                    'display_image' => false,
                    'required' => true,
                    'desc' => $this->l('Choose a file to import products list')
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Prefix for reference id'),
                    'name' => 'input_reference_prefix',
                    'id' => 'input_reference_prefix',
                    'required' => false,
                    'desc' => $this->l('Choose an optional prefix to put before reference product'),
                    'class' => 'input fixed-width-sm'
                ]
            ],
            'submit' => [
                'title' => $this->l('Import'),       
                'class' => 'btn btn-default pull-right',
                'name'  => 'submit_form',
                'id'    => 'submit_form',
                'icon'  => 'process-icon-configure'
            ],
        ];
        
        
        if(session_status()!=PHP_SESSION_ACTIVE) {
            session_start();
        } 
        if (!empty($this->list)) {
            $fields_form[1]['form'] = [
                'legend' => [
                    'title' => $this->l('Import Informations'),       
                    'image' => '../modules/mpisaccoimport/views/img/info.png'   
                ],   
                'input' => [
                    [
                        'type' => 'text',
                        'readonly' => true,
                        'label' => $this->l('Total new products'),
                        'name' => 'input_product_new',
                        'id' => 'input_product_new',
                        'required' => false,
                        'desc' => $this->l('Total new products in import file'),
                        'class' => 'input fixed-width-sm'
                    ],
                    [
                        'type' => 'text',
                        'readonly' => true,
                        'label' => $this->l('Total obsolete products'),
                        'name' => 'input_product_obsolete',
                        'id' => 'input_product_obsolete',
                        'required' => false,
                        'desc' => $this->l('Total products that are not in catalog'),
                        'class' => 'input fixed-width-sm'
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate to manufacturer'),
                        'desc' => $this->l('Choose a manufacturer to associate'),
                        'name' => 'input_select_manufacturer',
                        'required' => true,
                        'options' => [
                          'query' => $this->list_manufacturers,
                          'id' => 'id',
                          'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate to supplier'),
                        'desc' => $this->l('Choose a supplier to associate'),
                        'name' => 'input_select_supplier',
                        'required' => true,
                        'options' => [
                          'query' => $this->list_suppliers,
                          'id' => 'id',
                          'name' => 'value'
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Pagination'),
                        'desc' => $this->l('Choose how many records at a time to be displayed'),
                        'name' => 'input_select_pagination',
                        'required' => true,
                        'options' => [
                          'query' => [
                              ['id'=>10,'value'=>'10'],
                              ['id'=>25,'value'=>'25'],
                              ['id'=>50,'value'=>'50'],
                              ['id'=>100,'value'=>'100'],
                              ['id'=>500,'value'=>'500'],
                              ['id'=>1000000000,'value'=>$this->l('ALL')],
                              ],
                          'id' => 'id',
                          'name' => 'value'
                        ]
                    ],
                    [
                        'type' => 'hidden',
                        'name' => 'input_current_page',
                        'id' => 'input_current_page',
                    ],
                ]
            ];
        }
        
        
        $helper = new HelperFormCore();
        $helper->default_form_language = (int) ConfigurationCore::get('PS_LANG_DEFAULT');
        $helper->table = 'mp_isacco_import';
        $helper->allow_employee_form_lang = (int) ConfigurationCore::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminMpIsaccoImport',false);
        $helper->submit_action = 'submit_form';
        $helper->token = $token;
        $helper->fields_value['input_reference_prefix'] = $this->reference_prefix;
        $helper->fields_value['input_product_new'] = $this->total_new_products;
        $helper->fields_value['input_product_obsolete'] = $this->total_obs_products;
        $helper->fields_value['input_select_manufacturer'] = $this->selected_manufacturer;
        $helper->fields_value['input_select_supplier'] = $this->selected_supplier;
        $helper->fields_value['input_select_pagination'] = $this->current_pagination;
        $helper->fields_value['input_current_page'] = $this->current_page;
        
        $this->messages[] = [
            'function' => 'createForm',
            'return' => 'formfields'
            ];
        
        $form =  $helper->generateForm($fields_form);
        return $form;
    }
    
    private function createTable()
    {
        $token = Tools::getValue('token');
        $fields_list = [
            'id' => [
                'class' => 'hidden'],
            'check' => [
                'title' => $this->l('Exists'),
                'align' => 'center',
                'width' => 40,
                'type'  => 'bool',
                'float' => 'true'],
            'reference' => [
                'title' => $this->l('Product reference'),
                'align' => 'right',
                'width' => 'auto'],
            'product_id' => [
                'title' => $this->l('Product id'),
                'align' => 'right',
                'width' => 'auto'],
            'product' => [
                'title' => $this->l('Product'),
                'align' => 'left',
                'width' => 'auto'],
            'cat' => [
                'title' => $this->l('Category'),
                'align' => 'left',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'tags' => [
                'title' => $this->l('Tags'),
                'align' => 'left',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'colori' => [
                'title' => $this->l('Colors'),
                'align' => 'left',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'materiali' => [
                'title' => $this->l('Materials'),
                'align' => 'left',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'dimensioni' => [
                'title' => $this->l('Dimensions'),
                'align' => 'left',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'washing' => [
                'title' => $this->l('Washing'),
                'align' => 'left',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'image' => [
                'title' => $this->l('Image link'),
                'align' => 'center',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'thumb' => [
                'title' => $this->l('Thumb'),
                'align' => 'center',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'arr_tags' => [
                'class'=> 'hidden',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'arr_colori' => [
                'class'=> 'hidden',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'arr_dimensioni' => [
                'class'=> 'hidden',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'arr_washing' => [
                'class'=> 'hidden',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            'image_path' => [
                'class'=> 'hidden',
                'width' => 'auto',
                'type'  => 'bool',
                'float' => 'true'],
            ];
        
        $helper = new HelperListCore();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        // Actions to be displayed in the "Actions" column
        //$helper->actions = array('edit', 'delete', 'view');
        $helper->actions = [];
        $helper->identifier = 'id';
        $helper->title = $this->l('Products list');
        $helper->table = '';
        $helper->token = $token;
        $helper->currentIndex = AdminControllerCore::$currentIndex;
        $helper->show_toolbar = true;
        $helper->no_link=true; // Row is not clicable
        $helper->actions = ['edit','view','delete'];
        $helper->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('This action will delete selected items. Are you sure?'),
                'icon' => 'icon_trash'
            ],
            'import' => [
                'text' => $this->l('Import selected'),
                'confirm' => $this->l('This action will import selected items. Are you sure?'),
                'icon' => 'icon_download'
            ]            
        ];
        
        $table = $helper->generateList($this->paginationList, $fields_list);
        
        $this->messages[] = [
            'function' => 'createTable',
            'list' => count($this->list) . ' elements'
        ];
        
        $this->smarty->assign(['currentindex' => $helper->currentIndex]);
        $this->smarty->assign(['token' => '&token=' . $helper->token]);
        $this->smarty->assign(['pagelink' => $helper->currentIndex . '&token=' . $helper->token . '&page=']);
        
        return $table;
    }
    
    protected function processBulkDelete() {
        if (Tools::isSubmit('submitBulkdelete')) {
            $boxes = Tools::getValue('Box');
            //Delete selected
            $id=end($boxes);
            do {
                unset($this->list[$id]);
            } while ($id=prev($boxes));
            
            //renumber list id
            $i = 0;
            foreach($this->list as &$list)
            {
                $list['id'] = $i;
                $i++;
            }
            $this->list = array_values($this->list);
            //Save list to session
            if(session_status()!=PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION[$this->session_list] = Tools::jsonEncode($this->list);
            
            //message
            $this->messages[] = [
                'function' => 'processBulkDelete',
                'boxes' => count($boxes) . " elements",
                //'list' => htmlspecialchars(print_r($this->list,1))
                ];
        }
    }
    
    protected function processBulkImport() {
        $products = [];
        if (Tools::isSubmit('submitBulkimport')) {
            $boxes = Tools::getValue('Box');
            
            foreach ($boxes as $box)
            {
                $product = $this->getProductByReference($this->list[$box]['reference']);
                if(!empty($product)) {
                    $this->updateProduct($product,$this->list[$box]);
                } else {
                    $this->createProduct($this->list[$box]);
                }
            }
            
            $this->messages[] = [
                'function' => 'processBulkImport',
                'boxes' => count($boxes) . " elements",
                ];
        }
        
    }
    
    public function initContent()
    {
        parent::initContent();
        $this->initToolbar();
        if(session_status()!=PHP_SESSION_ACTIVE) {
            session_start();
        } 
        
        //Get input values
        $this->getInputValues();
        
        //get file content
        $this->readFile();

        //Call bulk process
        $this->processBulkDelete();
        $this->processBulkImport();
        //Create pagination list
        $this->createPaginationList();
        
        $this->list_manufacturers = $this->createOptionList('manufacturer', 'id_manufacturer', 'name', 'name');
        $this->list_suppliers    = $this->createOptionList('supplier', 'id_supplier', 'name', 'name');
        
        $smarty = $this->context->smarty;
        $form =  $this->createForm();
        $table = $this->createTable();
        $page =  $smarty->fetch(_PS_MODULE_DIR_ . 'mpisaccoimport/views/templates/admin/adminMpIsaccoImport.tpl');

        $this->messages[] = [
            'function' => 'initContent',
            'already parsed' => 'true',
            'list' => count($this->list) . " elements"
        ];
        
        if ($this->debug) {
            $this->messages = $this->displayConfirmation("<pre>" . print_r($this->messages, 1) . "</pre>");
        } else {
            $this->messages = '';
        }
        
        $this->context->smarty->assign(array('content' => $form . $table . $page . $this->messages));
    }
    
    private function getCarriers()
    {
        $db = Db::getInstance();
        $query = new DbQueryCore();
        $query->select('DISTINCT carrier_name')
                ->from($this->config_tablename)
                ->orderBy('carrier_name');
        $result = $db->executeS($query);
        $carriers = [];
        $carriers['select'] = [
                'id' => 'select',
                'value' => $this->l('Please, select a carrier')
            ];
        foreach ($result as $carrier)
        {
            $carriers[$carrier['carrier_name']] = [
                'id' => $carrier['carrier_name'],
                'value' => $carrier['carrier_name']
            ];
        }
        return $carriers;
    }
    
    private function getOrderStateName($id)
    {
        
        $orderStateLang = new OrderStateCore((int)$id);
        $state = $orderStateLang->getFieldByLang('name', $this->_lang);
        
        $this->messages[] = [
            'function' => 'getOrderStateLang',
            'param' => $id,
            'return' => $state
        ];
        
        return $state;
    }
    
    private function getCarrierImportName($id)
    {
        $carriersImport = [
            $this->l('Please select'),
            $this->l('Delivered'),
            $this->l('Shipped')
        ];
        
        return $carriersImport[(int)$id];
    }
    
    /**
     * Helper displaying error message(s)
     * @param string|array $error
     * @return string
     */
    public function displayError($error)
    {
        $output = '
		<div class="bootstrap">
		<div class="module_error alert alert-danger" >
			<button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($error)) {
            $output .= '<ul>';
            foreach ($error as $msg) {
                $output .= '<li>'.$msg.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= $error;
        }

        // Close div openned previously
        $output .= '</div></div>';

        $this->error = true;
        return $output;
    }

    /**
    * Helper displaying warning message(s)
    * @param string|array $error
    * @return string
    */
    public function displayWarning($warning)
    {
        $output = '
		<div class="bootstrap">
		<div class="module_warning alert alert-warning" >
			<button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($warning)) {
            $output .= '<ul>';
            foreach ($warning as $msg) {
                $output .= '<li>'.$msg.'</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= $warning;
        }

        // Close div openned previously
        $output .= '</div></div>';

        return $output;
    }

    public function displayConfirmation($string)
    {
        $output = '
		<div class="bootstrap">
		<div class="module_confirmation conf confirm alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			'.$string.'
		</div>
		</div>';
        return $output;
    }
    
    public function getOrderStateByReference($order_reference)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('current_state')
                ->from('orders')
                ->where('reference = \'' . pSQL($order_reference) . '\'');
        try {
            $value = $db->getValue($sql);
            $this->messages[] = [
                'function' => 'getOrderStateByReference',
                'param' => $order_reference,
                'return' => 'order id: ' . $value,
                'sql' => (string)$sql
                ];
            return $value;
        } catch (Exception $ex) {
            $this->messages[] = [
                'function' => 'getOrderStateByReference',
                'param' => $order_reference,
                'return' => 'error: ' . $ex->getMessage(),
                'sql' => (string)$sql
                ];
            return [];
        }
    }
    
    private function getSeparator()
    {
        $import_type = Tools::getValue('input_carrier_import_type_id');
        $carrier_name = Tools::getValue('input_carrier_name');
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        
        $sql->select('column_separator')
                ->from('mp_carrier_import')
                ->where('carrier_name =\'' . pSQL($carrier_name) .'\'')
                ->where('carrier_import_type_id=' . (int)$import_type);
        $value = $db->getValue($sql);
        if (empty($value)) {
            $this->messages[] = [
                'function' => 'getSeparator',
                'return' => 'no separator found'
            ];
            return null;
        } else {
            $this->messages[] = [
                'function' => 'getSeparator',
                'return' => 'separator: ' . $value
            ];
            return $value;
        } 
    }
    
    private function startsWith($string, $chunk)
    {
        return preg_match('#^' . $chunk . '#i', $string) === 1;
    }
    
    private function contains($string, $chunk)
    {
        return Tools::strpos($string,$chunk)===true;
    }
    
    private function removeFirstChar($string)
    {
        return $str = substr($string, 1);
    }
    
    private function explode($array)
    {
        $items = [];
        if (is_array($array)) {
            foreach ($array as $item)
            {
                if (!empty($item)) {
                    $items[] = "<option value='" . $item . "'>" . $item . "</option>";
                }
            }
        } elseif (!empty($array)) {
            $items[] = "<option value='" . $array . "'>" . $array . "</option>";
        } else {
            return "--";
        }
        if (!empty($items)) {
            return "<select>" . implode(" ", $items) . "</select>";
        } else {
            return "--";
        }
            
        
    }
    
    /**
     * Create an indexed array from array values or single string
     * @param mixed $array array or value
     * @return array indexed array
     */
    private function createArray($array)
    {
        $items = [];
        if (is_array($array)) {
            foreach ($array as $item)
            {
                if (!empty($item)) {
                    $items[] = $item;
                }
            }
        } elseif (!empty($array)) {
            $items[] = $array;
        } else {
            return [];
        }
        
        return $items;
    }
    
    private function import()
    {
        if(isset($_SESSION['parsed'])) {
            return array_values(Tools::jsonDecode($_SESSION['json_file'],true));
        }
        
        if (isset($_FILES['input_import_file'])) {
            
        } else {
            $fileContent = $_SESSION['json_file'];
        }
        
        
        $this->json_import = array_values($json_array);
        
        $_SESSION['json_file'] = $fileContent;
        $_SESSION['parsed'] = true;
        
        file_put_contents($this->json_file, $fileContent);
        $this->messages[] = [
            'function' => 'import',
            'tmp_name' => $this->tmp_file,
            'file' => (strlen($fileContent)*8) . ' Kb' ,
            'json' => count($this->json_import) . ' elements',
            'session_started' => isset($_SESSION['json_file'])?strlen($_SESSION['json_file'])*8 . ' Kb':'no'];
        return $this->json_import;
    }
    
    private function readFile()
    {
        if (empty($_FILES['input_import_file']['tmp_name'])) {
            if(empty($_SESSION[$this->session_list])) {
                $this->list = [];
            } else {
                $this->list = array_values(Tools::jsonDecode($_SESSION[$this->session_list],true));
            }
            return ; 
        } else {
            $fileContent = file_get_contents($_FILES['input_import_file']['tmp_name']);
            $json_array  = array_values(Tools::jsonDecode($fileContent, true));
            $this->list = $this->createList($json_array);
        }
    }
    
    /**
     * 
     * @param array $json object to process
     * @return table data for helperlist
     */
    private function createList($json)
    {
        $list = [];
        $this->total_new_products = 0;
        $i=0;
        foreach ($json as $row)
        {
            $reference = $this->reference_prefix . $row['id'];
            if ($this->productExists($reference)) {
                $check = '<img src="../modules/mpisaccoimport/views/img/ok.png">'; 
            } else {
                $check = '<img src="../modules/mpisaccoimport/views/img/new.png">'; 
            }
            
            $list[] = [
                'id' => $i,
                'check' => $check,
                'reference' => $reference,
                'product_id' => $row['product_id'],
                'product' => $row['product'],
                'cat' => $this->explode($row['cat']),
                'tags' => $this->explode($row['tags']),
                'colori' => $this->explode($row['colori']),
                'materiali' => $this->explode($row['materiali']),
                'dimensioni' => $this->explode($row['dimensioni']),
                'washing' => $this->explode($row['washing']),
                'image' => '<a href="https://www.isacco.it' . $row['image'] . '" target="_blank">link</a>',
                'thumb' => '<img src="https://www.isacco.it' . $row['thumb'] . '" style="width:100px;">',
                'arr_tags' => $this->createArray($row['tags']),
                'arr_colori' => $this->createArray($row['colori']),
                'arr_dimensioni' => $this->createArray($row['dimensioni']),
                'arr_materiali' => $this->createArray($row['materiali']),
                'image_path' => 'https://www.isacco.it/' . $row['image']
            ];
            $i++;
        }
        
        if(session_status()!=PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION[$this->session_list] = Tools::jsonEncode($list);
        $this->list = $list;
        
        $this->messages[] = [
            'function' => 'createList',
            'param' => count($json) . " elements",
            'return' => count($list) . " elements",
            //'list' => htmlentities(print_r($this->list, 1))
                ];
        return $list;
    }
    
    /**
     * Create pagination table for helperlist
     * @return array pagination list
     */
    private function createPaginationList()
    {
        $array = $this->pagination();
        $paginationList = [];
        
        for($i=$array['start'];$i<$array['end'];$i++)
        {
            if ($i<count($this->list)) {
                $paginationList[] = $this->list[$i];
            } else {
                break;
            }
        }
        
        $this->paginationList = $paginationList;
        
        $this->messages[] = [
            'function' => 'createList',
            'param' => count($this->list) . " elements",
            'return' => count($paginationList) . " elements",
            //'list' => htmlentities(print_r($this->paginationList, 1))
                ];
        return $paginationList;
    }
    
    /**
     * Set pagination values
     * @return array returns startrecord and endrecord to process
     */
    private function pagination()
    {
        $startPage = $this->current_page;
        $pagination = $this->current_pagination;
        $this->smarty->assign(['startPage' => $startPage]);
        $this->smarty->assign(['pagination' => $pagination]);
        $this->smarty->assign(['pages' => (int)(count($this->list)/$pagination)]);
        $this->smarty->assign(['totalProducts' => count($this->list)]);
        $startRecord = $startPage*$pagination;
        $endRecord = $startRecord+$pagination;
        
        return [
            'start' => $startRecord,
            'end' => $endRecord
        ];
    }
    
    private function productExists($reference)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('count(*)')
                ->from('product')
                ->where("reference='" . pSQL($reference) . "'" );
        
        $result =  (bool)$db->getValue($sql);
        if (!$result) {
            $this->total_new_products++;
        }
        
        return $result;
    }
    
    /**
     * 
     * @param string $tablename tablename to get data
     * @param string $id field id
     * @param string $value field description
     * @param string $orderBy field order
     * @return array option list for select item
     */
    private function createOptionList($tablename,$id,$value,$orderBy)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $options = [];
        
        $sql->select($id)
                ->select($value)
                ->from($tablename)
                ->orderBy($orderBy);
        $result = $db->executeS($sql);
        foreach ($result as $row)
        {
            $options[] = [
                'id' => $row[$id],
                'value' => $row[$value]
            ];
        }
        
        return $options;
    }
    
    private function getInputValues()
    {
        $this->selected_manufacturer = Tools::getValue('input_select_manufacturer',0);
        $this->selected_supplier = Tools::getValue('input_select_supplier',0);
        $this->total_new_products = Tools::getValue('input_product_new',0);
        $this->total_obs_products = Tools::getValue('input_product_obsolete',0);
        $this->reference_prefix = Tools::getValue('input_reference_prefix','');
        $this->current_page = Tools::getValue('input_current_page',0);
        $this->current_pagination = Tools::getValue('input_select_pagination',10);
    }
    
    private function getProductByReference($reference)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_product')
                ->from('product')
                ->where("reference='" . pSQL($reference) . "'" );
        $value = $db->getValue($sql);
        if ($value===false) {
            return null;
        } else {
            $product = new ProductCore($value);
            $messages[] = [
                'function' => 'updateproduct',
                'product id' => $product->id
            ];
            return $product;
        }
    }
    
    private function updateProduct(ProductCore $product, $productList)
    {
        $imagePath = $productList['image_path'];
        $chunks = explode(".",$imagePath);
        $format = end($chunks);
        $image = new ImageCore();
        $image->cover=false;
        $image->force_id=false;
        $image->id=0;
        $image->id_image=0;
        $image->id_product = $product->id;
        $image->image_format = $format;
        $image->legend = $productList['product'];
        $image->position=0;
        $image->source_index='';
        $image->add();
        
        $imageTargetFolder = _PS_PROD_IMG_DIR_ . ImageCore::getImgFolderStatic($image->id);
        if (!file_exists($imageTargetFolder)) {
            mkdir($imageTargetFolder, 0777, true);
        }
        $target = $imageTargetFolder . $image->id . '.' . $image->image_format;
        $copy = copy($imagePath, $target);
        
        
        
        $this->messages[] = [
            'function' => 'updateproduct',
            'params' => ['product' => htmlentities($product->description_short),'productList' => htmlentities(print_r($productList, 1))],
            'copy' => $copy,
            'image copy' => 'success',
            'target' => $target,
            '$image' => htmlentities(print_r($image, 1))
            ];
    }
    
    private function createProduct($productList)
    {
        return $productList;
    }
}
