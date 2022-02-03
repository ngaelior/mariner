{*
   * 2008 - 2020 (c) Prestablog
   *
   * MODULE PrestaBlog
   *
   * @author    Prestablog
   * @copyright Copyright (c) permanent, Prestablog
   * @license   Commercial
     *}
   <!-- Module Presta Blog -->
   <div id="prestablogauthor">
    <img src="{$prestablog_author_upimg|escape:'html':'UTF-8'}{$author_id|intval}.jpg" class="author" alt="{$firstname|escape:'htmlall':'UTF-8'}"/>

    <h1 id="prestablog_pseudo" data-referenceid="{$author_id|intval}">{$pseudo|escape:'htmlall':'UTF-8'}</h1>
<div id="prestablogfont">
  <p itemprop="text">{PrestaBlogContent return=$biography}</p>
</div>

</div>

<div id="blog_article_linked">
    <h2>{l s='From the same author' mod='prestablog'}</h2>
    {if (sizeof($articles_author))}
    <ul id="blog_list_1-7">
        {foreach from=$articles_author item=article key=key name=current}
        {if $article.title}
        <li class="blog-grid">
            <div class="block_cont">
                <div class="block_top">
                    {if $article.image_presente}
                    <a href="{$article.link|escape:'htmlall':'UTF-8'}" title="{$article.title|escape:'htmlall':'UTF-8'}">
                        <img src="{$prestablog_theme_upimg|escape:'html':'UTF-8'}thumb_{$key|intval}.jpg?{$md5pic|escape:'htmlall':'UTF-8'}" alt="{$article.title|escape:'htmlall':'UTF-8'}" title="{$article.title|escape:'htmlall':'UTF-8'}"/>
                        {/if}
                    </a>
                </div>
                <div class="block_bas">

                    <h3>
                        {if isset($article.link)}<a href="{$article.link|escape:'htmlall':'UTF-8'}" title="{$article.title|escape:'htmlall':'UTF-8'}">{/if}{$article.title|escape:'htmlall':'UTF-8'}{if isset($article.link)}</a>{/if}
                        <br /><span class="date_blog-cat">{l s='Published :' mod='prestablog'}
                            {dateFormat date=$article.date full=0}

                        </span>
                    </h3>
                </div>
                {if isset($article.link)}
                <div class="prestablog_more">
                    <a href="{$article.link|escape:'htmlall':'UTF-8'}" class="blog_link"><i class="material-icons">search</i> {l s='Read more' mod='prestablog'}</a>
                </div>{/if}
            </div>
        </li>
        {/if}
        {/foreach}
    </ul>
    {/if}
</div>
