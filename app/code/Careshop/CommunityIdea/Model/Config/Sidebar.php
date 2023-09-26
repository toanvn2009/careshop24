<?php


namespace Careshop\CommunityIdea\Model\Config;

use Magento\Framework\DataObject;
use Careshop\CommunityIdea\Api\Data\Config\SidebarInterface;

class Sidebar extends DataObject implements SidebarInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNumberRecent()
    {
        return $this->getData(self::NUMBER_RECENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setNumberRecent($value)
    {
        $this->setData(self::NUMBER_RECENT, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberMostView()
    {
        return $this->getData(self::NUMBER_MOST_VIEW);
    }

    /**
     * {@inheritdoc}
     */
    public function setNumberMostView($value)
    {
        $this->setData(self::NUMBER_MOST_VIEW, $value);

        return $this;
    }
}
