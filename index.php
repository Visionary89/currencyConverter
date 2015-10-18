<?php
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 18.10.2015
 * Time: 19:24
 */
function printr($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

require_once dirname(__FILE__).'/include/CentralBankRussianFederation.php';

$price = 123.53;

echo 'standard EUR: ' . $price . '<br>';
echo 'USD: ' . CentralBankRussianFederation::convert($price,'USD') . '<br>';
echo 'RUB: ' . CentralBankRussianFederation::convert($price,'RUB') . '<br>';
echo 'default (RUB): ' . CentralBankRussianFederation::convert($price) . '<br>';