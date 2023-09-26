<?php

namespace Careshop\CommunityIdea\Block;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Theme\Block\Html\Pager;
use Careshop\CommunityIdea\Model\Config\Source\DisplayType;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;

class Listidea extends Frontend
{
    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getIdeaCollection()
    {
        $collection = $this->getCollection();

        if ($collection && $collection->getSize()) {
            $pager = $this->getLayout()->createBlock(Pager::class, 'community.idea.pager');

            $perPageValues = (string)$this->helperData->getConfigGeneral('pagination');
            $perPageValues = explode(',', $perPageValues);
            $perPageValues = array_combine($perPageValues, $perPageValues);

            $pager->setAvailableLimit($perPageValues)
                ->setCollection($collection);

            $this->setChild('pager', $pager);
        }

        return $collection;
    }

    public function getTopics($idea_id=null) 
    {
        $pager = $this->getLayout()->createBlock(Pager::class, 'community.topics.pager');
        $perPageValues = (string)$this->helperData->getConfigGeneral('pagination');
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);

        $collection =  $this->helperData->getTopics($idea_id);
        $pager->setAvailableLimit($perPageValues)
                ->setCollection($collection);
            $this->setChild('pager', $pager);

        return $collection;
    }

    public function getPostByIdea($idea_id) {
        $collection =  $this->helperData->getTopics($idea_id)
                        ->setOrder('topic_id','DESC');

        if($collection) {
            return  $collection->getFirstItem();
        } else {
            return array();
        }

    }

    public function getNumberPostByIdea($idea_id) {
        $collection =  $this->helperData->getTopics($idea_id)
                        ->setOrder('topic_id','ASC');

        if($collection) {
            return  $collection->count();
        } else {
            return 0;
        }

    }


    /**
     * find /n in text
     *
     * @param $description
     *
     * @return string
     */
    public function maxShortDescription($description)
    {
        if (is_string($description)) {
            $html = '';
            foreach (explode("\n", trim($description)) as $value) {
                $html .= '<p>' . $value . '</p>';
            }

            return $html;
        }

        return $description;
    }

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        try {
            return $this->helperData->getIdeaCollection(null, null, $this->store->getStore()->getId());
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return bool
     */
    public function isGridView()
    {
        return $this->helperData->getCommunityConfig('general/display_style') == DisplayType::GRID;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Community Overview'),
                'title' => __('Go to Community Overview'),
                'link' => $this->_storeManager->getStore()->getBaseUrl().'community'
            ])
                ->addCrumb($this->helperData->getRoute(), $this->getBreadcrumbsData());
        }

        $this->applySeoCode();

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $label = __('Share Product Ideas');

        $data = [
            'label' => $label,
            'title' => $label
        ];

        if ($this->getRequest()->getFullActionName() !== 'community_idea_index') {
            $data['link'] = $this->_storeManager->getStore()->getBaseUrl().'community/idea';
        }

        return $data;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function applySeoCode()
    {
        $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($this->getCommunityTitle(true))));

        $object = $this->getCommunityObject();

        $description = $object ? $object->getMetaDescription() : $this->helperData->getSeoConfig('meta_description');
        $this->pageConfig->setDescription($description);

        $keywords = $object ? $object->getMetaKeywords() : $this->helperData->getSeoConfig('meta_keywords');
        $this->pageConfig->setKeywords($keywords);

        $robots = $object ? $object->getMetaRobots() : $this->helperData->getSeoConfig('meta_robots');
        $this->pageConfig->setRobots($robots);

        if ($this->getRequest()->getFullActionName() === 'community_idea_view') {
            $this->pageConfig->addRemotePageAsset(
                $object->getUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->getCommunityTitle());
        }

        return $this;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @return string
     */
    public function getTitleSeparator()
    {
        $separator = (string)$this->helperData->getConfigValue('catalog/seo/title_separator');

        return ' ' . $separator . ' ';
    }

    /**
     * @param bool $meta
     *
     * @return array|Phrase
     */
    public function getCommunityTitle($meta = false)
    {
        $pageTitle =  __('Share Product Ideas');
        if( $this->getRequest()->getParam('id') ) 
        {
            $idea = $this->ideaFactory->create()->load($this->getRequest()->getParam('id'));
            $pageTitle = $idea->getName();
        }
        if ($meta) {
            $title = $this->helperData->getSeoConfig('meta_title') ?: $pageTitle;

            return [$title];
        }

        return $pageTitle;
    }

    public function getIdeaUrl($id)
    {
        if($id >0) {
			
			$idea = $this->ideaFactory->create()->load($id);
            //echo "<pre>"; print_r($idea->getData()); die;
			if($idea->getData('url_key') !="") {
				return $this->getUrl('community/idea/'.$idea->getUrlKey());
			} else {
				return $this->getUrl('community/idea/view', ['id'=>$id]);
			}
		}
		return null;
    }
    public function getAuthorUrl($id = null) 
    {
        if( $id > 0 ) {
			return $this->getUrl('community/author/information', ['id' => $id]);
		}
		return null;
    }

    public function getIdeaInfoByAuthor($author_id)
    {
        if($author_id)
        {
            $idea = $this->ideaFactory->create()->getCollection()
                ->addFieldToFilter('author_id',$author_id);
            if($idea->count())
            {
                return $idea->getLastItem();
            } else {
                return array();
            }

        }
    }
}
