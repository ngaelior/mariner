{*
* 2007-2019 PrestaShop
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
* @author    Pro Business <tim9898@ya.ru>
* @copyright 2007-2019 Pro Business
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

    {foreach from=$section_product item="section_prod"}
    {if !empty($section_prod['title'])}
<section class="categorysectionsmain-products featured-products   clearfix">
  
  <div class="products">
    <div class="col-md-9">
    	  <h2 class="h2 float-md-left title_categorysectionsmain">	{$section_prod['title']|escape:'htmlall':'UTF-8'}</h2>
    </div>
    
    <div class="col-md-3">
      {if $section_prod['id_category'] != '0'}
        <a class="link_categorysectionsmain float-xs-left float-md-right h4" href="{url entity=category id=$section_prod['id_category']}">
         {l s='All products' mod='categorysectionsmain'} <i class="material-icons">&#xE315;</i>
        </a>
        {/if}   
    </div>
    
    
	     {foreach from=$section_prod['products'] item="product"}
	         {include file="catalog/_partials/miniatures/product.tpl" product=$product}
	     {/foreach}
    
         </div>
 
</section>
{/if}
    {/foreach}
