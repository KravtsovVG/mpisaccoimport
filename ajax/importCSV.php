<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/../../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../../init.php');

$db = Db::getInstance();
$json = Tools::getValue("json");
$obj  = json_decode($json);

foreach($obj as $row)
{
    $order_reference = $row->order_reference;
    $track_id = $row->track_id;
    $id_order_state = $row->id_order_state;
    $query = new DbQueryCore();
    //print "order: $order_reference, track: $track_id, state: $id_order_state\n";
    
    $query
            ->select("id_order")
            ->from("orders")
            ->where("reference = '$order_reference'");
    $order_id = (int) $db->getValue($query);
    if($order_id>0)
    {
        $order = new Order($order_id);
        $order->setCurrentState($id_order_state);
        $order_carrier = new OrderCarrierCore($order->getIdOrderCarrier());
        $order_carrier->tracking_number = $track_id;
        $order_carrier->update();
    }
    
}

exit();