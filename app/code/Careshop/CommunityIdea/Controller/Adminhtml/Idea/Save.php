<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Idea;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Careshop\CommunityIdea\Controller\Adminhtml\Idea;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\Idea as IdeaModel;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaHistoryFactory;
use RuntimeException;

class Save extends Idea
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
    protected $timezone;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param IdeaFactory $ideaFactory
     * @param Js $jsHelper
     * @param Image $imageHelper
     * @param Data $helperData
     * @param IdeaHistoryFactory $ideaHistory
     * @param DateTime $date
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IdeaFactory $ideaFactory,
        Js $jsHelper,
        Image $imageHelper,
        Data $helperData,
        IdeaHistoryFactory $ideaHistory,
        DateTime $date,
        TimezoneInterface $timezone
    ) {
        $this->jsHelper     = $jsHelper;
        $this->_helperData  = $helperData;
        $this->_ideaHistory = $ideaHistory;
        $this->imageHelper  = $imageHelper;
        $this->date         = $date;
        $this->timezone     = $timezone;

        parent::__construct($ideaFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $action         = $this->getRequest()->getParam('action');

        if ($data = $this->getRequest()->getPost('idea')) {
            /** @var IdeaModel $idea */
            $idea = $this->initIdea(false, true);
            $this->prepareData($idea, $data);

            $this->_eventManager->dispatch(
                'community_idea_prepare_save',
                ['idea' => $idea, 'request' => $this->getRequest()]
            );

            try {
                if (empty($action) || $action === 'add') {
                    $idea->save();
                    $this->messageManager->addSuccessMessage(__('The idea has been saved.'));
                }
                $this->addHistory($idea, $action);

                $this->_getSession()->setData('community_idea_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('community/*/edit', ['id' => $idea->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('community/*/');
                }

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Idea.'));
            }

            $this->_getSession()->setData('community_idea_data', $data);

            $resultRedirect->setPath('community/*/edit', ['id' => $idea->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('community/*/');

        return $resultRedirect;
    }

    /**
     * @param IdeaModel $idea
     * @param null $action
     */
    protected function addHistory($idea, $action = null)
    {
        if (!empty($action)) {
            $history      = $this->_ideaHistory->create();
            $historyCount = $history->getSumIdeaHistory($idea->getIdeaId());
            $limitHistory = (int)$this->_helperData->getConfigGeneral('history_limit');
            try {
                $data = $idea->getData();
                unset(
                    $data['is_changed_tag_list'],
                    $data['is_changed_topic_list'],
                    $data['is_changed_category_list'],
                    $data['is_changed_product_list']
                );
                if ($isSave = $this->checkHistory($data)) {
                    $this->messageManager->addErrorMessage(__(
                        'Record Id %1 like the one you want to save.',
                        $isSave->getId()
                    ));
                } else {
                    if ($historyCount >= $limitHistory) {
                        $history->removeFirstHistory($idea->getIdeaId());
                    }
                    $history->addData($data);
                    $history->save();
                }
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Idea History.')
                );
            }
        }
    }

    /**
     * @param array $data
     *
     * @return DataObject|null
     */
    protected function checkHistory($data)
    {
        unset($data['updated_at']);
        $historyItems = $this->_ideaHistory->create()->getCollection()
            ->addFieldToFilter('idea_id', $data['idea_id'])->getItems();

        if (count($historyItems) < 1) {
            return null;
        }
        $data['category_ids'] = implode(',', $data['categories_ids']);
        $data['topic_ids']    = implode(',', $data['topics_ids']);
        $data['tag_ids']      = implode(',', $data['tags_ids']);
        $data['product_ids']  = Data::jsonEncode($data['products_data']);

        $result = null;
        foreach ($historyItems as $historyItem) {
            $subReturn = false;
            foreach ($historyItem->getData() as $key => $value) {
                if (array_key_exists($key, $data)) {
                    if (is_array($data[$key])) {
                        $data[$key] = trim(implode(',', $data[$key]), ',');
                    }
                    if ($data[$key] === null) {
                        $data[$key] = '';
                    }
                    if ($value === null) {
                        $value = '';
                    }
                    if ($data[$key] !== $value) {
                        $subReturn = true;
                        break;
                    }
                }
            }

            if (!$subReturn) {
                $result = $historyItem;
                break;
            }
        }

        return $result;
    }

    /**
     * @param IdeaModel $idea
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
        try {
            $data['publish_date'] = $this->timezone->convertConfigTimeToUtc($data['publish_date']);
        } catch (Exception $e) {
            $data['publish_date'] = $this->timezone->convertConfigTimeToUtc($this->date->date());
        }

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

        if ($idea->getCreatedAt() == null) {
            $data['created_at'] = $this->date->date();
        }
        $data['updated_at'] = $this->date->date();

        $idea->addData($data);

        if ($tags = $this->getRequest()->getPost('tags', false)) {
            $idea->setTagsData(
                $this->jsHelper->decodeGridSerializedInput($tags)
            );
        }

        if ($topics = $this->getRequest()->getPost('topics', false)) {
            $idea->setTopicsData(
                $this->jsHelper->decodeGridSerializedInput($topics)
            );
        }

        $products = $this->getRequest()->getPost('products', false);

        if ($products || $products === '') {
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
