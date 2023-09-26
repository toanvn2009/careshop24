<?php
 
namespace Rokanthemes\SlideBanner\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
 
class Setting extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    protected $_newsStatus;
    protected $_objectManager;
 
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
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_objectManager = $objectManager;
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
        $model = $this->_coreRegistry->registry('slider_form_data');
		$defaultSetting = array(
			'items'=>'1',
			'items_bigdesktop'=>'1',
			'items_smalldesktop'=>'1',
			'items_bigtablet'=>'1',
			'items_tablet'=>'1',
			'items_smalltablet'=>'1',
			'items_mobile'=>'1',
			'auto'=>'true',
			'rtl'=>'false',
			'speed'=>'250',
			'autoplaytimeout'=>'5000',
			'autoplayhoverpause'=>'false',
			'buttonsplaypause'=>'false',
			'lazyload'=>'true',
			'dots'=>'true',
			'rewind'=>'true',
			'nav'=>'true',
			'navnext'=>'Next',
			'navprev'=>'Prev',
			'stagepadding'=>'1',
			'touchdrag'=>'true',
			'mousedrag'=>'true',
			'center'=>'true',
			'loop'=>'true',
			'margin'=>'0'
		);
		$setting = $model->getSliderSetting();
		
		$data = array_merge($defaultSetting, $setting);
		
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_');
        $form->setFieldNameSuffix('slider');
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Settings')]
        );
		
		$fieldset->addField(
            'items',
            'text',
            [
                'name'        => 'slider_setting[items]',
                'label'    => __('Items Default'),
                'required'     => true,
				'class' => 'validate-number', 
				'default'=> 1
            ]
        );
		
		$fieldset->addField(
            'items_bigdesktop',
            'text',
            [
                'name'        => 'slider_setting[items_bigdesktop]',
                'label'    => __('Items On Big Desktop'),
                'required'     => true,
				'note' => 'Items on big desktop (Over 1500px)'
            ]
        );
		$fieldset->addField(
            'items_smalldesktop',
            'text',
            [
                'name'        => 'slider_setting[items_smalldesktop]',
                'label'    => __('Items On Small Desktop'),
                'required'     => true,
				'note' => 'Items on small desktop (992px - 1199px)'
            ]
        );
		$fieldset->addField(
            'items_bigtablet',
            'text',
            [
                'name'        => 'slider_setting[items_bigtablet]',
                'label'    => __('Items On Big Tablet'),
                'required'     => true,
				'note' => 'Items on big tablet (768px - 991px)'
            ]
        );
		$fieldset->addField(
            'items_tablet',
            'text',
            [
                'name'        => 'slider_setting[items_tablet]',
                'label'    => __('Items On Tablet'),
                'required'     => true,
				'note' => 'Items on tablet (640px - 767px)'
            ]
        );
		$fieldset->addField(
            'items_smalltablet',
            'text',
            [
                'name'        => 'slider_setting[items_smalltablet]',
                'label'    => __('Items On Small Tablet'),
                'required'     => true,
				'note' => 'Items on small tablet (480px - 639px)'
            ]
        );
		$fieldset->addField(
            'items_mobile',
            'text',
            [
                'name'        => 'slider_setting[items_mobile]',
                'label'    => __('Items On mobile'),
                'required'     => true,
				'note' => 'Items on mobile (Under 479px)'
            ]
        );
		
		$fieldset->addField(
            'auto',
            'select',
            [
                'name'        => 'slider_setting[auto]',
                'label'    => __('Auto Play'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]]
            ]
        );
		
		$fieldset->addField(
            'autoplaytimeout',
            'text',
            [
                'name'        => 'slider_setting[autoplaytimeout]',
                'label'    => __('Autoplay Timeout'),
                'required'     => false,
				'note' => 'Autoplay interval timeout'
            ]
        );
		$fieldset->addField(
            'autoplayhoverpause',
            'select',
            [
                'name'        => 'slider_setting[autoplayhoverpause]',
                'label'    => __('Autoplay Hover Pause'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'default'=> 'true',
				'note' => 'Pause on mouse hover'
            ]
        ); 
		$fieldset->addField(
            'buttonsplaypause',
            'select',
            [
                'name'        => 'slider_setting[buttonsplaypause]',
                'label'    => __('Button Play Pause'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Pause and Play Click Button'
            ]
        );
		$fieldset->addField(
            'lazyload',
            'select',
            [
                'name'        => 'slider_setting[lazyload]',
                'label'    => __('Lazy Load'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Lazy load images. data-src and data-src-retina for highres. Also load images into background inline style if element is not img'
            ]
        );
		
		$fieldset->addField(
            'dots',
            'select',
            [
                'name'        => 'slider_setting[dots]',
                'label'    => __('Dots'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Show dots navigation'
            ]
        );
		
		$fieldset->addField(
            'rewind',
            'select',
            [
                'name'        => 'slider_setting[rewind]',
                'label'    => __('Rewind'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Go backwards when the boundary has reached'
            ]
        );
		
		$fieldset->addField(
            'nav',
            'select',
            [
                'name'        => 'slider_setting[nav]',
                'label'    => __('Navigation'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Show next/prev buttons'
            ]
        );
		
		$fieldset->addField(
            'navnext',
            'text',
            [
                'name'        => 'slider_setting[navnext]',
                'label'    => __('Navigation Next Text'),
                'required'     => false
            ]
        );
		
		$fieldset->addField(
            'navprev',
            'text',
            [
                'name'        => 'slider_setting[navprev]',
                'label'    => __('Navigation Prev Text'),
                'required'     => false
            ]
        );
		
		$fieldset->addField(
            'stagepadding',
            'text',
            [
                'name'        => 'slider_setting[stagepadding]',
                'label'    => __('Stage Padding'),
                'required'     => false,
				'note' => 'Padding left and right on stage (can see neighbours)'
            ]
        );
		
		$fieldset->addField(
            'touchdrag',
            'select',
            [
                'name'        => 'slider_setting[touchdrag]',
                'label'    => __('Touch Drag'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Touch drag enabled'
            ]
        );
		
		$fieldset->addField(
            'mousedrag',
            'select',
            [
                'name'        => 'slider_setting[mousedrag]',
                'label'    => __('Mouse Drag'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Mouse drag enabled'
            ]
        );
		
		$fieldset->addField(
            'center',
            'select',
            [
                'name'        => 'slider_setting[center]',
                'label'    => __('Center'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Center item. Works well with even an odd number of items'
            ]
        );
		
		$fieldset->addField(
            'loop',
            'select',
            [
                'name'        => 'slider_setting[loop]',
                'label'    => __('Loop'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'Infinity loop. Duplicate last and first items to get loop illusion'
            ]
        );
		
		$fieldset->addField(
            'margin',
            'text',
            [
                'name'        => 'slider_setting[margin]',
                'label'    => __('Margin'),
                'required'     => false,
				'note' => 'Margin-right(px) on item'
            ]
        );
		
		$fieldset->addField(
            'speed',
            'text',
            [
                'name'        => 'slider_setting[speed]',
                'label'    => __('Smart Speed'),
                'required'     => false,
				'note' => 'Speed Calculate. More info to come'
            ]
        );
		
		$fieldset->addField(
            'rtl',
            'select',
            [
                'name'        => 'slider_setting[rtl]',
                'label'    => __('RTL'),
                'required'     => false,
				'values'=> [['value'=>'false', 'label'=> __('False')], ['value'=>'true', 'label'=> __('True')]],
				'note' => 'RTL enabled'
            ]
        );
		
        $form->setValues($data);
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
        return __('Slider Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Slider Info');
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