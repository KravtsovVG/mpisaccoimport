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
<form method="POST" class='bootstrap defaultForm form-horizontal'>
<div id="form_carrier_import" name="form_carrier_import" class="panel">
    <div class="block_content">
            <fieldset>
                <legend>{l s="CARRIER IMPORT" mod='mpcarrierimport'}</legend>
                <span class="span_container">
                    <label>{l s='ID' mod='mpcarrierimport'}</label>
                    <input type="text" class="field width-s text-align-r" name="txtIdCarrierImport" id="txtIdCarrierImport" value="{$txtIdCarrierImport}" readonly="readonly">
                </span>
                <span class="span_container">
                    <label>{l s='Carrier Name' mod='mpcarrierimport'}</label>
                    <input type="text" class="field width-xl" name="txtCarrierName" value="{$txtCarrierName}">
                </span>
                <span class="span_container">
                    <label>{l s="Select import type" mod='mpcarrierimport'}</label>
                    <select id="selImportType" name="selImportType" class="field width-m">
                        {foreach $importTypes as $option}
                            <option value="{$option.id}" {if $selectedImportType eq $option.id}selected="selected"{/if}>{$option.value}</option>
                        {/foreach}
                    </select>
                </span>
                <span class="span_container">
                    <label>{l s="Select order state" mod='mpcarrierimport'}</label>
                    <select id="optStatus" name="selOrderState" class="field width-xl">
                        {foreach $orderStates as $option}
                            <option value="{$option.id}" {if $selectedState eq $option.id}selected="selected"{/if}>{$option.value}</option>
                        {/foreach}
                    </select>
                </span>
                <span class="span_container">
                    <label>{l s="Page link" mod='mpcarrierimport'}</label>
                    <input type="url" class="field width-xl text-align-l" name="txtPageLink" value="{$txtPageLink}">
                </span>
                <span class="span_container">
                    <label>{l s="Order reference" mod='mpcarrierimport'}</label>
                    <input type="text" class="field width-l text-align-l" name="txtColOrderReference" value="{$txtColOrderReference}">
                    <label class="info-field">c[{l s='col number'}], d[{l s='digit numbers'}]</label>
                </span>
                <span class="span_container">
                    <label>{l s="Track ID" mod='mpcarrierimport'}</label>
                    <input type="text" class="field width-l text-align-l" name="txtColTrackId" value="{$txtColTrackId}">
                    <label class="info-field">c[{l s='col number'}], d[{l s='digit numbers'}]</label>
                </span>
                <span class="span_container">
                    <label>{l s="Delivered State" mod='mpcarrierimport'}</label>
                    <input type="text" class="field width-l text-align-l" name="txtColDeliveredDate" value="{$txtColDeliveredDate}">
                    <label class="info-field">c[{l s='col number'}], d[{l s='digit numbers'}]</label>
                </span>
                <span class="span_container">
                    <label>{l s='Column separator' mod='mpcarrierimport'}</label>
                    <input type="text" class="field width-l text-align-l" name="txtColSeparator" value="{$txtColSeparator}">
                </span>
                <input type="button" name="submit_clear" id="submit_add" class="submit-btn icon-clear" value="{l s='CLEAR' mod='mpcarrierimport'}" onclick="clearPage();;">
                <input type="button" name="submit_add" id="submit_add" class="submit-btn icon-add" value="{l s='ADD' mod='mpcarrierimport'}" onclick="$('#dialog-add').show();">
                <input type="button" name="submit_update" id="submit_update" class="submit-btn icon-floppy" value="{l s='UPDATE' mod='mpcarrierimport'}" onclick="$('#dialog-update').show();">
                <input type="button" name="submit_delete" id="submit_update" class="submit-btn icon-delete" value="{l s='DELETE' mod='mpcarrierimport'}" onclick="$('#dialog-delete').show();">
                <input type="submit" id="submit_form" style="display:none;">
            </fieldset>
    </div>
</div>
<div class="bootstrap panel">
    <input type="submit" name="submit_csv" class="submit-btn icon-csv" value="CSV">
    <input type="submit" name="submit_xls" class="submit-btn icon-xls" value="EXCEL">
    <input type="hidden" name="action_row" id="action_row" value="{$actionRow}">
    <input type="hidden" name="action_btn" id="action_btn" value="{$actionBtn}">
    <table class="table-bordered table-data">
        <thead>
            <tr>
                <th>{l s='ID' mod='mpcarrierimport'}</th>
                <th>{l s='Carrier Name' mod='mpcarrierimport'}</th>
                <th class="hidden">{l s='id import type' mod='mpcarrierimport'}</th>
                <th>{l s='Import Name' mod='mpcarrierimport'}</th>
                <th>{l s='Page link' mod='mpcarrierimport'}</th>
                <th class="hidden">{l s='Order ref' mod='mpcarrierimport'}</th>
                <th class="hidden">{l s='Track id' mod='mpcarrierimport'}</th>
                <th class="hidden">{l s='Delivered date' mod='mpcarrierimport'}</th>
                <th class="hidden">{l s='id order state' mod='mpcarrierimport'}</th>
                <th>{l s='Order state' mod='mpcarrierimport'}</th>
                <th>{l s='Actions' mod='mpcarrierimport'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $tableRows as $row}
                <tr>
                    <td>{$row.id_carrier_import}</td>
                    <td>{$row.carrier_name}</td>
                    <td class="hidden">{$row.id_carrier_import_type}</td>
                    <td>{$row.import_name}</td>
                    <td>{$row.web_link}</td>
                    <td class="hidden">{$row.col_order_reference}</td>
                    <td class="hidden">{$row.col_tracking_id}</td>
                    <td class="hidden">{$row.col_delivered_date}</td>
                    <td class="hidden">{$row.id_order_state}</td>
                    <td>{$row.order_state_name}</td>
                    <td>{$row.actions}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>            
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
    
<!-- /Block mymodule -->

<script type="text/javascript">
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