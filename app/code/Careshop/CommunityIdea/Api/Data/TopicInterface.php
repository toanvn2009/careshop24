<?php

namespace Careshop\CommunityIdea\Api\Data;

interface TopicInterface
{
    /**
     * Constants used as data array keys
     */
    const TOPIC_ID         = 'topic_id';
    const NAME             = 'name';
    const DESCRIPTION      = 'description';
    const STORE_IDS        = 'store_ids';
    const URL_KEY          = 'url_key';
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS    = 'meta_keywords';
    const META_ROBOTS      = 'meta_robots';
    const UPDATED_AT       = 'updated_at';
    const CREATED_AT       = 'created_at';
    const IMPORT_SOURCE    = 'import_source';

    const ATTRIBUTES = [
        self::TOPIC_ID,
        self::NAME,
        self::DESCRIPTION,
        self::STORE_IDS,
        self::URL_KEY,
        self::META_TITLE,
        self::META_DESCRIPTION,
        self::META_KEYWORDS,
        self::META_ROBOTS,
        self::IMPORT_SOURCE
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
     * Get Idea Description
     *
     * @return string/null
     */
    public function getDescription();

    /**
     * Set Idea Short Description
     *
     * @param string $content
     *
     * @return $this
     */
    public function setDescription($content);

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
     * @return string|null
     */
    public function getImportSource();

    /**
     * @param string $importSource
     *
     * @return $this
     */
    public function setImportSource($importSource);
}
