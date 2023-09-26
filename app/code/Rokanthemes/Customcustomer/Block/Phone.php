<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rokanthemes\Customcustomer\Block;

class Phone extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var Customer
     */
    protected $_customer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\Customer $customer,
        array $data = []
    )
    {
        $this->_session = $session;
        $this->_customer = $customer;
        parent::__construct($context, $data);
    }

    public function getPhone()
    {
        $visitor_data = $this->_session->getData('visitor_data');
        if($visitor_data){
            $customerObj = $this->_customer->load($visitor_data['customer_id']);
            return $customerObj->getPhone();
        }
    }

    public function getLanguage()
    {
        $visitor_data = $this->_session->getData('visitor_data');
        if($visitor_data){
            $customerObj = $this->_customer->load($visitor_data['customer_id']);
            return $customerObj->getLanguage();
        }
    }
}
