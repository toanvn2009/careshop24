<?php

namespace Careshop\CommunityIdea\Observer;

use Exception;
use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\AuthorFactory;

class CreateAuthor implements ObserverInterface
{

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var AuthorFactory
     */
    protected $author;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Data $helper
     * @param AuthorFactory $authorFactory
     * @param ManagerInterface $manager
     */
    public function __construct(
        Data $helper,
        AuthorFactory $authorFactory,
        ManagerInterface $manager
    ) {
        $this->author = $authorFactory;
        $this->_helper = $helper;
        $this->messageManager = $manager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $accountController = $observer->getData('account_controller');
        $customer = $observer->getData('customer');

        /** @var CreateIdea $accountController */
        if ($this->_helper->isEnabled() && $accountController->getRequest()->getParam('is_community_author')) {
            $data = [
                'customer_id' => $customer->getId(),
                'name' => $customer->getFirstname(),
                'type' => '1',
                'status' => $this->_helper->getConfigGeneral('auto_approve') ? 1 : 0
            ];
            $author = $this->author->create();
            $author->addData($data);
            try {
                $author->save();
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Author.'));
            }
        }
    }
}
