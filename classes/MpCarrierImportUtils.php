<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MpCarrierImportUtils
 *
 * @author imprendo
 */

require_once(_CLASSES_ . "classImportCSV.php");

class MpCarrierImportUtils {
    public static function getFormFields($id)
    {
        //Get table Row
        $db = Db::getInstance();
        $row = new DbQueryCore();
        $row
                ->select("ci.id_carrier_import")
                ->select("ci.carrier_name")
                ->select("ci.id_carrier_import_type")
                ->select("cit.import_name")
                ->select("ci.web_link")
                ->select("ci.col_order_reference")
                ->select("ci.col_tracking_id")
                ->select("ci.col_delivered_date")
                ->select("ci.id_order_state")
                ->select("ci.column_separator")
                ->select("'order_state' as order_state_name")
                ->from("mp_carrier_import","ci")
                ->innerJoin("mp_carrier_import_type", "cit", "cit.id_carrier_import_type=ci.id_carrier_import_type")
                ->where("id_carrier_import = $id");
        $resultRow = $db->getRow($row);
        
        return $resultRow;
    }
    
    public static function getImportType()
    {
        //Get table Row
        $db = Db::getInstance();
        $row = new DbQueryCore();
        $row
                ->select("id_carrier_import_type")
                ->select("import_name")
                ->from("mp_carrier_import_type")
                ->orderBy("import_name");
        $resultRow = $db->executeS($row);
        
        return $resultRow;
    }
    
    public static function setSelectImportType()
    {
        $recordset = self::getImportType();
        $options = [];
        $options[] = [
                        "id"=>0,
                        "value"=>"---",
                    ];
        foreach ($recordset as $record) 
        {
            $options[]=
                    [
                        "id"=>$record['id_carrier_import_type'],
                        "value"=>$record['import_name'],
                    ];
        }
        return $options;
    }
    
    public static function setSelectCarrier($type=0)
    {
        $recordset = self::getCarriers($type);
        
        $options = [];
        foreach ($recordset as $record) 
        {
            $options[]=
                    [
                        "id"=>$record['id_carrier_import'],
                        "value"=>$record['carrier_name'],
                    ];
        }
        return $options;
    }
    
    public static function getCarriers($type=0)
    {
        //Get table Row
        if($type==0)
        {
            return array(['id_carrier_import'=>0,'carrier_name'=>'Select...']);
        }
        else
        {
            $db = Db::getInstance();
            $row = new DbQueryCore();
            $row
                    ->select("id_carrier_import")
                    ->select("carrier_name")
                    ->from("mp_carrier_import")
                    ->where("id_carrier_import_type = $type")
                    ->orderBy("carrier_name");
            $resultRow = $db->executeS($row);
            return $resultRow;
        }
    }
    
    public static function setTable($type,$idCarrier,$csv)
    {
        switch ($type) {
            case 1: //SHIPPED
                return self::setTableShipped($csv,$idCarrier);
            case 2: //DELIVERED
                return self::setTableDelivered($csv,$idCarrier);
            default:
                break;
        }
    }
    
    public static function setTableDelivered($csv,$idCarrier)
    {
        $tmpname = $csv['tmp_name'];
        $tableRows = [];
        $db = Db::getInstance();
        $query = new DbQueryCore();
        $query
                ->select("carrier_name")
                ->select("column_separator")
                ->select("web_link")
                ->select("col_order_reference")
                ->select("col_tracking_id")
                ->select("col_delivered_date")
                ->select("id_order_state")
                ->from("mp_carrier_import")
                ->where("id_carrier_import = $idCarrier");
        $result = $db->getRow($query);
        
        $CSV = new CsvImporter($tmpname, FALSE, $result["column_separator"]);
        
        $parsedCsv = $CSV->get();
        unset($parsedCsv[0]);
        foreach ($parsedCsv as $row) 
        {
            //print "<pre> ROW:" . print_r($parsedCsv,1) . "</pre>";
            $order_reference = self::parseRow($result['col_order_reference'], $row);
            $track_id        = self::parseRow($result['col_tracking_id'], $row);
            $delivered       = self::parseRow($result['col_delivered_date'], $row);
            $date            = new DateTime($delivered);
            $date_format     = $date->format('d/m/Y');
            $tableRows[] = 
                    "<tr>"
                        . "<td>"
                            . "<input type='checkbox' name='check_row[]' checked='checked'>"
                        . "</td>"
                        . "<td>" 
                            .  $order_reference
                        . "</td>"
                        . "<td>" 
                            .  $track_id
                        . "</td>"
                        . "<td>" 
                            . $result['carrier_name'] 
                        . "</td>"
                        . "<td>" 
                            . $date_format
                        . "</td>"
                        . "<td>"
                            . "<a target='_blank' href='" . $result['web_link'] . $track_id . "'>link</a>"
                        . "</td>"
                        . "<td class='hidden'>" 
                            . $result['id_order_state']
                        . "</td>"
                    . "</tr>";
        }
        
        //print "<pre>\n\n\n" . print_r($parsedCsv,1) . "</pre>";
        //print "<pre>\n\n\n" . print_r($tableRows,1) . "</pre>";
        
        return $tableRows;
    }
    
    public static function setTableShipped($csv,$idCarrier)
    {
        $tmpname = $csv['tmp_name'];
        $tableRows = [];
        $db = Db::getInstance();
        $query = new DbQueryCore();
        $query
                ->select("carrier_name")
                ->select("column_separator")
                ->select("web_link")
                ->select("col_order_reference")
                ->select("col_tracking_id")
                ->select("col_delivered_date")
                ->select("id_order_state")
                ->from("mp_carrier_import")
                ->where("id_carrier_import = $idCarrier");
        $result = $db->getRow($query);
        
        $CSV = new CsvImporter($tmpname, FALSE, $result["column_separator"]);
        
        $parsedCsv = $CSV->get();
        unset($parsedCsv[0]);
        foreach ($parsedCsv as $row) 
        {
            //print "<pre> ROW:" . print_r($parsedCsv,1) . "</pre>";
            $order_reference = self::parseRow($result['col_order_reference'], $row);
            $track_id        = self::parseRow($result['col_tracking_id'], $row);
            $tableRows[] = 
                    "<tr>"
                        . "<td>"
                            . "<input type='checkbox' name='check_row[]' checked='checked'>"
                        . "</td>"
                        . "<td>" 
                            .  $order_reference
                        . "</td>"
                        . "<td>" 
                            .  $track_id
                        . "</td>"
                        . "<td>" 
                            . $result['carrier_name'] 
                        . "</td>"
                        . "<td>" 
                            . "---" 
                        . "</td>"
                        . "<td>"
                            . "<a target='_blank' href='" . $result['web_link'] . $track_id . "'>link</a>"
                        . "</td>"
                        . "<td class='hidden'>" 
                            . $result['id_order_state']
                        . "</td>"
                    . "</tr>";
        }
        
        //print "<pre>\n\n\n" . print_r($parsedCsv,1) . "</pre>";
        //print "<pre>\n\n\n" . print_r($tableRows,1) . "</pre>";
        
        return $tableRows;
    }
    
    public static function displayConfirmation($string)
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
    
    public static function displayError($error)
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

        return $output;
    }
    
    public static function parseRow($match,$row)
    {
        //print "<pre>match: $match</pre>";
        if($match=="0"){return "";}
        
        $columns = explode("c", $match);
        unset($columns[0]);
        //print "<pre>split c:" . print_r($columns,1) . "</pre>";
        $result = ""; //sprintf("%02d", $row["VABNRS"]).
        
        foreach($columns as $col)
        {
            if(strpos($col,"d"))
            {
                $digit = explode("d",$col);
                //$digit[0] = column
                //$digit[1] = digits
                //print "<pre>split d:" . print_r($digit,1) . "</pre>";
                $dig = $digit[1];
                $idx = $digit[0]-1;
                $spr = "%0" . $dig . "d";
                $cell = $row[$idx];
                $value = sprintf($spr,$cell);
                $result .= $value;
                //print "<pre>col $idx digits: $dig, sprintf: $spr($cell) = $value</pre>";
                //print "<pre>result :" . $result . "</pre>";
            }
            else
            {
                $result .= $row[$col-1];
                //print "<pre>no split </pre>";
                //print "<pre>result :" . $result . "</pre>";
            }
        }
        return $result;
    }
}
