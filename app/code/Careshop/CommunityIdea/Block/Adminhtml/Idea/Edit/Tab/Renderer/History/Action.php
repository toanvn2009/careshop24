<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\History;

use Exception;
use Magento\Backend\Block\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Json\EncoderInterface;
use Magento\Store\Model\StoreManagerInterface;


class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
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
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $jsonEncoder, $data);
    }

    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $actions[] = [
            'url' =>
                $this->getUrl('*/history/edit', [
                    'id' => $row->getId(),
                    'idea_id' => $row->getIdeaId(),
                    'history' => true
                ]),
            'popup' => false,
            'caption' => __('Edit'),
        ];
        try {
            $actions[] = [
                'url' => $this->_storeManager->getStore()->getBaseUrl()
                    . 'community/idea/preview?id=' . $row->getIdeaId() . '&historyId=' . $row->getId(),
                'popup' => true,
                'caption' => __('Preview'),
            ];
        } catch (Exception $exception) {
            $actions[] = [];
        }
        $actions[] = [
            'url' =>
                $this->getUrl('*/history/restore', [
                    'id' => $row->getId(),
                    'idea_id' => $row->getIdeaId()
                ]),
            'popup' => false,
            'caption' => __('Restore'),
            'confirm' => 'Are you sure you want to do this?'
        ];

        $actions[] = [
            'url' =>
                $this->getUrl('*/history/delete', [
                    'id' => $row->getId(),
                    'idea_id' => $row->getIdeaId()
                ]),
            'popup' => false,
            'caption' => __('Delete'),
            'confirm' => 'Are you sure you want to do this?'
        ];

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
