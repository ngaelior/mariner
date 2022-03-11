{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{include file='_partials/helpers.tpl'}

<!doctype html>
<html lang="{$language.locale}">

<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css"
          integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <script src="//code-eu1.jivosite.com/widget/cw9y7i9du5" async></script>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window, document, 'script', 'dataLayer', 'GTM-5TVHMHW');</script>
    <!-- End Google Tag Manager -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-120811948-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }

      gtag('js', new Date());
      gtag('config', 'UA-120811948-1');
    </script>

</head>

<body id="{$page.page_name}" class="{$page.body_classes|classnames}">

{block name='hook_after_body_opening_tag'}
    {hook h='displayAfterBodyOpeningTag'}
{/block}

<main>
    {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
    {/block}

    <header id="header">
        {block name='header'}
            {include file='_partials/header.tpl'}
        {/block}
    </header>

    <section id="wrapper">
        {block name='notifications'}
            {include file='_partials/notifications.tpl'}
        {/block}

        {hook h="displayWrapperTop"}
        <div class="container">
            {block name='breadcrumb'}
                {include file='_partials/breadcrumb.tpl'}
            {/block}

            {block name="left_column"}
                <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
                    {if $page.page_name == 'product'}
                        {hook h='displayLeftColumnProduct'}
                    {else}
                        {hook h="displayLeftColumn"}
                    {/if}
                </div>
            {/block}

            {block name="content_wrapper"}
                <div id="content-wrapper" class="js-content-wrapper left-column right-column col-sm-4 col-md-6">
                    {hook h="displayContentWrapperTop"}
                    {block name="content"}
                        <p>Hello world! This is HTML5 Boilerplate.</p>
                    {/block}
                    {hook h="displayContentWrapperBottom"}
                </div>
            {/block}

            {block name="right_column"}
                <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
                    {if $page.page_name == 'product'}
                        {hook h='displayRightColumnProduct'}
                    {else}
                        {hook h="displayRightColumn"}
                    {/if}
                </div>
            {/block}
            {if $page.page_name == 'index'}

                {hook h="displayCustomHtml1"}
                {hook h="displayCustomHtml2"}
                <div class="white-arrow">
                {hook h="displayCustomHtml3"}
                </div>{/if}
        </div>
        {hook h="displayWrapperBottom"}
    </section>


    <footer id="footer" class="js-footer">
        {block name="footer"}
            {include file="_partials/footer.tpl"}
        {/block}
    </footer>

</main>

{block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{/block}

{block name='hook_before_body_closing_tag'}
    {hook h='displayBeforeBodyClosingTag'}
{/block}
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script type="text/javascript">
  window.omnisend = window.omnisend || [];
  omnisend.push(["accountID", "6218a97993e1ed4af96444aa"]);
  omnisend.push(["track", "$pageViewed"]);
  !function () {
    var e = document.createElement("script");
    e.type = "text/javascript", e.async = !0, e.src = "https://omnisnippet1.com/inshop/launcher-v2.js";
    var t = document.getElementsByTagName("script")[0];
    t.parentNode.insertBefore(e, t)
  }();
</script>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5TVHMHW"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

</body>

</html>
