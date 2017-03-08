<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$idType = Tools::getValue("id_type");

$db = Db::getInstance();
$query = new DbQueryCore();

$query
        ->select("id_carrier_import")
        ->select("carrier_name")
        ->from("mp_carrier_import")
        ->where("id_carrier_import_type = $idType")
        ->orderBy("carrier_name");

$result = $db->executeS($query);
$options = "";
foreach($result as $record)
{
    $options .= "<option value='" . $record["id_carrier_import"] . "'>". $record["carrier_name"] ."</option>";
}
print $options;
exit();