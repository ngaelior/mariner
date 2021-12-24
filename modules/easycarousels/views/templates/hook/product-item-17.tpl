{**
*
* @author    Amazzing <mail@amazzing.ru>
* @copyright 2007-2019 Amazzing
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
* NOTE: this file is extendable. You can override only selected blocks in your template.
* Path for extending: 'modules/easycarousels/views/templates/hook/product-item-17.tpl'
*
**}
{block name='product_item'}
<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
<div class="thumbnail-container">

    {block name='product_image'}
    {if $settings.image_type != '--'}
        {$img_type = $settings.image_type}
        <a href="{$product.url}" class="thumbnail product-thumbnail" itemprop="url">
            <img src="{$product.cover.bySize.$img_type.url}" alt="{$product.cover.legend}"{if !empty($product.second_img_src)} class="primary-image"{/if}{if !empty($image_sizes[$img_type])} width="{$image_sizes[$img_type]['width']}" height="{$image_sizes[$img_type]['height']}"{/if}>
            {if !empty($product.second_img_src)}
                <img src="{$product.second_img_src}" class="img-responsive secondary-image" itemprop="image"{if !empty($image_sizes[$img_type])} width="{$image_sizes[$img_type]['width']}" height="{$image_sizes[$img_type]['height']}"{/if}>
            {/if}
        </a>
    {/if}
    {/block}

    {block name='product_quick_view'}
    {if $settings.quick_view}
        <div class="highlighted-informations">
            <a href="#" class="quick-view" data-link-action="quickview">{l s='Quick view' d='Shop.Theme.Actions'}</a>
        </div>
    {/if}
    {/block}

    {block name='product_variants'}{* can be optionally filled in extended file *}{/block}

    {block name='product_informations'}
    <div class="product-description">

        {block name='product_title'}
        {if !empty($settings.title)}
            <h5 class="product-title" itemprop="name">
                <a href="{$product.url}"{if !empty($settings.title_one_line)} class="nowrap"{/if}>
                    {$product.name|truncate:$settings.title:'...'}
                </a>
            </h5>
        {/if}
        {/block}

        {block name='product_other_fields'}
        {if !empty($settings.reference)}
            <div class="prop-line product-reference"><span class="nowrap">{$product.reference}</span></div>
        {/if}
        {if !empty($settings.product_cat) && !empty($product.cat_url)}
            <div class="prop-line product-category">
                <a href="{$product.cat_url}" class="cat-name nowrap">{$product.cat_name|truncate:45:'...'}</a>
            </div>
        {/if}
        {if !empty($settings.product_man) && $product.id_manufacturer && $product.man_name && !empty($product.man_url)}
            <div class="prop-line product-manufacturer">
                <a href="{$product.man_url}" class="man-name nowrap">
                {if !empty($product.man_img_src)}
                    {$img_type = $settings.product_man}
                    <img src="{$product.man_img_src}" class="product-manufacturer-img"{if !empty($image_sizes[$img_type])} width="{$image_sizes[$img_type]['width']}" height="{$image_sizes[$img_type]['height']}"{/if}>
                {else}
                    {$product.man_name|truncate:45:'...'}
                {/if}
                </a>
            </div>
        {/if}
        {if !empty($settings.description)}
            <div class="prop-line product-description-short" itemprop="description">
                {$product.description_short|strip_tags:'UTF-8'|truncate:$settings.description:'...'}
            </div>
        {/if}
        {if !empty($settings.stock) && $product.availability_message}
            <div class="prop-line product-availability {$product.availability}">
                <span class="inline-block nowrap">{$product.availability_message}</span>
            </div>
        {/if}
        {/block}

        {block name='product_price'}
        {if $settings.price && $product.show_price}
            <div class="product-price-and-shipping" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                {if $product.has_discount}
                    {if $settings.displayProductPriceBlock}
                        {hook h='displayProductPriceBlock' product=$product type="old_price"}
                    {/if}
                    <span class="regular-price">{$product.regular_price}</span>
                {/if}
                {if $settings.displayProductPriceBlock}
                    {hook h='displayProductPriceBlock' product=$product type="before_price"}
                {/if}
                <span class="price">{$product.price}</span>
                <meta itemprop="price" content="{$product.price_amount}" />
                <meta itemprop="priceCurrency" content="{$currency_iso_code}" />
                {if !empty($settings.stock) && $product.availability_message}
                    <meta itemprop="availability" content="{$product.availability_message}" />
                {/if}
                {if $settings.displayProductPriceBlock}
                    {hook h='displayProductPriceBlock' product=$product type='unit_price'}
                    {hook h='displayProductPriceBlock' product=$product type='weight'}
                {/if}
            </div>
        {/if}
        {/block}

        {block name='product_buttons'}
        {if $settings.add_to_cart || $settings.view_more || $settings.displayProductListFunctionalButtons}
            <form type="post" action="{$urls.pages.cart}" class="product-item-buttons">
                {if $settings.add_to_cart}
                    <input type="hidden" name="token" value="{$static_token}">
                    <input type="hidden" name="id_product" value="{$product.id_product}">
                    <input type="hidden" name="qty" value="{$product.minimal_quantity}">
                    <button data-button-action="add-to-cart" class="btn btn-primary">{l s='Add to cart' d='Shop.Theme.Actions'}</button>
                {/if}
                {if $settings.view_more}
                    <a href="{$product.url}" class="btn btn-tertiary-outline">{l s='More' d='Shop.Theme.Actions'}</a>
                {/if}
                {if !empty($settings.displayProductListFunctionalButtons)}
                    {hook h='displayProductListFunctionalButtons' product=$product}
                {/if}
            </form>
        {/if}
        {/block}

    </div>
    {/block}

    {block name='hook_reviews'}
    {if !empty($settings.displayProductListReviews)}
        {hook h='displayProductListReviews' product=$product hide_thumbnails=empty($settings.thumbnails)|intval}
    {/if}
    {/block}

    {block name='product_stickers'}
    {if $settings.stickers }
        <ul class="product-flags">
        {if $product.show_price && $product.has_discount && $product.discount_type === 'percentage'}
            <li class="discount-percentage">{$product.discount_percentage}</li>
        {/if}
        {foreach $product.flags as $flag}
            <li class="{$flag.type}">{$flag.label}</li>
        {/foreach}
        </ul>
    {/if}
    {/block}

</div>
</article>
{/block}

{* since 2.6.1 *}
