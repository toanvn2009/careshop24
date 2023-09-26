<?php

namespace Careshop\CommunityIdea\Api\Data;

interface CommentInterface
{
    /**
     * Constants used as data array keys
     */
    const COMMENT_ID    = 'comment_id';
    const IDEA_ID       = 'idea_id';
    const CONTENT       = 'content';
    const STORE_IDS     = 'store_ids';
    const CREATED_AT    = 'created_at';
    const STATUS        = 'status';
    const IMPORT_SOURCE = 'import_source';
    const USER_NAME     = 'user_name';
    const USER_EMAIL    = 'user_email';

    const ATTRIBUTES = [
        self::COMMENT_ID,
        self::IDEA_ID,
        self::CONTENT,
        self::STORE_IDS,
        self::CREATED_AT,
        self::USER_EMAIL,
        self::USER_NAME,
        self::IMPORT_SOURCE
    ];

    /**
     * Get Comment id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Comment Id
     *
     * @param int $commentId
     *
     * @return $this
     */
    public function setId($commentId);

    /**
     * Get Idea id
     *
     * @return int|null
     */
    public function getIdeaId();

    /**
     * Set Idea Id
     *
     * @param int $ideaId
     *
     * @return $this
     */
    public function setIdeaId($ideaId);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status Id
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Comment content
     *
     * @return string/null
     */
    public function getContent();

    /**
     * Set Content
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * Get Idea Store Id
     *
     * @return int/null
     */
    public function getStoreIds();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setStoreIds($id);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * @return string|null
     */
    public function getImportSource();

    /**
     * @return string|null
     */
    public function getUserEmail();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setUserEmail($email);

    /**
     * @return string|null
     */
    public function getUserName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setUserName($name);
}
