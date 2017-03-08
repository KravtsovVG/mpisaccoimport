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
 * to info@mpsoft.it so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    mpSOFT <info@mpsoft.it>
 *  @copyright 2017 mpSOFT Massimiliano Palermo
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of mpSOFT
 */

class classCarrierImport {
    public $id_carrier_import;
    public $carrier_name;
    public $carrier_import_type_id;
    public $column_separator;
    public $web_link;
    public $col_order_reference;
    public $col_tracking_id;
    public $col_delivered_date;
    public $order_state_id;
    public $error;
    private $tablename;
    private $messages;
    
    public function __construct() 
    {
        $this->id_carrier_import = 0;
        $this->carrier_name = '';
        $this->carrier_import_type_id = 0;
        $this->column_separator = '';
        $this->web_link = '';
        $this->col_order_reference = '';
        $this->col_tracking_id = '';
        $this->col_delivered_date = '';
        $this->order_state_id;
        $this->tablename = 'mp_carrier_import';
        $this->messages = [];
        $this->error = '';
    }
    
    public function create()
    {
        $db = Db::getInstance();
        try {
            return $db->insert($this->tablename, $this->getData(), true);
        } catch (Exception $exc) {
            return ['type' => 'error', 'msg' => $exc->getMessage()];
        }
    }
    
    public function read($id)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('ci.*')
                ->select('osl.name as order_state_name')
                ->from($this->tablename, 'ci')
                ->innerJoin('order_state_lang', 'osl', 'osl.order_state_id=ci.order_state_id')
                ->where('osl.id_lang=' . (int)Context::getContext()->language->id)
                ->where('ci.id_carrier_import='.(int)$id)
                ->orderBy('ci.carrier_name')
                ->orderBy('ci.carrier_import_type_id');
        return $db->getRow($sql);
    }
    
    public function update($id)
    {
        $db = Db::getInstance();
        try {
            return $db->update($this->tablename, $this->getData(), 'id_carrier_import='.(int)$id, 0,true);
        } catch (Exception $exc) {
            return ['type' => 'error', 'msg' => $exc->getMessage()];
        }
    }
    
    public function delete($id)
    {
        $db = Db::getInstance();
        return $db->delete($this->tablename, 'id_carrier_import='.(int)$id);
    }
    
    public function getList()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('ci.*')
                ->select('osl.name as order_state_name')
                ->from($this->tablename, 'ci')
                ->innerJoin('order_state_lang', 'osl', 'osl.order_state_id=ci.order_state_id')
                ->where('osl.id_lang=' . (int)Context::getContext()->language->id)
                ->orderBy('ci.carrier_name')
                ->orderBy('ci.carrier_import_type_id');
        return $db->executeS($sql);
    }
    
    public function getData()
    {
        return [
            'carrier_name' => pSQL($this->carrier_name),
            'carrier_import_type_id' => (int)$this->carrier_import_type_id,
            'column_separator' => pSQL($this->column_separator),
            'web_link' => pSQL($this->web_link),
            'col_order_reference' => pSQL($this->col_order_reference),
            'col_tracking_id' => pSQL($this->col_tracking_id),
            'col_delivered_date' => pSQL($this->col_delivered_date),
            'order_state_id' => (int)$this->order_state_id
        ];
    }
    
    public function getCarrierImport($importType,$carrierName)
    {
        $db = Db::getInstance();
        $sqlConfig = new DbQueryCore();
        $sqlConfig->select('*')
                ->from($this->tablename)
                ->where('carrier_import_type_id = ' . (int)$importType)
                ->where('carrier_name = \'' . pSQL($carrierName) . '\'');
        try {
            $row = $db->getRow($sqlConfig);
            
            $this->id_carrier_import = (int)$row['id_carrier_import'];
            $this->carrier_name = pSQL($row['carrier_name']);
            $this->carrier_import_type_id = (int)$row['carrier_import_type_id'];
            $this->column_separator = pSQL($row['column_separator']);
            $this->web_link = pSQL($row['web_link']);
            $this->col_order_reference = pSQL($row['col_order_reference']);
            $this->col_tracking_id = pSQL($row['col_tracking_id']);
            $this->col_delivered_date = pSQL($row['col_delivered_date']);
            $this->order_state_id = (int)$row['order_state_id'];
            $this->error = '';
            
            return $row;
        } catch (Exception $exc) {
            $this->id_carrier_import = 0;
            $this->carrier_name = '';
            $this->carrier_import_type_id = 0;
            $this->column_separator = '';
            $this->web_link = '';
            $this->col_order_reference = '';
            $this->col_tracking_id = '';
            $this->col_delivered_date = '';
            $this->order_state_id = 0;
            $this->error = $exc->getMessage();
        }
    }
}
