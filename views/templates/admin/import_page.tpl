{*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block mymodule -->
<form method='POST' class='bootstrap defaultForm form-horizontal' enctype='multipart/form-data'>
<div id="form_carrier_import" name="form_carrier_import" class="panel">
    <div class="block_content">
            <fieldset>
                <span class="span_container">
                    <label>{l s="Select import type" mod='mpcarrierimport'}</label>
                    <select id="selImportType" name="selImportType" class="field width-m">
                        {foreach $selImportTypes as $option}
                            <option value="{$option.id}" {if $idImportType eq $option.id}selected="selected"{/if}>{$option.value}</option>
                        {/foreach}
                    </select>
                </span>
                <span class="span_container">                    
                    <label>{l s="Select carrier" mod='mpcarrierimport'}</label>
                    <select id="selCarrier" name="selCarrier" class="field width-xl">
                        {foreach $selCarriers as $option}
                            <option value="{$option.id}" {if $idCarrier eq $option.id}selected="selected"{/if}>{$option.value}</option>
                        {/foreach}
                    </select>
                </span>
                <span class="span_container">
                    <label>{l s="File CSV" mod='mpcarrierimport'}</label>
                    <input type="file" class="field" name="csv" accept=".csv">
                </span>
                
                <input type="submit" name="btnGo" id="submit_go" class="submit-btn icon-gear" value="{l s='GO' mod='mpcarrierimport'}">
            </fieldset>
    </div>
</div>
{if $displayTable}
<div class="bootstrap panel">
    <input type="button" name="btnImport" id="btnImport" class="submit-btn icon-import" value="{l s='IMPORT'}">
    <table class="table-bordered table-data" id="tableCSV">
        <thead>
            <tr>
                <th><input type='checkbox' id="check_row" checked='checked'></th>
                <th>{l s='Order Reference' mod='mpcarrierimport'}</th>
                <th>{l s='Track ID' mod='mpcarrierimport'}</th>
                <th>{l s='Carrier name' mod='mpcarrierimport'}</th>
                <th>{l s='Delivered Date' mod='mpcarrierimport'}</th>
                <th>{l s='Page link' mod='mpcarrierimport'}</th>
                <th class="hidden">{l s='Order state change' mod='mpcarrierimport'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $tableRows as $row}
                {$row}
            {/foreach}
        </tbody>
    </table>
</div>
{/if}
</form>
<!-- The Modal -->
<div id="dialog-add" class="modal-custom-dialog">
  <!-- Modal content -->
  <div class="modal-dialog-content">
    <div class="modal-dialog-header">
        <span class="close" onclick="$(this).parent().parent().parent().fadeOut();"></span>
      <h2>{l s="Add new record" mod='mpcarrierimport'}</h2>
    </div>
    <div class="modal-dialog-body">
        <p><span class="icon-info-circle" style="margin-right: 10px;"></span>{l s="This action will insert a new record. Are you sure?" mod='mpcarrierimport'}</p>
    </div>
    <div class="modal-dialog-footer">
        <input type="button" value="{l s='OK' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut(); insertRow();" class='submit-btn modal-button icon-confirm'>
        <input type="button" value="{l s='CANCEL' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut();" class='submit-btn modal-button icon-delete'>
    </div>
  </div>
</div>

<div id="dialog-update" class="modal-custom-dialog">
  <!-- Modal content -->
  <div class="modal-dialog-content">
    <div class="modal-dialog-header">
        <span class="close" onclick="$(this).parent().parent().parent().fadeOut();"></span>
      <h2>{l s="Update record" mod='mpcarrierimport'}</h2>
    </div>
    <div class="modal-dialog-body">
        <p><span class="icon-info-circle" style="margin-right: 10px;"></span>{l s="This action will update this record. Are you sure?" mod='mpcarrierimport'}</p>
    </div>
    <div class="modal-dialog-footer">
        <input type="button" value="{l s='OK' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut(); updateRow($('#txtIdCarrierImport').val());" class='submit-btn modal-button icon-confirm'>
        <input type="button" value="{l s='CANCEL' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut();" class='submit-btn modal-button icon-delete'>
    </div>
  </div>
</div>
    
<div id="dialog-delete" class="modal-custom-dialog">
  <!-- Modal content -->
  <div class="modal-dialog-content">
    <div class="modal-dialog-header">
        <span class="close" onclick="$(this).parent().parent().parent().fadeOut();"></span>
      <h2>{l s="Notice" mod='mpcarrierimport'}</h2>
    </div>
    <div class="modal-dialog-body">
        <p><span class="icon-info-circle" style="margin-right: 10px;"></span>{l s="This action will delete this record. Are you sure?" mod='mpcarrierimport'}</p>
    </div>
    <div class="modal-dialog-footer">
        <input type="button" value="{l s='OK' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut(); deleteRow($('#txtIdCarrierImport').val());" class='submit-btn modal-button icon-confirm'>
        <input type="button" value="{l s='CANCEL' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut();" class='submit-btn modal-button icon-delete'>
    </div>
  </div>
</div>
    
<div id="dialog-info" class="modal-custom-dialog">
  <!-- Modal content -->
  <div class="modal-dialog-content">
    <div class="modal-dialog-header">
        <span class="close" onclick="$(this).parent().parent().parent().fadeOut();"></span>
      <h2>{l s="Notice" mod='mpcarrierimport'}</h2>
    </div>
    <div class="modal-dialog-body">
        <p>
            
        </p>
    </div>
    <div class="modal-dialog-footer">
        <input type="button" value="{l s='OK' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut();" class='submit-btn modal-button icon-confirm'>
    </div>
  </div>
</div>
    
<div id="dialog-import" class="modal-custom-dialog">
  <!-- Modal content -->
  <div class="modal-dialog-content">
    <div class="modal-dialog-header">
        <span class="close" onclick="$(this).parent().parent().parent().fadeOut();"></span>
      <h2>{l s="Notice" mod='mpcarrierimport'}</h2>
    </div>
    <div class="modal-dialog-body">
        <p>
        {l s='CSV successfully imported. Orders updated.'}
        </p>
    </div>
    <div class="modal-dialog-footer">
        <input type="button" value="{l s='OK' mod='mpcarrierimport'}" onclick="$(this).parent().parent().parent().fadeOut();" class='submit-btn modal-button icon-confirm'>
    </div>
  </div>
</div>
    
<!-- /Block mymodule -->

<script type="text/javascript">
    $(document).ready(function()
    {
        $("#selImportType").on("change",function()
        {
            $.ajax(
            {
                url: "../modules/mpcarrierimport/ajax/getCarrierInputSelect.php",
                type: "post",
                data: 
                {
                    id_type : $("#selImportType").val()
                },
                success: function(msg)
                    {
                        $("#selCarrier").html(msg);
                    }
            });
        });
        
        $("#check_row").on("click",function(){
            var table= $("#tableCSV");
            $('td input:checkbox',table).prop('checked',this.checked);
        });
        
        $("#btnImport").on("click",function()
        {
            //JSON
            //[order_reference]
            //[track_id]
            //[id_order_state]
            
            var table = $("#tableCSV");
            var rows  = $(table).find("tbody").children();
            
            //alert("ROWS: " + rows.length);
            var resultObj = new Array();
            
            $('tbody td input:checkbox',table).each(function(){
                //alert(this.checked);
                var row = $(this).parent().parent();
                //alert("row: " + $(row).html());
                if(this.checked)
                {
                    var cols = $(row).children();
                    //alert("ROW " + i + ", cols: " + cols.length);

                    //get order reference
                    var order_reference = Number($(cols[1]).text());
                    var track_id        = $(cols[2]).text();
                    var id_order_state  = Number($(cols[6]).text());

                    var obj = new Object();
                    obj.order_reference = order_reference;
                    obj.track_id  = track_id;
                    obj.id_order_state = id_order_state;
                    resultObj.push(obj);
                    //alert("order: " + order_reference + ", trackid: " + track_id + ", change to: " + id_order_state); 
                } 
            });
            var jsonString= JSON.stringify(resultObj);
            //alert("JSON: " + jsonString);
            
            $.ajax(
            {
                url: "../modules/mpcarrierimport/ajax/importCSV.php",
                type: "post",
                data: 
                {
                    json : jsonString
                },
                success: function(msg)
                    {
                        $("#dialog-import").show();
                    }
            });
        });
    });
    function add_OK()
    {
        
        if(validation())
        {
            insertRow();
        }
    }
    
    function insertRow()
    {
        if(!validation())
        {
            return false;
        }
        $("#action_row").val("0");
        $("#action_btn").val("insert");
        $("#submit_form").click();
    }
    
    function updateRow(id)
    {
        if(!validation())
        {
            return false;
        }
        $("#action_row").val(id);
        $("#action_btn").val("update");
        $("#submit_form").click();
    }

    function deleteRow(id)
    {
        $("#action_row").val(id);
        $("#action_btn").val("delete");
        $("#submit_form").click();
    }
    
    function showRow(id)
    {
        $("#action_row").val(id);
        $("#action_btn").val("show");
        $("#submit_form").click();
    }
    
    function clearPage()
    {
        $("#action_row").val("");
        $("#action_btn").val("");
        $("#submit_form").click();
    }
    
    function validation()
    {
        var notice = "";
        var carrierName = $("input[name='txtCarrierName']").val();
        var pageLink    = $("input[name='txtPageLink']").val();
        var colOrderReference = $("input[name='txtColOrderReference']").val();
        var colTrackId = $("input[name='txtColTrackId']").val();
        var colDeliveredDate = $("input[name='txtColDeliveredDate']").val();
        
        if(carrierName==="")
        {
            notice += "{l s='Carrier name can\'t be empty!' mod='mpcarrierimport'} <br>";
        }
        if(pageLink==="")
        {
            notice += "{l s='Page link can\'t be empty!' mod='mpcarrierimport'} <br>";
        }
        if(colOrderReference==="")
        {
            notice += "{l s='Order reference column index can\'t be empty!' mod='mpcarrierimport'} <br>";
        }
        if(colTrackId==="")
        {
            notice += "{l s='Track id column index can\'t be empty!' mod='mpcarrierimport'} <br>";
        }
        if(colDeliveredDate==="")
        {
            notice += "{l s='Delivered date column index can\'t be empty!' mod='mpcarrierimport'} <br>";
        }
        if(colOrderReference<0)
        {
            notice += "{l s='Order reference column index must be greater than 0!' mod='mpcarrierimport'} <br>";
        }
        if(colTrackId<0)
        {
            notice += "{l s='Track id column index index must be greater than 0!' mod='mpcarrierimport'} <br>";
        }
        if(colDeliveredDate<0)
        {
            notice += "{l s='Delivered date column index index must be greater than 0!' mod='mpcarrierimport'} <br>";
        }
        
        if(notice==="")
        {
           return true;
        }
        else
        {
            var dialog = $("#dialog-info");
            var span = "<span class=\"icon-warning-sign\" style=\"margin-right: 10px;\"></span>";
            $(dialog).find(".modal-dialog-body p").html(span + notice);
            dialog.show();
            return false;
        }
    }
</script>