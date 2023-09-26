<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\ReportForumFactory;

abstract class Report extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityDevelopProduct::develop_forum';

    public $reportForumFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Idea constructor.
     *
     * @param DevelopFactory $developFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        ReportForumFactory $reportForumFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->reportForumFactory = $reportForumFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

}
