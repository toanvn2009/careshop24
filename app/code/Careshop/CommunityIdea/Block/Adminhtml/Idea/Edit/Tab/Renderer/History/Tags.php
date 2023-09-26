<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\History;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\Text;
use Magento\Framework\DataObject;
use Careshop\CommunityIdea\Helper\Data;


class Tags extends Text
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Categories constructor.
     *
     * @param Context $context
     * @param Data $_helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $_helperData,
        array $data = []
    ) {
        $this->_helperData = $_helperData;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        if (!empty($row->getData($this->getColumn()->getIndex()))) {
            $text = '';
            $tagIds = explode(',', $row->getData($this->getColumn()->getIndex()));
            foreach ($tagIds as $tagId) {
                $tag = $this->_helperData->getFactoryByType('tag')->create()->load($tagId);
                $text .= $tag->getName() . ',';
            }
            $row->setData($this->getColumn()->getIndex(), trim($text, ','));
        }

        return parent::render($row);
    }
}
