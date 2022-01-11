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

<section class="categorysectionsmain-products featured-products clearfix">
    {foreach from=$section_product item="section_prod"}
        {if !empty($section_prod['title'])}
            {if $section_prod['id_category'] != '0'}
                <a class="link_categorysectionsmain item float-xs-left float-md-right h4"
                href="{url entity=category id=$section_prod['id_category']}">
            {/if}
            <div class="content">
                <h2 class="h2 title_categorysectionsmain c-white">    {$section_prod['title']|escape:'htmlall':'UTF-8'}</h2>
            </div>
            {if $section_prod['id_category'] != '0'}
                </a>
            {/if}
        {/if}
    {/foreach}
    <div class="row discover-section" style="width: 100%">
        <div class="col-xs-12 col-lg-6 bg-primary bg-light-dark ">
            <h2 class="c-yellow italic">DECOUVRIR LA NOUVELLE COLLECTION</h2>
            <p class="c-white">Une nouvelle gamme de shortys, de pyjamas et de sous-vêtements</p>
            <a style="display: block;width: fit-content" href="#" class="mariner-button">Je découvre la nouvelle
                collection</a>
        </div>
        <div class="col-xs-12 col-lg-6 image-section">
            <img width="100%" src="/img/c/Rectangle%20226.jpg">
        </div>
    </div>
</section>
