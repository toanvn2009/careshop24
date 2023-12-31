<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Careshop\CommunityIdea\Model\ResourceModel\Topic\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Topic\CollectionFactory as CommunityTopicCollectionFactory;

/**
 * Class Topic
 * @package Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer
 */
class Topic extends Multiselect
{
    /**
     * @var CommunityTopicCollectionFactory
     */
    public $collectionFactory;

    /**
     * Authorization
     *
     * @var AuthorizationInterface
     */
    public $authorization;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Topic constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param CommunityTopicCollectionFactory $collectionFactory
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        CommunityTopicCollectionFactory $collectionFactory,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        $html = '<div class="admin__field-control admin__control-grouped">';
        $html .= '<div id="community-topic-select" class="admin__field" data-bind="scope:\'communityTopic\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="idea[topics_ids]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div>';

        $html .= '<div class="admin__field admin__field-group-additional admin__field-small"'
            . ' data-bind="scope:\'create_topic_button\'">';
        $html .= '<div class="admin__field-control">';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '</div></div></div>';

        $html .= '<!-- ko scope: \'create_topic_modal\' --><!-- ko template: getTemplate() --><!-- /ko --><!-- /ko -->';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Careshop_CommunityIdea::topic');

        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * @return mixed
     */
    public function getTopicsCollection()
    {
        /* @var $collection Collection */
        $collection = $this->collectionFactory->create();
        $topicById = [];
        foreach ($collection as $topic) {
            $topicById[$topic->getId()]['value'] = $topic->getId();
            $topicById[$topic->getId()]['is_active'] = 1;
            $topicById[$topic->getId()]['label'] = $topic->getName();
        }

        return $topicById;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (!count($values)) {
            return [];
        }

        /* @var $collection Collection */
        $collection = $this->collectionFactory->create()
            ->addIdFilter($values);

        $options = [];
        foreach ($collection as $topic) {
            $options[] = $topic->getId();
        }

        return $options;
    }

    /**
     * Attach Community Topic suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "communityTopic": {
                                "component": "uiComponent",
                                "children": {
                                    "community_select_topic": {
                                        "component": "Careshop_CommunityIdea/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . json_encode($this->getTopicsCollection()) . ',
                                            "value": ' . json_encode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_topic:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
                                            "config": {
                                                "dataScope": "community_select_topic",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            },
                            "create_topic_button": {
                                "title": "' . __('New Topic') . '",
                                "formElement": "container",
                                "additionalClasses": "admin__field-small",
                                "componentType": "container",
                                "component": "Magento_Ui/js/form/components/button",
                                "template": "ui/form/components/button/container",
                                "actions": [
                                    {
                                        "targetName": "create_topic_modal",
                                        "actionName": "toggleModal"
                                    },
                                    {
                                        "targetName": "create_topic_modal.create_topic",
                                        "actionName": "render"
                                    },
                                    {
                                        "targetName": "create_topic_modal.create_topic",
                                        "actionName": "resetForm"
                                    }
                                ],
                                "additionalForGroup": true,
                                "provider": false,
                                "source": "product_details",
                                "displayArea": "insideGroup"
                            },
                            "create_topic_modal": {
                                "config": {
                                    "isTemplate": false,
                                    "componentType": "container",
                                    "component": "Magento_Ui/js/modal/modal-component",
                                    "options": {
                                        "title": "' . __('New Topic') . '",
                                        "type": "slide"
                                    },
                                    "imports": {
                                        "state": "!index=create_topic:responseStatus"
                                    }
                                },
                                "children": {
                                    "create_topic": {
                                        "label": "",
                                        "componentType": "container",
                                        "component": "Magento_Ui/js/form/components/insert-form",
                                        "dataScope": "",
                                        "update_url": "' . $this->_urlBuilder->getUrl('mui/index/render') . '",
                                        "render_url": "' .
            $this->_urlBuilder->getUrl(
                'mui/index/render_handle',
                [
                    'handle' => 'community_topic_create',
                    'buttons' => 1
                ]
            ) . '",
                                        "autoRender": false,
                                        "ns": "community_new_topic_form",
                                        "externalProvider": "community_new_topic_form.new_topic_form_data_source",
                                        "toolbarContainer": "${ $.parentName }",
                                        "formSubmitType": "ajax"
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }
}
