<?php

namespace Careshop\CommunityIdea\Api\Data;

interface CommunityConfigInterface
{
    const GENERAL = 'general';
    const SIDEBAR = 'sidebar';
    const SEO     = 'seo';

    /**
     * @return \Careshop\CommunityIdea\Api\Data\Config\GeneralInterface
     */
    public function getGeneral();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\Config\GeneralInterface $value
     *
     * @return $this
     */
    public function setGeneral($value);

    /**
     * @return \Careshop\CommunityIdea\Api\Data\Config\SidebarInterface
     */
    public function getSidebar();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\Config\SidebarInterface $value
     *
     * @return $this
     */
    public function setSidebar($value);

    /**
     * @return \Careshop\CommunityIdea\Api\Data\Config\SeoInterface
     */
    public function getSeo();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\Config\SeoInterface $value
     *
     * @return $this
     */
    public function setSeo($value);
}
