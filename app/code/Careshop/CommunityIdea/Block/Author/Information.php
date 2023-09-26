<?php

namespace Careshop\CommunityIdea\Block\Author;

use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;


class Information extends \Careshop\CommunityIdea\Block\Listidea
{
    /**
     * @var AuthorFactory
     */
    protected $_author;

    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    public function getCollection()
    {
        if ($author = $this->getAuthor()) {
            return $this->helperData->getIdeaCollection(Data::TYPE_AUTHOR, $author->getId());
        }
        return null;
    }

    public function getIdeaListActive($type = null, $storeId = null)
    {
        $id = $this->getRequest()->getParam('id');
        $collection = $this->helperData->getFactoryByType(Data::TYPE_IDEA)
            ->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $id)
            ->addFieldToFilter('enabled', 1)
            ->setOrder('publish_date', 'desc')
            ->setPageSize(5);
        return $collection;

        $collection = $this->helperData->getFactoryByType(Data::TYPE_IDEA)
            ->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $id)
            ->setPageSize(5);
        return $collection;
    }

    public function getIdeaList($type = null, $storeId = null)
    {
        $id = $this->getRequest()->getParam('id');
        $collection = $this->helperData->getFactoryByType(Data::TYPE_IDEA)
            ->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $id)
            ->setPageSize(5);
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        $id = $this->getRequest()->getParam('id'); 
        if ($id) {
            $customer = $this->customer->create()->load($id);
            return $customer;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCustomerProfile($customer_id)
    {
        $profile  = $this->helperData->getCustomerProfile($customer_id);
        return $profile;
    }

    public function getAuthorParams()
    {
        $params = $this->getRequest()->getParams();
        if($params){
            return $params;
        }
        return '';
    }


    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $author = $this->getAuthor();
            if ($author) {
                $breadcrumbs->addCrumb($author->getUrlKey(), [
                    'label' => __('Author'),
                    'title' => __('Author')
                ]);
            }
        }
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getCommunityTitle($meta = false)
    {
        $communityTitle = parent::getCommunityTitle($meta);
        $author = $this->getAuthor();
        if (!$author) {
            return $communityTitle;
        }

        if ($meta) {
            array_push($communityTitle, ucfirst($author->getName()));

            return $communityTitle;
        }

        return ucfirst($author->getName());
    }
}
