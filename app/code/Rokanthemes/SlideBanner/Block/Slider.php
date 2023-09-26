<?php

namespace Rokanthemes\SlideBanner\Block;

/**
 * Cms block content block
 */
class Slider extends \Magento\Framework\View\Element\Template
{
    protected $_filterProvider;
    protected $_sliderFactory;
    protected $_bannerFactory;

    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_slider;

    /**
     * @param Context $context
     * @param array $data
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Rokanthemes\SlideBanner\Model\SliderFactory $sliderFactory,
        \Rokanthemes\SlideBanner\Model\SlideFactory $slideFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_sliderFactory = $sliderFactory;
        $this->_bannerFactory = $slideFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
        $this->_filterProvider = $filterProvider;
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $sliderId = $this->getSliderId();
        if ($this->getIdentifier()) {
            $sliderId = $this->getIdentifier();
        }
        if ($sliderId && !$this->getTemplate()) {
            $this->setTemplate("Rokanthemes_SlideBanner::slider.phtml");
        }
        return parent::_beforeToHtml();
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getImageElement($src)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return '<img class="lazyOwl lazy" alt="' . $this->getSlider()->getSliderTitle() . '" data-original="'. $mediaUrl . $src . '" data-src="'. $mediaUrl . $src . '" />';
    }
    public function getContentElementMobile($banner, $slider)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $html = '';
        $src_mobile = $banner->getSlideImageMobile();
        $data_setting = $slider->getSliderSetting();

        if ($banner->getSlideLink()) {
            $taget = '';
            if ($banner->getOpennewtab() == 'yes') {
                $taget = ' target="_blank"';
            }
            $html .= '<a href="'.$banner->getSlideLink().'" title="' . $slider->getSliderTitle() . '"'.$taget.'><img alt="' . $slider->getSliderTitle() . '" src="'. $mediaUrl . $src_mobile . '" /></a>';
        } else {
            $html .= '<img alt="' . $slider->getSliderTitle() . '" src="'. $mediaUrl . $src_mobile . '" />';
        }

        if ($banner->getSlideText()) {
            $html .= '<div class="the-blue-sky-banner-text"><div class="'.$banner->getTextPosition().'">'.$this->getContentText($banner->getSlideText()).'</div></div>';
        }
        return $html;
    }
    public function getContentElement($banner, $slider)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $html = '';
        $src = $banner->getSlideImage();
        $data_setting = $slider->getSliderSetting();

        if ($banner->getSlideLink()) {
            $taget = '';
            if ($banner->getOpennewtab() == 'yes') {
                $taget = ' target="_blank"';
            }
            $html .= '<a href="'.$banner->getSlideLink().'" title="' . $slider->getSliderTitle() . '"'.$taget.'><img alt="' . $slider->getSliderTitle() . '" src="'. $mediaUrl . $src . '" /></a>';
        } else {
            $html .= '<img alt="' . $slider->getSliderTitle() . '" src="'. $mediaUrl . $src . '" />';
        }

        if ($banner->getSlideText()) {
            $html .= '<div class="the-blue-sky-banner-text"><div class="'.$banner->getTextPosition().'">'.$this->getContentText($banner->getSlideText()).'</div></div>';
        }
        return $html;
    }
    public function getBannerCollection($slider)
    {
        $sliderId = $slider->getId();
        $collection = $this->_bannerFactory->create()->getCollection();
        $collection->addFieldToFilter('slider_id', $sliderId);
        $collection->addFieldToFilter('slide_status', 1);
        $collection->setOrder('slide_position', 'ASC');
        return $collection;
    }
    public function getSlider()
    {
        if (is_null($this->_slider)):
            $sliderId = $this->getSliderId();
            if ($this->getIdentifier()) {
                $sliderId = $this->getIdentifier();
            }
            $this->_slider = $this->_sliderFactory->create();
            $this->_slider->load($sliderId);
        endif;
        return $this->_slider;
    }
    public function getContentText($html)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($html);
        return $html;
    }
}
