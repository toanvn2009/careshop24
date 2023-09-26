<?php

namespace Careshop\CommunityTesterProduct\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CustomerName
 * @package Careshop\CommunityIdea\Ui\Component\Listing\Columns
 */
class CustomerName extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    protected $customer;

    /**
     * Actions constructor.
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Model\CustomerFactory $customer,
        array $components = [],
        array $data = []
    ) {
        $this->customer = $customer;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $id = $item['customer_id'];
                    $customerData = $this->customer->create()->load($id);
                    $item[$this->getData('name')] = '<span>'.$customerData->getName().'</span>';
                }
            }
        }

        return $dataSource;
    }
}
