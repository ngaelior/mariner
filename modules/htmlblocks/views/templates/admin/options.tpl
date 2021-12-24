{* HTML Blocks Prestashop module
 * Copyright 2016, Prestaddons
 * Author: Prestaddons
 * Website: http://www.prestaddons.fr
 *}
 
<option value="CAT{$id_category|intval}">{$spacer|@print_r|rtrim:'1'|escape:'htmlall':'UTF-8'}{$category_name|escape:'htmlall':'UTF-8'} ({$shop->name|escape:'htmlall':'UTF-8'})</option>