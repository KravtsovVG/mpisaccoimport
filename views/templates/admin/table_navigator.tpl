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

<div class="panel" style='text-align: center;'>
    <div style="margin: 0 auto;">
        <div>
            <strong>{l s='Total products'}: <i>{$totalProducts}</i></strong>
        </div>
        <div>
            <a href='javascript:jumpToPage(0);'><i class='icon-step-backward'></i></a>
            <a href='javascript:jumpToPage({if $startPage-1>-1}{$startPage-1}{else}0{/if});'><i class='icon-backward'></i></a>
            <select id='select_page' style='display: inline-block; margin: 5px; width: auto; text-align: center;' onchange='javascript:jumpToPage($(this).val());'>
                {for $i=0 to $pages}
                    <option value='{$i}' {if ($i)==$startPage}selected='selected'{/if} >{$i+1}</option>
                {/for}
            </select>
            <a href='javascript:jumpToPage({if $startPage+1<$pages}{$startPage+1}{else}{$pages}{/if});'><i class='icon-forward'></i></a>
            <a href='javascript:jumpToPage({$pages});'><i class='icon-step-forward'></i></a>
        </div>
    </div>
</div>