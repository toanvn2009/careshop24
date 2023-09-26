<?php

namespace Careshop\CommunityIdea\Api;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CommunityRepositoryInterface
{
    /**
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getAllIdea();

    /**
     * @return \Careshop\CommunityIdea\Api\Data\MonthlyArchiveInterface[]
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getMonthlyArchive();

    /**
     * @param string $monthly
     * @param string $year
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeaMonthlyArchive($monthly, $year);

    /**
     * @param string $ideaId
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface
     * @throws Exception
     */
    public function getIdeaView($ideaId);

    /**
     * @param string $authorName
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeaViewByAuthorName($authorName);

    /**
     * @param string $authorId
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeaViewByAuthorId($authorId);

    /**
     * @param string $ideaId
     *
     * @return \Careshop\CommunityIdea\Api\Data\CommentInterface[]
     */
    public function getIdeaComment($ideaId);

    /**
     * Get All Comment
     *
     * @return \Careshop\CommunityIdea\Api\Data\CommentInterface[]
     */
    public function getAllComment();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Careshop\CommunityIdea\Api\Data\SearchResult\CommentSearchResultInterface
     */
    public function getCommentList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $commentId
     *
     * @return \Careshop\CommunityIdea\Api\Data\CommentInterface
     */
    public function getCommentView($commentId);

    /**
     * @param string $ideaId
     *
     * @return string
     */
    public function getIdeaLike($ideaId);

    /**
     * @param string $tagName
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeaByTagName($tagName);

    /**
     * @param string $ideaId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductByIdea($ideaId);

    /**
     * @param string $ideaId
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdeaRelated($ideaId);

    /**
     * @param string $ideaId
     * @param \Careshop\CommunityIdea\Api\Data\CommentInterface $commentData
     *
     * @return \Careshop\CommunityIdea\Api\Data\CommentInterface
     * @throws Exception
     */
    public function addCommentInIdea($ideaId, $commentData);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Careshop\CommunityIdea\Api\Data\SearchResult\IdeaSearchResultInterface
     * @throws NoSuchEntityException
     */
    public function getIdeaList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Idea
     *
     * @param \Careshop\CommunityIdea\Api\Data\IdeaInterface $idea
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface
     * @throws Exception
     */
    public function createIdea($idea);

    /**
     * Delete Idea
     *
     * @param string $ideaId
     *
     * @return string
     */
    public function deleteIdea($ideaId);

    /**
     * @param string $ideaId
     * @param \Careshop\CommunityIdea\Api\Data\IdeaInterface $idea
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateIdea($ideaId, $idea);

    /**
     * Get All Tag
     *
     * @return \Careshop\CommunityIdea\Api\Data\TagInterface[]
     */
    public function getAllTag();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Careshop\CommunityIdea\Api\Data\SearchResult\TagSearchResultInterface
     */
    public function getTagList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Idea
     *
     * @param \Careshop\CommunityIdea\Api\Data\TagInterface $tag
     *
     * @return \Careshop\CommunityIdea\Api\Data\TagInterface
     * @throws Exception
     */
    public function createTag($tag);

    /**
     * Delete Tag
     *
     * @param string $tagId
     *
     * @return string
     */
    public function deleteTag($tagId);

    /**
     * @param string $tagId
     *
     * @return \Careshop\CommunityIdea\Api\Data\TagInterface
     */
    public function getTagView($tagId);

    /**
     * @param string $tagId
     * @param \Careshop\CommunityIdea\Api\Data\TagInterface $tag
     *
     * @return \Careshop\CommunityIdea\Api\Data\TagInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTag($tagId, $tag);

    /**
     * Get Topic List
     *
     * @return \Careshop\CommunityIdea\Api\Data\TopicInterface[]
     */
    public function getAllTopic();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Careshop\CommunityIdea\Api\Data\SearchResult\TopicSearchResultInterface
     */
    public function getTopicList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $topicId
     *
     * @return \Careshop\CommunityIdea\Api\Data\TagInterface
     */
    public function getTopicView($topicId);

    /**
     * @param string $topicId
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeasByTopic($topicId);

    /**
     * Create Topic
     *
     * @param \Careshop\CommunityIdea\Api\Data\TopicInterface $topic
     *
     * @return \Careshop\CommunityIdea\Api\Data\TopicInterface
     * @throws Exception
     */
    public function createTopic($topic);

    /**
     * Delete Topic
     *
     * @param string $topicId
     *
     * @return string
     */
    public function deleteTopic($topicId);

    /**
     * @param string $topicId
     * @param \Careshop\CommunityIdea\Api\Data\TopicInterface $topic
     *
     * @return \Careshop\CommunityIdea\Api\Data\TopicInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTopic($topicId, $topic);

    /**
     * Get All Category
     *
     * @return \Careshop\CommunityIdea\Api\Data\CategoryInterface[]
     */
    public function getAllCategory();

    /**
     * Get Category List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Careshop\CommunityIdea\Api\Data\SearchResult\CategorySearchResultInterface
     */
    public function getCategoryList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $categoryId
     *
     * @return \Careshop\CommunityIdea\Api\Data\CategoryInterface
     */
    public function getCategoryView($categoryId);

    /**
     * @param string $categoryId
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeasByCategoryId($categoryId);

    /**
     * @param string $categoryKey
     *
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getIdeasByCategory($categoryKey);

    /**
     * @param string $ideaId
     *
     * @return \Careshop\CommunityIdea\Api\Data\CategoryInterface[]
     */
    public function getCategoriesByIdeaId($ideaId);

    /**
     * Create Category
     *
     * @param \Careshop\CommunityIdea\Api\Data\CategoryInterface $category
     *
     * @return \Careshop\CommunityIdea\Api\Data\CategoryInterface
     * @throws Exception
     */
    public function createCategory($category);

    /**
     * Delete Category
     *
     * @param string $categoryId
     *
     * @return string
     */
    public function deleteCategory($categoryId);

    /**
     * @param string $categoryId
     * @param \Careshop\CommunityIdea\Api\Data\CategoryInterface $category
     *
     * @return \Careshop\CommunityIdea\Api\Data\CategoryInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateCategory($categoryId, $category);

    /**
     * Get Author List
     *
     * @return \Careshop\CommunityIdea\Api\Data\AuthorInterface[]
     */
    public function getAuthorList();

    /**
     * Create Author
     *
     * @param \Careshop\CommunityIdea\Api\Data\AuthorInterface $author
     *
     * @return \Careshop\CommunityIdea\Api\Data\AuthorInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAuthor($author);

    /**
     * Delete Author
     *
     * @param string $authorId
     *
     * @return string
     */
    public function deleteAuthor($authorId);

    /**
     * @param string $authorId
     * @param \Careshop\CommunityIdea\Api\Data\AuthorInterface $author
     *
     * @return \Careshop\CommunityIdea\Api\Data\AuthorInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateAuthor($authorId, $author);

    /**
     * @return \Careshop\CommunityIdea\Api\Data\CommunityConfigInterface
     *
     * @throws InputException
     */
    public function getConfig();
}
