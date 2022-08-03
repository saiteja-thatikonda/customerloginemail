<?php


namespace I95dev\SendEmail\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerLogin implements ObserverInterface
{
    
    protected $transportBuilder;

  
    protected $storeManager;

    protected $logger;

   
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
       
        if (!$customer) {
            return $this;
        }

       
        $receiverInfo = [
            'name' => 'Sai teja ',
            'email' => 'venkata.thatikonda@i95dev.com' 
        ];

        $store = $this->storeManager->getStore();

        $templateParams = ['store' => $store, 'customer' => $customer, 'customer_name' => $receiverInfo['name']];

        $transport = $this->transportBuilder->setTemplateIdentifier(
            'i95dev_send_email_customer_logg_in_email_template'
        )->setTemplateOptions(
            ['area' => 'frontend', 'store' => $store->getId()]
        )->addTo(
            $receiverInfo['email'], $receiverInfo['name']
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            'general'
        )->getTransport();

        try {
            
            $transport->sendMessage();
        } catch (\Exception $e) {
            
            $this->logger->critical($e->getMessage());
        }
        return $this;
    }
}

