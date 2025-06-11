<?php

namespace App\Http\Controllers;

use App\DAO\ComprarDAO;
use App\DAO\ProdutoDao;
use App\DAO\JurosDAO;
use App\Model\Compra;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
use Ramsey\Uuid\Uuid;


class ComprarController
{
    public function criar(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            $idProduto = $data['idProduto'] ?? '';
            $valorEntrada = (float)($data['valorEntrada'] ?? 0);
            $qtdParcelas = (int)($data['qtdParcelas'] ?? 0);

            if (!$idProduto || $valorEntrada < 0 || $qtdParcelas <= 0) {
                $response->getBody()->write(json_encode(['erro' => 'Dados inválidos.']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $compraDao = new ComprarDAO();
            $jurosDao = new JurosDAO();

            $valorProduto = $compraDao->buscarValorProduto($idProduto);
            if ($valorProduto <= 0) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado.']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            $valorFinanciado = $valorProduto - $valorEntrada;
            if ($valorFinanciado <= 0) {
                $response->getBody()->write(json_encode(['erro' => 'Valor de entrada maior ou igual ao valor do produto.']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            $juros = $jurosDao->mostrarJuros();
            if (!$juros) {
                $response->getBody()->write(json_encode(['erro' => 'Nenhuma taxa de juros válida encontrada.']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            $taxa = $juros->getJuros();
            $valorFinal = $valorFinanciado * pow(1 + $taxa, $qtdParcelas);
            $vlrParcela = $valorFinal / $qtdParcelas;

            $idCompra = Uuid::uuid4()->toString();

            $compra = new Compra($idCompra, $idProduto, $valorEntrada, $qtdParcelas, $vlrParcela);
            $compra->setJurosAplicado($taxa);
            $compra->setIdTaxaJuros($juros->getId());

            $compraDao->inserir($compra);

            $parcelas = [];
            $dataAtual = new \DateTime();
            for ($i = 1; $i <= $qtdParcelas; $i++) {
                $dataVencimento = clone $dataAtual;
                $dataVencimento->modify("+{$i} months");

                $parcelas[] = [
                    'idCompra' => $idCompra,
                    'numeroParcela' => $i,
                    'valorParcela' => round($vlrParcela, 2),
                    'dataVencimento' => $dataVencimento->format('Y-m-d')
                ];
            }

            $compraDao->inserirParcelas($parcelas);

            $response->getBody()->write(json_encode([
                'mensagem' => 'Compra realizada com sucesso.',
                'idCompra' => $idCompra,
                'valorParcela' => round($vlrParcela, 2),
                'totalComJuros' => round($valorFinal, 2),
                'jurosAplicado' => $taxa
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
