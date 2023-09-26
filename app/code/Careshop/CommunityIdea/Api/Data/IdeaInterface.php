<?php

namespace Careshop\CommunityIdea\Api\Data;

interface IdeaInterface
{
    /**
     * Constants used as data array keys
     */
    const IDEA_ID           = 'idea_id';
    const NAME              = 'name';
    const SHORT_DESCRIPTION = 'short_description';
    const IDEA_CONTENT      = 'idea_content';
    const STORE_IDS         = 'store_ids';
    const IMAGE             = 'image';
    const ENABLED           = 'enabled';
    const URL_KEY           = 'url_key';
    const IN_RSS            = 'in_rss';
    const ALLOW_COMMENT     = 'allow_comment';
    const META_TITLE        = 'meta_title';
    const META_DESCRIPTION  = 'meta_description';
    const META_KEYWORDS     = 'meta_keywords';
    const META_ROBOTS       = 'meta_robots';
    const UPDATED_AT        = 'updated_at';
    const CREATED_AT        = 'created_at';
    const AUTHOR_ID         = 'author_id';
    const MODIFIER_ID       = 'modifier_id';
    const PUBLISH_DATE      = 'publish_date';
    const IMPORT_SOURCE     = 'import_source';
    const LAYOUT            = 'layout';
    const CATEGORY_IDS      = 'category_ids';
    const TAG_IDS           = 'tag_ids';
    const TOPIC_IDS         = 'topic_ids';
    const AUTHOR_URL        = 'author_url';
    const AUTHOR_NAME       = 'author_name';
    const VIEW_TRAFFIC      = 'view_traffic';

    const ATTRIBUTES = [
        self::IDEA_ID,
        self::NAME,
        self::SHORT_DESCRIPTION,
        self::IDEA_CONTENT,
        self::STORE_IDS,
        self::IMAGE,
        self::ENABLED,
        self::URL_KEY,
        self::IN_RSS,
        self::ALLOW_COMMENT,
        self::META_TITLE,
        self::META_DESCRIPTION,
        self::META_KEYWORDS,
        self::META_ROBOTS,
        self::AUTHOR_ID,
        self::MODIFIER_ID,
        self::PUBLISH_DATE,
        self::IMPORT_SOURCE,
        self::LAYOUT,
        self::CATEGORY_IDS,
        self::TAG_IDS,
        self::TOPIC_IDS,
        self::AUTHOR_NAME,
        self::AUTHOR_URL,
        self::VIEW_TRAFFIC
    ];

    /**
     * Get Idea id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Idea id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get Idea Name
     *
     * @return string/null
     */
    public function getName();

    /**
     * Set Idea Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Idea Short Description
     *
     * @return string/null
     */
    public function getShortDescription();

    /**
     * Set Idea Short Description
     *
     * @param string $content
     *
     * @return $this
     */
    public function setShortDescription($content);

    /**
     * Get Idea Content
     *
     * @return string/null
     */
    public function getIdeaContent();

    /**
     * Set Idea Content
     *
     * @param string $content
     *
     * @return $this
     */
    public function setIdeaContent($content);

    /**
     * Get Idea Store Id
     *
     * @return int/null
     */
    public function getStoreIds();

    /**
     * Set Idea Store Id
     *
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreIds($storeId);

    /**
     * Get Idea Image
     *
     * @return string/null
     */
    public function getImage();

    /**
     * Set Idea Image
     *
     * @param string $content
     *
     * @return $this
     */
    public function setImage($content);

    /**
     * Get Idea Enabled
     *
     * @return int/null
     */
    public function getEnabled();

    /**
     * Set Idea Enabled
     *
     * @param int $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled);

    /**
     * Get Idea Url Key
     *
     * @return string/null
     */
    public function getUrlKey();

    /**
     * Set Idea Url Key
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrlKey($url);

    /**
     * Get Idea In RSS
     *
     * @return int/null
     */
    public function getInRss();

    /**
     * Set Idea Enabled
     *
     * @param int $inRss
     *
     * @return $this
     */
    public function setInRss($inRss);

    /**
     * Get Idea Allow Comment
     *
     * @return int/null
     */
    public function getAllowComment();

    /**
     * Set Idea Allow Comment
     *
     * @param int $allow
     *
     * @return $this
     */
    public function setAllowComment($allow);

    /**
     * Get Idea Meta Title
     *
     * @return string/null
     */
    public function getMetaTitle();

    /**
     * Set Idea Meta Title
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaTitle($meta);

    /**
     * Get Idea Meta Description
     *
     * @return string/null
     */
    public function getMetaDescription();

    /**
     * Set Idea Meta Description
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaDescription($meta);

    /**
     * Get Idea Meta Keywords
     *
     * @return string/null
     */
    public function getMetaKeywords();

    /**
     * Set Idea Meta Keywords
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaKeywords($meta);

    /**
     * Get Idea Meta Robots
     *
     * @return string/null
     */
    public function getMetaRobots();

    /**
     * Set Idea Meta Robots
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaRobots($meta);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Idea updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Idea updated date
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Idea Author Id
     *
     * @return int/null
     */
    public function getAuthorId();

    /**
     * Set Idea Store Id
     *
     * @param int $authorId
     *
     * @return $this
     */
    public function setAuthorId($authorId);

    /**
     * Get Idea Modifier Id
     *
     * @return int/null
     */
    public function getModifierId();

    /**
     * Set Idea Modifier Id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setModifierId($id);

    /**
     * get Idea Publish date
     *
     * @return string|null
     */
    public function getPublishDate();

    /**
     * Set Idea Publish date
     *
     * @param string $publishDate
     *
     * @return $this
     */
    public function setPublishDate($publishDate);

    /**
     * @return string|null
     */
    public function getImportSource();

    /**
     * @param string $importSource
     *
     * @return $this
     */
    public function setImportSource($importSource);

    /**
     * @return string|null
     */
    public function getLayout();

    /**
     * @param string $layout
     *
     * @return $this
     */
    public function setLayout($layout);

    /**
     * @return int[]|null
     */
    public function getCategoryIds();

    /**
     * @param int[] $array
     *
     * @return $this
     */
    public function setCategoryIds($array);

    /**
     * @return int[]|null
     */
    public function getTagIds();

    /**
     * @param int[] $array
     *
     * @return $this
     */
    public function setTagIds($array);

    /**
     * @return int[]|null
     */
    public function getTopicIds();

    /**
     * @param int[] $array
     *
     * @return $this
     */
    public function setTopicIds($array);

    /**
     * @return string|null
     */
    public function getAuthorName();

    /**
     * @return string|null
     */
    public function getAuthorUrl();

    /**
     * @return int
     */
    public function getViewTraffic();
}
