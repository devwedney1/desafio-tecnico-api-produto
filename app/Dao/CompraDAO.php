<?php

namespace App\Dao;

use App\Connection\DataConnection;
use App\Model\Compra;
use App\Model\Parcela;
use App\Model\Produto;
use PDO;
use PDOException;
use taxaJuros;

class CompraDAO
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

    public function todasComprasGet (): array
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

                $todasComprasGet[] = ['idCompra' => $row['idCompra'], 'nomeProduto' => $row['nomeProduto'], 'tipoProduto' => $row['tipoProduto'], 'valorProduto' => (float)$row['valorProduto'], 'valorEntrada' => (float)$row['valorEntrada'], 'qtdParcelas' => (int)$row['qtdParcelas'], 'valorParcela' => (float)$row['valorParcela'], 'taxaJuros' => (float)$row['taxaJuros']];
            }

            return $todasComprasGet;
        } catch (PDOException $e) {
            throw new PDOException("ERRO ao recuparr todas as compras e suas informaçoes: " . $e->getMessage());
        }

    }

    public function inserirParcelas (array $parcelas)
    {
        try {

            $stmt = $this->connection->prepare('INSERT INTO parcelas (id, idcompra, numeroParcela, valorParcela, dataVencimento) VALUES (?, ?, ?, ?, ?)');

            foreach ($parcelas as $parcela) {

                $stmt->bindValue(1, $parcela['id']);
                $stmt->bindValue(2, $parcela['idCompra']);
                $stmt->bindValue(3, $parcela['numeroParcela'], PDO::PARAM_INT);
                $stmt->bindValue(4, number_format($parcela['valorParcela'], 2, '.', ''), PDO::PARAM_STR);
                $stmt->bindValue(5, $parcela['dataVencimento']);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            throw new PDOException("ERRO ao cadastra as compras: " . $e->getMessage());
        }
    }

    /**
     * @return array|null
     */
    public function buscarTaxaJurosAtual (): ?array
    {
        try {
            $sql = "SELECT id, taxa FROM taxa_juros WHERE CURRENT_DATE BETWEEN dataInicio AND dataFinal LIMIT 1";
            $stmt = $this->connection->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new PDOException("ERRO ao buscar taxa de juros atual: " . $e->getMessage());
        }
    }

    /**
     * @param Compra $compra
     *
     * @return void
     */
    public function inserir (Compra $compra): void
    {
        $sql = "INSERT INTO compras (id, idProduto, valorEntrada, qtdParcelas, idTaxaJuros)
            VALUES (:id, :idProduto, :valorEntrada, :qtdParcelas, :idTaxaJuros)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $compra->getId());
        $stmt->bindValue(':idProduto', $compra->getIdProduto());
        $stmt->bindValue(':valorEntrada', $compra->getValorEntrada());
        $stmt->bindValue(':qtdParcelas', $compra->getQtdParcelas());
        $stmt->bindValue(':idTaxaJuros', $compra->getIdTaxaJuros());

        $stmt->execute();
    }

    /**
     * @param string $idProduto
     *
     * @return float
     */
    public function buscarValorProduto (string $idProduto): float
    {
        try {
            $sql = "SELECT valorProduto FROM produtos WHERE id = :id";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $idProduto, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (float)$result['valorProduto'] : 0.0;
        } catch (PDOException $e) {
            throw new PDOException("ERRO ao buscar valor produto: " . $e->getMessage());
        }
    }
}