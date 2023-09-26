<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\History;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Careshop\CommunityIdea\Controller\Adminhtml\History;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\Idea as IdeaModel;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaHistoryFactory;
use RuntimeException;

class Save extends History
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var IdeaHistoryFactory
     */
    protected $_ideaHistory;

    /**
     * @var TimezoneInterface
     */
    protected $_timezone;

    /**
     * Save constructor.
     *
     * @param IdeaHistoryFactory $ideaHistoryFactory
     * @param IdeaFactory $ideaFactory
     * @param Registry $coreRegistry
     * @param DateTime $date
     * @param Js $jsHelper
     * @param Image $imageHelper
     * @param Data $helperData
     * @param TimezoneInterface $timezone
     * @param Context $context
     */
    public function __construct(
        IdeaHistoryFactory $ideaHistoryFactory,
        IdeaFactory $ideaFactory,
        Registry $coreRegistry,
        DateTime $date,
        Js $jsHelper,
        Image $imageHelper,
        Data $helperData,
        TimezoneInterface $timezone,
        Context $context
    ) {
        $this->jsHelper = $jsHelper;
        $this->_helperData = $helperData;
        $this->imageHelper = $imageHelper;
        $this->_timezone = $timezone;

        parent::__construct($ideaHistoryFactory, $ideaFactory, $coreRegistry, $date, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('post')) {
            /** @var IdeaModel $idea */
            $history = $this->initIdeaHistory(false);
            $this->prepareData($history, $data);

            try {
                $history->save();
                $this->messageManager->addSuccessMessage(__('The idea history has been saved.'));

                $resultRedirect->setPath(
                    'community/idea/edit',
                    ['id' => $history->getIdeaId(), '_current' => true]
                );

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Idea.'));
            }
        }

        $resultRedirect->setPath('community/*/edit', [
            'id' => $this->getRequest()->getParam('id'),
            'idea_id' => $this->getRequest()->getParam('idea_id'),
            'history' => true,
            '_current' => true
        ]);

        return $resultRedirect;
    }

    /**
     * @param $idea
     * @param array $data
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function prepareData($idea, $data = [])
    {
        if (!$this->getRequest()->getParam('image')) {
            try {
                $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_IDEA, $idea->getImage());
            } catch (Exception $exception) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        } else {
            $data['image'] = '';
        }

        /** Set specify field data */
        $data['publish_date'] = $this->_timezone->convertConfigTimeToUtc(isset($data['publish_date'])
            ? $data['publish_date'] : null);
        $data['modifier_id'] = $this->_auth->getUser()->getId();
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

        if ($idea->getCreatedAt() === null) {
            $data['created_at'] = $this->date->date();
        }
        $data['updated_at'] = $this->date->date();

        $idea->addData($data);

        if ($products = $this->getRequest()->getPost('products', false)) {
            $idea->setProductsData(
                $this->jsHelper->decodeGridSerializedInput($products)
            );
        } else {
            $productData = [];
            foreach ($idea->getProductsPosition() as $key => $value) {
                $productData[$key] = ['position' => $value];
            }
            $idea->setProductsData($productData);
        }

        return $this;
    }
}
