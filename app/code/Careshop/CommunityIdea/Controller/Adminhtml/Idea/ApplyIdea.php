<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Idea;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Idea;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\Idea as IdeaModel;
use Careshop\CommunityIdea\Model\IdeaFactory;
use RuntimeException;

class ApplyIdea extends Idea
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    const CHARS_DIGITS = '0123456789';

    protected $resultPageFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param IdeaFactory $ideaFactory
     * @param Js $jsHelper
     * @param Image $imageHelper
     * @param Data $helperData
     * @param DateTime $date
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IdeaFactory $ideaFactory,
        Js $jsHelper,
        Image $imageHelper,
        Data $helperData,
        DateTime $date,
        TimezoneInterface $timezone,
        \Magento\Framework\Math\Random $mathRandom,
        PageFactory $resultPageFactory
    ) {
        $this->jsHelper     = $jsHelper;
        $this->_helperData  = $helperData;
        $this->imageHelper  = $imageHelper;
        $this->date         = $date;
        $this->timezone     = $timezone;
        $this->mathRandom   = $mathRandom;

        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($ideaFactory, $registry, $context);
    }

    public function getRandomNumber($min = 0, $max = null)
    {
        return $this->mathRandom->getRandomNumber($min, $max);
    }

     // Generate Random string
     public function getRandomString($length,  $chars = null)
     {
         return $this->mathRandom->getRandomString($length, $chars);
     }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {  

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityIdea::applyIdea');
        $resultPage->getConfig()->getTitle()->set(__('Apply Idea to Product'));

        $title = __('Apply Idea to Product');

        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }

    
}
