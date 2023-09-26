<?php

namespace Careshop\CommunityIdea\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityIdea\Block\Category\Menu;
use Careshop\CommunityIdea\Helper\Data;

class Topmenu
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Topmenu constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param $html
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $html
    ) {
        if ($this->helper->isEnabled() && $this->helper->getCommunityConfig('general/toplinks')) {
            $communityHtml = $subject->getLayout()->createBlock(Menu::class)
                ->setTemplate('Careshop_CommunityIdea::category/topmenu.phtml')->toHtml();

            return $html . $communityHtml;
        }

        return $html;
    }
}
