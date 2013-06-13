<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weboniselab
 * Date: 3/6/13
 * Time: 2:27 PM
 * To change this template use File | Settings | File Templates.
 */
class DbWrapper {
//initialize the instance variable
    private static $instance;
    private static $db;


    /*create db connection*/
    public static function getInstance() {
        if (!self::$instance) {
            try {
                $config['db'] =
                    array(
                        'username' => 'root',
                        'password' => 'webonise6186',
                        'database_name' => 'test',
                        'host' => 'localhost',
                    );
                self::$db = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['database_name'], $config['db']['username'], $config['db']['password']);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // set error reporting for PDO
                self::$instance = new DbWrapper();
            } catch (PDOException $message) {
                echo 'Connection failed: ' . $message->getMessage();
            }
        }
        return self::$instance;
    }

    /*This is  select method*/
    public function select($fields = '*') {
        /*if array then implode the array */
        is_array($fields) ? $this->query = 'SELECT ' . implode(', ', $fields) : $this->query = 'SELECT *';
        return $this;
    }

    public function from($tables) {
        /*Check that $tables are empty or not*/
        if (isset($tables) && !empty($tables)) {
            $this->query .= ' FROM ' . implode(', ', $tables);
        }
        return $this;
    }

    /*This function is used */
    public function where($conditions) {
        $where_string = ' WHERE ';
        $operator = '';
        $result_conditions = $conditions;

        if (isset($conditions['between'])) {
            $where_string .= $conditions['between'][0] . " BETWEEN " . $conditions['between'][1] . ' AND ' . $conditions['between'][2];
            $operator = 'between';
        }
        if (isset($conditions['OR'])) {
            $result_conditions = $conditions['OR'];
            $operator = 'OR';
        }

        if (isset($conditions['AND']) || isset($conditions['and'])) {
            $result_conditions = isset($conditions['AND']) ? $conditions['AND'] : $conditions['and'];
            $operator = 'AND';
        }

        foreach ($result_conditions as $key => $condition) {
            $where_string .= $key . $condition . " $operator ";
        }
        $this->query .= rtrim($where_string, " $operator");
        return $this;
    }

    public function groupBy($field) {
        $this->query .= " group by $field";
        return $this;
    }

    public function result($query = null) {
        try {
            $query = $query == null ? $this->query . ';' : $query;
            $statement = self::$db->query($query);
            echo $query;
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $message) {
            echo 'Result is not generated properly';
            echo $message->getMessage();
        }
    }

    /*saving function*/
    public function save($table, $params, $conditions = null) {
        if (isset($conditions) && !empty($conditions)) {

            $this->query = "UPDATE " . $table . "set ";
            foreach ($params as $key => $param) {
                $this->query .= $key . $param . ' ,';
            }
            $this->query = rtrim($this->query, ',');
            $whereStr = ' WHERE ';
            $operator = 'AND';
            $cnd = $conditions;

            if (isset($conditions['OR'])) {
                $cnd = $conditions['OR'];
                $operator = 'OR';
            }
            if (isset($conditions['between'])) {
                $whereStr .= $conditions['between'][0] . " BETWEEN " . $conditions['between'][1] . ' AND ' . $conditions['between'][2];
                return $whereStr;
            }
            foreach ($cnd as $key => $condition) {
                $whereStr .= $key . $condition . " $operator ";
            }
            $this->query .= rtrim($whereStr, " $operator");

            echo $this->query;
            try {
                $stmt = self::$db->prepare($this->query);
                if (!$stmt->execute()) {
                    throw new Exception('Error in saving.');
                }
                return true;
            } catch (PDOException $e) {
                echo 'There is an error in saving function';
                echo $e->getMessage();
            }

        } else {
            $this->insert($table, $params);
        }
    }

    public function delete($table, $conditions = null) {
        $whereStr = ' WHERE ';
        $operator = 'AND';
        $cnd = $conditions;

        if (isset($conditions['OR'])) {
            $cnd = $conditions['OR'];
            $operator = 'OR';
        }
        if (isset($conditions['between'])) {
            $whereStr .= $conditions['between'][0] . " BETWEEN " . $conditions['between'][1] . ' AND ' . $conditions['between'][2];
            return $whereStr;
        }
        foreach ($cnd as $key => $condition) {
            $whereStr .= $key . $condition . " $operator ";
        }
        $conditional_string = rtrim($whereStr, " $operator");


        $this->query = "DELETE FROM $table" . ($conditions != null ? $conditional_string : '');
        echo $this->query;
        try {
            $stmt = self::$db->prepare($this->query);
            if (!$stmt->execute()) {
                throw new Exception('Error in deleting.');
            }
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function insert($table, $params) {

        $this->query = "INSERT INTO" . $table;
        $values = "";
        foreach ($params as $key => $param) {
            $values .= ":$key ,";
        }
        $cols = implode(array_keys($params), ',');
        $values = rtrim($values, ' ,');
        $this->query .= " ($cols) VALUES($values);";
        $stmt = self::$db->prepare($this->query);
        foreach ($params as $key => $param) {
            $stmt->bindValue(":{$key}", $param);
        }
        if (!$stmt->execute()) {
            throw new Exception('Error in saving.');
        }
        return true;
    }

    public function limit($limit, $offset = null) {

        settype($limit, 'integer');
        $offset != null ? settype($offset, 'integer') : '';
        $this->query .= " LIMIT $limit" . ($offset != null ? ", $offset" : '');
        return $this;
    }

    public function orderBy($fieldName, $order = 'ASC') {
        $this->query .= " ORDER BY $fieldName $order";
        return $this;
    }

    public function getQuery() {
        return $this->query;
    }

}
