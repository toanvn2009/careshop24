<?php

namespace Careshop\CommunityIdea\Block\Idea;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Messages;

class Create extends \Careshop\CommunityIdea\Block\Listidea
{

    public function getCategoriesTree() {
        return $this->helperData->getCategoriesTree();
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
            $breadcrumbs->addCrumb('an_alias', array(
                    'label'=>__('Create Message'),
                    'title'=>__('Create Message'),
                )
            );
        }
    }
}
