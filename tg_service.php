<?

class BotTgNotify {

  private $TG_TOKEN;
	public $BOT_ID;
	public $BOT_USERNAME;

	public function __construct()
	{
    $this->TG_TOKEN = ''; //tg bot token
		$this->BOT_ID = ''; //tg bot id
		$this->BOT_USERNAME = ''; //tg bot username
	}

	public function send_message($chat_id, $message)
	{
		$bot_token = $this->TG_TOKEN;
		$queryUrl = 'https://api.telegram.org/bot'.$bot_token.'/sendMessage';
		$queryData = array(
			'chat_id' => $chat_id,
			'text' => $message,
			'parse_mode' => 'MARKDOWN'
		);
		$result = $this->send_post($queryUrl, $queryData);
		return $result;
	}

	public function reply_message($chat_id, $message, $reply_to_message_id)
	{
		$bot_token = $this->TG_TOKEN;
		$queryUrl = 'https://api.telegram.org/bot'.$bot_token.'/sendMessage';
		$queryData = array(
			'chat_id' => $chat_id,
			'text' => $message,
			'parse_mode' => 'HTML',
			'reply_to_message_id' => $reply_to_message_id
		);
		$result = $this->send_post($queryUrl, $queryData);
		return $result;
	}

	public function send_sticker($chat_id, $sticker, $reply_to_message_id = false)
	{
		$bot_token = $this->TG_TOKEN;
		$queryUrl = 'https://api.telegram.org/bot'.$bot_token.'/sendSticker';
		$queryData = array(
			'chat_id' => $chat_id,
			'sticker' => $sticker,
		);
		if ($reply_to_message_id) {
			$queryData['reply_to_message_id'] = $reply_to_message_id;
		}
		$result = $this->send_post($queryUrl, $queryData);
		return $result;
	}

	public function send_animation($chat_id, $gif, $reply_to_message_id = false)
	{
		$bot_token = $this->TG_TOKEN;
		$queryUrl = 'https://api.telegram.org/bot'.$bot_token.'/sendAnimation';
		$queryData = array(
			'chat_id' => $chat_id,
			'animation' => $gif,
		);
		if ($reply_to_message_id) {
			$queryData['reply_to_message_id'] = $reply_to_message_id;
		}
		$result = $this->send_post($queryUrl, $queryData);
		return $result;
	}

  public function send_file($tg_chat_id, $file_url)
	{
		$bot_token = $this->TG_TOKEN;
		$queryUrl = 'https://api.telegram.org/bot'.$bot_token.'/sendPhoto';
		$queryData = array(
			'chat_id' => $tg_chat_id,
			'photo' => new \CURLFile($file_url),
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_URL, $queryUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

  public function set_webhook()
	{
		$bot_token = $this->TG_TOKEN;
		$queryUrl = 'https://api.telegram.org/bot'.$bot_token.'/setWebhook';
		$queryData = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_URL, $queryUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);
		$output = curl_exec($ch);
		return $output;
	}

	public function send_post($queryUrl, $queryData) {
		$queryData = http_build_query($queryData);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $queryUrl,
			CURLOPT_POSTFIELDS => $queryData,
		));
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

}