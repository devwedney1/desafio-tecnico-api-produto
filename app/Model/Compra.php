<?php
require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/Produto.php';

class Compra {
    public function registrar($data) {
        $pdo = getDatabaseConnection();
        $produto = new Produto();
        $infoProduto = $produto->buscarPorId($data['idProduto']);

        $valorTotal = $infoProduto['valor'] - $data['valorEntrada'];
        $qtdParcelas = $data['qtdParcelas'];
        $juros = 0.00;

        if ($qtdParcelas > 6) {
            $selic = new Selic();
            $juros = $selic->getTaxa();
            $valorTotal *= (1 + $juros);
        }

        $valorParcela = $valorTotal / $qtdParcelas;

        /* Gravar compra */
        $stmt = $pdo->prepare("INSERT INTO compras (id, valorEntrada, qtdParcelas, idProduto, jurosAplicado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['id'], $data['valorEntrada'], $qtdParcelas, $data['idProduto'], $juros]);

        /* Gravar parcelas */
        for ($i = 1; $i <= $qtdParcelas; $i++) {
            $stmt = $pdo->prepare("INSERT INTO parcelas (id_compra, numero, valor) VALUES (?, ?, ?)");
            $stmt->execute([$data['id'], $i, $valorParcela]);
        }

        return True;
    }

    public function getEstatisticas() {
        $pdo = getDatabaseConnection();
    
        $stmt = $pdo->query("SELECT COUNT(*) AS count FROM compras");
        $count = (int) $stmt->fetch()['count'];
    
        if ($count === 0) {
            return [
                'count' => 0,
                'sum' => 0.0,
                'avg' => 0.0,
                'sumTx' => 0.0,
                'avgTx' => 0.0
            ];
        }
    
        $stmt = $pdo->query("
            SELECT 
                SUM(valorEntrada + (
                    SELECT SUM(valorParcela)
                    FROM parcelas p
                    WHERE p.idCompra = c.id
                )) AS sum,
                AVG(valorEntrada + (
                    SELECT SUM(valorParcela)
                    FROM parcelas p
                    WHERE p.idCompra = c.id
                )) AS avg
            FROM compras c
        ");
        $result = $stmt->fetch();
        $sum = (float) $result['sum'];
        $avg = (float) $result['avg'];
    
        $stmt = $pdo->query("SELECT SUM(jurosAplicado) AS sumTx, AVG(jurosAplicado) AS avgTx FROM compras");
        $juros = $stmt->fetch();
        $sumTx = $juros['sumTx'] !== null ? (float) $juros['sumTx'] : 0.0;
        $avgTx = $juros['avgTx'] !== null ? (float) $juros['avgTx'] : 0.0;
    
        return [
            'count' => $count,
            'sum' => $sum,
            'avg' => $avg,
            'sumTx' => $sumTx,
            'avgTx' => $avgTx
        ];
    }           
}