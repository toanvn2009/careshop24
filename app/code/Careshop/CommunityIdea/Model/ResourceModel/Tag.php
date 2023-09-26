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

class Tag extends AbstractDb
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
    public $tagIdeaTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param ManagerInterface $eventManager
     * @param DateTime $date
     * @param RequestInterface $request
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        DateTime $date,
        RequestInterface $request,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->request = $request;

        parent::__construct($context);

        $this->tagIdeaTable = $this->getTable('community_idea_tag');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_tag', 'tag_id');
    }

    /**
     * Retrieves Tag Name from DB by passed id.
     *
     * @param $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTagNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('tag_id = :tag_id');
        $binds = ['tag_id' => (int)$id];

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

        $object->setUrlKey(
            $this->helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        return parent::_beforeSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveIdeaRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Tag $tag
     *
     * @return array
     */
    public function getIdeasPosition(\Careshop\CommunityIdea\Model\Tag $tag)
    {
        $select = $this->getConnection()->select()
            ->from($this->tagIdeaTable, ['idea_id', 'position'])
            ->where('tag_id = :tag_id');

        $bind = ['tag_id' => (int)$tag->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Tag $tag
     *
     * @return $this
     */
    protected function saveIdeaRelation(\Careshop\CommunityIdea\Model\Tag $tag)
    {
        $tag->setIsChangedIdeaList(false);
        $id = $tag->getId();
        $ideas = $tag->getIdeasData();
        $oldIdeas = $tag->getIdeasPosition();
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
                $condition = ['idea_id =?' => (int)$value, 'tag_id=?' => (int)$id];
                $adapter->delete($this->tagIdeaTable, $condition);
            }

            return $this;
        }
        if (!empty($delete)) {
            foreach (array_keys($delete) as $value) {
                $condition = ['idea_id =?' => (int)$value, 'tag_id=?' => (int)$id];
                $adapter->delete($this->tagIdeaTable, $condition);
            }
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $ideaId => $position) {
                $data[] = [
                    'tag_id' => (int)$id,
                    'idea_id' => (int)$ideaId,
                    'position' => (int)$position['position']
                ];
            }
            $adapter->insertMultiple($this->tagIdeaTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $ideaId => $position) {
                $where = ['tag_id = ?' => (int)$id, 'idea_id = ?' => (int)$ideaId];
                $bind = ['position' => (int)$position['position']];
                $adapter->update($this->tagIdeaTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $ideaIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_tag_change_ideas',
                ['tag' => $tag, 'idea_ids' => $ideaIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $tag->setIsChangedIdeaList(true);
            $ideaIds = array_keys($insert + $delete + $update);
            $tag->setAffectedIdeaIds($ideaIds);
        }

        return $this;
    }

    /**
     * Check category url key is exists
     *
     * @param $urlKey
     *
     * @return string
     * @throws LocalizedException
     */
    public function isDuplicateUrlKey($urlKey)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'tag_id')
            ->where('url_key = :url_key');
        $binds = ['url_key' => $urlKey];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Check is import tag
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
            ->from($this->getMainTable(), 'tag_id')
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
