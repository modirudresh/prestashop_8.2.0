{**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}
    <div class="block-category card card-block hidden-sm-down">
      <h1 class="h1">{$category.name|default:''}</h1>
      {if $category.image.large.url|default:false}
        <div class="category-cover">
          <img src="{$category.image.large.url|default:''}" alt="{$category.image.legend|default:''}">
        </div>
      {/if}
      {if $category.description|default:false}
        <div id="category-description" class="text-muted">{$category.description|default:'' nofilter}</div>
      {/if}
    </div>
    <div class="text-xs-center hidden-md-up">
      <h1 class="h1">{$category.name|default:''}</h1>
    </div>
{/block}
