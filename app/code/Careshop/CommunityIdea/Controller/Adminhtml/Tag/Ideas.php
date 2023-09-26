<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Tag;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Tag;
use Careshop\CommunityIdea\Model\TagFactory;

class Ideas extends Tag
{
    /**
     * @var LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Ideas constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param LayoutFactory $resultLayoutFactory
     * @param TagFactory $ideaFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LayoutFactory $resultLayoutFactory,
        TagFactory $ideaFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $registry, $ideaFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initTag(true);

        return $this->resultLayoutFactory->create();
    }
}
