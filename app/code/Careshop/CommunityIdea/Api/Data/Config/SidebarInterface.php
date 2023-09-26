<?php

namespace Careshop\CommunityIdea\Api\Data\Config;

interface SidebarInterface
{
    const NUMBER_RECENT    = 'number_recent';
    const NUMBER_MOST_VIEW = 'number_most_view';

    /**
     * @return string/null
     */
    public function getNumberRecent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNumberRecent($value);

    /**
     * @return string/null
     */
    public function getNumberMostView();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNumberMostView($value);
}
