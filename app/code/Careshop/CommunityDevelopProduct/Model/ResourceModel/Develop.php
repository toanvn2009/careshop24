<?php

namespace Careshop\CommunityDevelopProduct\Model\ResourceModel;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityDevelopProduct\Model\Develop as DevelopModel;

class Develop extends AbstractDb
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
        RequestInterface $request
    ) {
        $this->date           = $date;
        $this->eventManager   = $eventManager;
        $this->_auth          = $auth;
        $this->_request       = $request;
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_develop', 'develop_id');
    }

    public function getDevelop($product_id)
    {
        $table_develop_forum = $this->getTable('community_develop');
        $select = $this->getConnection()->select();
        $select->from($table_develop_forum);
        $select->where('product_id = ?', $product_id);
        $community_develop = $this->getConnection()->fetchRow($select);
        return $community_develop;
    } 

    public function getDevelopComment($product_id) 
    {
        $community_develop_forum = $this->getTable('community_develop_forum');
        $select = $this->getConnection()->select();
        $select->from($community_develop_forum);
        $select->where('product_id = ?', $product_id);
        $community_develop_comment = $this->getConnection()->fetchAll($select);
        return count($community_develop_comment);
    }
    
}
