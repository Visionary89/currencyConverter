<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 18.10.2015
 * Time: 19:21
 */


/**
 * Class CentralBankRussianFederation
 */
class CentralBankRussianFederation {
    protected static $arCurrency = array();
    public static $convert = false;
    const fileUrl = 'http://www.cbr.ru/scripts/XML_daily.asp';
    const filePath = '/data/serialized.cached';
    protected static $defaultCurrency = 'RUB';
    protected static $bankCurrency = 'EUR';
    protected static $arCurrenciesFormat = array(
        'RUB' => array(0,'.',' ','# руб'),
        'EUR' => array(2,'.',' ','$ #'),
        'USD' => array(2,'.',' ','€ #'),
    );

    public static function getCurrentCurrency(){
        if(isset($_COOKIE['current_currency'])){
            return $_COOKIE['current_currency'];
        }
        return self::$defaultCurrency;
    }


    /**
     * Получаем список валют.
     * @return string
     */
    protected static function request() {
        // получаем список валют
        if($response = file_get_contents(self::fileUrl)) {
            // конвертируем овтет
            if(self::$convert){
                return iconv('windows-1251', 'UTF-8', $response);
            }
            else{
                return $response;
            }
        }
        return false;
    }

    /**
     * Получаем список валют в массиве.
     * @return bool
     */
    public static function getXmlData() {
        // получаем валюты
        if($response = self::request()) {
            return (simplexml_load_string(self::request()));
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public static function getCachedArray(){
        if(!empty(self::$arCurrency)){
            return self::$arCurrency;
        }
        $file = @file_get_contents(dirname(__FILE__).self::filePath);
        if($file){
            $temp = unserialize($file);
            if(is_array($temp) && !empty($temp)){
                if($temp['date'] > time() - 60*60*24){
                    self::$arCurrency = $temp;
                    return self::$arCurrency;
                }
            }
        }
        $request = self::getXmlData();
        if($request) {
            self::$arCurrency['date'] = time();
            foreach ($request->Valute as $currency) {
                self::$arCurrency['currencies'][(string)$currency->CharCode] = floatval(str_replace(',', '.', (string)$currency->Value));
            }
            if (!file_exists(dirname(dirname(__FILE__) . self::filePath))) {
                mkdir(dirname(dirname(__FILE__) . self::filePath), 0777, true);
            }
            file_put_contents(dirname(__FILE__) . self::filePath, serialize(self::$arCurrency));
            return self::$arCurrency;
        }
        return false;
    }

    /**
     * Получаем только нужную валюту.
     * @param $currency
     * @return bool
     */
    public static function get($currency) {
        if($list = self::getCachedArray()) {
            if(isset($list['currencies'][$currency]) ){
                return $list['currencies'][$currency] ;
            }
            elseif($currency == 'RUB'){
                return 1;
            }
        }

        return false;
    }

    /**
     * @param $price
     * @param bool $currency_to
     * @param bool $currency_from
     * @return string
     */
    public static function convert($price, $currency_to = false, $currency_from = false) {
        $k=1;
        if(!$currency_to){
            $currency_to = self::getCurrentCurrency();
        }
        if(!$currency_from){
            $currency_from = self::$bankCurrency;
        }
        if($currency = self::get($currency_to)){
            $k = $currency;
        }

        $b = self::get($currency_from);
        $k = $k/$b;
        return $price / $k;
    }

    /**
     * @param $price
     * @param bool $currency
     * @return string
     */
    public static function format($price, $currency = false) {
        if(!$currency){
            $currency = self::getCurrentCurrency();
        }

        $params = array(2,'.','');

        if(isset(self::$arCurrenciesFormat[$currency])){
            $params = self::$arCurrenciesFormat[$currency];
            array_pop($params);
        }
        array_unshift($params,$price);

        $price = call_user_func_array('number_format', $params);

        if(isset(self::$arCurrenciesFormat[$currency][3])){
            $price = str_replace('#',$price,self::$arCurrenciesFormat[$currency][3]);
        }
        return $price;
    }

    /**
     * @param $price
     * @param bool $currency_to
     * @param bool $currency_from
     * @return string
     */
    public static function convertAndFormatted($price, $currency_to = false, $currency_from = false){
        $price = self::convert($price, $currency_to = false, $currency_from = false);
        return self::format($price, $currency_to);
    }
}