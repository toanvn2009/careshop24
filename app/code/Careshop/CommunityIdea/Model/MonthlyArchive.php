<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\DataObject;
use Careshop\CommunityIdea\Api\Data\MonthlyArchiveInterface;

class MonthlyArchive extends DataObject implements MonthlyArchiveInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($value)
    {
        $this->setData(self::LABEL, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdeaCount()
    {
        return $this->getData(self::IDEA_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdeaCount($value)
    {
        $this->setData(self::IDEA_COUNT, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return $this->getData(self::LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setLink($value)
    {
        $this->setData(self::LINK, $value);

        return $this;
    }
}
