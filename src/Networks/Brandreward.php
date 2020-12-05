<?php

namespace Padosoft\AffiliateNetwork\Networks;

use Padosoft\AffiliateNetwork\Transaction;
use Padosoft\AffiliateNetwork\DealsResultset;
use Padosoft\AffiliateNetwork\Merchant;
use Padosoft\AffiliateNetwork\Stat;
use Padosoft\AffiliateNetwork\Deal;
use Padosoft\AffiliateNetwork\AbstractNetwork;
use Padosoft\AffiliateNetwork\NetworkInterface;
use Padosoft\AffiliateNetwork\ProductsResultset;

/**
 * Class Brandreward
 * @package Padosoft\AffiliateNetwork\Networks
 */
class Brandreward extends AbstractNetwork implements NetworkInterface
{
    /**
     * @var object
     */
    private $_network = null;
    private $_username = '';
    private $_password = '';
    private $_idSite = '';

    /**
     * @method __construct
     */
    public function __construct(string $username, string $password, string $idSite='')
    {
        $this->_network = new \Oara\Network\Publisher\Brandreward;
        $this->_username = $username;
        $this->_password = $password;
        $this->_idSite = $idSite;

        $this->login( $this->_username, $this->_password, $idSite );
    }

    public function login(string $username, string $password,string $idSite=''): bool{
        $this->_logged = false;
        if (isNullOrEmpty( $username ) || isNullOrEmpty( $password )) {

            return false;
        }
        $this->_username = $username;
        $this->_password = $password;
        $this->_idSite = $idSite;
        $credentials = array();
        $credentials["user"] = $this->_username;
        $credentials["password"] = $this->_password;
        $credentials["idSite"] = $this->_idSite;
        $this->_network->login($credentials);
        if ($this->_network->checkConnection()) {
            $this->_logged = true;
        }

        return $this->_logged;
    }

    /**
     * @return bool
     */
    public function checkLogin() : bool
    {
        return $this->_logged;
    }

    /**
	 * @return array of Merchants
	 * @throws \Exception
	 */
    public function getMerchants() : array
    {
        throw new \Exception("Not implemented yet");
    }

    /**
	 * @param null $merchantID
	 * @param int $page
	 * @param int $items_per_page
	 * @return DealsResultset  array of Deal
	 * @throws \Exception
	 */
    public function getDeals($merchantID=NULL,int $page=0,int $items_per_page=100 ): DealsResultset
    {
        throw new \Exception("Not implemented yet");

    }

   	/**
	 * @param \DateTime $dateFrom
	 * @param \DateTime $dateTo
	 * @param array $arrMerchantID
	 * @return array of Transaction
	 * @throws \Exception
	 */
    public function getSales(\DateTime $dateFrom, \DateTime $dateTo, array $arrMerchantID = array()) : array
    {

        $arrResult = array();
        $transactionList = $this->_network->getTransactionList($arrMerchantID, $dateFrom, $dateTo);
        foreach($transactionList as $transaction) {
            $Transaction = Transaction::createInstance();
            if (isset($transaction['currency']) && !empty($transaction['currency'])) {
                $Transaction->currency = $transaction['currency'];
            } else {
                $Transaction->currency = "USD";
            }
            $Transaction->status = $transaction['status'];
            $Transaction->amount = 0; //value set default to 0, attribute does not exist in the API
            array_key_exists_safe( $transaction,'custom_id' ) ? $Transaction->custom_ID = $transaction['custom_id'] : $Transaction->custom_ID = '';
            $Transaction->title = '';
            $Transaction->unique_ID = $transaction['unique_id'];
            $Transaction->commission = $transaction['commission'];
            $Transaction->date = $transaction['date'];
            $Transaction->click_date = $transaction['click_date'];
            $Transaction->update_date = $transaction['update_date'];
            $Transaction->program_name =  $transaction['merchantName'];
            $Transaction->approved = false;
            if ($Transaction->status == \Oara\Utilities::STATUS_CONFIRMED){
                $Transaction->approved = true;
            }
            $arrResult[] = $Transaction;
        }

        return $arrResult;
    }

    /**
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param int $merchantID
     * @return array of Stat
     */
    public function getStats(\DateTime $dateFrom, \DateTime $dateTo, int $merchantID = 0) : array
    {
        return array();
    }


    /**
	 * @param array $params
	 * @return ProductsResultset
	 * @throws \Exception
	 */
    public function getProducts(array $params = []): ProductsResultset
    {
        throw new \Exception("Not implemented yet");
    }




}
