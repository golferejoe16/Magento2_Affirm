<?php
/**
 * Astound
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@astoundcommerce.com so we can send you a copy immediately.
 *
 * @category  Affirm
 * @package   Astound_Affirm
 * @copyright Copyright (c) 2016 Astound, Inc. (http://www.astoundcommerce.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Astound\Affirm\Block\Promotion\CartPage;

use Astound\Affirm\Block\Promotion\AslowasAbstract;
use Astound\Affirm\Model\Ui\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;
use Astound\Affirm\Helper;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class AsLowAs
 *
 * @package Astound\Affirm\Block\Promotion\CartPage
 */
class Aslowas extends AslowasAbstract
{
    /**
     * Data which should be converted to json from the Block data.
     *
     * @var array
     */
    protected $data = ['logo', 'script', 'public_api_key', 'min_order_total', 'max_order_total', 'element_id'];

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Financing program helper factory
     *
     * @var Helper\FinancingProgram
     */
    protected $fpHelper;

    /**
     * Cart page block.
     *
     * @param Template\Context               $context
     * @param ConfigProvider                 $configProvider
     * @param \Astound\Affirm\Model\Config   $configAffirm
     * @param \Astound\Affirm\Helper\Payment $helperAffirm
     * @param Session                        $session
     * @param array                          $data
     * @param Helper\AsLowAs                 $asLowAs
     */
    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        \Astound\Affirm\Model\Config $configAffirm,
        \Astound\Affirm\Helper\Payment $helperAffirm,
        Session $session,
        array $data = [],
        Helper\AsLowAs $asLowAs,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->checkoutSession = $session;
        parent::__construct($context, $configProvider, $configAffirm, $helperAffirm, $data, $asLowAs, $categoryCollectionFactory);
    }

    /**
     * Get current quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Validate block before showing on front in checkout cart
     * There can be added new validators by needs.
     *
     * @return boolean
     */
    public function validate()
    {
        if ($this->getQuote()) {
            // Payment availability flag
            $isAvailableFlag = $this->getPaymentConfigValue('active');

            //Validate aslowas block based on appropriate values and conditions
            if ($isAvailableFlag && $this->affirmPaymentHelper->isAffirmAvailable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add selector data to the block context.
     * This needs for bundle product, because bundle has
     * different structure.
     */
    public function process()
    {
        $this->setData('element_id', 'als_pcc');

        parent::process();
    }

    /**
     * get MFP value for current cart
     * @return string
     */
    public function getMFPValue()
    {
        return $this->asLowAsHelper->getFinancingProgramValue();
    }
}
