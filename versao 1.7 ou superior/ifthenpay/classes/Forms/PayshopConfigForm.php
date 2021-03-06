<?php
/**
 * 2007-2020 Ifthenpay Lda
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @copyright 2007-2020 Ifthenpay Lda
 * @author    Ifthenpay Lda <ifthenpay@ifthenpay.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


namespace PrestaShop\Module\Ifthenpay\Forms;

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\Ifthenpay\Utility\Utility;
use PrestaShop\Module\Ifthenpay\Forms\ConfigForm;

class PayshopConfigForm extends ConfigForm
{
    protected $paymentMethod = 'payshop';
    /**
    * Set payshop config form options
    * @return void
    */
    public function setOptions()
    {
        $this->options[] = [
            'id' => $this->ifthenpayModule->l('Choose Payshop key'),
            'name' => $this->ifthenpayModule->l('Choose Payshop key')
        ];
        $this->addToOptions();
    }
    /**
    * Get payshop config form
    * @return array
    */
    public function getForm()
    {
        $this->setOptions();
        $this->form['form']['input'][] = [
            'type' => 'select',
            'label' => $this->ifthenpayModule->l('Payshop key'),
            'desc' => $this->ifthenpayModule->l('Choose Payshop key'),
            'name' => 'IFTHENPAY_PAYSHOP_KEY',
            'required' => true,
            'options' => [
                'query' => $this->options,
                'id' => 'id',
                'name' => 'name'
            ]
        ];
        $this->form['form']['input'][] = [
            'type' => 'text',
            'label' => $this->ifthenpayModule->l('Validity'),
            'name' => 'IFTHENPAY_PAYSHOP_VALIDADE',
            'desc' => $this->ifthenpayModule->l('Choose the number of days, leave empty if you do not want validity'),
            'size' => 2,
            'required' => true
        ];
        return $this->form;
    }
    /**
    * Set payshop smarty variables for view
    * @return void
    */
    public function setSmartyVariables()
    {
        \Context::getContext()->smarty->assign('payshopKey', \Configuration::get('IFTHENPAY_PAYSHOP_KEY'));
        \Context::getContext()->smarty->assign('payshopValidade', \Configuration::get('IFTHENPAY_PAYSHOP_VALIDADE'));
        \Context::getContext()->smarty->assign('chaveAntiPhishing', \Configuration::get('IFTHENPAY_PAYSHOP_CHAVE_ANTI_PHISHING'));
        \Context::getContext()->smarty->assign('urlCallback', \Configuration::get('IFTHENPAY_PAYSHOP_URL_CALLBACK'));
    }
    /**
    * Set payshop gateway data
    * @return void
    */
    public function setGatewayBuilderData()
    {
        parent::setGatewayBuilderData();
        $this->gatewayDataBuilder->setEntidade(\Tools::strtoupper($this->paymentMethod));
        $this->gatewayDataBuilder->setSubEntidade(\Tools::getValue('IFTHENPAY_PAYSHOP_KEY'));
    }
    /**
    * Process payshop config form
    * @return void
    */
    public function processForm()
    {
        $this->setGatewayBuilderData();
        \Configuration::updateValue('IFTHENPAY_PAYSHOP_KEY', $this->gatewayDataBuilder->getData()->subEntidade);
        \Configuration::updateValue('IFTHENPAY_PAYSHOP_VALIDADE', \Tools::getValue('IFTHENPAY_PAYSHOP_VALIDADE'));

        $ifthenpayCallback = $this->getIfthenpayCallback();

        $ifthenpayCallback->make($this->paymentMethod, $this->getCallbackControllerUrl());

        \Configuration::updateValue('IFTHENPAY_PAYSHOP_URL_CALLBACK', $ifthenpayCallback->getUrlCallback());
        \Configuration::updateValue('IFTHENPAY_PAYSHOP_CHAVE_ANTI_PHISHING', $ifthenpayCallback->getChaveAntiPhishing());
        Utility::setPrestashopCookie('success', $this->ifthenpayModule->l('Payshop key successfully updated.'));
    }
    /**
    * Delete payshop config values
    * @return void
    */
    public function deleteConfigValues()
    {
        \Configuration::deleteByName('IFTHENPAY_PAYSHOP_KEY');
        \Configuration::deleteByName('IFTHENPAY_PAYSHOP_VALIDADE');
        \Configuration::deleteByName('IFTHENPAY_PAYSHOP_URL_CALLBACK');
        \Configuration::deleteByName('IFTHENPAY_PAYSHOP_CHAVE_ANTI_PHISHING');
    }
}
