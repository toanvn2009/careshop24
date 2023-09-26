<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityIdea\Helper\Data;

class Author extends AbstractDb
{
    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var string
     */
    public $ideaTable;

    /**
     * Author constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        Data $helperData,
        DateTime $dateTime
    ) {
        $this->helperData = $helperData;
        $this->dateTime = $dateTime;

        parent::__construct($context);
        $this->ideaTable = $this->getTable('community_idea');
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('community_author', 'user_id');
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUrlKey(
            $this->helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        if (!$object->isObjectNew()) {
            $timeStamp = $this->dateTime->gmtDate();
            $object->setUpdatedAt($timeStamp);
        }

        return $this;
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Author $author
     *
     * @return array
     */
    public function getIdeaIds(\Careshop\CommunityIdea\Model\Author $author)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->ideaTable,
            'idea_id'
        )
            ->where(
                'author_id = ?',
                (int)$author->getId()
            );

        return $adapter->fetchCol($select);
    }
}
