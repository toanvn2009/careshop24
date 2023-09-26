<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class CreateProduct implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();  
        $request = $objectManager->get('Magento\Framework\App\Request\Http');  


        return [
            'label' => __('Create Product'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'community_idea_applyidea.community_idea_applyidea',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    ['id' => ($request->getParam('id')) ? $request->getParam('id') : ''],
                                ]
                            ]
                        ]
                    ]
                ],

            ],
            'sort_order' => 10
        ];
    }
}
