<?php

namespace Careshop\CommunityIdea\Block\Product;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Messages;


class Developer extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{   
		parent::__construct($context);
	}

	public function _prepareLayout()
	{
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('home', array(
			'label'=>__('Community Overview'),
			 'title'=>__('Community Overview'),
			'link'=> $this->_storeManager->getStore()->getBaseUrl().'community'
			)
		);
		$breadcrumbs->addCrumb('an_alias', array(
			'label'=>__('Share Products Ideas'),
			 'title'=>__('Share Products Ideas'),
			)
		);

		$this->pageConfig->getTitle()->set(__('Share Products Ideas'));  
		
		return parent::_prepareLayout();
	}

}