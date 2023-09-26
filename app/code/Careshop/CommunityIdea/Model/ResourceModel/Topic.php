<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityIdea\Helper\Data;

/**
 * Class Topic
 * @package Careshop\CommunityIdea\Model\ResourceModel
 */
class Topic extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    public $date;

    /**
     * Event Manager
     *
     * @var ManagerInterface
     */
    public $eventManager;

    /**
     * Idea relation model
     *
     * @var string
     */
    public $topicIdeaTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Topic constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param RequestInterface $request
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        DateTime $date,
        ManagerInterface $eventManager,
        RequestInterface $request,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->request = $request;

        parent::__construct($context);

        $this->topicIdeaTable = $this->getTable('community_idea_topic');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_topic', 'topic_id');
    }

    /**
     * Retrieves Topic Name from DB by passed id.
     *
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTopicNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('topic_id = :topic_id');
        $binds = ['topic_id' => (int)$id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }

        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

       // $object->setUrlKey(
           // $this->helperData->generateUrlKey($this, $object, ($object->getUrlKey()) ?$object->getUrlKey() : '' ?: $object->getName())
       // );

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveIdeaRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Topic $topic
     *
     * @return array
     */
    public function getIdeasPosition(\Careshop\CommunityIdea\Model\Topic $topic)
    {
        $select = $this->getConnection()->select()->from(
            $this->topicIdeaTable,
            ['idea_id', 'position']
        )
            ->where(
                'topic_id = :topic_id'
            );
        $bind = ['topic_id' => (int)$topic->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Topic $topic
     *
     * @return $this
     */
    protected function saveIdeaRelation(\Careshop\CommunityIdea\Model\Topic $topic)
    {
        $topic->setIsChangedIdeaList(false);
        $id = $topic->getId();
        $ideas = $topic->getIdeasData();
        $oldIdeas = $topic->getIdeasPosition();
        if (is_array($ideas)) {
            $insert = array_diff_key($ideas, $oldIdeas);
            $delete = array_diff_key($oldIdeas, $ideas);
            $update = array_intersect_key($ideas, $oldIdeas);
            $_update = [];
            foreach ($update as $key => $settings) {
                if (isset($oldIdeas[$key]) && $oldIdeas[$key] != $settings['position']) {
                    $_update[$key] = $settings;
                }
            }
            $update = $_update;
        }
        $adapter = $this->getConnection();
        if ($ideas === null && $this->request->getActionName() === 'save') {
            foreach (array_keys($oldIdeas) as $value) {
                $condition = ['idea_id =?' => (int)$value, 'topic_id=?' => (int)$id];
                $adapter->delete($this->topicIdeaTable, $condition);
            }

            return $this;
        }
        if (!empty($delete)) {
            foreach (array_keys($delete) as $value) {
                $condition = ['idea_id =?' => (int)$value, 'topic_id=?' => (int)$id];
                $adapter->delete($this->topicIdeaTable, $condition);
            }
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $ideaId => $position) {
                $data[] = [
                    'topic_id' => (int)$id,
                    'idea_id' => (int)$ideaId,
                    'position' => (int)$position['position']
                ];
            }
            $adapter->insertMultiple($this->topicIdeaTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $ideaId => $position) {
                $where = ['topic_id = ?' => (int)$id, 'idea_id = ?' => (int)$ideaId];
                $bind = ['position' => (int)$position['position']];
                $adapter->update($this->topicIdeaTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $ideaIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_topic_change_ideas',
                ['topic' => $topic, 'idea_ids' => $ideaIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $topic->setIsChangedIdeaList(true);
            $ideaIds = array_keys($insert + $delete + $update);
            $topic->setAffectedIdeaIds($ideaIds);
        }

        return $this;
    }

    /**
     * Check is import topic
     *
     * @param $importSource
     * @param $oldId
     *
     * @return string
     * @throws LocalizedException
     */
    public function isImported($importSource, $oldId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'topic_id')
            ->where('import_source = :import_source');
        $binds = ['import_source' => $importSource . '-' . $oldId];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param $importType
     *
     * @throws LocalizedException
     */
    public function deleteImportItems($importType)
    {
        $adapter = $this->getConnection();
        $adapter->delete($this->getMainTable(), "`import_source` LIKE '" . $importType . "%'");
    }
}
