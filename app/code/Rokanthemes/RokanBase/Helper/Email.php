<?php
namespace Rokanthemes\RokanBase\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Helper Config Admin
     * @var Data
     */
    protected $helper;

    /**
     * Scope Config Interface
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * State Interface
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Escaper
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;


    /**
     * Transport Builder
     *
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    protected $storeManager;

    /**
     * Email constructor.
     * @param Context $context
     * @param Data $helper
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
    }

    /**
     * @param $receivers
     * @param $emailTemplate
     * @param $templateVar
     */
    public function sendEmail($receivers, $templateVar, $email_template)
    {
        $email_template_speed = $email_template;
        $emailValue = 'trans_email/ident_sales/email';
        $emailNameValue = 'trans_email/ident_sales/name';
        $emailNameSender = $this->scopeConfig->getValue($emailNameValue, ScopeInterface::SCOPE_STORE);
        $emailSender = $this->scopeConfig->getValue($emailValue, ScopeInterface::SCOPE_STORE);
        $this->inlineTranslation->suspend();
        $sender = [
            'name' => $this->escaper->escapeHtml($emailNameSender),
            'email' => $this->escaper->escapeHtml($emailSender),
        ];
        if (!$receivers) {
            $receivers = $emailSender;
        }

        $storeId = $this->storeManager->getStore()->getId();

        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $storeId
        ];

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
        //Send Email

        $transport = $this->transportBuilder->setTemplateIdentifier($email_template_speed, $storeScope)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVar)
            ->setFrom($sender)
            ->addTo($receivers)
            ->getTransport();
        
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
