<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) { exit; }
class CustomerForm extends CustomerFormCore
{
    /*
    * module: ets_recaptcha_free
    * date: 2025-07-15 08:05:46
    * version: 1.1.3
    */
    public function validate()
    {
        if (Tools::isSubmit('submitCreate') && Module::isEnabled('ets_recaptcha_free') && ($captcha = Module::getInstanceByName('ets_recaptcha_free')) && $captcha->hookVal('register') && Tools::getValue('controller') != 'identity') {
            $captchaField = $this->getField('captcha');
            $errors = array();
            $captcha->captchaVal($errors);
            if ($errors) {
                $captchaField->addError(implode(',', $errors));
            }
        }
        return parent::validate();
    }
}