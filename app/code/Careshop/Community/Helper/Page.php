<?php
namespace Careshop\Community\Helper;


use Magento\Framework\App\Helper\AbstractHelper;

/**
 *
 */
class Page extends AbstractHelper
{
    /**
     * CMS home page config path
     */
    const XML_PATH_HOME_PAGE_COMMUNITY = 'community/default/cms_home_page';

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
}
