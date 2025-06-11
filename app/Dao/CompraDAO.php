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
        SELECT
            compras.id AS idCompra,
            produtos.nome AS nomeProduto,
            produtos.tipo AS tipoProduto,
            produtos.valorProduto AS valorProduto,
            compras.valorEntrada AS valorEntrada,
            compras.qtdParcelas AS qtdParcelas,
            parcelas.valorParcela AS valorParcela,
            taxa_juros.taxa AS taxaJuros
        FROM parcelas
        INNER JOIN compras ON parcelas.idCompra = compras.id
        INNER JOIN produtos ON compras.idProduto = produtos.id
        INNER JOIN taxa_juros ON compras.idTaxaJuros = taxa_juros.id
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