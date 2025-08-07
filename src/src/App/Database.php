<?php

namespace App;

use PDO;
use PDOException;
use Redis;
use stdClass;

class Database
{
    private static $connection;
    private static $redis;

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

    public static function getRedisConnection()
    {
        if (!self::$redis) {
            try {
                $params = self::getConnectionParams(true);

                self::$redis = new Redis();
                self::$redis->connect($params->redis_host, $params->redis_port);
                self::$redis->ping();
                
                return self::$redis;
            } catch (\RedisException $e) {
                die("Ошибка подключения к redis: " . $e->getMessage());
            }
        }

        return self::$redis;
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

            'redis_host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'redis_port' => $_ENV['REDIS_PORT'] ?? 6379,
        ];

        if ($asObject) {
            $object = new stdClass();
            $object->host = $params['host'];
            $object->port = $params['port'];
            $object->dbname = $params['dbname'];
            $object->username = $params['user'];
            $object->password = $params['password'];

            $object->redis_host = $params['redis_host'];
            $object->redis_port = $params['redis_port'];

            return $object;
        }

        return $params;
    }
}