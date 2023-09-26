<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Widget;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Number extends Column
{
    /**
     * @param AbstractElement $element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $html = '<input type="text" name="' . $element->getName() . '" id="' . $element->getId()
            . '" class=" input-text admin__control-text required-entry _required validate-digits" value="'
            . $element->getValue() . '">';
        $element->setData('value', '');
        $element->setData('after_element_html', $html);

        return $element;
    }
}
