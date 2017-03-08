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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ReturnMessage.php';

class classImportRow 
{
    //FIELDS
    public $id_import_row;
    public $id_carrier_import;
    public $carrier_name;
    public $carrier_import_type_id;
    public $carrier_import_type_name;
    public $import_file_name;
    public $order_reference;
    public $tracking_code;
    public $delivered_date;
    public $import_date;
    public $order_state_id;
    public $order_state_name;
    //
    private $return_messages;
    private $msg;
    private $tablename;
    
    public function __construct() 
    {
        $this->id_import_row=0;
        $this->id_carrier_import=0;
        $this->carrier_name='';
        $this->carrier_import_type_id=0;
        $this->carrier_import_type_name='';
        $this->import_file_name='';
        $this->order_reference='';
        $this->tracking_code='';
        $this->delivered_date='';
        $this->import_date='';
        $this->order_state_id=0;
        $this->order_state_name='';
        $this->return_messages=[];
        $this->msg = new ReturnMessage();
        $this->tablename = 'mp_carrier_import_rows';
    }
    
    public function setRow($id_import_row,
            $id_carrier_import,
            $carrier_name,
            $carrier_import_type_id,
            $carrier_import_name,
            $import_file_name,
            $order_reference,
            $tracking_code,
            $delivered_date,
            $import_date,
            $order_state_id,
            $order_state_name) 
    {
        $this->id_import_row = (int)$id_import_row;
        $this->id_carrier_import = (int)$id_carrier_import;
        $this->carrier_name = $carrier_name;
        $this->carrier_import_type_id = $carrier_import_type_id;
        $this->carrier_import_type_name = $carrier_import_name;
        $this->import_file_name = $import_file_name;
        $this->order_reference = $order_reference;
        $this->tracking_code = $tracking_code;
        $this->delivered_date = $delivered_date;
        $this->import_date = $import_date;
        $this->order_state_id = $order_state_id;
        $this->order_state_name = $order_state_name;
    }
    
    public function add()
    {
        $db=Db::getInstance();
        if ($this->id_carrier_import==0) {
            $this->setErrorMessage('Carrier not selected.');
            $this->return_messages[] = $this->getReturnMessage();
            return $this->getReturnMessage();
        }
        if (empty($this->order_reference)) {
            $this->setErrorMessage('Order reference can\'t be empty.');
            $this->return_messages[] = $this->getReturnMessage();
            return $this->getReturnMessage();
        }
        $this->import_date = '';
        if (empty($this->import_file_name)) {
            $this->setErrorMessage('Import file not found.');
            $this->return_messages[] = $this->getReturnMessage();
            return $this->getReturnMessage();
        }
        if (empty($this->carrier_name)) {
            $this->setErrorMessage('Carrier name not found.');
            $this->return_messages[] = $this->getReturnMessage();
            return $this->getReturnMessage();
        }
        if ($this->id_import_row>0) {
            return $this->update();
        }
        
        $data = $this->fillData();
        
        try {
            $insert = $db->insert($this->tablename, $data, TRUE);
            if ($insert) {
                $this->id_import_row = $db->Insert_ID();
            } else {
                $this->id_import_row = 0;
            }
        } catch (Exception $exc) {
            $this->id_import_row = 0;
            
            $insert = ['type' => 'error', 'msg' => $exc->getMessage()];
            $messages[] = $insert;
            
        }
        return $insert;
    }
    
    public function read($refTypeCarrier, $dateFrom, $dateTo, $typeImport, $carrierName)
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore;
        if ($refTypeCarrier>0) {
            /**
             * If $refCarrierType is set, there's no need to check for $typeImport and $carrierName
             */
            $sql = $this->createQueryByTypeCarrier($dateFrom, $dateTo);
        } elseif (!empty($dateFrom) || !empty($dateTo)) {
            /**
             * If there's a date restriction, get record by date
             */
            $sql = $this->createQueryByDate($dateFrom, $dateTo, $typeImport, $carrierName);
        } else {
            /**
             * There's no date and no type carrier, get record by $typeImport or $carrierName             
             */
        }  
    }
    
    private function createQueryByDate($dateFrom, $dateTo, $typeImport, $carrierName)
    {
        $sql = new DbQueryCore();
        $sql->select('*')
                ->from($this->tablename)
                ->orderBy('import_date')
                ->orderBy('id_import_row');
        
        if (!empty($dateFrom) && !empty($dateTo)) {
            /**
             * Search by carrier type and full range date
             */
            $sql->where('import_date>='.pSQL($dateFrom))
                    ->where('import_date<='.pSQL($dateFrom));
        } elseif(!empty($dateFrom) && empty($dateTo)) {
            /**
             * Search by carrier type and start date
             */
            $sql->where('import_date>='.pSQL($dateFrom));
        } elseif(empty($dateFrom) && !empty($dateTo)) {
            /**
             * Search by carrier type and end date
             */
            $sql->where('import_date<='.pSQL($dateTo));
        } 
        if (!empty($typeImport)) {
            $sql->where();
        }
        return $sql;
    }
    
    private function createQueryByTypeCarrier($dateFrom, $dateTo)
    {
        $sql = new DbQueryCore();
        if (!empty($dateFrom) && !empty($dateTo)) {
            /**
             * Search by carrier type and full range date
             */
            $sql = new DbQueryCore();
            $sql->select('*')
                    ->from($this->tablename)
                    ->where('ref_type_carrier='.(int)$refTypeCarrier)
                    ->where('import_date>='.pSQL($dateFrom))
                    ->where('import_date<='.pSQL($dateFrom))
                    ->orderBy('import_date')
                    ->orderBy('id_import_row');
        } elseif(!empty($dateFrom) && empty($dateTo)) {
            /**
             * Search by carrier type and start date
             */
            $sql = new DbQueryCore();
            $sql->select('*')
                    ->from($this->tablename)
                    ->where('ref_type_carrier='.(int)$refTypeCarrier)
                    ->where('import_date>='.pSQL($dateFrom))
                    ->orderBy('import_date')
                    ->orderBy('id_import_row');
        } elseif(empty($dateFrom) && !empty($dateTo)) {
            /**
             * Search by carrier type and end date
             */
            $sql = new DbQueryCore();
            $sql->select('*')
                    ->from($this->tablename)
                    ->where('ref_type_carrier='.(int)$refTypeCarrier)
                    ->where('import_date<='.pSQL($dateFrom))
                    ->orderBy('import_date')
                    ->orderBy('id_import_row');
        } elseif($refTypeCarrier>0 && empty($dateFrom) && empty($dateTo)) {
            /**
             * Search by carrier type
             */
            $sql = new DbQueryCore();
            $sql->select('*')
                    ->from($this->tablename)
                    ->where('ref_type_carrier='.(int)$refTypeCarrier)
                    ->orderBy('import_date')
                    ->orderBy('id_import_row');
        } else {
            
        }
        return $sql;
    }
    
    public function update()
    {
        $db = Db::getInstance();
        $data = $this->fillData();
        
        $update = $db->update($this->tablename, $data, 'id_import_row='. $this->id_import_row, TRUE);
        return $update;
    }
    
    public function delete()
    {
        $db = Db::getInstance();
        $delete = $db->delete($this->tablename, 'id_import_row=' . $this->id_import_row);
        return $delete;
    }
    
    public function deleteByCarrierName($carrierName)
    {
        $db = Db::getInstance();
        $delete = $db->delete($this->tablename, 'carrier_name=\'' . pSQL($carrierName) . '\'');
        return $delete;
    }
    
    public function deleteByFileName($fileName)
    {
        $db = Db::getInstance();
        $delete = $db->delete($this->tablename, 'import_file_name=\'' . pSQL($fileName) . '\'');
        return $delete;
    }
    
    public function deleteByImportType($importType)
    {
        $db = Db::getInstance();
        $delete = $db->delete($this->tablename, 'carrier_import_type_id=' . (int)$importType);
        return $delete;
    }
    
    public function setMessage($type,$message)
    {
        $this->msg->setMessage($type, $message);
    }
    
    public function setErrorMessage($message)
    {
        $this->setMessage(ERROR_MSG, $message);
    }
    
    public function setWarningMessage($message)
    {
        $this->setMessage(WARNING_MSG, $message);
    }
    
    public function setSuccessMessage($message)
    {
        $this->setMessage(SUCCESS_MSG, $message);
    }
    
    public function getReturnMessage()
    {
        return ['type' => $this->msg->getType(), 'message' => $this->msg->getMessage()];
    }
    
    public function fillData()
    {
        return [
            'id_carrier_import' => (int)$this->id_carrier_import,
            'carrier_name' => pSQL($this->carrier_name),
            'carrier_import_type_id' => (int)$this->carrier_import_type_id,
            'carrier_import_type_name' => pSQL($this->carrier_import_type_name),
            'import_file_name' => pSQL($this->import_file_name),
            'order_reference' => pSQL($this->order_reference),
            'tracking_code' => pSQL($this->tracking_code),
            'delivered_date' => pSQL($this->delivered_date),
            'import_date' => pSQL($this->import_date),
            'order_state_id' => (int)$this->order_state_id,
            'order_state_name' => pSQL($this->order_state_name)
            ];
    }
    
    public function getCSV($filename,$skipHeader=true)
    {
        
    }    
}
