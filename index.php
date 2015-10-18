<?
/**
 * Created by PhpStorm.
 * User: Dmitry
 * Date: 18.10.2015
 * Time: 19:24
 */

require_once dirname(__FILE__).'/include/CentralBankRussianFederation.php';
?><!DOCTYPE html>
<html>
<head lang="en">
    <title>Test currency format</title>
    <style>
        .currency{
            color: #1b5975;
            text-decoration: underline;
            cursor: pointer;
        }
        .currency:hover{
            text-decoration: none;
        }
    </style>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="js/cookie.js"></script>
    <script>
        $(document).ready(function(){
            $(document).on('click','.js_currency',function(){
                setCookie('current_currency',$(this).attr('data-currency'),{expires:3600*24});
                location.reload();
            })
        })
    </script>
</head>
<body>
<div>
<?
    $currencyList = array(
        'RUB',
        'EUR',
        'USD',
    );
    foreach ($currencyList as $currency) {?>
        <div class="js_currency currency <?=($currency == CentralBankRussianFederation::getCurrentCurrency()?'current':'')?>" data-currency="<?=$currency?>"><?=$currency?></div>
    <?}
?>
</div>
<?

$price = 123.53; // price in EUR

echo 'standard EUR: ' . $price . '<br>';
echo 'USD: ' . CentralBankRussianFederation::convert($price,'USD') . '<br>';
echo 'RUB: ' . CentralBankRussianFederation::convert($price,'RUB') . '<br>';
echo 'current ('.CentralBankRussianFederation::getCurrentCurrency().'): ' . CentralBankRussianFederation::convert($price) . '<br>';
?>
</body>
</html>