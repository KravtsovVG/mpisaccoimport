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

class DebugMessageType {
    const MessageTypeInput = 1;
    const MessageTypeOutput = 2;
    const MessageTypeNotes = 3;
}

class DebugMessage {
    private $active;
    private $name;
    private $call;
    private $input;
    private $output;
    private $notes;
    
    public function __construct() {
        $this->active = false;
        $this->name = '';
        $this->call = '';
        $this->input = [];
        $this->output = [];
        $this->notes = [];
    }
    
    public function setStatus($status)
    {
        $this->active = (bool)$status;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setCall($call)
    {
        $this->call = $call;
    }
    
    public function addInput($param, $value)
    {
        $this->input[] = ['param'=>$param, 'value'=>$value];
    }
    
    public function addOutput($param, $value)
    {
        $this->output[] = ['param'=>$param, 'value'=>$value];
    }
    
    public function addNotes($note)
    {
        $this->note[] = $note;
    }
    
    public function add($type,$param,$value)
    {
        switch ($type) 
        {
            case DebugMessageType::MessageTypeInput:
                $this->addInput($param,$value);
                break;
            case DebugMessageType::MessageTypeOutput:
                $this->addOutput($param,$value);
                break;
            case DebugMessageType::MessageTypeNotes:
                $this->addNotes($param,$value);
                break;
            default:
                break;
        } 
    }
    
    public function isActive()
    {
        return (bool)$this->active;
    }
    
    public function displayHTML()
    {
        return "<pre>" .
                    "Function: <b>" . $this->name . '</b>' . PHP_EOL .
                    "  Call    : " . $this->call . PHP_EOL . 
                    "  Input   : " . $this->explode($this->input) .
                    "  Output  : " . $this->explode($this->output) . 
                    "  Notes   : " . $this->explode($this->notes) .
                "</pre>";
                
    }
    
    private function explode($array)
    {
        $output = $this->explodeArray($array);
        if (empty($output)) {
            return str_repeat(" ", 16) . "*** EMPTY ***" . PHP_EOL;
        } else {
            return str_repeat(" ", 16) . $output . PHP_EOL;
        }
    }
    
    private function explodeArray($array, $tab = 0)
    {
        if (empty($array)) {
            return "";
        }
                
        $tab++;
        $output = [];
        foreach($array as $key=>$value)
        {
            if (is_array($value)) {
                $output[] = str_repeat(" ", $tab*4) . '[' . $key . "]: " . PHP_EOL .  $this->explodeArray($value, $tab);
            } else {
                $output[] = str_repeat(" ", $tab*4) . '[' . $key . "]: " . $value;
            }
        }
        return implode(PHP_EOL, $output);
    }
}

class DebugMessageList {
    private $messages;
    
    public function __construct() {
        $this->messages = [];
    }
    
    public function clear()
    {
        $this->messages = [];
    }
    
    public function add(DebugMessage $message)
    {
        $this->messages[] = $message;
    }
    
    public function displayMessages()
    {
        $output = "";
        
        /* @var $message DebugMessage */
        foreach($this->messages as $message)
        {
           if($message->isActive()) {
               $output .= $message->displayHTML() . PHP_EOL;
           }
        }
        
        return $output;
    }
}
