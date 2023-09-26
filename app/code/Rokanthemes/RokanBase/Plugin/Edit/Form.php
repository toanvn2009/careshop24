<?php

namespace Rokanthemes\RokanBase\Plugin\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
    public function beforeSetForm(\Magento\Review\Block\Adminhtml\Edit\Form $object, $form) {

        $review = $object->_coreRegistry->registry('review_data');

        $fieldset = $form->addFieldset(
            'replay_text',
            ['legend' => __('Review Replay'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'replay',
            'textarea',
            ['label' => __('Replay'), 'required' => false, 'name' => 'replay']
        );

        $form->setValues($review->getData());

        return [$form];
    }
}