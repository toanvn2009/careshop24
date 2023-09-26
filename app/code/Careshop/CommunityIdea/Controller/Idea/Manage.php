<?php

namespace Careshop\CommunityIdea\Controller\Idea;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Author\Collection as AuthorCollection;

class Manage extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Data
     */
    protected $_helperCommunity;

    /**
     * @var AuthorCollection
     */
    protected $authorCollection;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var IdeaFactory
     */
    protected $ideaFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param IdeaFactory $ideaFactory
     * @param AuthorCollection $authorCollection
     * @param Session $customerSession
     * @param Registry $coreRegistry
     * @param DateTime $date
     * @param Image $imageHelper
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        IdeaFactory $ideaFactory,
        AuthorCollection $authorCollection,
        Session $customerSession,
        Registry $coreRegistry,
        DateTime $date,
        Image $imageHelper,
        Data $helperData
    ) {
        $this->_helperCommunity = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->authorCollection = $authorCollection;
        $this->customerSession = $customerSession;
        $this->coreRegistry = $coreRegistry;
        $this->ideaFactory = $ideaFactory;
        $this->date = $date;
        $this->imageHelper = $imageHelper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|null
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $this->_helperCommunity->setCustomerContextId();
        $author = $this->_helperCommunity->getCurrentAuthor();
        $idea = $this->ideaFactory->create();

        if (!$author) {
            return null;
        }

        if ($this->getRequest()->getFiles('image')['size'] > 0) {
            try {
                $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_IDEA, $idea->getImage());
            } catch (Exception $exception) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        }

        if (isset($data['image']['delete']) || (isset($data['image']) && $data['image'] === 'null')) {
            $data['image'] = '';
        }

        $data['categories_ids'] = (isset($data['categories_ids']) && $data['categories_ids']) ? explode(
            ',',
            $data['categories_ids']
        ) : [];
        $data['tags_ids'] = (isset($data['tags_ids']) && $data['tags_ids'])
            ? explode(',', $data['tags_ids']) : [];
        $data['topics_ids'] = (isset($data['topics_ids']) && $data['topics_ids']) ? explode(
            ',',
            $data['topics_ids']
        ) : [];

        $data['author_id'] = $author->getId();

        $data['store_ids'] = $this->_helperCommunity->getCurrentStoreId();

        $data['enabled'] = $this->_helperCommunity->getConfigGeneral('auto_idea') ? 1 : 0;

        $data['in_rss'] = '0';

        $data['meta_robots'] = 'INDEX,FOLLOW';

        $data['layout'] = 'empty';

        $data['publish_date'] = !empty($data['publish_date']) ? $data['publish_date'] : $this->date->date();

        if ($data['idea_id']) {
            $idea->load($data['idea_id']);
            if ($idea->getId()) {
                $idea->setData($data);
            }
            $data['updated_at'] = $this->date->date();
        } else {
            unset($data['idea_id']);
            $data['created_at'] = $this->date->date();
            $idea->setData($data);
        }

        try {
            $idea->save();
            $this->messageManager->addSuccessMessage(__('The idea has been saved.'));

            return $this->getResponse()->representJson(Data::jsonEncode([
                'status' => 1
            ]));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $this->getResponse()->representJson(Data::jsonEncode([
                'status' => 0
            ]));
        }
    }
}
