{*
   * 2008 - 2020 (c) Prestablog
   *
   * MODULE PrestaBlog
   *
   * @author    Prestablog
   * @copyright Copyright (c) permanent, Prestablog
   * @license   Commercial
     *}
{if isset($news)}
  <div id="prestablogfont" itemprop="articleBody">{PrestaBlogContent return=$news->content}</div>
{/if}
