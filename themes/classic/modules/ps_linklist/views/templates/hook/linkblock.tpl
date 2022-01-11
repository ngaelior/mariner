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
<div class="col-md-12 links">
    <div class="row">
        {foreach item=linkBlock from=$linkBlocks name=linkLoop }
            {if $smarty.foreach.linkLoop.index}
                <div class="col-md-3 wrapper">
                    <p class="h3 hidden-sm-down">Bulletin de mariner</p>
                    <div class="block_newsletter" id="blockEmailSubscription_{$hookName}">
                        <div class="col-md-7 col-xs-12">
                            <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <input
                                                class="btn btn-primary float-xs-right hidden-xs-down"
                                                name="submitNewsletter"
                                                type="submit"
                                                value="{l s='Subscribe' d='Shop.Theme.Actions'}"
                                        >
                                        <input
                                                class="btn btn-primary float-xs-right hidden-sm-up"
                                                name="submitNewsletter"
                                                type="submit"
                                                value="{l s='OK' d='Shop.Theme.Actions'}"
                                        >
                                        <div class="input-wrapper">
                                            <input
                                                    name="email"
                                                    type="email"
                                                    value=""
                                                    placeholder="{l s='Your email address' d='Shop.Forms.Labels'}"
                                                    aria-labelledby="block-newsletter-label"
                                                    required
                                            >
                                        </div>
                                        <input type="hidden" name="blockHookName" value="{$hookName}"/>
                                        <input type="hidden" name="action" value="0">
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="col-xs-12">
                                        {hook h='displayNewsletterRegistration'}
                                        {if isset($id_module)}
                                            {hook h='displayGDPRConsent' id_module=$id_module}
                                        {/if}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            {/if}
            <div class="col-md-3 wrapper">
                <p class="h3 hidden-sm-down">{$linkBlock.title}</p>
                <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_{$linkBlock.id}"
                     data-toggle="collapse">
                    <span class="h3">{$linkBlock.title}</span>
                    <span class="float-xs-right">
          <span class="navbar-toggler collapse-icons">
            <i class="material-icons add">&#xE313;</i>
            <i class="material-icons remove">&#xE316;</i>
          </span>
        </span>
                </div>
                <ul id="footer_sub_menu_{$linkBlock.id}" class="collapse">
                    {foreach $linkBlock.links as $link}
                        <li>
                            <a
                                    id="{$link.id}-{$linkBlock.id}"
                                    class="{$link.class}"
                                    href="{$link.url}"
                                    title="{$link.description}"
                                    {if !empty($link.target)} target="{$link.target}" {/if}
                            >
                                {$link.title}
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/foreach}
    </div>
</div>
