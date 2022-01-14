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
            {if $smarty.foreach.linkLoop.index === 1}
                <div class="col-md-3 wrapper">
                    <p class="h3 hidden-sm-down">Bulletin de mariner</p>
                    <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_newsletter"
                         data-toggle="collapse">
                        <span class="h3">Bulletin de mariner</span>
                        <span class="float-xs-right">
          <span class="navbar-toggler collapse-icons">
            <i class="material-icons add">&#xE313;</i>
            <i class="material-icons remove">&#xE316;</i>
          </span>
        </span>
                    </div>
                    <div id="footer_sub_menu_newsletter" class="collapse">
                        <div class="block_newsletter row" id="blockEmailSubscription_{$hookName}">
                            <div>
                                <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <button
                                                    class="newsletter-submit "
                                                    name="submitNewsletter"
                                                    type="submit"
                                                    value="envoyer"
                                            ><i class="far fa-envelope"></i>
                                            </button>
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
                                        <div>
                                            {hook h='displayNewsletterRegistration'}
                                            {if isset($id_module)}
                                                {hook h='displayGDPRConsent' id_module=$id_module}
                                            {/if}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="social-networks row">
                            <ul class="d-flex col-xs-12 ">
                                <li class="social-link">
                                    <a href="https://www.facebook.com/marinerunderwear/" target="_blank">
                                        <svg width="32" height="32" viewBox="0 0 32 32"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <title>Facebook</title>
                                            <path d="M30.109 0H1.759C.787 0 0 .787 0 1.759v28.35c0 .971.787 1.759 1.759 1.759h15.263V19.527h-4.153v-4.81h4.153v-3.546c0-4.117 2.514-6.358 6.185-6.358 1.76 0 3.271.131 3.712.19v4.301l-2.547.001c-1.997 0-2.384.95-2.384 2.342v3.07h4.763l-.62 4.81h-4.143v12.34h8.121c.971 0 1.759-.787 1.759-1.758V1.76C31.868.787 31.08 0 30.109 0"
                                                  fill="#000" fill-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </li>


                                <li class="social-link">
                                    <a href="https://twitter.com/marinerunderwr" target="_blank">
                                        <svg width="32" height="32" viewBox="0 0 32 32"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <title>Twitter</title>
                                            <path d="M32 7.079a13.127 13.127 0 0 1-3.77 1.033 6.585 6.585 0 0 0 2.886-3.632 13.148 13.148 0 0 1-4.169 1.593A6.557 6.557 0 0 0 22.155 4a6.565 6.565 0 0 0-6.565 6.565c0 .515.058 1.016.17 1.496a18.639 18.639 0 0 1-13.532-6.86 6.534 6.534 0 0 0-.89 3.301 6.562 6.562 0 0 0 2.922 5.465 6.539 6.539 0 0 1-2.974-.821v.082a6.569 6.569 0 0 0 5.266 6.438 6.574 6.574 0 0 1-2.965.112 6.572 6.572 0 0 0 6.133 4.56 13.173 13.173 0 0 1-8.154 2.81c-.53 0-1.052-.031-1.566-.092a18.583 18.583 0 0 0 10.064 2.95c12.076 0 18.679-10.004 18.679-18.68 0-.284-.006-.567-.019-.849A13.344 13.344 0 0 0 32 7.079"
                                                  fill="#000" fill-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </li>


                                <li class="social-link">
                                    <a href="https://www.instagram.com/marinerunderwear" target="_blank">
                                        <svg width="32" height="32" viewBox="0 0 32 32"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <title>Instagram</title>
                                            <g fill="#0A0A08" fill-rule="evenodd">
                                                <path d="M16 2.887c4.27 0 4.777.016 6.463.093 1.56.071 2.407.332 2.97.551.747.29 1.28.637 1.84 1.196.56.56.906 1.093 1.196 1.84.219.563.48 1.41.55 2.97.078 1.686.094 2.192.094 6.463 0 4.27-.016 4.777-.093 6.463-.071 1.56-.332 2.407-.551 2.97a4.955 4.955 0 0 1-1.196 1.84c-.56.56-1.093.906-1.84 1.196-.563.219-1.41.48-2.97.55-1.686.078-2.192.094-6.463.094s-4.777-.016-6.463-.093c-1.56-.071-2.407-.332-2.97-.551a4.955 4.955 0 0 1-1.84-1.196 4.955 4.955 0 0 1-1.196-1.84c-.219-.563-.48-1.41-.55-2.97-.078-1.686-.094-2.192-.094-6.463 0-4.27.016-4.777.093-6.463.071-1.56.332-2.407.551-2.97.29-.747.637-1.28 1.196-1.84a4.956 4.956 0 0 1 1.84-1.196c.563-.219 1.41-.48 2.97-.55 1.686-.078 2.192-.094 6.463-.094m0-2.882c-4.344 0-4.889.018-6.595.096C7.703.18 6.54.45 5.523.845A7.84 7.84 0 0 0 2.69 2.69 7.84 7.84 0 0 0 .845 5.523C.449 6.54.179 7.703.1 9.405.023 11.111.005 11.656.005 16c0 4.344.018 4.889.096 6.595.078 1.702.348 2.865.744 3.882A7.84 7.84 0 0 0 2.69 29.31a7.84 7.84 0 0 0 2.833 1.845c1.017.396 2.18.666 3.882.744 1.706.078 2.251.096 6.595.096 4.344 0 4.889-.018 6.595-.096 1.702-.078 2.865-.348 3.882-.744a7.84 7.84 0 0 0 2.833-1.845 7.84 7.84 0 0 0 1.845-2.833c.396-1.017.666-2.18.744-3.882.078-1.706.096-2.251.096-6.595 0-4.344-.018-4.889-.096-6.595-.078-1.702-.348-2.865-.744-3.882A7.84 7.84 0 0 0 29.31 2.69 7.84 7.84 0 0 0 26.477.845C25.46.449 24.297.179 22.595.1 20.889.023 20.344.005 16 .005"></path>
                                                <path d="M16 7.786a8.214 8.214 0 1 0 0 16.428 8.214 8.214 0 0 0 0-16.428zm0 13.546a5.332 5.332 0 1 1 0-10.664 5.332 5.332 0 0 1 0 10.664zM26.458 7.462a1.92 1.92 0 1 1-3.84 0 1.92 1.92 0 0 1 3.84 0"></path>
                                            </g>
                                        </svg>
                                    </a>
                                </li>


                                <li class="social-link">
                                    <a href="mailto:contact@mariner-underwear.com" target="_blank">
                                        <svg width="32" height="32" viewBox="0 0 32 32"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <title>Mail</title>
                                            <path d="M28.014 10.534V7.767l-12.03 6.884-12.03-6.884v2.767l12.03 6.82 12.03-6.82zm0-5.534c.797 0 1.49.279 2.076.836.586.557.879 1.2.879 1.93v16.469c0 .729-.293 1.372-.88 1.93a2.913 2.913 0 0 1-2.075.835H3.955c-.797 0-1.49-.279-2.076-.836-.586-.557-.879-1.2-.879-1.93V7.766c0-.729.293-1.372.88-1.93A2.913 2.913 0 0 1 3.954 5h24.059z"
                                                  fill="#000" fill-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </li>


                            </ul>
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
