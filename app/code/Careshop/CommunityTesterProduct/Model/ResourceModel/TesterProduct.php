<?php

namespace Careshop\CommunityTesterProduct\Model\ResourceModel;

use Magento\Backend\Model\Auth;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityTesterProduct\Model\TesterProduct as TesterProductModel;

class TesterProduct extends AbstractDb
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
        $this->_init('community_tester_product', 'entity_id');
    }
}
