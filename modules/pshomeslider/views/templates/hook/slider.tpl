{*
* 2007-2019 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2019 PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*}
{if $slides}

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family={$sliderTitleFont|escape:'htmlall':'UTF-8'}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family={$sliderParagraphFont|escape:'htmlall':'UTF-8'}">

<div id="pshomeslider" class="carousel slide {if $sliderTransition eq 2}pshomeslider-fade{/if}" data-ride="carousel" data-wrap="{$sliderLoop|escape:'htmlall':'UTF-8'}" data-pause="{$sliderPauseHover|escape:'htmlall':'UTF-8'}" data-interval="{$sliderSpeed|escape:'htmlall':'UTF-8'}">
    {* carousel indicators *}
    {if $slides|@count > 1}
        {if $sliderNavigationType eq 2 || $sliderNavigationType eq 3}
        <ol class="carousel-indicators">
            {foreach from=$slides key=key item=slide}
            <li data-target="#pshomeslider" data-slide-to="{$key|escape:'htmlall':'UTF-8'}" class="{if $key eq 0}active{/if}"></li>
            {/foreach}
        </ol>
        {/if}
    {/if}

    {* carousel images *}
    <div class="carousel-inner pshomeslider-height">
        {foreach from=$slides key=key item=slide}
            <div
            class="carousel-item pshomeslider-item {if $key eq 0}active{/if} {if $slide.url != ''}pshomeslider-cursor{/if}"
            style="background-image: url({$baseUrl|escape:'htmlall':'UTF-8'}{$slide.image|escape:'htmlall':'UTF-8'});"
            data-url="{$slide.url}"
            data-open-in-new-tab="{$slide.open_new_tab}"
            onclick="openUrl(this)"
            >
                <div class="pshomeslider-slide-content {$slide.text_position|escape:'htmlall':'UTF-8'}">
                    <div class="pshomeslider-slide-heading {if $slide.text_background}background{/if}">
                        {$slide.title|escape:'htmlall':'UTF-8'}
                    </div>
                    <div class="pshomeslider-slide-title {if $slide.text_background}background{/if}">
                        {$slide.description|unescape: "html" nofilter}
                    </div>
                    {if $slide.call_to_action && $slide.call_to_action_text != ''}
                    <div class="pshomeslider-slide-cta">
                        <a class="btn btn-primary custom-button" href="{$slide.url|escape:'htmlall':'UTF-8'}" {if $slide.open_new_tab}target="_blank"{/if}>{$slide.call_to_action_text|escape:'htmlall':'UTF-8'}</a>
                    </div>
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>

    {* carousel buttons left/right *}
    {if $slides|@count > 1}
        {if $sliderNavigationType eq 1 || $sliderNavigationType eq 3}
        <div class="direction" aria-label="Carousel buttons">
            <a class="left carousel-control" href="#pshomeslider" role="button" data-slide="prev">
                <span class="icon-prev hidden-xs" aria-hidden="true">
                    <i class="material-icons"></i>
                </span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#pshomeslider" role="button" data-slide="next">
                <span class="icon-next" aria-hidden="true">
                    <i class="material-icons"></i>
                </span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        {/if}
    {/if}

</div>

{/if}

<style>
.pshomeslider-slide-heading {
    font-family: {$sliderTitleFont|escape:'htmlall':'UTF-8'};
    font-size: {$sliderTitleSize|escape:'htmlall':'UTF-8'}px !important;
}
.pshomeslider-slide-title {
    font-family: {$sliderParagraphFont|escape:'htmlall':'UTF-8'};
    font-size: {$sliderParagraphSize|escape:'htmlall':'UTF-8'}px !important;
}

#pshomeslider>.direction .material-icons {
    color: {$sliderNavigationColor|escape:'htmlall':'UTF-8'} !important;
}
#pshomeslider>.carousel-indicators li {
    background-color: {$sliderNavigationColor|escape:'htmlall':'UTF-8'} !important;
    border: 1px solid {$sliderNavigationColor|escape:'htmlall':'UTF-8'} !important;
    opacity: .3;
}
#pshomeslider>.carousel-indicators li:hover {
    background-color: {$sliderNavigationColor|escape:'htmlall':'UTF-8'} !important;
    opacity: 1;
}
#pshomeslider>.carousel-indicators .active {
    background-color: {$sliderNavigationColor|escape:'htmlall':'UTF-8'} !important;
    opacity: 1;
}
</style>

{literal}
<script type="text/javascript">
    function openUrl(el) {
        if (el.getAttribute('data-open-in-new-tab') == 1) {
            window.open(el.getAttribute('data-url'))
        } else {
            window.open(el.getAttribute('data-url'), '_self')
        }
    }
</script>
{/literal}
