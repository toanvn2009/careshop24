<?php
/**
 * Copyright © 2019 The_Blue_Sky. All rights reserved.
 * @Author: Tony Pham
 * @Email: tonypham.web.developer@gmail.com
 */

namespace Rokanthemes\RokanBase\Helper;

class Lazyload extends \Magento\Framework\App\Helper\AbstractHelper {
	
	protected $_escaper;
	protected $_themeconfig;
	
	public function __construct(
		\Magento\Framework\Escaper $_escaper,
		\Rokanthemes\Themeoption\Helper\Themeconfig $themeconfig,
		\Magento\Framework\App\Helper\Context $context
	) {
		$this->_escaper = $_escaper;
		$this->_themeconfig = $themeconfig;
		parent::__construct($context);
	}
	
	public function getImageByUrl($img_url, $img_title, $lazyload = false, $class='')
    {
		if($lazyload){
			$icon_load = $this->_themeconfig->getIconlazyLoadUrl();
			$html = '<img class="owl-lazy'.$class.'" src="'.$icon_load.'" data-src="'.$img_url.'" alt="'.$this->_escaper->escapeHtml($img_title).'"/>';
		}
		else{
			$html = '<img class="'.$class.'" src="'.$img_url.'" alt="'.$this->_escaper->escapeHtml($img_title).'"/>';
		}
		return $html;
    }
	
	function checkHasAddToCartProduct($product){
		if(!$product->getData('has_options') && $product->getTypeID() == 'simple'){
			return true;
		}
		return false;
	}
	
	function cutStringCommon($string, $number){
		if(strlen($string) <= $number) {
			return $string;
		}
		else {	
			if(strpos($string," ",$number) > $number){
				$new_space = strpos($string," ",$number);
				$new_string = substr($string,0,$new_space)."...";
				return $new_string;
			}
			$new_string = substr($string,0,$number)."...";
			return $new_string;
		}
	}
}
