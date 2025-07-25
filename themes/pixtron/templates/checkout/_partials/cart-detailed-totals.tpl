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
{block name='cart_detailed_totals'}
<div class="cart-detailed-totals">

  <div class="card-block">
    {foreach from=$cart.subtotals|default:[] item="subtotal"}
      {if $subtotal.value|default:false && $subtotal.type|default:'' !== 'tax'}
        <div class="cart-summary-line" id="cart-subtotal-{$subtotal.type|default:''}">
          <span class="label{if 'products' === $subtotal.type|default:''} js-subtotal{/if}">
            {if 'products' == $subtotal.type|default:''}
              {$cart.summary_string|default:''}
            {else}
              {$subtotal.label|default:''}
            {/if}
          </span>
          <span class="value">{$subtotal.value|default:''}</span>
          {if $subtotal.type|default:'' === 'shipping'}
              <div><small class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small></div>
          {/if}
        </div>
      {/if}
    {/foreach}
  </div>

  {block name='cart_voucher'}
    {include file='checkout/_partials/cart-voucher.tpl'}
  {/block}

  <hr class="separator">

  <div class="card-block">
    <div class="cart-summary-line cart-total">
      <span class="label">{$cart.totals.total.label|default:''} {$cart.labels.tax_short|default:''}</span>
      <span class="value">{$cart.totals.total.value|default:''}</span>
    </div>

    <div class="cart-summary-line">
      <small class="label">{$cart.subtotals.tax.label|default:''}</small>
      <small class="value">{$cart.subtotals.tax.value|default:''}</small>
    </div>
  </div>

  <hr class="separator">
</div>
{/block}
