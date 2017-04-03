<?php
class Database { 
    public function __construct() {
        $dsn = 'mysql:host=' . HOST . ';dbname=' . DBNAME;
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        try {
            $this->dbh = new PDO($dsn, DBUSER, DBPASS, $options);
			$this->dbh->exec("SET NAMES 'utf8'");
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            var_dump($this->error);exit;
        }
    }
    
    public function query($query) {
        $this->stmt = $this->dbh->prepare($query);
    }
    
    public function bind($param, $value = null, $type = null) {
        if(is_array($param)) {
            foreach($param AS $field=>$value) {
                $this->bind($field, $value);
            }
        } else {
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
        }
    }
    
    public function execute() {
        return $this->stmt->execute();
    }
    
    public function resultset() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    public function endTransaction() {
        return $this->dbh->commit();
    }
    
    public function cancelTransaction() {
        return $this->dbh->rollBack();
    }
    
    public function debugDumpParams() {
        //return $this->stmt->debugDumpParams();        
        echo '<pre>Debug Start>>><br />';
        print_r($this->stmt->debugDumpParams());
        echo '</pre><br />Debug End<<<<hr />';
    }
    
    public static function debugQuery($query, $params = null) {
        if($params !== null) {
            foreach ($params as $key => $value) {
                $query = str_replace($key, '"'.$value.'"', $query);
            }
        }
        echo $query;
    }
}
