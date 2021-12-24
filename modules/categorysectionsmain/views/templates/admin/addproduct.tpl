{**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}


<div class="panel col-lg-12" >
 <p class="center" >
                 {l s='Search' mod='categorysectionsmain'}:
               <input type="text" id="product_categorysectionsmain">
            </p>        

    

<div class="row form-wrapper">
<div class="col-lg-6">
     {l s='Search result' mod='categorysectionsmain'}:
    <div class="blocmodule ">
           
            <table cellspacing="0" cellpadding="0" class="table" style="width:100%">
                <thead>
                    <tr>
                  
                        <th class="center" style="width:30px;">ID</th>

                        <th>{l s='Name' mod='categorysectionsmain'}</th>
                        <th>{l s='Add' mod='categorysectionsmain'}</th>
                    </tr>
                </thead>
                <tbody id="productLinkResult">
                                   
            
                              

                       
</tbody>
            </table>
<div class="cssload-container" style="height: 65px;">
    <div class="cssload-whirlpool"></div>
</div>
        </div>

</div>









<div class="col-lg-6">
{l s='Add product' mod='categorysectionsmain'}:
<table cellspacing="0" cellpadding="0" class="table" style="width:100%">
                <thead>
                    <tr>
                        <th class="center" style="width:30px;">ID</th>
                        <th >{l s='Name' mod='categorysectionsmain'}</th>
                        <th class="center" style="width:40px;">{l s='Delete' mod='categorysectionsmain'}</th>
                    </tr>
                </thead>
                <tbody id="productLinked">
{if $products_s}                  
{foreach from=$products_s item=products}

<tr class="noInlisted_5" id="div-{$products['id']}"> 
    <td class="center">{$products['id']|escape:'html':'UTF-8'}   
        <input type="hidden" name="products[]" id="input-{$products['id']}" value="{$products['id']}">
    </td>
    <td class="">{$products['name']|escape:'html':'UTF-8'}</td>
    <td class="center">
        <button type="button" onclick="removeInput('{$products['id']|escape:'html':'UTF-8'}')" class="btn btn-default"><i class="material-icons">close</i></button>
       
    </td>
</tr>
                       
{/foreach}
{/if}
</tbody>
            </table>

</div>

</div>


    </div>
                    
                    
    

<script type="text/javascript">

$(document).ready(function(){
var timer;

 $( "#product_categorysectionsmain" ).keyup(function() {
        var thiss = $(this);
        $('.cssload-container').show();
        $('#productLinkResult').html(' ');
        clearTimeout(timer);
        timer = setTimeout(function() {
            
            var search_key = thiss.val();
            $.ajax({
                type: 'GET',
                url: baseAdminDir  + currentIndex,
                headers: { "cache-control": "no-cache" },
                async: true,
                data: 'action=searchsectionsmain&name='+ search_key+'&ajax=1&token={$token|escape:'html':'UTF-8'}',
                dataType: 'json',
                success: function(data) {
                    $('.cssload-container').hide();
                if (data) {
                    for (var index in data) {
                        
                        $('#productLinkResult').append('<tr><td class="center" data-id='+data[index].id_product+'>'+data[index].id_product+'</td><td>'+data[index].name+'</td><td><button type="button" onclick="addInput(\'' + data[index].id_product + '\' ,\'' + data[index].name + '\')" class="btn btn-default but_prod_f"><i class="material-icons">chevron_right</i></button></td></tr>');
                    }
                } else {
                    $('#productLinkResult').html('<tr><td class="center"></td><td class="center">Not result</td><td class="center"></td></tr>');
                }
                    
                    
                   
                }
            }) .done(function( msg ) {
                
            });
        }, 1000);


    });
    });
function addInput(id,namea) {
 if (!document.getElementById("div-" + id )) {
 $("#productLinked").append('<tr class="noInlisted_5" id="div-' + id + '"> <td class="center">'+id+'<input name="products[]" type="hidden" id="input-' + id + '" value="' + id + '"></td><td class="">'+namea+'</td><td class="center"><button type="button" onclick="removeInput(\'' + id + '\')" class="btn btn-default"><i class="material-icons">close</i></button></td></div></tr>');
}
 

}

function removeInput(id) {
$("#div-" + id).remove();

}
</script>
<style type="text/css">

.cssload-container{
    position:relative;
}
    
.cssload-whirlpool,
.cssload-whirlpool::before,
.cssload-whirlpool::after {
    position: absolute;
    top: 50%;
    left: 50%;
    border: 1px solid rgb(204,204,204);
    border-left-color: rgb(0,0,0);
    border-radius: 649px;
        -o-border-radius: 649px;
        -ms-border-radius: 649px;
        -webkit-border-radius: 649px;
        -moz-border-radius: 649px;
}

.cssload-whirlpool {
    margin: -16px 0 0 -16px;
    height: 32px;
    width: 32px;
    animation: cssload-rotate 1150ms linear infinite;
        -o-animation: cssload-rotate 1150ms linear infinite;
        -ms-animation: cssload-rotate 1150ms linear infinite;
        -webkit-animation: cssload-rotate 1150ms linear infinite;
        -moz-animation: cssload-rotate 1150ms linear infinite;
}

.cssload-whirlpool::before {
    content: "";
    margin: -15px 0 0 -15px;
    height: 29px;
    width: 29px;
    animation: cssload-rotate 1150ms linear infinite;
        -o-animation: cssload-rotate 1150ms linear infinite;
        -ms-animation: cssload-rotate 1150ms linear infinite;
        -webkit-animation: cssload-rotate 1150ms linear infinite;
        -moz-animation: cssload-rotate 1150ms linear infinite;
}

.cssload-whirlpool::after {
    content: "";
    margin: -19px 0 0 -19px;
    height: 36px;
    width: 36px;
    animation: cssload-rotate 2300ms linear infinite;
        -o-animation: cssload-rotate 2300ms linear infinite;
        -ms-animation: cssload-rotate 2300ms linear infinite;
        -webkit-animation: cssload-rotate 2300ms linear infinite;
        -moz-animation: cssload-rotate 2300ms linear infinite;
}



@keyframes cssload-rotate {
    100% {
        transform: rotate(360deg);
    }
}

@-o-keyframes cssload-rotate {
    100% {
        -o-transform: rotate(360deg);
    }
}

@-ms-keyframes cssload-rotate {
    100% {
        -ms-transform: rotate(360deg);
    }
}

@-webkit-keyframes cssload-rotate {
    100% {
        -webkit-transform: rotate(360deg);
    }
}

@-moz-keyframes cssload-rotate {
    100% {
        -moz-transform: rotate(360deg);
    }
}
.cssload-container{
    display: none;
}
</style>
