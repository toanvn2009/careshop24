<?php

namespace Careshop\CommunityIdea\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class Sitemap extends Frontend
{
    /**
     * @return $this|void
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('sitemap', [
                'label' => __('Site Map'),
                'title' => __('Site Map')
            ]);
        }
    }

    /**
     * @param bool $meta
     *
     * @return array|Phrase
     */
    public function getCommunityTitle($meta = false)
    {
        $communityTitle = parent::getCommunityTitle($meta);

        if ($meta) {
            $communityTitle[] = __('Site Map');
        } else {
            $communityTitle = __('Site Map');
        }

        return $communityTitle;
    }
}
