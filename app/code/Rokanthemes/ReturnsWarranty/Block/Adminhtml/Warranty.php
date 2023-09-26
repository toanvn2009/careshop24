<?php
 
namespace Rokanthemes\ReturnsWarranty\Block\Adminhtml;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class Warranty extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
	protected function _construct()
    {
		$this->_controller = 'adminhtml_warranty';
        $this->_blockGroup = 'Rokanthemes_ReturnsWarranty';
        $this->_headerText = __('Warranty');
        $this->_removeButtonLabel = __('Warranty');
        parent::_construct();
    }
}
