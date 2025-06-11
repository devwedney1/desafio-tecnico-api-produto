<?php

namespace App\Dao;

use App\Connection\DataConnection;
use App\Model\Parcela;
use App\Model\Produto;
use Compra;
use PDO;
use PDOException;
use taxaJuros;

Class CompraDAO
{
    private $connection;
    private const tableNameCompra = 'Compras';
    private const tableNameProduto = 'Produtos';
    private const tableNameParcela = 'Parcelas';
    private const tableNameTaxaJuros = 'taxa_juros';

    public function __construct ()
    {
        $this->connection = DataConnection::get_connection();
    }

    public function todasComprasGet(): array
    {
        try {
            $sql = "
        SELECT DISTINCT
            c.id AS idCompra,
            p.nome AS nomeProduto,
            p.tipo AS tipoProduto,
            p.valorProduto AS valorProduto,
            c.valorEntrada AS valorEntrada,
            c.qtdParcelas AS qtdParcelas,
            parc.valorParcela AS valorParcela,
            tj.taxa AS taxaJuros
        FROM compras c
        LEFT JOIN produtos p ON c.idProduto = p.id
        LEFT JOIN taxa_juros tj ON c.idTaxaJuros = tj.id
        LEFT JOIN (
            SELECT idCompra, valorParcela 
            FROM parcelas 
            WHERE numeroParcela = 1
        ) parc ON parc.idCompra = c.id
        WHERE c.deleted_at IS NULL
    ";

            $stmt = $this->connection->prepare($sql);
            $stmt->execute();

            $todasComprasGet = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $todasComprasGet[] = [
                    'idCompra'      => $row['idCompra'],
                    'nomeProduto'   => $row['nomeProduto'],
                    'tipoProduto'   => $row['tipoProduto'],
                    'valorProduto'  => (float)$row['valorProduto'],
                    'valorEntrada'  => (float)$row['valorEntrada'],
                    'qtdParcelas'   => (int)$row['qtdParcelas'],
                    'valorParcela'  => (float)$row['valorParcela'],
                    'taxaJuros'     => (float)$row['taxaJuros']
                ];
            }

            return $todasComprasGet;
        } catch (PDOException $e) {
            throw new PDOException("ERRO ao recuparr todas as compras e suas informaçoes: " . $e->getMessage());
        }

    }

}