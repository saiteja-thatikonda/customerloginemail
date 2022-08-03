<?php


namespace I95dev\SendEmail\Observer;

class CustomerLoggin implements \Magento\Framework\Event\ObserverInterface
{
    
    protected $scopeConfig;

    
    protected $uri;

    
    protected $responseFactory;

   
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Zend\Validator\Uri $uri,
        \Magento\Framework\App\ResponseFactory $responseFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->uri = $uri;
        $this->responseFactory = $responseFactory;
    }

   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $redirectDashboard = $this->scopeConfig->isSetFlag(
            'customer/startup/redirect_dashboard',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
        );

        
        if (!$redirectDashboard) {
            $customPage = $this->scopeConfig->getValue(
                'customer/startup/custom_page_for_redirecting',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
            );
            
            if (!empty($customPage) && $this->uri->isValid($customPage)) {
                $resultRedirect = $this->responseFactory->create();
                
                $resultRedirect->setRedirect($customPage)->sendResponse('200');
                exit();
            }
        }
    }
}

