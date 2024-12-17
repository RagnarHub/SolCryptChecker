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


}