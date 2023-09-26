<?php

namespace Careshop\CommunityDevelopProduct\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;

/**
 * Class CommentContent
 * @package Careshop\CommunityIdea\Ui\Component\Listing\Columns
 */
class Develop extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

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
        DevelopFactory $developFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->developFactory = $developFactory;
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
                    $develop_id = $item['develop_id'];
                    $develop = $this->developFactory->create()->load($develop_id);
                    $item[$this->getData('name')] = '<a href="'.$this->urlBuilder->getUrl('communitydevelop/develop/edit/id/'.$develop_id.'').'">' . $develop->getName() . '</a>';
                }
            }
        }

        return $dataSource;
    }
}
