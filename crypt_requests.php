<?
/*For starting from cron or test from browser*/
$CryptRequests = new CryptRequests();

$request = $_GET;
if ($request['pass'] != $CryptRequests->PASS) exit;

include_once("crypt_handler.php");
$CryptHandler = new CryptHandler();

if ($request['action'] == 'regular_prices_check') {
  $token_prices = $CryptHandler->get_data(true);
  $notify_data = $CryptHandler->price_comparing_notify($token_prices);
  if ($notify_data) {
    $CryptHandler->send_users_notifications($notify_data);
  }
} else if ($request['action'] == 'test') {
  $token_prices = $CryptHandler->get_data(false);
  $notify_data = $CryptHandler->price_comparing_notify($token_prices, true);
  if ($notify_data) {
    $CryptHandler->send_users_notifications($notify_data);
  }
}

$CryptHandler->$BD_REQUESTS->close_connection();


class CryptRequests
{
  public $PASS;

  public function __construct()
  {
    $this->PASS = ''; //password as get-param
  }
}