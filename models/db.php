<?php
/**
 * Created by PhpStorm.
 * User: kalivoda
 * Date: 05.12.2017
 * Time: 17:05
 */

class db
{
    private static $connection;

    private static $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    );

    public static function connect($host, $database, $user, $pw){
            if(!isset(self::$connection)) {
                try {
                    self::$connection = new PDO("mysql:host=" . $host . ";dbname=" . $database . "",
                        $user, $pw, self::$options);
                } catch (PDOException $e) {
                    print "Error!: " . $e->getMessage() . "<br/>";
                    die();
                }
            }
    }

    public static function selectOne($query, $params) {
        self::connect("localhost", "web", "root", "");
        $ret = self::$connection->prepare($query);
        $ret->execute($params);
        return $ret->fetch();
    }

    public static function selectAll($query, $params) {
        self::connect("localhost", "web", "root", "");
        $ret = self::$connection->prepare($query);
        $ret->execute($params);
        return $ret->fetchAll();
    }

    public static function insert($table, $item) {
        self::connect("localhost", "web", "root", "");
        $insert_columns = "";
        $insert_values = "";

        if($item != null)
            foreach ($item as $column => $value) {
                if ($insert_columns != "") $insert_columns .= ", ";
                if ($insert_columns != "") $insert_values .= ", ";
                $insert_columns .= "`$column`";
                $insert_values .= "?";
            }

        $query = "INSERT INTO `$table` ($insert_columns) VALUES ($insert_values);";

        $stmt = self::$connection->prepare($query);

        $bind_param_number = 1;
        if ($item != null)
            foreach ($item as $column => $value)
            {
                if ($value)
                {
                    $stmt->bindValue($bind_param_number, $value);
                    $bind_param_number ++;
                }
            }
        $stmt->execute();

        $item_id = self::$connection->lastInsertId();
        return $item_id;
    }

    public static function updateRecord($query, $params) {
        self::connect("localhost", "web", "root", "");
        $ret = self::$connection->prepare($query);
        $ret->execute($params);
    }
}