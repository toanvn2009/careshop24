<?php
 
namespace Rokanthemes\SlideBanner\Model;
 
class Slider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Rokanthemes\SlideBanner\Model\Resource\Slider');
    }
	public function getSliderSetting()
	{
		if(!$this->getData('slider_setting'))
			return array();
		$data = $this->getData('slider_setting');
		$data = json_decode($data, true);
		return $data;
	}
	public function getSetting()
	{
		$data = $this->getData('slider_setting');
		return $data;
	}
}