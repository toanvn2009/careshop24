<?php

namespace Careshop\MagefanBlog\Block\Post;

use Magento\Store\Model\ScopeInterface;

class View extends \Magefan\Blog\Block\Post\View
{
    protected function _addBreadcrumbs($title = null, $key = null)
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $blogTitle = $this->_scopeConfig->getValue(
                'mfblog/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'blog',
                [
                    'label' => __($blogTitle),
                    'title' => __($blogTitle),
                    'link' => $this->_url->getBaseUrl()
                ]
            );

            $parentCategories = [];
            $parentCategory = $this->getPost()->getParentCategory();
            $post_current = $this->getPost();
            while ($parentCategory) {
                $parentCategories[] = $parentCategory;
                $parentCategory = $parentCategory->getParentCategory();
            }

            for ($i = count($parentCategories) - 1; $i >= 0; $i--) {
                $parentCategory = $parentCategories[$i];
                $post_collections = $this->getPostCollectionByCatId($parentCategory->getId());

                if(count($post_collections) > 0){
                    foreach ($post_collections as $val_post) {
                        if($post_current->getId() != $val_post->getId()){
                            $breadcrumbsBlock->addCrumb('blog_parent_category_' . $parentCategory->getId(), [
                                'label' => $parentCategory->getTitle(),
                                'title' => $parentCategory->getTitle(),
                                'link' => $val_post->getPostUrl()
                            ]);
                            break;
                        }
                    }
                }
            }

            $breadcrumbsBlock->addCrumb($key, [
                'label' => $title ,
                'title' => $title
            ]);
        }
    }

    public function getPostCollectionByCatId($cat_id)
    {
        $post = $this->_postFactory->create();
        $post_collections = $post->getCollection();
        $post_collections->addActiveFilter();
        $post_collections->getSelect()->join(array('magefan_blog_post_category' => 'magefan_blog_post_category'), 'main_table.post_id= magefan_blog_post_category.post_id');
        $post_collections->getSelect()->where("magefan_blog_post_category.category_id=".$cat_id);
        $post_collections->getSelect()->where("main_table.root_post = 1");
        return $post_collections;
    }

    public function getPostCollectionByCatIdAndNotRootPost($cat_id)
    {
        $post = $this->_postFactory->create();
        $post_collections = $post->getCollection();
        $post_collections->addActiveFilter();
        $post_collections->getSelect()->join(array('magefan_blog_post_category' => 'magefan_blog_post_category'), 'main_table.post_id= magefan_blog_post_category.post_id');
        $post_collections->getSelect()->where("magefan_blog_post_category.category_id=".$cat_id);
        return $post_collections;
    }
}