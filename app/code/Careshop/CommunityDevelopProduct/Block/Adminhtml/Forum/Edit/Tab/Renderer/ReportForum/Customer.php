<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml\Forum\Edit\Tab\Renderer\ReportForum;

use Exception;
use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Json\EncoderInterface;
use Magento\Store\Model\StoreManagerInterface;


class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Action constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\CustomerFactory $customer,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->customer = $customer;
        parent::__construct($context, $jsonEncoder, $data);
    }

    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $difference = '';
        if ($row->getCustomerId()) {
            $customerData = $this->customer->create()->load($row->getCustomerId());
            $difference = '<span>' . $customerData->getName(). '</span>';
        }
        return $difference;
    }
}
