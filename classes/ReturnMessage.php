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

class ReturnMessage {
    private $type;
    private $message;
    
    public function __construct() 
    {
        if (!defined('UNDEF_MSG')) {
            define('UNDEF_MSG','UNDEF_MSG');
        }
        if (!defined('ERROR_MSG')) {
            define('ERROR_MSG','ERROR_MSG');
        }
        if (!defined('WARNING_MSG')) {
            define('WARNING_MSG','WARNING_MSG');
        }
        if (!defined('SUCCESS_MSG')) {
            define('SUCCESS_MSG','SUCCESS_MSG');
        }
        
        $this->type = UNDEF_MSG;
        $this->message = '';
    }
    
    public function setMessage($type,$message)
    {
        if ($type!=ERROR_MSG && $type!=WARNING_MSG && $type!=SUCCESS_MSG) {
            $this->type = UNDEF_MSG;
        } else {
            $this->type = $type;
        }
        
        if (empty($message)) {
            $this->type=UNDEF_MSG;
            $this->message = '';
        } else {
            $this->message = $message;
        }
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
}
