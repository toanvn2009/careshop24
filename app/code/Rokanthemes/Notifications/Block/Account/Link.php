<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rokanthemes\Notifications\Block\Account;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    
    /**
     * @var \Vnecoms\Credit\Model\Credit
     */
    protected $creditAccount;
    
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Rokanthemes\Notifications\Model\NotificationsFactory $creditAccountFactory,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        if($customerSession->isLoggedIn() && ($customerId = $customerSession->getId())){
            $this->creditAccount = $creditAccountFactory->create();
            $this->creditAccount->loadByCustomerId($customerId);
        }
        parent::__construct($context, $data);
    }
}
