<?php

namespace Careshop\CommunityCustomer\Controller\Customer;

/**
 * Cummunity Custommer  Page 
 */
class Macros extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * View Cummunity action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {   
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
