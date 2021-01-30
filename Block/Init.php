<?php

namespace Freire\SwitchButtonsColor\Block;

use Freire\SwitchButtonsColor\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Init extends Template
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Init constructor.
     * @param Template\Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        $currentStoreId = $this->_storeManager->getStore()->getId();

        if ($this->helperData->getStoreId() == $currentStoreId) {
            $this->pageConfig->addPageAsset('Freire_SwitchButtonsColor::css/buttons.css');
        }
        parent::_construct();
    }
}
