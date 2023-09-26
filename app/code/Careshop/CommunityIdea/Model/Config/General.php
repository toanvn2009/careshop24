<?php


namespace Careshop\CommunityIdea\Model\Config;

use Magento\Framework\DataObject;
use Careshop\CommunityIdea\Api\Data\Config\GeneralInterface;


class General extends DataObject implements GeneralInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCommunityName()
    {
        return $this->getData(self::COMMUNITY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommunityName($value)
    {
        $this->setData(self::COMMUNITY_NAME, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsLinkInMenu()
    {
        return $this->getData(self::IS_LINK_IN_MENU);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsLinkInMenu($value)
    {
        $this->setData(self::IS_LINK_IN_MENU, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisplayAuthor()
    {
        return $this->getData(self::IS_DISPLAY_AUTHOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDisplayAuthor($value)
    {
        $this->setData(self::IS_DISPLAY_AUTHOR, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommunityMode()
    {
        return $this->getData(self::COMMUNITY_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommunityMode($value)
    {
        $this->setData(self::COMMUNITY_MODE, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommunityColor()
    {
        return $this->getData(self::COMMUNITY_COLOR);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommunityColor($value)
    {
        $this->setData(self::COMMUNITY_COLOR, $value);

        return $this;
    }
}
