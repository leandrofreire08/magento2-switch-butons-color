<?php
namespace Freire\SwitchButtonsColor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_SWITCHBUTTONSCOLOR = 'switchbuttonscolor/';

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return ($this->getGeneralConfig("enable")) ? true : false;
    }

    public function getStoreId()
    {
        return $this->getGeneralConfig("store_id");
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SWITCHBUTTONSCOLOR .'general/'. $code, $storeId);
    }
}
