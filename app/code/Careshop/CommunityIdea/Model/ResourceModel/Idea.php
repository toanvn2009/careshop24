<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\Author;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\Idea as IdeaModel;

class Idea extends AbstractDb
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
     * Tag relation model
     *
     * @var string
     */
    public $ideaTagTable;

    /**
     * Topic relation model
     *
     * @var string
     */
    public $ideaTopicTable;

    /**
     * Community Category relation model
     *
     * @var string
     */
    public $ideaCategoryTable;

    /**
     * @var string
     */
    public $ideaProductTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var AuthorFactory
     */
    protected $_authorFactory;

    /**
     * @var Auth
     */
    protected $_auth;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var string
     */
    protected $ideaTrafficTable;

    /**
     * @var string
     */
    protected $ideaAuthorTable;

    /**
     * Idea constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Auth $auth
     * @param Data $helperData
     * @param RequestInterface $request
     * @param AuthorFactory $authorFactory
     */
    public function __construct(
        Context $context,
        DateTime $date,
        ManagerInterface $eventManager,
        Auth $auth,
        Data $helperData,
        RequestInterface $request,
        AuthorFactory $authorFactory
    ) {
        $this->date           = $date;
        $this->eventManager   = $eventManager;
        $this->_auth          = $auth;
        $this->helperData     = $helperData;
        $this->_request       = $request;
        $this->_authorFactory = $authorFactory;

        parent::__construct($context);

        $this->ideaTagTable      = $this->getTable('community_idea_tag');
        $this->ideaTopicTable    = $this->getTable('community_idea_topic');
        $this->ideaCategoryTable = $this->getTable('community_idea_category');
        $this->ideaProductTable  = $this->getTable('community_idea_product');
        $this->ideaTrafficTable  = $this->getTable('community_idea_traffic');
        $this->ideaAuthorTable   = $this->getTable('community_author');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_idea', 'idea_id');
    }

    /**
     * Retrieves Idea Name from DB by passed id.
     *
     * @param int $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getIdeaNameById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('idea_id = :idea_id');
        $binds   = ['idea_id' => (int) $id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * before save callback
     *
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        } 

        return $this;
    }

    /**
     * @param IdeaModel|AbstractModel $object
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
      //  $this->saveUrlKeyIdea($object);
        $this->saveTagRelation($object);
        $this->saveTopicRelation($object);
        $this->saveCategoryRelation($object);
        $this->saveProductRelation($object);

        if ($this->_request->getActionName() !== 'manage') {
            $this->saveAuthor();
        }

        return parent::_afterSave($object);
    }

    public function saveUrlKeyIdea($object)
    {
        if ($object->getId()) {
                $adapter = $this->getConnection();
                $where = ['idea_id = ?' => (int)$object->getId()];
                $bind = ['url_key' => $object->getUrlKey().'-'.$object->getId()];
                $adapter->update($this->getMainTable(), $bind, $where);
        }

        return $this;
    }

    /**
     * @param IdeaModel $idea
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveTagRelation(IdeaModel $idea)
    {
        $idea->setIsChangedTagList(false);
        $id      = $idea->getId();
        $tags    = $idea->getTagsIds();
        $oldTags = $idea->getTagIds();

        if ($tags === null) {
            return $this;
        }

        $insert  = array_diff($tags, $oldTags);
        $delete  = array_diff($oldTags, $tags);
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = ['tag_id IN(?)' => $delete, 'idea_id=?' => $id];
            $adapter->delete($this->ideaTagTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                $data[] = [
                    'idea_id'  => (int) $id,
                    'tag_id'   => (int) $tagId,
                    'position' => 1
                ];
            }
            $adapter->insertMultiple($this->ideaTagTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $tagIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_idea_change_tags',
                ['idea' => $idea, 'tag_ids' => $tagIds]
            );
        }

        if (!empty($insert) || !empty($delete)) {
            $idea->setIsChangedTagList(true);
            $tagIds = array_keys($insert + $delete);
            $idea->setAffectedTagIds($tagIds);
        }

        return $this;
    }

    /**
     * @param IdeaModel $idea
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveTopicRelation(IdeaModel $idea)
    {
        $idea->setIsChangedTopicList(false);
        $id        = $idea->getId();
        $topics    = $idea->getTopicsIds();
        $oldTopics = $idea->getTopicIds();

        if ($topics === null) {
            return $this;
        }

        $insert  = array_diff($topics, $oldTopics);
        $delete  = array_diff($oldTopics, $topics);
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = ['topic_id IN(?)' => $delete, 'idea_id=?' => $id];
            $adapter->delete($this->ideaTopicTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $topicId) {
                $data[] = [
                    'idea_id'  => (int) $id,
                    'topic_id' => (int) $topicId,
                    'position' => 1
                ];
            }
            $adapter->insertMultiple($this->ideaTopicTable, $data);
        }

        if (!empty($insert) || !empty($delete)) {
            $topicIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_idea_change_topics',
                ['idea' => $idea, 'topic_ids' => $topicIds]
            );
        }
        if (!empty($insert) || !empty($delete)) {
            $idea->setIsChangedTopicList(true);
            $topicIds = array_keys($insert + $delete);
            $idea->setAffectedTopicIds($topicIds);
        }

        return $this;
    }

    /**
     * @param IdeaModel $idea
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveCategoryRelation(IdeaModel $idea)
    {
        $idea->setIsChangedCategoryList(false);
        $id             = $idea->getId();
        $categories     = $idea->getCategoriesIds();
        $oldCategoryIds = $idea->getCategoryIds();

        if ($categories === null) {
            return $this;
        }

        $insert         = array_diff($categories, $oldCategoryIds);
        $delete         = array_diff($oldCategoryIds, $categories);
        $adapter        = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['category_id IN(?)' => $delete, 'idea_id=?' => $id];
            $adapter->delete($this->ideaCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    'idea_id'     => (int) $id,
                    'category_id' => (int) $categoryId,
                    'position'    => 1
                ];
            }
            $adapter->insertMultiple($this->ideaCategoryTable, $data);
        }
        if (!empty($insert) || !empty($delete)) {
            $categoryIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_idea_change_categories',
                ['idea' => $idea, 'category_ids' => $categoryIds]
            );
        }
        if (!empty($insert) || !empty($delete)) {
            $idea->setIsChangedCategoryList(true);
            $categoryIds = array_keys($insert + $delete);
            $idea->setAffectedCategoryIds($categoryIds);
        }

        return $this;
    }

    /**
     * @param IdeaModel $idea
     *
     * @return array
     */
    public function getCategoryIds(IdeaModel $idea)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from(
            $this->ideaCategoryTable,
            'category_id'
        )
            ->where(
                'idea_id = ?',
                (int) $idea->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param IdeaModel $idea
     *
     * @return array
     */
    public function getTagIds(IdeaModel $idea)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from(
            $this->ideaTagTable,
            'tag_id'
        )
            ->where(
                'idea_id = ?',
                (int) $idea->getId()
            );

        return $adapter->fetchCol($select);
    }

    /**
     * @param IdeaModel $idea
     *
     * @return array
     */
    public function getTopicIds(IdeaModel $idea)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from($this->ideaTopicTable, 'topic_id')
            ->where('idea_id = ?', (int) $idea->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * @param IdeaModel $idea
     * @return array
     */
    public function getAuthor(IdeaModel $idea)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from($this->ideaAuthorTable, '*')
            ->where('user_id = ?', (int) $idea->getAuthorId());

        return $adapter->fetchRow($select);
    }

    /**
     * @param IdeaModel $idea
     *
     * @return array
     */
    public function getViewTraffic(IdeaModel $idea)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()->from($this->ideaTrafficTable, 'numbers_view')
            ->where('idea_id = ?', (int) $idea->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * @param IdeaModel $idea
     *
     * @return $this
     */
    public function saveProductRelation(IdeaModel $idea)
    {
        $idea->setIsChangedProductList(false);
        $id          = $idea->getId();
        $products    = $idea->getProductsData();
        $oldProducts = $idea->getProductsPosition();

        if (is_array($products)) {
            $insert  = array_diff_key($products, $oldProducts);
            $delete  = array_diff_key($oldProducts, $products);
            $update  = array_intersect_key($products, $oldProducts);
            $_update = [];
            foreach ($update as $key => $settings) {
                if (isset($oldProducts[$key]) && $oldProducts[$key] != $settings['position']) {
                    $_update[$key] = $settings;
                }
            }
            $update = $_update;
        }
        $adapter = $this->getConnection();
        if ($products === null && $this->_request->getActionName() === 'save') {
            foreach (array_keys($oldProducts) as $value) {
                $condition = ['entity_id =?' => (int) $value, 'idea_id=?' => (int) $id];
                $adapter->delete($this->ideaProductTable, $condition);
            }

            return $this;
        }
        if (!empty($delete)) {
            foreach (array_keys($delete) as $value) {
                $condition = ['entity_id =?' => (int) $value, 'idea_id=?' => (int) $id];
                $adapter->delete($this->ideaProductTable, $condition);
            }
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $entityId => $position) {
                $data[] = [
                    'idea_id'   => (int) $id,
                    'entity_id' => (int) $entityId,
                    'position'  => (int) $position['position']
                ];
            }
            $adapter->insertMultiple($this->ideaProductTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $entityId => $position) {
                $where = ['idea_id = ?' => (int) $id, 'entity_id = ?' => (int) $entityId];
                $bind  = ['position' => (int) $position['position']];
                $adapter->update($this->ideaProductTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $entityIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_idea_change_products',
                ['idea' => $idea, 'entity_ids' => $entityIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $idea->setIsChangedProductList(true);
            $entityIds = array_keys($insert + $delete + $update);
            $idea->setAffectedEntityIds($entityIds);
        }

        return $this;
    }

    /**
     * @param IdeaModel $idea
     *
     * @return array
     */
    public function getProductsPosition(IdeaModel $idea)
    {
        $select = $this->getConnection()->select()->from(
            $this->ideaProductTable,
            ['entity_id', 'position']
        )
            ->where(
                'idea_id = :idea_id'
            );
        $bind   = ['idea_id' => (int) $idea->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Check post url key is exists
     *
     * @param string $urlKey
     *
     * @return string
     * @throws LocalizedException
     */
    public function isDuplicateUrlKey($urlKey)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'idea_id')
            ->where('url_key = :url_key');
        $binds   = ['url_key' => $urlKey];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Save the idea author when creating post
     */
    public function saveAuthor()
    {
        $currentUser = $this->_auth->getUser();

        if ($currentUser) {
            $currentUserId = $currentUser->getId();
            /** @var Author $author */
            $author = $this->_authorFactory->create()->load($currentUserId);

            /** Create the new author if that author isn't exist */
            if (!$author->getId()) {
                $author->setId($currentUserId)
                    ->setName($currentUser->getName())->save();
            }
        }
    }

    /**
     * Check is imported post
     *
     * @param string $importSource
     * @param string $oldId
     *
     * @return string
     * @throws LocalizedException
     */
    public function isImported($importSource, $oldId)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'idea_id')
            ->where('import_source = :import_source');
        $binds   = ['import_source' => $importSource . '-' . $oldId];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * @param string $importType
     *
     * @return int
     * @throws LocalizedException
     */
    public function deleteImportItems($importType)
    {
        $adapter = $this->getConnection();

        return $adapter->delete($this->getMainTable(), "`import_source` LIKE '" . $importType . "%'");
    }
}
