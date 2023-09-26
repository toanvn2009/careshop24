<?php

namespace Careshop\CommunityIdea\Api\Data;

interface MonthlyArchiveInterface
{
    const LABEL      = 'label';
    const IDEA_COUNT = 'idea_count';
    const LINK       = 'link';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLabel($value);

    /**
     * @return int
     */
    public function getIdeaCount();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIdeaCount($value);

    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLink($value);
}
