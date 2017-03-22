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

if (!defined('MP_ISACCOIMPORT_CLASSES_')) {
    define('MP_ISACCOIMPORT_CLASSES_', _PS_MODULE_DIR_ 
            . DIRECTORY_SEPARATOR . 'mpisaccoimport'
            . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
}

if(!class_exists('DebugMessage')) {
    require_once MP_ISACCOIMPORT_CLASSES_ . "DebugMessage.php";
}

class AdminMpIsaccoImportController extends ModuleAdminControllerCore {
    private $_lang;
    private $tablename;
    private $list;
    private $paginationList;
    private $filename;
    private $messages;
    private $tmp_file;
    private $json_file;
    private $current_page;
    private $current_pagination;
    private $current_color_attribute_group;
    private $current_material_attribute_group;
    private $current_dimension_attribute_group;
    private $current_washing_attribute_group;
    private $current_color_feature;
    private $current_material_feature;
    private $current_dimension_feature;
    private $current_washing_feature;
    private $current_switch_import_image;
    private $current_manufacturer;
    private $current_supplier;
    private $session_list;
    private $list_manufacturers;
    private $list_suppliers;
    private $list_attribute_group;
    private $list_attributes;
    private $list_features;
    private $total_new_products;
    private $total_obs_products;
    private $reference_prefix;
    private $result_message;
    
    
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
        $this->current_color_attribute_group = Tools::getValue('input_select_color_attribute_group',0);
        $this->current_material_attribute_group = Tools::getValue('input_select_material_attribute_group',0);
        $this->current_dimension_attribute_group = Tools::getValue('input_select_dimension_attribute_group',0);
        $this->current_washing_attribute_group = Tools::getValue('input_select_washing_attribute_group',0);
        $this->current_color_feature = Tools::getValue('input_select_color_feature',0);
        $this->current_material_feature = Tools::getValue('input_select_material_feature',0);
        $this->current_dimension_feature = Tools::getValue('input_select_dimension_feature',0);
        $this->current_washing_feature = Tools::getValue('input_select_washing_feature',0);
        $this->current_switch_import_image = Tools::getValue('input_switch_import_image',1);
        
        $this->session_list = 'isacco_list';
        $this->list_manufacturers = [];
        $this->list_suppliers = [];
        $this->list_attribute_group = [];
        $this->list_attributes = [];
        $this->list_features = [];
        $this->result_message = '';
       
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
                        'type' => 'switch',
                        'label' => $this->l('Import images?'),
                        'name' => 'input_switch_import_image',
                        'is_bool' => true,
                        'desc' => $this->l('If set, this module will import images from Isacco server'),
                        'values' => [
                            [
                                'id' => 'import_on',
                                'value' => 1,
                                'label' => $this->l('YES')
                            ],
                            [
                                'id' => 'import_off',
                                'value' => 0,
                                'label' => $this->l('NO')
                            ]
                        ]
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
                        'label' => $this->l('Associate colors to attribute group'),
                        'desc' => $this->l('Choose an attribute group to associate color field'),
                        'name' => 'input_select_color_attribute_group',
                        'required' => true,
                        'options' => [
                            'query' => $this->list_attribute_group,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate materials to attribute group'),
                        'desc' => $this->l('Choose an attribute group to associate material field'),
                        'name' => 'input_select_material_attribute_group',
                        'required' => true,
                        'options' => [
                            'query' => $this->list_attribute_group,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate dimensions to attribute group'),
                        'desc' => $this->l('Choose an attribute group to associate color field'),
                        'name' => 'input_select_dimension_attribute_group',
                        'required' => true,
                            'options' => [
                            'query' => $this->list_attribute_group,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate washing to attribute group'),
                        'desc' => $this->l('Choose an attribute group to associate washing field'),
                        'name' => 'input_select_washing_attribute_group',
                        'required' => true,
                            'options' => [
                            'query' => $this->list_attribute_group,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate color to feature'),
                        'desc' => $this->l('Choose a feature to associate color field'),
                        'name' => 'input_select_color_feature',
                        'required' => true,
                        'options' => [
                            'query' => $this->list_features,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate material to feature'),
                        'desc' => $this->l('Choose a feature to associate material field'),
                        'name' => 'input_select_material_feature',
                        'required' => true,
                        'options' => [
                            'query' => $this->list_features,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate dimension to feature'),
                        'desc' => $this->l('Choose a feature to associate dimension field'),
                        'name' => 'input_select_dimension_feature',
                        'required' => true,
                        'options' => [
                            'query' => $this->list_features,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Associate washing to feature'),
                        'desc' => $this->l('Choose a feature to associate washing field'),
                        'name' => 'input_select_washing_feature',
                        'required' => true,
                        'options' => [
                            'query' => $this->list_features,
                            'id' => 'id',
                            'name' => 'value'
                        ],
                ],
                    [
                        'type' => 'hidden',
                        'name' => 'input_current_page',
                        'id' => 'input_current_page',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('SAVE'),       
                    'class' => 'btn btn-default pull-right',
                    'name'  => 'submit_form',
                    'id'    => 'submit_form',
                    'icon'  => 'process-icon-save'
                ],
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
        $helper->fields_value['input_switch_import_image'] = $this->current_switch_import_image;
        $helper->fields_value['input_current_page'] = $this->current_page;
        $helper->fields_value['input_select_manufacturer'] = $this->current_manufacturer;
        $helper->fields_value['input_select_supplier'] = $this->current_supplier;
        $helper->fields_value['input_select_pagination'] = $this->current_pagination;
        $helper->fields_value['input_select_color_attribute_group'] = $this->current_color_attribute_group;
        $helper->fields_value['input_select_material_attribute_group'] = $this->current_material_attribute_group;
        $helper->fields_value['input_select_dimension_attribute_group'] = $this->current_dimension_attribute_group;
        $helper->fields_value['input_select_washing_attribute_group'] = $this->current_washing_attribute_group;
        $helper->fields_value['input_select_color_feature'] = $this->current_color_feature;
        $helper->fields_value['input_select_material_feature'] = $this->current_material_feature;
        $helper->fields_value['input_select_dimension_feature'] = $this->current_dimension_feature;
        $helper->fields_value['input_select_washing_feature'] = $this->current_washing_feature;
        
        
        $this->messages[]['createForm'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            'return' => count($helper->fields_value) . ' elements',
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
        
        $this->messages[]['createTable'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
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
            $this->result_message .= $this->displayConfirmation($this->l('Selected products deleted with success.'));
            $this->messages[]['processBulkDelete'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'boxes' => count($boxes) . " elements",
                //'list' => htmlspecialchars(print_r($this->list,1))
                ];
        }
    }
    
    protected function processBulkImport() {
        if (Tools::isSubmit('submitBulkimport')) {
            $boxes = Tools::getValue('Box');
            
            foreach ($boxes as $box)
            {
                $product = $this->getProductByReference($this->list[$box]['reference']);
                if(!empty($product)) {
                    $this->productUpdate($product,$this->list[$box]);
                } else {
                    $this->productInsert($this->list[$box]);
                }
            }
            
            $this->result_message .= $this->displayConfirmation($this->l('Selected products imported with success.'));
            
            $this->messages[]['processBulkImport'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
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
        
        //Get Attributes
        $this->getAttributeGroups();
        
        //get Features
        $this->getFeatures();
        
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
        $form   =  $this->createForm();
        $table  = $this->createTable();
        $script =  $smarty->fetch(_PS_MODULE_DIR_ . 'mpisaccoimport/views/templates/admin/script.tpl');
        $nav    =  $smarty->fetch(_PS_MODULE_DIR_ . 'mpisaccoimport/views/templates/admin/table_navigator.tpl');

        $this->messages[]['initContent'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            'already parsed' => 'true',
            'list' => count($this->list) . " elements"
        ];
        
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
        
        $this->context->smarty->assign(array('content' => $this->result_message . $form . $nav . $table . $nav . $script . $this->messages));
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
        
        $this->messages[]['getOrderStateLang'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
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
            $this->messages[]['getOrderStateByReference'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'param' => $order_reference,
                'return' => 'order id: ' . $value,
                'sql' => (string)$sql
                ];
            return $value;
        } catch (Exception $ex) {
            $this->messages[]['getOrderStateByReference'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
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
            $this->messages[]['getSeparator'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'return' => 'no separator found'
            ];
            return null;
        } else {
            $this->messages[]['getSeparator'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
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
    
    /**
     * Conver an array to <option> tag
     * @param array $array array of options
     * @return string option list
     */
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
    
    /**
     * create list for helperlist table
     * @return void
     */
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
     * create a list for helperlist table content
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
                'thumb' => '<a href="https://www.isacco.it' . $row['image'] . '" target="_blank"><img src="https://www.isacco.it' . $row['thumb'] . '" style="width:64px;"></a>',
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
        
        $this->messages[]['createList'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
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
        
        $this->messages[]['createPaginationList'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
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
        $options[] = [
            'id' => 0,
            'value' => $this->l('Please select')
        ];
        foreach ($result as $row)
        {
            $options[] = [
                'id' => $row[$id],
                'value' => $row[$value]
            ];
        }
        
        return $options;
    }
    
    private function createOptionListFromArray($array,$id,$value)
    {
        $options = [];
        $options[] = [
            'id' => 0,
            'value' => $this->l('Please select')
        ];
        foreach ($array as $row)
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
        if(session_status()!=PHP_SESSION_ACTIVE) {
            session_start();
        } 
        
        if(Tools::isSubmit('submitBulkdelete') || Tools::isSubmit('submitBulkimport')) {
            $array = tools::jsonDecode($_SESSION['input_values'],true);
            
            $this->current_manufacturer = $array['manufacturer'];
            $this->current_supplier = $array['supplier'];
            $this->total_new_products = $array['new'];
            $this->total_obs_products = $array['obs'];
            $this->reference_prefix = $array['prefix'];
            $this->current_page = $array['page'];
            $this->current_pagination = $array['pagination'];
            $this->current_color_attribute_group = $array['attribute_color_group'];
            $this->current_material_attribute_group = $array['attribute_material_group'];
            $this->current_dimension_attribute_group = $array['attribute_dimension_group'];
            $this->current_washing_attribute_group = $array['attribute_washing_group'];
            $this->current_color_feature = $array['feature_color'];
            $this->current_material_feature = $array['feature_material'];
            $this->current_dimension_feature = $array['feature_dimension'];
            $this->current_washing_feature = $array['feature_washing'];
            $this->current_switch_import_image = $array['switch_import_image'];

        } else {
            if (!empty($_FILES['input_import_file']['name'])) {
                $this->current_manufacturer = 0;
                $this->current_supplier = 0;
                $this->total_new_products = 0;
                $this->total_obs_products = 0;
                $this->reference_prefix = Tools::getValue('input_reference_prefix','');
                $this->current_page = 0;
                $this->current_pagination = 10;
                $this->current_color_attribute_group = 0;
                $this->current_material_attribute_group = 0;
                $this->current_dimension_attribute_group = 0;
                $this->current_washing_attribute_group = 0;
                $this->current_color_feature = 0;
                $this->current_dimension_feature = 0;
                $this->current_material_feature = 0;
                $this->current_washing_feature = 0;
                $this->current_switch_import_image = 1;
                unset($_SESSION[$this->session_list]);
                unset($_SESSION['input_values']);
            } else {
                $this->current_manufacturer = Tools::getValue('input_select_manufacturer',0);
                $this->current_supplier = Tools::getValue('input_select_supplier',0);
                $this->total_new_products = Tools::getValue('input_product_new',0);
                $this->total_obs_products = Tools::getValue('input_product_obsolete',0);
                $this->reference_prefix = Tools::getValue('input_reference_prefix','');
                $this->current_page = Tools::getValue('input_current_page',0);
                $this->current_pagination = Tools::getValue('input_select_pagination',10);
                $this->current_color_attribute_group = Tools::getValue('input_select_color_attribute_group',0);
                $this->current_material_attribute_group = Tools::getValue('input_select_material_attribute_group',0);
                $this->current_dimension_attribute_group = Tools::getValue('input_select_dimension_attribute_group',0);
                $this->current_washing_attribute_group = Tools::getValue('input_select_washing_attribute_group',0);
                $this->current_color_feature = Tools::getValue('input_select_color_feature',0);
                $this->current_material_feature = Tools::getValue('input_select_material_feature',0);
                $this->current_dimension_feature = Tools::getValue('input_select_dimension_feature',0);
                $this->current_washing_feature = Tools::getValue('input_select_washing_feature',0);
                $this->current_switch_import_image = Tools::getValue('input_switch_import_image',1);
            }
            
            $array = [
                'manufacturer' => $this->current_manufacturer,
                'supplier' => $this->current_supplier,
                'new' => $this->total_new_products,
                'obs' => $this->total_obs_products,
                'prefix' => $this->reference_prefix,
                'page' => $this->current_page,
                'pagination' => $this->current_pagination,
                'attribute_color_group' => $this->current_color_attribute_group,
                'attribute_material_group' => $this->current_material_attribute_group,
                'attribute_dimension_group' => $this->current_dimension_attribute_group,
                'attribute_washing_group' => $this->current_washing_attribute_group,
                'feature_color' => $this->current_color_feature,
                'feature_material' => $this->current_material_feature,
                'feature_dimension' => $this->current_dimension_feature,
                'feature_washing' => $this->current_washing_feature,
                'switch_import_image' => $this->current_switch_import_image,
            ];
            
            $_SESSION['input_values'] = Tools::jsonEncode($array);
        }
            
        $this->messages[]['getInputValues'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            'manufacturer' => $this->current_manufacturer,
            'supplier' => $this->current_supplier,
            'new products' => $this->total_new_products,
            'obsolete products' => $this->total_obs_products,
            'reference prefix' => $this->reference_prefix,
            'current page' => $this->current_page,
            'pagination' => $this->current_pagination,
            'attribute group' => $this->current_color_attribute_group,
            'session' => htmlentities(print_r($_SESSION['input_values'], 1)),
            'submitBulkdelete' => (int)  Tools::isSubmit('submitBulkdelete'),
            'submitBulkimport' => (int)  Tools::isSubmit('submitBulkimport'),
        ];
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
            $this->messages[]['getProductByReference'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'product id' => $product->id
            ];
            return $product;
        }
    }
    
    private function productUpdate(ProductCore &$product, $productList)
    {
        $error = false;
        //import image
        if (!empty($productList['image_path'])) {
            $imagePath = $productList['image_path'];
            $chunks = explode(".",$imagePath);
            $format = end($chunks);
            
            if ($this->current_switch_import_image==1) {
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
            }
        }
            
        //Update product fields
        
        if (!empty($productList['product'])) {
            $product->name[$this->_lang] = $productList['product'];
        }
        if ($this->current_manufacturer>0) {
            $product->id_manufacturer = $this->current_manufacturer;
        }
        if ($this->current_supplier>0) {
            $product->id_supplier = $this->current_supplier;
        }
        
        $this->messages[]['productUpdate CHECK OBJECT'] = [
            'on' => false,
            'call' => debug_backtrace()[1]['function'],
            'product' => htmlentities(print_r($product, 1)),
        ];
        
        try {
            $this->messages[]['productUpdate CALL $product->update'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'product reference' => $product->reference,
                'product name' => $product->name,
                'product manufacturer' => $product->id_manufacturer,
                'product supplier' => $product->id_supplier,
                'status' => 'updating...'
            ];
            $product->update();
        } catch (Exception $exc) {
            $error = true;
            $this->messages[]['productUpdate -ERROR-'] = [
                'on' => true,
                'call' => debug_backtrace()[1]['function'],
                'product reference' => $product->reference,
                'product import reference' => $productList['product_id'],
                'error' => $exc->getMessage(),
                'product name' => $product->name,
                'getProductName' => $product->getProductName($product->id),
                'stack' => $exc->getTraceAsString(),
                //'product' => htmlentities(print_r($product, 1))
            ];      
        }
        
        //Update Attributes
        if ($this->current_color_attribute_group>0) {
            $this->updateAttribute($product, $productList, $this->current_color_attribute_group, true);
        }
        
        //Update product supplier
        if ($this->current_supplier>0) {
            $product_attributes = $product->getProductAttributesIds($product->id);
            if (is_array($product_attributes)) {
               foreach($product_attributes as $product_attribute_row_id) 
               {
                   $this->messages[]['productUpdate SUPPLIER'] = [
                       'on' => true,
                       'product attribute' => htmlentities(print_r($product_attribute_row_id, 1))
                   ];
                   $prod_suppliers = $this->getProductSuppliers($product->id,$product_attribute_row_id['id_product_attribute']);
                   if(is_array($prod_suppliers)) {
                       foreach($prod_suppliers as $prod_supplier_row_id)
                       {
                           $prod_supplier = new ProductSupplierCore($prod_supplier_row_id['id_product_supplier']);
                           $prod_supplier->id_supplier = $this->current_supplier;
                           $prod_supplier->product_supplier_reference = $product->reference;
                           try {
                               $prod_supplier->update();
                           } catch (Exception $exc) {
                               $this->messages[]['productUpdate SUPPLIERS'] = [
                                   'on' => true,
                                   'error' => $exc->getMessage(),
                               ];
                           }
                        }
                   }
               }
            }
        }
        
        //Messages
        $this->messages[]['productUpdate SUMMARY'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            'status' => $error==true?'ERROR DURING UPDATE':'UPDATE OK',
            'params' => [
                'product reference' => htmlentities($product->reference),
                'product import reference' => htmlentities($productList['product_id'])],
            'copy' => isset($copy)?$copy:'not set',
            'image copy' => $this->current_switch_import_image==1?'enabled':'disabled',
            'target' => isset($target)?$target:'not set',
            '$image' => isset($image)?htmlentities($image->legend):'not set'
            ];
    }
    
    private function productInsert($productList)
    {
        return $productList;
    }
    
    private function getAttributeGroups()
    {  
        $attrGroups = AttributeGroupCore::getAttributesGroups($this->_lang);
        $this->list_attribute_group = $this->createOptionListFromArray($attrGroups, 'id_attribute_group', 'name');
        
        
        $this->messages[]['getAttributeGroups'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            //'groups' => htmlentities(print_r($attrGroups, 1)),
            'groups' => count($attrGroups) . ' elements',
        ];
        
    }
    
    private function getFeatures()
    {
        $features = FeatureCore::getFeatures($this->_lang);
        $this->list_features = $this->createOptionListFromArray($features, 'id_feature', 'name');
        
        $this->messages[]['getFeatures'] = [
            'on' => true,
            'call' => debug_backtrace()[1]['function'],
            //'features' => htmlentities(print_r($features, 1)),
            'features' => count($features) . ' elements',
        ];
    }
    
    private function updateAttribute(&$product, &$productList, $attribute_group_id, $isColor = false)
    {
        if( $isColor) {
            $color = '';
            $colors = $productList['arr_colori'];
            if(is_array($colors)) {
                $color = $colors[0];
            } elseif ($colors!='--') {
                $color = $colors;
            }
            
            if (!empty($color)) {
                $row = $this->getAttributeByName($attribute_group_id,$color);
                if (!empty($row)) {
                    $id_attribute = $row['id_attribute'];
                    $color_html   = $row['color'];
                    $position     = $row['position'];
                } else {
                    $id_attribute = 0;
                    $color_html = '';
                    $position = -1;
                }
                
                $this->messages[]['updateAttribute'] = [
                    'on' => true,
                    'product' => $productList['product_id'],
                    'attribute_name' => $color,
                    'attribute_id' => $id_attribute!=0?$id_attribute:'not found',
                    'color' => $color_html!=''?$color_html:'not found',
                    'position' => $position!=-1?$position:'not found',
                ];
            }
        }
        $attrGrp = new AttributeGroupCore($attribute_group_id);
        $attr = new AttributeCore();
    }
    
    /**
     * Get id attribute looking for attribute name
     * @param int $attribute_group_id 
     * @param string $attribute_name
     * @return array associated array [id_attribute,color,position]
     */
    private function getAttributeByName($attribute_group_id, $attribute_name)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('a.id_attribute')
                ->select('a.color')
                ->select('a.position')
                ->from('attribute','a')
                ->innerJoin('attribute_lang','al','al.id_attribute=a.id_attribute')
                ->where('al.id_lang='. (int)$this->_lang)
                ->where('al.name like \'%' . pSQL($attribute_name) . '%\'')
                ->where('a.id_attribute_group=' . pSQL($attribute_group_id));
        return $db->getRow($sql);
    }
    
    private function getProductSuppliers($product_id,$product_attribute_id)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('id_product_supplier')
                ->from('product_supplier')
                ->where('id_product='. (int)$product_id)
                ->where('id_product_attribute=' . (int)$product_attribute_id);
        $result = $db->executeS($sql);
        $this->messages[]['getProductSuppliers'] = [
            'on' => true,
            'result' => htmlentities(print_r($result, 1)),
            'product id' => $product_id,
            'product_attribute_id' => $product_attribute_id,
        ];
        return $result;
    }
}
