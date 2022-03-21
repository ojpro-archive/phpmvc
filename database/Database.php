<?php

namespace ojpro\phpmvc\database;

use ojpro\phpmvc\Application;
use PDO;

class Database
{
    public PDO $pdo;
    public function __construct(array $config)
    {
        $this->pdo = new PDO($config['dsn'], $config['user'], $config['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function applayMigration()
    {
        $newMigrations = [];
        $this->createMigrationTable();
        $applied = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR.'/migrations');

        $toApplayMigrations = array_diff($files, $applied);

        foreach ($toApplayMigrations as $migration) {
            if ($migration !== '.' && $migration !== '..') {
                require_once Application::$ROOT_DIR .'/migrations/'.$migration;
                $className = "app\migrations\\".pathinfo($migration, PATHINFO_FILENAME);
                $instance = new $className();
                echo $this->log("Applaying migration: $migration");
                $instance->up();
                echo $this->log("Applied migration: $migration");
                $newMigrations[] = $migration;
            }
        }
        if (!empty($newMigrations)) {
            $this->saveMigration($newMigrations);
        } else {
            echo $this->log("All migrations are applied");
        }
    }
    public function createMigrationTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations(
            id INT auto_increment PRIMARY KEY,
            migration VARCHAR(200),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=INNODB;");
    }
    public function getAppliedMigrations()
    {
        $statement =$this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }
    public function saveMigration(array $migrations)
    {
        $migrations = array_map(fn ($migration) => "('$migration')", $migrations);
        $migrationsValues = implode(',', $migrations);
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $migrationsValues");
        $statement->execute();
    }
    protected function log($message)
    {
        return '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }
}