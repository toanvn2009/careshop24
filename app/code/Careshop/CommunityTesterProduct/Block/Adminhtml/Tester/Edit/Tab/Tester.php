<?php

namespace Careshop\CommunityTesterProduct\Block\Adminhtml\Tester\Edit\Tab;

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

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab
 */
class Tester extends Generic implements TabInterface
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
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->systemStore = $systemStore;
        $this->authSession = $authSession;
        $this->_date = $dateTime;
        $this->_layoutOptions = $layoutOption;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareForm()
    {
        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $tester = $this->_coreRegistry->registry('community_tester');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('tester_');
        $form->setFieldNameSuffix('tester');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Tester Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ]);

        $fieldset->addField(
            'product_id',
            \Careshop\CommunityTesterProduct\Block\Adminhtml\Tester\Edit\Tab\Renderer\Product::class,
            [
                'name'  => 'product_id',
                'label' => __('Product'),
                'title' => __('Product')
            ]
        );

        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Publish Date'),
                'title' => __('Publish Date'),
                'date_format' => 'yyyy-MM-dd',
                'timezone' => false,
                'time_format' => 'hh:mm:ss'
            ]
        );

        /** Get the public_date from database */
        if ($tester->getData('created_at')) {
            $publicDateTime = new \DateTime($tester->getData('created_at'), new DateTimeZone('UTC'));
            $publicDateTime->setTimezone(new DateTimeZone($this->_localeDate->getConfigTimezone()));
            $publicDateTime = $publicDateTime->format('m/d/Y H:i:s');
            $tester->setData('created_at', $publicDateTime);
        }

        $form->addValues($tester->getData());
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
        return __('Tester');
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
