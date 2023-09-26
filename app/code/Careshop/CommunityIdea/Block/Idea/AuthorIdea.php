<?php

namespace Careshop\CommunityIdea\Block\Idea;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\Idea;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;

class AuthorIdea extends \Careshop\CommunityIdea\Block\Listidea
{
    /**
     * @return AbstractCollection|Collection|null
     */
    public function getIdeaCollection()
    {
        try {
            $collection = $this->helperData->getFactoryByType()->create()->getCollection();
            $this->helperData->addStoreFilter($collection, $this->store->getStore()->getId());

            $userId = $this->getAuthor()->getId();

            $collection->addFieldToFilter('author_id', $userId);

            if ($collection && $collection->getSize()) {
                $pager         = $this->getLayout()->createBlock(Pager::class, 'community.idea.pager');
                $perPageValues = (string) $this->helperData->getConfigGeneral('pagination');
                $perPageValues = explode(',', $perPageValues);
                $perPageValues = array_combine($perPageValues, $perPageValues);

                $pager->setAvailableLimit($perPageValues)->setCollection($collection);
                $this->setChild('pager', $pager);
            }
        } catch (Exception $e) {
            $collection = null;
        }

        return $collection;
    }

    /**
     * @param string $statusId
     *
     * @return mixed
     */
    public function getStatusHtmlById($statusId)
    {
        $statusText = $this->authorStatusType->toArray()[$statusId]->getText();

        switch ($statusId) {
            case '2':
                $html = '<div class="cscm-post-status cscm-post-disapproved">' . __($statusText) . '</div>';
                break;
            case '1':
                $html = '<div class="cscm-post-status cscm-post-approved">' . __($statusText) . '</div>';
                break;
            case '0':
            default:
                $html = '<div class="cscm-post-status cscm-post-pending">' . __($statusText) . '</div>';
                break;
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $array = explode('/', $this->helperData->getConfigValue('cms/wysiwyg/editor'));
        if ($array[count($array) - 1] === 'tinymce4Adapter') {
            return 4;
        }

        return 3;
    }

    /**
     * @return int
     */
    public function getMagentoVersion()
    {
        return (int) $this->helperData->versionCompare('2.3.0') ? 3 : 2;
    }

    /**
     * @param AbstractCollection|Collection|null $ideaCollection
     *
     * @return string
     */
    public function getIdeaDatas($ideaCollection)
    {
        $result = [];

        if ($ideaCollection) {
            try {
                /** @var Idea $idea */
                foreach ($ideaCollection->getItems() as $idea) {
                    $idea->getCategoryIds();
                    $idea->getTopicIds();
                    $idea->getTagIds();
                    if ($idea->getIdeaContent()) {
                        $idea->setData('idea_content', $this->getPageFilter($idea->getIdeaContent()));
                    }
                    $result[$idea->getId()] = $idea->getData();
                }
            } catch (Exception $e) {
                $result = [];
            }
        }

        return Data::jsonEncode($result);
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->getAuthor()->getName();
    }

    /**
     * @return bool
     */
    public function getAuthorStatus()
    {
        $author = $this->getAuthor();

        return $author->getStatus() === '1';
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->coreRegistry->registry('community_author');
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getCommunityTitle($meta = false)
    {
        return $meta ? [$this->getAuthor()->getName()] : $this->getAuthor()->getName();
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
