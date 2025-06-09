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

        // Gravar compra
        $stmt = $pdo->prepare("INSERT INTO compras (id, valorEntrada, qtdParcelas, idProduto, jurosAplicado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['id'], $data['valorEntrada'], $qtdParcelas, $data['idProduto'], $juros]);

        // Gravar parcelas
        for ($i = 1; $i <= $qtdParcelas; $i++) {
            $stmt = $pdo->prepare("INSERT INTO parcelas (id_compra, numero, valor) VALUES (?, ?, ?)");
            $stmt->execute([$data['id'], $i, $valorParcela]);
        }

        return True;
    }
}