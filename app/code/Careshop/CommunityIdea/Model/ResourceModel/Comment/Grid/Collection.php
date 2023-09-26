<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Comment\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Careshop\CommunityIdea\Model\ResourceModel\Comment;
use Psr\Log\LoggerInterface as Logger;
use Zend_Db_Expr;

class Collection extends SearchResult
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    // phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'community_comment',
        $resourceModel = Comment::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addIdeaName();
        $this->addCustomerName();

        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return SearchResult
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field === 'customer_name') {
            parent::setOrder('	user_name', $direction);
        }

        return parent::setOrder($field, $direction);
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer_name') {
            return parent::addFieldToFilter('user_name', $condition);
        }

        if ($field === 'idea_name') {
            $field = 'mp.name';
        } elseif ($field === 'created_at') {
            $field = 'main_table.created_at';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @return $this
     */
    public function addIdeaName()
    {
        $this->getSelect()->joinLeft(
            ['mp' => $this->getTable('community_idea')],
            'main_table.idea_id = mp.idea_id',
            ['idea_name' => 'name']
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addCustomerName()
    {
        $this->getSelect()->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'main_table.entity_id = ce.entity_id',
            ['firstname', 'lastname']
        )->columns([
            'customer_name' => new Zend_Db_Expr("CONCAT(`ce`.`firstname`,' ',`ce`.`lastname`)")
        ]);

        return $this;
    }
}
