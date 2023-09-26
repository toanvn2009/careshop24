<?php

namespace Careshop\CommunityIdea\Api\Data\Config;

interface SeoInterface
{
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';

    /**
     * @return string/null
     */
    public function getMetaTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaTitle($value);

    /**
     * @return string/null
     */
    public function getMetaDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaDescription($value);
}
