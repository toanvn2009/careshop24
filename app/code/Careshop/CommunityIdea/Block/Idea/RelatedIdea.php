<?php

namespace Careshop\CommunityIdea\Block\Idea;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;

class RelatedIdea extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Collection
     */
    protected $_relatedIdeas;

    /**
     * @var int
     */
    protected $_limitIdea;

    /**
     * RelatedIdea constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param array $data
     *
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helperData = $helperData;

        parent::__construct($context, $data);

        $this->setTabTitle();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');

        return $product ? $product->getId() : null;
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getRelatedIdeaList()
    {
        if ($this->_relatedIdeas == null) {
            /** @var Collection $collection */
            $collection = $this->helperData->getIdeaList();
            $collection->getSelect()
                ->join(
                    [
                        'related' => $collection->getTable('community_idea_product')
                    ],
                    'related.idea_id=main_table.idea_id AND related.entity_id=' . $this->getProductId()
                )
                ->limit($this->getLimitIdeas());

            $this->_relatedIdeas = $collection;
        }

        return $this->_relatedIdeas;
    }

    /**
     * @return int|mixed
     */
    public function getLimitIdeas()
    {
        if (!$this->_limitIdea) {
            $this->_limitIdea = (int)$this->helperData->getCommunityConfig('product_idea/product_detail/idea_limit') ?: 1;
        }

        return $this->_limitIdea;
    }

    /**
     * Set tab title
     *
     * @return void
     * @throws NoSuchEntityException
     */
    public function setTabTitle()
    {
        $relatedSize = min($this->getRelatedIdeaList()->getSize(), $this->getLimitIdeas());
        $title = $relatedSize
            ? __('Related Ideas %1', '<span class="counter">' . $relatedSize . '</span>')
            : __('Related Ideas');
        if ($this->helperData->isEnabled()) {
            $this->setTitle($title);
        }
    }

    /**
     * @return bool
     */
    public function isEnabledCommunity()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * @return bool
     */
    public function getRelatedMode()
    {
        return (int)$this->helperData->getConfigGeneral('related_mode') === 1;
    }

    /**
     * Resize Image Function
     *
     * @param $image
     * @param null $size
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function resizeImage($image, $size = null, $type = Image::TEMPLATE_MEDIA_TYPE_IDEA)
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->helperData->getImageHelper()->resizeImage($image, $size, $type);
    }

    /**
     * get default image url
     */
    public function getDefaultImageUrl()
    {
        return $this->getViewFileUrl('Careshop_CommunityIdea::media/images/mageplaza-logo-default.png');
    }
}
