<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classUpdatePrices
 *
 * @author massimiliano
 */

/** Include PHPExcel */
require_once _PS_TOOL_DIR_ . '/PHPExcel-1.8/Classes/PHPExcel.php';

class classImportExcel 
{    
    public $filename;
    public $col_order_reference;
    public $col_tracking_id;
    public $col_delivered_date;
    public $id_order_state;
    private $tablename;
    
    public function __construct() {
        $this->tablename = 'mp_carrier_import_rows';
    } 
    
    public function read($fileName)
    {
        // Read from Excel5 (.xls) template
        $inputFileType = 'Excel5';
        $objPHPExcel = new PHPExcel_Reader_Excel5();
        $objPHPExcel->setReadDataOnly(true);
        $objPHPExcel->load($fileName);
        
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        
        $objExcel = $objReader->load($fileName);
        $sheet = $objExcel->getActiveSheet()->toArray(TRUE,TRUE);
        
        return $sheet;
    }
    
    public function getCSV($file)
    {
        // Read from CSV (.csv) template
        $objPHPExcel = new PHPExcel_Reader_CSV();
 
    }
}

class ExcelImportContentFormat 
{
    private $header;
    private $rows;
    private $col;
    private $row;
    
    public function __construct() 
    {
        $this->header = [];
        $this->rows = [];
        $this->row = [];
        $this->col = "";
    }
    
    public function newRow()
    {
        $this->row = [];
    }
    
    public function addCol($value)
    {
        $this->col = $value;
        $this->row[] = $value;
    }
    
    public function addHeader()
    {
        $this->header[] = $this->row;
        $this->row = [];
    }
    
    public function addContentRow()
    {
        $this->rows[] = $this->row;
        $this->row = [];
    }
    
    public function getHeader()
    {
        return $this->header;
    }
    
    public function getContentRows()
    {
       return $this->rows;
    }
}