<?php

namespace App;

use PDO;
use PDOException;
use stdClass;

class Database
{
    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            try {
                $params = self::getConnectionParams(true);
                $dsn = "pgsql:host=$params->host;port=$params->port;dbname=$params->dbname;";

                self::$connection = new PDO($dsn, $params->username, $params->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                return self::$connection;
            } catch (PDOException $e) {
                die("Ошибка подключения: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function getConnectionParams(bool $asObject = false): array|object
    {

        $params = [
            'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_pgsql',
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'] ?? '',
        ];

        if ($asObject) {
            $object = new stdClass();
            $object->host = $params['host'];
            $object->port = $params['port'];
            $object->dbname = $params['dbname'];
            $object->username = $params['user'];
            $object->password = $params['password'];

            return $object;
        }

        return $params;
    }
}