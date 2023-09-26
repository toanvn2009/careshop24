<?php

namespace Careshop\CommunityDevelopProduct\Block\Forum;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Messages;

class View extends \Careshop\CommunityDevelopProduct\Block\Listforum
{

    /**
     * @return bool
     */
    public function getDevelop()
    {
        $develop = $this->developFactory->create();
        $id = $this->getRequest()->getParam('id');
        $develop->load($id);
        return $develop;
    }

    /**
     * @return bool
     */
    public function getProduct()
    {
        $develop = $this->getDevelop();
        $_product = $this->_productloader->create()->load($develop->getProductId());
        return $_product;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $_product = $this->getProduct();
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                    'label'=>__('Community Overview'),
                    'title'=>__('Community Overview'),
                    'link'=> $this->_storeManager->getStore()->getBaseUrl().'community'
                )
            );
            $breadcrumbs->addCrumb('product', array(
                'label'=>__($_product->getName()),
                'title'=>__($_product->getName()),
                'link'=> $_product->getProductUrl()
            )
        );
            $breadcrumbs->addCrumb('an_alias', array(
                    'label'=>__('Reply Forum'),
                    'title'=>__('Reply Forum'),
                )
            );
        }
    }
}
