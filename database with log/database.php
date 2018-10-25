<?php

/**
*
* PDO-Simple-Class
* @author:  Pramote Supnoi
* @git:   https://github.com/pramotesupnoi/PDO-Simple-Class
* @license: MIT
*
*/

class Database {

  # @string database type
  private $dbType;

  # @string charset
  private $charset;

  # @string database hostname
  private $hostname;

  # @string database username
  private $username;

  # @string database password
  private $password;

  # @string database name
  private $database;

  # @boolean Emulation prepare value 
  private $useEmulate;

  # @object PDO object
  public $pdo = null;

  # @object, PDO statement object
  private $stmtQuery;

  # @object, Log object
  private $log;

  /**
  *
  * Class Constructor 
  * Set default value and connect to database
  *
  */ 
  public function __construct() {
    $this->Connect();
  }

  /**
  *
  * Class Destructor 
  * Clear connection
  *
  */ 
  public function __destruct() {
    $this->CloseConnection();
  }

  /**
  *
  * Read configuration file
  * Connect to database
  * Create pdo object
  * Set PDO Error mode
  * Set PDO emulation of prepared statements
  *
  */
  private function Connect() {
    $conf = parse_ini_file("db.config.ini", true);
    $this->dbType = $conf["database"]["dbtype"];
    $this->charset = $conf["database"]["charset"];
    $this->hostname = $conf["database"]["host"];
    $this->username = $conf["database"]["uname"];
    $this->password = $conf["database"]["pwd"];
    $this->database = $conf["database"]["dbname"];
    $this->useEmulate = $conf["database"]["emulate"];

    $strConn = $this->dbType.':host='.$this->hostname.';dbname='.$this->database.';charset='.$this->charset;

    try {
      $this->log = new DatabaseLog();
      $this->pdo = new PDO($strConn, $this->username, $this->password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
      $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->useEmulate);
    }
    catch (PDOException $e) 
    {
      echo $e->getMessage();
      die();
    }
  }

  /**
  *
  * Close connection by set pdo object to null
  *
  */
  private function CloseConnection() {
    $this->pdo = null;
  }

  /**
  *
  * Sql command will execute here
  *
  * @param string $sql
  * @param array $param
  * @return boolean
  *
  * 1. Check if pdo object is null, connect to database
  * 2. trim sql command
  * 3. prepare sql statement
  * 4. execute sql with parameter(s)
  *
  */
  private function executeSql($sql, $param) {
    try {
      if($this->pdo===null) { $this->Connect(); }

      $sql = trim($sql);
      $this->stmtQuery = $this->pdo->prepare($sql);
      $this->stmtQuery->execute($param);
      $this->log->writeLog(true, $sql, "");
      return true;
    }
    catch (PDOException $e) {
      $this->log->writeLog(false, $sql, $e->getMessage());
      return false;
    }
  }

  /** 
  * Count data
  * return (integer) count result if query success
  * return (boolean) false if query fail
  */
  public function dbCount($sql, $param = null) {
    $exc = $this->executeSql($sql, $param);
    return ($exc===true) ? $this->stmtQuery->fetchColumn() : false;
  }

  /**
  * Query data
  *
  * @param string $sql
  * @param array $param
  * @param integer|array $fetchMode // Fetch style can be more than one value by using array Exp. array(PDO::FETCH_COLUMN, 0) (Max array length is 3)
  * @return mixed
  * return (integer) number of affected row if statement is INSERT, UPDATE OR DELETE
  * return (array) data of query result if statement is SELECT or SHOW
  * return (boolean) false if query fail
  * 
  */
  public function dbQuery($sql, $param = null, $fetchMode = PDO::FETCH_ASSOC) {
    $exc = $this->executeSql($sql, $param);

    $statement = explode(" ", $sql, 2);
    $statement = strtolower($statement[0]);
    if($statement==='select' || $statement==='show') {
      if(gettype($fetchMode)==="array") {
        $fetchMode = array_slice($fetchMode, 0, 3);
        $result = call_user_func_array(array($this->stmtQuery, "fetchAll"), $fetchMode);
      } else {
        $result = $this->stmtQuery->fetchAll($fetchMode);
      } 
    } else if($statement==='insert' || $statement==='delete' || $statement==='update') {
      $result = $this->stmtQuery->rowCount();
    } else {
      $result = false;
    }
    return ($exc===true) ? $result : false;
  }

  /**
  * Query only one data row
  *
  * @param string $sql
  * @param array $param
  * @param integer|array $fetchMode // Fetch style can be more than one value by using array Exp. array(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT) (Max array length is 3)
  * @return mixed
  * return (array) array represents a data row
  * return null if not found result
  * return (boolean) false if query fail
  * 
  */
  public function dbRow($sql, $param = null, $fetchMode = PDO::FETCH_ASSOC) {
    $exc = $this->executeSql($sql, $param);

    if(gettype($fetchMode)==="array") {
      $fetchMode = array_slice($fetchMode, 0, 3);
      $result = call_user_func_array(array($this->stmtQuery, "fetch"), $fetchMode);
    } else {
      $result = $this->stmtQuery->fetch($fetchMode);
    } 
    $result = ($result===false) ? null : $result;
    return ($exc===true) ? $result : false;
  }

  /**
  * Query only specific column
  *
  * @param string $sql
  * @param array $param
  * @return mixed
  * return (array) array represents a data column
  * return null if not found result
  * return (boolean) false if query fail
  * 
  */
  public function dbColumn($sql, $param = null) {
    $exc = $this->executeSql($sql, $param);
    $Columns = $this->stmtQuery->fetchAll(PDO::FETCH_NUM);
    $result = null;
    foreach($Columns as $cells) {
      $result[] = $cells[0];
    }
    return ($exc===true) ? $result : false;
  }

  /**
  * Get id of last insert data
  */
  public function lastInsertID() {
    return $this->pdo->lastInsertId();
  }
}

/**
*
* Log class
*
*/
class DatabaseLog {

  # @string log file directory
  private $log_dir;

  # @string log file name prefix
  private $log_pref;

  # @string log file name suffix
  private $log_suff;

  # @string log file type
  private $log_type;

  /**
  *
  * Class Constructor 
  * Set directory name
  * Set log file name prefix and suffix
  *
  */ 
  public function __construct() {
    $conf = parse_ini_file("db.config.ini", true);
    $this->log_dir = __DIR__ . "/" . $conf["database_log"]["log_dir"];
    $this->log_pref = $conf["database_log"]["log_name_prefix"];
    $this->log_suff = $conf["database_log"]["log_name_suffix"];
    $this->log_type = $conf["database_log"]["log_type"];
    $this->checkLogDirectoty();
  }

  /**
  *
  * Class Destructor 
  *
  */ 
  public function __destruct() {
  }

  /**
  *
  * Write log
  * @param boolean sql execute result
  * @param string sql statement
  * @param string message (optional) 
  * 1. Check directory
  * 2. Create log data object
  * 3. Read log file data and convert to array (create new if not exists)
  * 4. Push log data object to array
  * 5. Write data to log file
  *
  */
  public function writeLog($res, $stmt, $msg = "") {
    $this->checkLogDirectoty();

    $logname = $this->getLogName();
    $logtarget = $this->log_dir . $logname;
    $now_stamp = strtotime("now");
    $now = date("Y-m-d H:i:s", $now_stamp);

    $logObj = array(
       'datetime' => $now,
       'timestamp' => $now_stamp,
       'result' => $res,
       'statement' => $stmt, 
       'message' => $msg,
       'ip' => $_SERVER['REMOTE_ADDR'],
    );

    if(!file_exists($logtarget)) {
      $tmpArr = array();
    } else {
      $oldJson = file_get_contents($logtarget);
      $tmpArr = json_decode($oldJson, true);
    }
    array_push($tmpArr, $logObj);
    $newJson = json_encode($tmpArr);
    file_put_contents($logtarget, $newJson);
  }

  /**
  *
  * Check log directory
  * Create log directory if not exists
  *
  */
  private function checkLogDirectoty() {
    if(!is_dir($this->log_dir)){
       mkdir($this->log_dir, 0777, true);
    }
  }

  /**
  *
  * Get log file name using prefix + current date + suffix
  *
  */
  private function getLogName() {
    return $filename = $this->log_pref . date("Y-m-d") . $this->log_suff . "." . $this->log_type;
  }
}

?>
