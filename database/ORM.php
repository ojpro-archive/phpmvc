<?php

namespace ojpro\phpmvc\database;

use ojpro\phpmvc\Application;
use ojpro\phpmvc\Model;

abstract class ORM extends Model
{
    public static string $primaryKey = "id";
    abstract public function tableName():string;
    public static function primaryKey()
    {
        return self::$primaryKey;
    }
    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();

        $params = implode(",", $attributes);

        $values = implode(",", array_map(fn ($attr) => "':$attr'", $attributes));

        $query = "INSERT INTO $tableName ($params) VALUES ($values)";

        foreach ($attributes as $attribute) {
            $query = str_replace(":$attribute", $this->{$attribute}, $query);
        }

        $statement = self::prepare($query);

        $statement->execute();
        return true;
    }
    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);

        $condition = implode(" AND ", array_map(fn ($attr) =>" $attr = ':$attr'", $attributes));
        $statement = "SELECT * FROM $tableName WHERE $condition";

        foreach ($where as $key => $item) {
            $statement = str_replace(":$key", $item, $statement);
        }

        $stmt = self::prepare($statement);
        $stmt->execute();
        return $stmt->fetchObject(static::class);
    }
}