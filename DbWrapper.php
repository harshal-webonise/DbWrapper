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

    private static function getInstance() {
        if (!self::$instance) {
            self::$host = 'localhost';
            self::$dbName = 'test';
            self::$connect = self::connect();
            self::$instance = new DbWrapper();
        }
        return self::$instance;
    }

    public function connect() {
        try {
            $connection = new PDO('mysql:host=localhost;dbname=test', 'root', 'webonise6186');
            {
                return $connection;
            }
        } catch (PDOException $message) {
            print"Error Occurred" . $message->getMessage() . "</br>";
        }

    }

}
