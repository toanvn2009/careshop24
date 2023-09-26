<?php
 
namespace Rokanthemes\SlideBanner\Block\Adminhtml\Slide\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
 
class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    protected $_newsStatus;
    protected $_objectManager;
    protected $_systemStore;
 
   /**
    * @param Context $context
    * @param Registry $registry
    * @param FormFactory $formFactory
    * @param Config $wysiwygConfig
    * @param array $data
    */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_objectManager = $objectManager;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Rokanthemes\SlideBanner\Model\Slide */
        $model = $this->_coreRegistry->registry('slide_form_data');
        $data = $model->getData();
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slide_');
        $form->setFieldNameSuffix('slide');
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );
 
        $fieldset->addField(
            'slider_id',
            'select',
            [
                'name'        => 'slider_id',
                'label'    => __('Slider'),
                'required'     => false,
                'values'=>$this->_getSliderOptions()
            ]
        );
        $fieldset->addField(
            'slide_status',
            'select',
            [
                'name'        => 'slide_status',
                'label'    => __('Status'),
                'required'     => false,
                'values'=> [['value'=>1, 'label'=> __('Enable')], ['value'=>2, 'label'=> __('Disable')]]
            ]
        );
        $fieldset->addField(
            'slide_image',
            'image',
            [
                'name'        => 'slide_image',
                'label'    => __('Image'),
                'required'     => true
            ]
        );
        $fieldset->addField(
            'slide_image_mobile',
            'image',
            [
                'name'        => 'slide_image_mobile',
                'label'    => __('Image on Mobile'),
                'required'     => true
            ]
        );
        $fieldset->addField(
            'slide_link',
            'text',
            [
                'name'        => 'slide_link',
                'label'    => __('Add Link'),
                'required'     => false
            ]
        );
        $fieldset->addField(
            'opennewtab',
            'select',
            [
                'name'        => 'opennewtab',
                'label'    => __('Open Link New Tab'),
                'required'     => false,
                'values'=> [['value'=>'no', 'label'=> __('No')], ['value'=>'yes', 'label'=> __('Yes')]]
            ]
        );
        $fieldset->addField(
            'slide_position',
            'text',
            [
                'name'        => 'slide_position',
                'label'    => __('Image Position'),
                'required'     => false
            ]
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig();
        $fieldset->addField(
            'slide_text',
            'editor',
            [
                'name'        => 'slide_text',
                'label'    => __('Add Text'),
                'required'     => false,
                'config'    => $wysiwygConfig
            ]
        );
        $fieldset->addField(
            'text_position',
            'select',
            [
                'name'        => 'text_position',
                'label'    => __('Slider Text Position'),
                'required'     => false,
                'values'=> [['value'=>'left_center', 'label'=> __('Left Center')], ['value'=>'right_center', 'label'=> __('Right Center')], ['value'=>'center_center', 'label'=> __('Center Center')]]
            ]
        );

        $fieldset->addField(
           'store_ids',
           'multiselect',
           [
             'name'     => 'store_ids[]',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
           ]
        );
        
        if(isset($data['store_ids']) && $data['store_ids'] != ''){
            $data['store_ids'] = json_decode($data['store_ids'], true);
        }

        $form->setValues($data);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
    protected function _getSliderOptions()
    {
        $result = [];
        $collection = $this->_objectManager->create('Rokanthemes\SlideBanner\Model\Slider', [])->getCollection();
        foreach ($collection as $slider) {
            $result[] = ['value'=>$slider->getId(), 'label'=>$slider->getSliderTitle()];
        }
        return $result;
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Banner Info');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
