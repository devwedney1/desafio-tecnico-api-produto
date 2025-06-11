<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Dao\CompraDAO;

Class Compra
{

    public function index (Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['mensagem' => 'Endpoint /compras ativo']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        require_once __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../../Dao/CompraDAO.php';
        $dao = new CompraDAO();
        
        $data = $request->getParsedBody();

        // Verifica se todos os campos estão presentes
        if (!isset($data['id'], $data['valorEntrada'], $data['qtdParcelas'], $data['idProduto'])) {
            return $response->withStatus(400);
        }

        $id = $data['id'];
        $valorEntrada = (float)$data['valorEntrada'];
        $qtdParcelas = (int)$data['qtdParcelas'];
        $idProduto = $data['idProduto'];
        
        try {
            //require_once __DIR__ . '/../../config/database.php';

            // Verifica se a Compra Existe
            if ($dao->compraExiste($id)) {
                $response->getBody()->write(json_encode(['erro' => 'ID da compra já existe']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Verifica se o produto existe
            $produto = $dao->buscarProduto($idProduto);
            if (!$produto) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }


            $valorProduto = (float)$produto['valorProduto'];

            // Validação 
            if (!is_numeric($data['valorEntrada']) || !is_numeric($data['qtdParcelas'])) {
                $response->getBody()->write(json_encode(['erro' => 'Entrada inválida']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }
            // Regras de negócio
            if ($valorEntrada < 0 || $valorEntrada > $valorProduto || $qtdParcelas <= 0) {
                $response->getBody()->write(json_encode(['erro' => 'Dados inválidos: valor de entrada ou quantidade de parcelas.']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            $valorRestante = $valorProduto - $valorEntrada;
            $parcelas = [];
            $juros = 0;
            $idTaxa = null;

            // Calculo das parcelas com ou sem juros
            if ($qtdParcelas > 6) {
                // Buscar a taxa de juros mais recente
                $taxa = $dao->buscarTaxaMaisRecente();
                if (!$taxa) {
                    $response->getBody()->write(json_encode(['erro' => 'Nenhuma taxa de juros encontrada.']));
                    return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
                }

                $juros = (float)$taxa['taxa'];
                $idTaxa = $taxa['id'];

                // Calculo de juros compostos mensais
                $taxaMensal = pow(1 + $juros, 1 / 12) - 1;
                $valorParcela = $valorRestante * $taxaMensal / (1 - pow(1 + $taxaMensal, -$qtdParcelas));
            } else {
                $valorParcela = $valorRestante / $qtdParcelas;

                // Garantir que um ID de taxa ainda seja preenchida
                // Mesmo que sem juros, podemos buscar ainda uma taxa de 0% registrada
                $taxaZero = $dao->buscarTaxaZero();
                if (!$taxaZero) {
                        $response->getBody()->write(json_encode(['erro' => 'Taxa zero não encontrada.']));
                        return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
                }
                $idTaxa = $taxaZero['id'];
            }

            // Gerar parcelas
            $baseDate = new \DateTimeImmutable();

            for ($i = 1; $i <= $qtdParcelas; $i++) {
                $parcelas[] = [
                    'numero' => $i,
                    'valor' => round($valorParcela, 2),
                    'vencimento' => $baseDate->modify("+{$i} month")->format('Y-m-d')
                ];
            }

            // Inserir a compra
            $dao->inserirCompra($id, $idProduto, $valorEntrada, $qtdParcelas, $idTaxa);

            // Inserir parcelas
            foreach ($parcelas as $p) {
                $dao->inserirParcela($id, $p['numero'], $p['valor'], $p['vencimento']);
            }

            $response->getBody()->write(json_encode(['mensagem' => 'Compra registrada com sucesso']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            // Pode logar o erro real
            $response->getBody()->write(json_encode(['erro' => 'Erro interno ao processar a compra. Tente novamente.']));
            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        }
    }
}