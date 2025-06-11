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

        public function compraExiste(string $id): bool
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM compras WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function buscarProduto(string $idProduto): ?array
    {
        $stmt = $this->connection->prepare("SELECT valorProduto FROM produtos WHERE id = ?");
        $stmt->execute([$idProduto]);
        return $stmt->fetch() ?: null;
    }

    public function buscarTaxaMaisRecente(): ?array
    {
        $stmt = $this->connection->query("SELECT id, taxa FROM taxa_juros WHERE deleted_at IS NULL ORDER BY dataFinal DESC LIMIT 1");
        return $stmt->fetch() ?: null;
    }

    public function buscarTaxaZero(): ?array
    {
        $stmt = $this->connection->query("SELECT id FROM taxa_juros WHERE taxa = 0 ORDER BY dataFinal DESC LIMIT 1");
        return $stmt->fetch() ?: null;
    }

    public function inserirCompra(string $id, string $idProduto, float $valorEntrada, int $qtdParcelas, string $idTaxa): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO compras (id, idProduto, valorEntrada, qtdParcelas, idTaxaJuros)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id, $idProduto, $valorEntrada, $qtdParcelas, $idTaxa]);
    }

    public function inserirParcela(string $idCompra, int $numero, float $valor, string $vencimento): void
    {
        $stmt = $this->connection->prepare("
            INSERT INTO parcelas (id, idCompras, numeroParcela, valorParcela, dataVencimento)
            VALUES (UUID(), ?, ?, ?, ?)
        ");
        $stmt->execute([$idCompra, $numero, $valor, $vencimento]);
    }
}