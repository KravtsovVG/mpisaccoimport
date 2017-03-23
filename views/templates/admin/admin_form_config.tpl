{*
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
*}
<style>
    .label-br
    {
        display: block;
        margin-bottom: 16px !important;
    }
    .table tbody tr:nth-child(odd) td
    {
        background-color: #FAFDFF !important;
    }
    .table tbody tr td
    {
        border: thin solid #EEEEEE;
    }
    .table tbody tr td:nth-child(3)
    {
        max-width: 60px;
    }
    .config-buttons:hover
    {
        color: #00aff0 !important;
    }
    a:hover
    {
        text-decoration: #0083c4;
        cursor: pointer;
    }
</style>
<form class='defaultForm form-horizontal' method='post'>
    <input type='hidden' name='input_json_data' value='{$input_json_data}'>
    <input type='hidden' name='submit_config'>
    <div class='panel' id='panel-config'>
        <div class='panel-heading'>
            <img src='../modules/mpisaccoimport/views/img/config.png' alt='Config'>
            {l s='Configuration section' mod='mpisaccoimport'}
        </div>  
        <!-- ******************************************
             ** EXCEL HEADER FOR PRODUCT DESCRIPTION **
             ****************************************** -->
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label label-br required">{l s='Product description' mod='mpisaccoimport'}</label>
                <br>
                <div>
                    <div style="float: left; margin-right: 10px;">
                        <select size="10" style='width: 300px;' name='input_excel_header'>
                            {foreach $input_excel_header as $option}
                                <option value='{$option['id']}'>{$option['value']}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div style="float: left; width: 64px;">
                        <i class="process-icon-plus config-buttons" onclick="addDescription();"></i>
                        <br>
                        <i class="process-icon-cancel config-buttons" onclick="delDescription();"></i>
                    </div>
                    <div style="float: left; margin-right: 10px;">
                        <select size="10" style='width: 300px;'>
                            
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- *******************
             ** FEATURE ITEMS **
             ******************* -->
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label label-br required">{l s='Product feature' mod='mpisaccoimport'}</label>
                <i class='icon-arrow-right'></i>
                <input type='checkbox' name='input_chk_feature_description'>
                    <label class="control-label label-br" for='input_chk_feature_description'>
                        {l s='Search by description' mod='mpisaccoimport'}
                    </label>
                <br>
                <div>
                    <div style="float: left; margin-right: 10px;">
                        <select style='width: 300px;' name='input_list_feature' id='input_list_feature'>
                            {foreach $input_list_features as $option}
                                <option value='{$option['id']}'>{$option['value']}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div style="float: left; margin-right: 10px;">
                        <select style='width: 300px;' name='input_list_feature_header' id='input_list_feature_header'>
                            <option value="0">{l s='Please select' mod='mpisaccoimport'}</option>
                            {foreach $input_excel_header as $option}
                                <option value='{$option['id']}'>{$option['value']}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div style="float: left; width: 64px;">
                        <i class="process-icon-plus config-buttons" style="display: inline-block;" onclick="addFeature();"></i>
                        <i class="process-icon-cancel config-buttons" style="display: inline-block;" onclick="delFeature();"></i>
                    </div>
                </div>
                <br style='clear: both;'>
                <br>
                <div>
                    <table class='table' id="table_features">
                        <thead>
                            <tr>
                                <th style='display: none;'></th>
                                <th>{l s='Feature' mod='mpisaccoimport'}</th>
                                <th style='display: none;'></th>
                                <th>{l s='Column' mod='mpisaccoimport'}</th>
                                <th>{l s='Actions' mod='mpisaccoimport'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- *********************
             ** ATTRIBUTE ITEMS **
             ********************* -->
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label label-br required">{l s='Product attributes' mod='mpisaccoimport'}</label>
                <i class='icon-arrow-right'></i>
                <input type='checkbox' name='input_chk_attribute_description'>
                    <label class="control-label label-br" for='input_chk_attribute_description'>
                        {l s='Search by description' mod='mpisaccoimport'}
                    </label>
                <br>
                <br>
                <div>
                    <select style='width: 300px;' name='input_list_attribute_groups' id='input_list_attribute_groups'>
                        {foreach $input_list_attribute_groups as $option}
                            <option value='{$option['id']}'>{$option['value']}</option>
                        {/foreach}
                    </select>
                </div>
                <br>
                <div>
                    <div style="float: left; margin-right: 10px;">
                        <select style='width: 300px;' name='input_list_attributes' id='input_list_attributes'>
                            
                        </select>
                    </div>
                    <div style="float: left; margin-right: 10px;">
                        <select style='width: 300px;' name='input_list_attribute_header' id='input_list_attribute_header'>
                            <option value="0">{l s='Please select' mod='mpisaccoimport'}</option>
                            {foreach $input_excel_header as $option}
                                <option value='{$option['id']}'>{$option['value']}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div style="float: left; width: 64px;">
                        <i class="process-icon-plus config-buttons" style="display: inline-block;" onclick="addAttribute();"></i>
                        <i class="process-icon-cancel config-buttons" style="display: inline-block;" onclick="delAttribute();"></i>
                    </div>
                </div>
                <br style='clear: both;'>
                <br>
                <div>
                    <table class='table' id='table_attributes'>
                        <thead>
                            <tr>
                                <th style='display: none;'></th>
                                <th>{l s='Attribute' mod='mpisaccoimport'}</th>
                                <th style='display: none;'></th>
                                <th>{l s='Column' mod='mpisaccoimport'}</th>
                                <th>{l s='Actions' mod='mpisaccoimport'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class='panel-footer'>
            <button type="submit" value="1" id="submit_config" name="submit_config" class="btn btn-default pull-right">
                <i class="process-icon-configure"></i> Import
            </button>
        </div>
                                
    </div>
</form>
                            
<script type="text/javascript">
    $(document).ready(function(){
        $('#input_list_attribute_groups').on('change',function(){
            var id_attribute_group = $(this).val();
            $.ajax({
                method: 'POST',
                url   : '../modules/mpisaccoimport/ajax/getAttributes.php',
                data  : 
                    {
                        'id_attribute_group' : id_attribute_group
                    },
                success : function(response)
                    {
                        response = "<option value='0'>{l s='Please select' mod='mpisaccoimport'}</option>" + response;
                        $('#input_list_attributes').html(response);
                    }
            });
        });
    });
    
    function deleteRow(elem)
    {
        var row = $(elem).parent().parent();
        $(row).remove();
    }
    
    function addFeature()
    {
        var id=$('#input_list_feature').val();
        var value=$('#input_list_feature option:selected').text();
        
        var id_header = $('#input_list_feature_header').val();
        var value_header = $('#input_list_feature_header option:selected').text();
        
        if(id==='0') {
            return;
        }
        
        if(id_header==='0') {
            return;
        }
        
        $row = createRow(id,value,id_header,value_header);
        
        $('#table_features tbody').append($row);
    }
    
    function addAttribute()
    {
        var id=$('#input_list_attributes').val();
        var value=$('#input_list_attributes option:selected').text();
        
        var id_header = $('#input_list_attribute_header').val();
        var value_header = $('#input_list_attribute_header option:selected').text();
        
        if(id==='0') {
            return;
        }
        
        if(id_header==='0') {
            return;
        }
        
        $row = createRow(id,value,id_header,value_header);
        
        $('#table_attributes tbody').append($row);
    }
    
    function createRow(id,value,id_header,value_header)
    {
        var output;
        output = "<tr>" +
                    "<td style='display: none;'>" + id + "</td>" +
                    "<td>" + value + "</td>" +
                    "<td style='display: none;'>" + id_header + "</td>" +
                    "<td>" + value_header + "</td>" +
                    "<td>" + createDeleteButton() + "</td>" +
                + "</tr>";
        return output;
    }
    
    function createDeleteButton()
    {
        return "<a onclick='javascript:deleteRow(this);'>" +
                    "<img src='../modules/mpisaccoimport/views/img/delete.png' style='margin-right: 10px;'>" +
                    "{l s='Delete' mod='mpisaccoimport'}" + 
                "</a>";
    }
</script>