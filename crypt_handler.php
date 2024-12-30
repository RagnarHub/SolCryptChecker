<?

class CryptHandler
{
  public $BD_REQUESTS;
  public $TG_NOTIFY;
  public $USERS;
  public $FOLLOWING_TOKENS;
  public $SETTINGS;

  public function __construct()
  {
    include_once("bd_requests.php");
    include_once("tg_service.php");

    $this->BD_REQUESTS = new BdRequests();
    $this->TG_NOTIFY = new BotTgNotify();

    $this->USERS = array(
      'user1' => array(
        'tg_chat_id' => '', //chat id with bot
      ),
      'user2' => array(
        'tg_chat_id' => '', //chat id with bot
      ),
    );
    $this->FOLLOWING_TOKENS = array(
      'user1' => array(
        'Bonk' => 'DezXAZ8z7PnrnRJjz3wXBoRgixCa6xjnB7YaB1pPB263',
        'GOAT' => 'CzLSujWBLFsSjncfkh59rUFqvafWcY5tzedWJSuypump',
        'ai16z' => 'HeLp6NuQkmYB4pYWo2zYs22mESHXPQYzXbB8n4V98jwC',
        'koko' => 'FsA54yL49WKs7rWoGv9sUcbSGWCWV756jTD349e6H2yW',
        'aura' => 'DtR4D9FtVoTX2569gaL837ZgrB6wNjj6tkmnX9Rdk9B2',
        'WMM' => '9pWPUXoZKWNPWyaegPQeR3Kn8aFz9nrGtm5jeAFzpump',
        'Manyu' => 'CS7LmjtuugEUWtFgfyto79nrksKigv7Fdcp9qPuigdLs',
        'MELON' => '7DGJnYfJrYiP5CKBx6wpbu8F5Ya1swdFoesuCrAKCzZc',

        'PTNS-autista' => '6QbkqyarW5ET7TvNsENGmV7e4fg7SpACdYb8ba5Ppump',
        'PTNS-comeback' => 'FDjuAogfRBQ9LiYQfaKa7hUJDTBbZdUY1B9V3a9Rpump',
        'PTNS-25-HOLD' => 'BDhAFmRfBku6h6R8ePC3Br67HHeqbMnNQydQaHKDzgLq',
        'PTNS-30-Sam404' => 'EhbAzVRVkDf7CrWVEroaX6P3vyHE13dmjZyRqvRypump',
        'PTNS-KEK' => '2fZTwhbLg3xPquk8CqS3KKYbrNMbhKj7Q5zBKuT2pump',
        'PTNS-Cheyenne' => 'UrAE9vVdrWxncikcCRp7TgNqEsArFtP22iXzH7gpump',
        'PTNS-DOGEX' => '9t77JBusmE5yEad6idpT1rWf7NQRPoHvV6DFaS7dsWy2',
        'PTNS-20-XIAOXIAO' => 'aYEXzn89ThxPX73GNFCUzEtJDb61PaHsmmejNoqpump',
        'PTNS-dream' => '6b1j4SQVxyf5YvCK6QGfQ2mAUfnnNxRKFV4Am7mjpump',
        'PTNS-JetCat' => 'JCPeeLCT7U6unsdtAxftLTfQ2RV3DpYJxUAbQg4Epump',
        'PTNS-20-ZOOMBIE' => 'DcMkFnb9ktSqsnjnZmEQZShhSXVRf4ZXq8YXv644pump',
        'PTNS-20-RAID' => 'FJennnpTJRfZPgyk8EZ4TY21m1HqGamYoRChfZxCpump',
        'PTNS-EUPHORIA' => 'D5zv6yDc8ybeXaHtxYrzoPVwQAdjAqUi5nXJWE8tpump',
        'PTNS-MOTIVATED' => '4QsUYypaCeH4VEFByhpr6sFEGhrSQxxLt4xGGyt8pump',
        'PTNS-BABYTARDIO' => '2bNVW8VhmZLuXjy5ApD2nxA76rMoQr9hmJG65MMHpump',
        'PTNS-SCRAT' => '3GEznP41VaAGv9yRdBiuYdA8zkffNc8b7DzNJgFbH4Kh',
        'PTNS-SCRAT|FP3fR' => 'FP3fRkMhD6w28ZJRfnitEb5AUfGpcauvtSRQQhpjz64o',
      ),
      'user2' => array(
        'Bonk' => 'DezXAZ8z7PnrnRJjz3wXBoRgixCa6xjnB7YaB1pPB263',
      ),
    );
    $this->SETTINGS = array(
      'limits' => array(
        'simple_request_tokens' => 30,
      ),
    );
  }

  public function get_data($write_bd)
  {
    $tokens_list = array();
    $token_prices = array();
    //сбор массива для запроса
    foreach ($this->FOLLOWING_TOKENS as $user => $tokens) {
      foreach ($tokens as $token_title => $token_address) {
        if (!in_array($token_address, $tokens_list)) {
          if ($tokens_list[$token_title]) {
            $tokens_list[$token_title.'|'.mb_substr($token_address, 0, 5)] = $token_address;
          } else {
            $tokens_list[$token_title] = $token_address;
          }
        }
      }
    }
    //запрос данных пакетами
    $tokens_list_str = '';
    $i = 0;
    foreach ($tokens_list as $token_title => $token_address) {
      $i++;
      if ($i > $this->SETTINGS['limits']['simple_request_tokens']) {
        $prices_data = $this->send_token_price_request($tokens_list_str);
        $token_prices = array_merge($token_prices, $prices_data);
        $tokens_list_str = '';
        $i = 1;
      }
      if ($tokens_list_str) $tokens_list_str = $tokens_list_str.',';
      $tokens_list_str = $tokens_list_str.$token_address;
    }
    if ($tokens_list_str) {
      $prices_data = $this->send_token_price_request($tokens_list_str);
      $token_prices = array_merge($token_prices, $prices_data);
    }
    //запись данных в БД
    if ($write_bd) {
      $log = array();
      foreach ($tokens_list as $token_title => $token_address) {
        $log[$token_address] = array('title' => $token_title, 'price' => $token_prices[$token_address]);
      }
      $this->BD_REQUESTS->add_log_data($log);
    }
    return $token_prices;
  }

  public function price_comparing_notify($token_prices, $disable_checks = false)
  {
    $log_5min = $this->BD_REQUESTS->get_log_data(5, $disable_checks);
    if ($log_5min == false) {
      $chat_id = $this->USERS['andy']['tg_chat_id'];
      $user_notification = '⚠️Ошибка в работе системы - не найдена запись 5-минутной давности!⚠️';
      if ($chat_id) {
        $this->TG_NOTIFY->send_message($chat_id, $user_notification);
      }
      return false;
    }
    $log_30min = $this->BD_REQUESTS->get_log_data(30, $disable_checks);
    $log_1hour = $this->BD_REQUESTS->get_log_data(60, $disable_checks);
    $log_6hour = $this->BD_REQUESTS->get_log_data(360, $disable_checks);
    $log_1day = $this->BD_REQUESTS->get_log_data(1440, $disable_checks);
    $notify_data = array();
    foreach ($token_prices as $token_address => $token_price) {
      if (!$token_address or !$token_price) continue;
      if ($token_price >= 1) {
        $token_price_round = round($token_price, 2);
      } else {
        $round_level = abs(floor(log10($token_price)))+2;
        //$token_price_round = round($token_price, $round_level);
        $token_price_round = number_format($token_price, $round_level, '.', '');
      }
      $token_5min_price_diff = false;
      $token_30min_price_diff = false;
      $token_1hour_price_diff = false;
      $token_6hour_price_diff = false;
      $token_1day_price_diff = false;

      $token_info_5min_ago = $log_5min[$token_address];
      if (!$token_info_5min_ago) continue;
      if ($log_30min) $token_info_30min_ago = $log_30min[$token_address];
      if ($log_1hour) $token_info_1hour_ago = $log_1hour[$token_address];
      if ($log_6hour) $token_info_6hour_ago = $log_6hour[$token_address];
      if ($log_1day) $token_info_1day_ago = $log_1day[$token_address];

      if ($log_5min and $token_info_5min_ago) {
        if ($token_info_5min_ago['price']) {
          $token_5min_price_diff = round(($token_price/$token_info_5min_ago['price'])*100 - 100, 2);
        }
      }
      if ($log_30min and $token_info_30min_ago) {
        if ($token_info_30min_ago['price']) {
          $token_30min_price_diff = round(($token_price/$token_info_30min_ago['price'])*100 - 100, 2);
        }
      }
      if ($log_1hour and $token_info_1hour_ago) {
        if ($token_info_1hour_ago['price']) {
          $token_1hour_price_diff = round(($token_price/$token_info_1hour_ago['price'])*100 - 100, 2);
        }
      }
      if ($log_6hour and $token_info_6hour_ago) {
        if ($token_info_6hour_ago['price']) {
          $token_6hour_price_diff = round(($token_price/$token_info_6hour_ago['price'])*100 - 100, 2);
        }
      }
      if ($log_1day and $token_info_1day_ago) {
        if ($token_info_1day_ago['price']) {
          $token_1day_price_diff = round(($token_price/$token_info_1day_ago['price'])*100 - 100, 2);
        }
      }
      $token_notify = '';
      if (mb_stripos($token_info_5min_ago['title'], 'PTNS') !== false) {
        //pump tracking and notification system
        $notify_level = 15;
        $title_array = explode('-', $token_info_5min_ago['title']);
        if ((int)$title_array[1] and $title_array[2]) {
          $notify_level = $title_array[1];
        }
        if ($token_5min_price_diff >= $notify_level) {
          $token_notify = '✴️✴️✴️ '.$token_info_5min_ago['title'].' взлетает! ✴️✴️✴️';
        }
      } else {
        //main tokens
        if ($token_5min_price_diff >= 30) {
          $token_notify = '❇️❇️❇️ '.$token_info_5min_ago['title'].' пампится! ❇️❇️❇️';
        } else if ($token_5min_price_diff >= 7.5 or ($token_30min_price_diff >= 30 and $token_5min_price_diff >= 5 and $token_30min_price_diff)) {
          $token_notify = '🟢 '.$token_info_5min_ago['title'].' быстро растёт 🟢';
        } else if ($token_5min_price_diff <= -25) {
          $token_notify = '🛑🛑🛑 '.$token_info_5min_ago['title'].' обваливается! 🛑🛑🛑';
        } else if ($token_5min_price_diff <= -7.5 or ($token_30min_price_diff <= -25 and $token_5min_price_diff <= -5 and $token_30min_price_diff)) {
          $token_notify = '🔴 '.$token_info_5min_ago['title'].' быстро падает 🔴';
        }
      }
      if ($token_notify) {
        $token_notify = $token_notify."\r\n".'`'.$token_address.'`'."\r\n".'Текущая цена - '.$token_price_round."\r\n";
        if ($token_5min_price_diff !== false) {
          if ($token_5min_price_diff > 0) {
            $token_notify = $token_notify.'🔹 5 мин +'.$token_5min_price_diff.' %';
          } else if ($token_5min_price_diff == 0) {
            $token_notify = $token_notify.'▫️ 5 мин 0 %';
          } else {
            $token_notify = $token_notify.'🔻 5 мин '.$token_5min_price_diff.' %';
          }
        }
        if ($token_30min_price_diff !== false) {
          if ($token_30min_price_diff > 0) {
            $token_notify = $token_notify.' // 🔹 30 мин +'.$token_30min_price_diff.' %';
          } else if ($token_30min_price_diff == 0) {
            $token_notify = $token_notify.' // ▫️ 30 мин 0 %';
          } else {
            $token_notify = $token_notify.' // 🔻 30 мин '.$token_30min_price_diff.' %';
          }
        }
        if ($token_1hour_price_diff !== false) {
          if ($token_1hour_price_diff > 0) {
            $token_notify = $token_notify."\r\n".'🔹 1 час +'.$token_1hour_price_diff.' %';
          } else if ($token_1hour_price_diff == 0) {
            $token_notify = $token_notify."\r\n".'▫️ 1 час 0 %';
          } else {
            $token_notify = $token_notify."\r\n".'🔻 1 час '.$token_1hour_price_diff.' %';
          }
        }
        if ($token_6hour_price_diff !== false) {
          if ($token_6hour_price_diff > 0) {
            $token_notify = $token_notify.' // 🔹 6 часов +'.$token_6hour_price_diff.' %';
          } else if ($token_6hour_price_diff == 0) {
            $token_notify = $token_notify.' // ▫️ 6 часов 0 %';
          } else {
            $token_notify = $token_notify.' // 🔻 6 часов '.$token_6hour_price_diff.' %';
          }
        }
        if ($token_1day_price_diff !== false) {
          if ($token_1day_price_diff > 0) {
            $token_notify = $token_notify.' // 🔹 сутки +'.$token_1day_price_diff.' %';
          } else if ($token_1day_price_diff == 0) {
            $token_notify = $token_notify.' // ▫️ сутки 0 %';
          } else {
            $token_notify = $token_notify.' // 🔻 сутки '.$token_1day_price_diff.' %';
          }
        }
        $notify_data[$token_address] = $token_notify;
      }
      //echo 'token - '.$token_info_5min_ago['title'].', price_diff '.$token_5min_price_diff.'<br>';
    }
    //echo '<pre>'; print_r($notify_data); echo '</pre>';
    return $notify_data;
  }

  public function send_users_notifications($notify_data)
  {
    foreach ($this->USERS as $user => $user_data) {
      $user_notification = '';
      $user_tokens = $this->FOLLOWING_TOKENS[$user];
      foreach ($notify_data as $token => $token_info) {
        if (in_array($token, $user_tokens)) {
          if ($user_notification) $user_notification = $user_notification."\r\n"."\r\n";
          $user_notification = $user_notification.$token_info;
        }
      }
      $chat_id = $this->USERS[$user]['tg_chat_id'];
      if ($chat_id) {
        $this->TG_NOTIFY->send_message($chat_id, $user_notification);
      }
    }
    return true;
  }

  public function send_token_price_request($tokens_list_str)
  {
    $queryUrl = 'simple/networks/solana/token_price/'.$tokens_list_str;
    $res = $this->send_post_geckoterminal($queryUrl, false);
    $prices_data = $res['data']['attributes']['token_prices'];
    return $prices_data;
    return array();
  }

	public function send_post_geckoterminal($queryUrl, $queryData)
	{
    $queryUrl = 'https://api.geckoterminal.com/api/v2/'.$queryUrl;
    $headers = array(
      'Content-Type: application/json; charset=utf-8',
			'Accept: application/json;version=20230302',
		);
    if ($queryData) {
      $queryData = http_build_query($queryData);
      $queryUrl = $queryUrl.'?'.$queryData;
    }
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_SSL_VERIFYPEER => 0,
			//CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $queryUrl,
      //CURLOPT_POSTFIELDS => $queryData,
      CURLOPT_HTTPHEADER => $headers,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_NOSIGNAL => 60,
		));
		$result = curl_exec($curl);
		curl_close($curl);
		return json_decode($result, 1);
	}
}
