<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\Category as CategoryModel;
use Zend_Db_Expr;

class Category extends AbstractDb
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
    public $categoryIdeaTable;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * Category constructor.
     *
     * @param Data $helperData
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Context $context
     */
    public function __construct(
        Context $context,
        DateTime $date,
        ManagerInterface $eventManager,
        Data $helperData
    ) {
        $this->helperData   = $helperData;
        $this->date         = $date;
        $this->eventManager = $eventManager;

        parent::__construct($context);

        $this->categoryIdeaTable = $this->getTable('community_idea_category');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_category', 'category_id');
    }

    /**
     * Retrieves Community Category Name from DB by passed id.
     *
     * @param int $id
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCategoryNameById($id)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('category_id = :category_id');
        $binds   = ['category_id' => (int) $id];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Before save call back
     *
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }
        /** @var CategoryModel $object */
        parent::_beforeSave($object);

        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }

        if ($object->isObjectNew()) {
            if ($object->getPosition() === null) {
                $object->setPosition($this->getMaxPosition($object->getPath()) + 1);
            }
            $path          = explode('/', $object->getPath());
            $level         = count($path) - ($object->getId() ? 1 : 0);
            $toUpdateChild = array_diff($path, [$object->getId()]);

            if (!$object->hasPosition()) {
                $object->setPosition($this->getMaxPosition(implode('/', $toUpdateChild)) + 1);
            }
            if (!$object->hasLevel()) {
                $object->setLevel($level);
            }
            if (!$object->hasParentId() && $level) {
                $object->setParentId($path[$level - 1]);
            }
            if (!$object->getId()) {
                $object->setPath($object->getPath() . '/');
            }

            $this->getConnection()->update(
                $this->getMainTable(),
                ['children_count' => 'children_count+1'],
                ['category_id IN(?)' => $toUpdateChild]
            );
        }

        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        $object->setUrlKey(
            $this->helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     * @throws LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        /** @var CategoryModel $object */
        if (substr($object->getPath(), -1) === '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->savePath($object);
        }
        $this->saveIdeaRelation($object);

        return parent::_afterSave($object);
    }

    /**
     * @param string $path
     *
     * @return int|string
     */
    protected function getMaxPosition($path)
    {
        $adapter       = $this->getConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level         = count(explode('/', $path));
        $bind          = ['c_level' => $level, 'c_path' => $path . '/%'];
        $select        = $adapter->select()->from(
            $this->getTable('community_category'),
            'MAX(' . $positionField . ')'
        )->where(
            $adapter->quoteIdentifier('path') . ' LIKE :c_path'
        )->where(
            $adapter->quoteIdentifier('level') . ' = :c_level'
        );

        $position = $adapter->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }

        return $position;
    }

    /**
     * Check category url key is exists
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
            ->from($this->getMainTable(), 'category_id')
            ->where('url_key = :url_key');
        $binds   = ['url_key' => $urlKey];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Check is imported category
     *
     * @param string $importSource
     * @param int $oldId
     *
     * @return string
     * @throws LocalizedException
     */
    public function isImported($importSource, $oldId)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'category_id')
            ->where('import_source = :import_source');
        $binds   = ['import_source' => $importSource . '-' . $oldId];

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Update path field
     *
     * @param Object $object
     *
     * @return $this
     * @throws LocalizedException
     */
    public function savePath($object)
    {
        if ($object->getId()) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['path' => $object->getPath()],
                ['category_id = ?' => $object->getId()]
            );
            $object->unsetData('path_ids');
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        parent::_beforeDelete($object);

        /**
         * Update children count for all parent Categories
         */
        $parentIds = $object->getParentIds();
        if ($parentIds) {
            $childDecrease = $object->getChildrenCount() + 1;
            // +1 is itself
            $data  = ['children_count' => 'children_count - ' . $childDecrease];
            $where = ['category_id IN(?)' => $parentIds];
            $this->getConnection()->update($this->getMainTable(), $data, $where);
        }
        $this->deleteChildren($object);

        return $this;
    }

    /**
     * @param DataObject $object
     *
     * @return $this
     * @throws LocalizedException
     */
    public function deleteChildren(DataObject $object)
    {
        $adapter   = $this->getConnection();
        $pathField = $adapter->quoteIdentifier('path');

        $select = $adapter->select()->from(
            $this->getMainTable(),
            ['category_id']
        )->where(
            $pathField . ' LIKE :c_path'
        );

        $childrenIds = $adapter->fetchCol($select, ['c_path' => $object->getPath() . '/%']);

        if (!empty($childrenIds)) {
            $adapter->delete($this->getMainTable(), ['category_id IN (?)' => $childrenIds]);
        }

        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);

        return $this;
    }

    /**
     * @param CategoryModel $category
     * @param CategoryModel $newParent
     * @param null $afterCategoryId
     *
     * @return $this
     * @throws LocalizedException
     */
    public function changeParent(
        CategoryModel $category,
        CategoryModel $newParent,
        $afterCategoryId = null
    ) {
        $childrenCount = (int) $this->getChildrenCount($category->getId()) + 1;
        $table         = $this->getMainTable();
        $adapter       = $this->getConnection();
        $levelField    = $adapter->quoteIdentifier('level');
        $pathField     = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old Community Category parent Categories
         */
        $adapter->update(
            $table,
            ['children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)],
            ['category_id IN(?)' => $category->getParentIds()]
        );

        /**
         * Increase children count for new Community Category parents
         */
        $adapter->update(
            $table,
            ['children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)],
            ['category_id IN(?)' => $newParent->getPathIds()]
        );

        $position         = $this->processPositions($category, $newParent, $afterCategoryId);
        $newPath          = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        $newLevel         = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            [
                'path'  => new Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $adapter->quote(
                        $category->getPath() . '/'
                    ) . ', ' . $adapter->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                'level' => new Zend_Db_Expr($levelField . ' + ' . $levelDisposition)
            ],
            [$pathField . ' LIKE ?' => $category->getPath() . '/%']
        );
        /**
         * Update moved Community Category data
         */
        $data = [
            'path'      => $newPath,
            'level'     => $newLevel,
            'position'  => $position,
            'parent_id' => $newParent->getId(),
        ];
        $adapter->update($table, $data, ['category_id = ?' => $category->getId()]);

        /** Update Community Category object to new data */
        $category->addData($data);
        $category->unsetData('path_ids');

        return $this;
    }

    /**
     * @param CategoryModel $category
     * @param CategoryModel $newParent
     * @param int|null $afterCategoryId
     *
     * @return int|string
     * @throws LocalizedException
     */
    public function processPositions(
        CategoryModel $category,
        CategoryModel $newParent,
        $afterCategoryId
    ) {
        $table   = $this->getMainTable();
        $connect = $this->getConnection();
        /** Get old category position */
        $positionOld = $category->getPosition();
        /** Get new category position */
        if (empty($afterCategoryId)) {
            $positionNew = 1;
        } else {
            $select      = $connect->select()->from($table, 'position')->where('category_id = :category_id');
            $positionNew = $connect->fetchOne($select, ['category_id' => $afterCategoryId]);
        }

        /** Update position when the item is moved */
        /** Move to other category parent */
        if ($category->getParentId() != $newParent->getId()) {
            if ($afterCategoryId == 0) {
                $positionNew = 0;
            }
            $positionNew++;
            // phpcs:disable Magento2.SQL.RawQuery
            $sql = "UPDATE `" . $table . "` SET `position`= (`position`-1) WHERE `parent_id`= "
                . $category->getParentId() . " AND `position` >= " . $positionOld;
            $connect->query($sql);
            $sql = "UPDATE `" . $table . "` SET `position`= (`position`+1) WHERE `parent_id`= " . $newParent->getId()
                . " AND `position` >= " . $positionNew;
            $connect->query($sql);
        } else {
            /** Move in the same parent */
            /** Move down */
            if ($positionNew > $positionOld) {
                $sql = "UPDATE `" . $table . "` SET `position`= (`position`-1) WHERE `parent_id`= "
                    . $newParent->getId() . " AND `position` <= " . $positionNew;
                $connect->query($sql);
                $sql = "UPDATE `" . $table . "` SET `position`= (`position`+1) WHERE `parent_id`= "
                    . $newParent->getId() . " AND `position` < " . $positionOld;
                $connect->query($sql);
            } else {
                /** Move up */
                $positionNew++;
                if (empty($afterCategoryId)) {
                    $positionNew = 1;
                }
                $sql = "UPDATE `" . $table . "` SET `position`= (`position`+1) WHERE `parent_id`= "
                    . $newParent->getId() . " AND `position` >= " . $positionNew;
                $connect->query($sql);
                $sql = "UPDATE `" . $table . "` SET `position`= (`position`-1) WHERE `parent_id`= "
                    . $newParent->getId() . " AND `position` > " . $positionOld;
                $connect->query($sql);
            }
        }

        return $positionNew;
    }

    /**
     * @param int $categoryId
     *
     * @return string
     * @throws LocalizedException
     */
    public function getChildrenCount($categoryId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            'children_count'
        )->where(
            'category_id = :category_id'
        );
        $bind   = ['category_id' => $categoryId];

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * @param CategoryModel $category
     *
     * @return array
     */
    public function getIdeasPosition(CategoryModel $category)
    {
        $select = $this->getConnection()->select()->from(
            $this->categoryIdeaTable,
            ['idea_id', 'position']
        )
            ->where(
                'category_id = :category_id'
            );
        $bind   = ['category_id' => (int) $category->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * @param CategoryModel $category
     *
     * @return $this
     */
    public function saveIdeaRelation(CategoryModel $category)
    {
        $category->setIsChangedIdeaList(false);
        $id    = $category->getId();
        $ideas = $category->getIdeasData();
        if ($ideas === null) {
            return $this;
        }
        $oldIdeas = $category->getIdeasPosition();
        $insert   = array_diff_key($ideas, $oldIdeas);
        $delete   = array_diff_key($oldIdeas, $ideas);
        $update   = array_intersect_key($ideas, $oldIdeas);
        $_update  = [];
        foreach ($update as $key => $position) {
            if (isset($oldIdeas[$key]) && $oldIdeas[$key] != $position) {
                $_update[$key] = $position;
            }
        }
        $update  = $_update;
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = ['idea_id IN(?)' => array_keys($delete), 'category_id=?' => $id];
            $adapter->delete($this->categoryIdeaTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $ideaId => $position) {
                $data[] = [
                    'category_id' => (int) $id,
                    'idea_id'     => (int) $ideaId,
                    'position'    => (int) $position
                ];
            }
            $adapter->insertMultiple($this->categoryIdeaTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $ideaId => $position) {
                $where = ['category_id = ?' => (int) $id, 'idea_id = ?' => (int) $ideaId];
                $bind  = ['position' => (int) $position];
                $adapter->update($this->categoryIdeaTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $ideaIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'community_category_change_ideas',
                ['category' => $category, 'idea_ids' => $ideaIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedIdeaList(true);
            $ideaIds = array_keys($insert + $delete + $update);
            $category->setAffectedIdeaIds($ideaIds);
        }

        return $this;
    }

    /**
     * @param string $importType
     *
     * @throws LocalizedException
     */
    public function deleteImportItems($importType)
    {
        $adapter = $this->getConnection();
        $adapter->delete($this->getMainTable(), "`import_source` LIKE '" . $importType . "%'");
    }
}
