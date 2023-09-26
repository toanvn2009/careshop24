<?php

namespace Rokanthemes\RokanBase\Plugin\Magento\Ui\Component\Wysiwyg;

class ConfigPlugin
{
    public function afterGetConfig(
        \Magento\Ui\Component\Wysiwyg\ConfigInterface $configInterface,
        \Magento\Framework\DataObject $result
    ) {
        if($result->getData('tinymce4') && is_array($result->getData('tinymce4'))){
			$tinymce4 = $result->getData('tinymce4');
			if(isset($tinymce4['toolbar'])){
				$tinymce4['toolbar'] = 'forecolor backcolor | '.$tinymce4['toolbar'];
			}
			if(isset($tinymce4['plugins'])){
				$tinymce4['plugins'] = $tinymce4['plugins'].' textcolor';
			}
			$result->setData('tinymce4', $tinymce4);
            return $result;
        } 
		else{
            return $result;
        }
    }
}