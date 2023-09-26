<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab;

use DateTimeZone;
use Exception;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Cms\Model\Page\Source\PageLayout as BasePageLayout;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;
use Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\Category;
use Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\Tag;
use Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\Topic;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\Config\Source\Author;
use Careshop\CommunityIdea\Model\Config\Source\AuthorStatus;
use Careshop\CommunityIdea\Model\Config\Source\IdeaStatus;

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab
 */
class Idea extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    public $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    public $booleanOptions;

    /**
     * @var Robots
     */
    public $metaRobotsOptions;

    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var BasePageLayout
     */
    protected $_layoutOptions;

    /**
     * @var Author
     */
    protected $_author;

    /**
     * @var AuthorStatus
     */
    protected $_status;

    /**
     * @var IdeaStatus
     */
    protected $_status_idea;

    /**
     * Idea constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Session $authSession
     * @param DateTime $dateTime
     * @param BasePageLayout $layoutOption
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Robots $metaRobotsOptions
     * @param Store $systemStore
     * @param Image $imageHelper
     * @param Author $author
     * @param AuthorStatus $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Session $authSession,
        DateTime $dateTime,
        BasePageLayout $layoutOption,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Robots $metaRobotsOptions,
        Store $systemStore,
        Image $imageHelper,
        Author $author,
        AuthorStatus $status,
        IdeaStatus $status_idea,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->systemStore = $systemStore;
        $this->authSession = $authSession;
        $this->_date = $dateTime;
        $this->_layoutOptions = $layoutOption;
        $this->imageHelper = $imageHelper;
        $this->_author = $author;
        $this->_status = $status;
        $this->_status_idea = $status_idea;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareForm()
    {
        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $idea = $this->_coreRegistry->registry('community_idea');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('idea_');
        $form->setFieldNameSuffix('idea');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Idea Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ]);
        $fieldset->addField('enabled', 'select', [
            'name' => 'enabled',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->_status->toOptionArray()
        ]);
        $fieldset->addField('status_idea', 'select', [
            'name' => 'status_idea',
            'label' => __('Status Idea'),
            'title' => __('Status Idea'),
            'values' => $this->_status_idea->toOptionArray()
        ]);
        if (!$idea->hasData('enabled')) {
            $idea->setEnabled(1);
        }

        $fieldset->addField('short_description', 'textarea', [
            'name' => 'short_description',
            'label' => __('Short Description'),
            'title' => __('Short Description')
        ]); 
        $fieldset->addField('post_content', 'editor', [
            'name' => 'post_content',
            'label' => __('Content'),
            'title' => __('Content'),
            'config' => $this->wysiwygConfig->getConfig([
                'add_variables' => false,
                'add_widgets' => true,
                'add_directives' => true
            ])
        ]);

        $fieldset->addField('categories_ids', Category::class, [
            'name' => 'categories_ids',
            'label' => __('Categories'),
            'title' => __('Categories'),
        ]);
        if (!$idea->getCategoriesIds()) {
            $idea->setCategoriesIds($idea->getCategoryIds());
        }

        $fieldset->addField(
            'publish_date',
            'date',
            [
                'name' => 'publish_date',
                'label' => __('Publish Date'),
                'title' => __('Publish Date'),
                'date_format' => 'yyyy-MM-dd',
                'timezone' => false,
                'time_format' => 'hh:mm:ss'
            ]
        );

        /** Get the public_date from database */
        if ($idea->getData('publish_date')) {
            $publicDateTime = new \DateTime($idea->getData('publish_date'), new DateTimeZone('UTC'));
            $publicDateTime->setTimezone(new DateTimeZone($this->_localeDate->getConfigTimezone()));
            $publicDateTime = $publicDateTime->format('m/d/Y H:i:s');
            $idea->setData('publish_date', $publicDateTime);
        }

        $form->addValues($idea->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Idea');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
