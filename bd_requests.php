<?

class BdRequests
{
  private $MYSQL;
  public $TABLES;

	public function __construct()
	{
    //put here your data base user name, DB user password, DB name
    $this->MYSQL = new mysqli("localhost", "", "", "");
    $this->MYSQL->query("SET NAMES 'utf-8'");
    if ($this->MYSQL->connect_error) {
      echo 'Error Number: '.$this->MYSQL->connect_errno.'<br>';
      echo 'Error '.$this->MYSQL->connect_error;
    }
    $this->TABLES = array(
      'crypt_log' => 'crypt_log',
    );
	}

  public function close_connection()
  {
    $this->MYSQL->close();
    return true;
  }

  public function get_by_id($table_name, $id)
  {
    $result = $this->MYSQL->query("SELECT * FROM `".$table_name."` WHERE `ID` = ".$id);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        return $row;
      }
    }
    return false;
  }

  public function add_log_data($log)
  {
    $time_point = date_create();
    $title = 'Запись от '.date_format($time_point, 'd.m.Y H:i');
    $date = date_format($time_point, 'Y-m-d H:i:s');
    $log = json_encode($log);
    $this->MYSQL->query("INSERT INTO `".$this->TABLES['crypt_log']."` (`TITLE`, `DATE`, `LOG`) VALUES ('$title', '$date', '$log')");
  }

  public function get_log_data($minutes_ago, $disable_checks = false)
  {
    $time_point = date_create();
    $minutes_ago_request = $minutes_ago + 1; //зазор, чтобы зацепить нужную запись
    date_add($time_point, date_interval_create_from_date_string("-".$minutes_ago_request." minutes"));
    $date = date_format($time_point, 'Y-m-d H:i:s');
    $result = $this->MYSQL->query("SELECT `LOG`, `DATE` FROM `".$this->TABLES['crypt_log']."` WHERE `DATE` >= '$date' LIMIT 1");
    $row = false;
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
    }
    if (!$row) return false;
    $row_date = date_create($row['DATE']);
    date_add($time_point, date_interval_create_from_date_string("1 minutes"));
    $seconds_diff = date_timestamp_get($row_date) - date_timestamp_get($time_point); //между предполагаемым и реальным местом нахождения нужной записи
    $seconds_border = $minutes_ago*60/5;
    //echo 'строка за '.$row['DATE'].'<br>';
    //echo $seconds_diff.'<br>';
    if (!$disable_checks) {
      if ($seconds_diff > $seconds_border) return false;
    }
    return json_decode($row['LOG'], 1);
  }

  public function clear_old_log()
  {
    $time_point = date_create();
    date_add($time_point, date_interval_create_from_date_string("-40 days"));
    $date = date_format($time_point, 'Y-m-d H:i:s');
    echo $date.'<br>';
    $result = $this->MYSQL->query("SELECT `ID` FROM `".$this->TABLES['crypt_log']."` WHERE `DATE` <= '$date' LIMIT 1000");
    while ($row = $result->fetch_assoc()) {
      $this->MYSQL->query("DELETE FROM `".$this->TABLES['crypt_log']."` WHERE `ID` = ".$row['ID']." LIMIT 1");
    }
  }

}
