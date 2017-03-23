<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$id_attribute_group = Tools::getValue("id_attribute_group");
$id_lang = Context::getContext()->language->id;

$db = Db::getInstance();
$query = new DbQueryCore();

$query
        ->select("a.id_attribute")
        ->select("al.name")
        ->from("attribute","`a`")
        ->innerJoin('attribute_lang','`al`','a.id_attribute=al.id_attribute')
        ->where("a.id_attribute_group = $id_attribute_group")
        ->where("al.id_lang = $id_lang")
        ->orderBy("a.position")
        ->orderBy('al.name');

$result = $db->executeS($query);
$options = "";
foreach($result as $record)
{
    $options .= "<option value='" . $record["id_attribute"] . "'>". $record["name"] ."</option>";
}
print $options;
exit();