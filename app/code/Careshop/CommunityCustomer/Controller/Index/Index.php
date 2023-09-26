<?php

namespace Rokanthemes\Portfolio\Controller\Index;

/**
 * Portfolio page 
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * View blog homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    { 
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
