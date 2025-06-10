<?php

use PDO;
use PDOException;
use Exception;

class DataConnection
{
    private static $instance;

    /**
     * Retorna a instância da conexão PDO com tratamento de erro.
     * @return PDO|null
     */
    public static function get_connection(): ?PDO
    {
        try {
            if (!isset(self::$instance)) {
                $config = self::loadDatabaseConfig();
                self::$instance = new PDO(
                    "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
                    $config['user'],
                    $config['password']
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return self::$instance;
        } catch (PDOException $e) {
            error_log("Erro de conexão com o banco: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Carrega as credenciais do banco de dados a partir do arquivo de configuração.
     * @return array
     */
    private static function loadDatabaseConfig(): array
    {
        $configPath = __DIR__ . '/../config/database.php';
        if (!file_exists($configPath)) {
            throw new Exception("Arquivo de configuração do banco não encontrado.");
        }
        return require $configPath;
    }
}