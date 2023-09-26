<?php

namespace Careshop\CommunityIdea\Api\Data\Config;

interface GeneralInterface
{
    const COMMUNITY_NAME           = 'community_name';
    const IS_LINK_IN_MENU   = 'is_link_in_menu';
    const IS_DISPLAY_AUTHOR = 'is_display_author';
    const COMMUNITY_MODE         = 'community_mode';
    const COMMUNITY_COLOR        = 'community_color';

    /**
     * @return string/null
     */
    public function getCommunityName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCommunityName($value);

    /**
     * @return boolean/null
     */
    public function getIsLinkInMenu();

    /**
     * @param boolean $value
     *
     * @return $this
     */
    public function setIsLinkInMenu($value);

    /**
     * @return boolean/null
     */
    public function getIsDisplayAuthor();

    /**
     * @param boolean $value
     *
     * @return $this
     */
    public function setIsDisplayAuthor($value);

    /**
     * @return string/null
     */
    public function getCommunityMode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCommunityMode($value);

    /**
     * @return string/null
     */
    public function getCommunityColor();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCommunityColor($value);
}
