<?php

namespace Careshop\CommunityIdea\Setup;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\Author;
use Careshop\CommunityIdea\Model\CategoryFactory;
use Careshop\CommunityIdea\Model\CommentFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * @var CommentFactory
     */
    protected $comment;

    /**
     * @var AuthorFactory
     */
    protected $author;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * UpgradeData constructor.
     *
     * @param DateTime $date
     * @param CommentFactory $commentFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AuthorFactory $authorFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        DateTime $date,
        CommentFactory $commentFactory,
        CustomerRepositoryInterface $customerRepository,
        AuthorFactory $authorFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->comment            = $commentFactory;
        $this->author             = $authorFactory;
        $this->date               = $date;
        $this->customerRepository = $customerRepository;
        $this->categoryFactory    = $categoryFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.4.4', '<')) {
            $commentIds = $this->comment->create()->getCollection()->getAllIds();
            $commentIds = implode(',', $commentIds);
            if ($commentIds) {
                /** Add create at old comment */
                $sampleTemplates = [
                    'created_at' => $this->date->date(),
                    'status'     => 3
                ];
                $setup->getConnection()->update(
                    $setup->getTable('community_comment'),
                    $sampleTemplates,
                    'comment_id IN (' . $commentIds . ')'
                );
            }
        }

        if (!$this->author->create()->getCollection()->getSize()) {
            $this->author->create()->addData(
                [
                    'name'       => 'Admin',
                    'type'       => 0,
                    'status'     => 1,
                    'created_at' => $this->date->date()
                ]
            )->save();
        }

        if (!$this->categoryFactory->create()->getCollection()->getSize()) {
            $this->categoryFactory->create()->addData(
                [
                    'path'           => '1',
                    'position'       => 0,
                    'children_count' => 0,
                    'level'          => 0,
                    'name'           => 'ROOT',
                    'url_key'        => 'root'
                ]
            )->save();
        }

        if (version_compare($context->getVersion(), '2.5.3', '<')) {
            /** @var Author $author */
            foreach ($this->author->create()->getCollection()->getItems() as $author) {
                if ($customerId = $author->getCustomerId()) {
                    try {
                        $author->setEmail($this->customerRepository->getById($customerId)->getEmail());
                        $author->save();
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }

        $installer->endSetup();
    }
}
