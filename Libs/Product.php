<?php
namespace BibianBidAccount\Libs;
class Product
{
    private $_title;

    private $_seller;

    private $_price_now;

    private $_starting_time;

    private $_end_time;

    private $_bibian_users;

    private $_used_acconut;

    public function __construct($title, $seller)
    {
        $this->_title = $title;
        $this->_seller = $_seller;
        $this->_getPriceNow();
        $this->_getStartingTime();
        $this->_getEndTime();
        $this->_bibian_users = array();
    }

    private function _getPriceNow()
    {//取價函數，尚未實作
        $this->_price_now = 50;
    }

    private function _getStartingTime()
    {//取時函數，尚未實作
        $this->_starting_time = time();
    }

    private function _getEndTime()
    {//取時函數，尚未實作
        $this->_end_time = time()+5000;
    }


    Public function getBibianUser($bibianUser)
    {
        $this->_bibian_users = array_push($bibianUser);
    }
 
    public function assignAccount()
    {
        
    }
}