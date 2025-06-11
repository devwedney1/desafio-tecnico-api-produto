<?php

namespace App\Http\Controllers;

use App\DAO\ComprarDAO;
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

            if (!is_array($data)) {
                return $response->withStatus(400); // JSON inválido
            }

            $idProduto = $data['idProduto'] ?? '';
            $valorEntrada = (float)($data['valorEntrada'] ?? 0);
            $qtdParcelas = (int)($data['qtdParcelas'] ?? 0);

            if (!$idProduto || $valorEntrada < 0 || $qtdParcelas <= 0) {
                return $response->withStatus(400); // Estrutura incorreta
            }

            $compraDao = new ComprarDAO();
            $jurosDao = new JurosDAO();

            $valorProduto = $compraDao->buscarValorProduto($idProduto);
            if ($valorProduto <= 0) {
                return $response->withStatus(422); // Produto inválido
            }

            $valorFinanciado = $valorProduto - $valorEntrada;
            if ($valorFinanciado <= 0) {
                return $response->withStatus(422); // Entrada inválida
            }

            $juros = $jurosDao->mostrarJuros();
            if (!$juros) {
                return $response->withStatus(422); // Sem taxa de juros
            }

            $taxa = $juros->getJuros();
            $valorFinal = $valorFinanciado * pow(1 + $taxa, $qtdParcelas);
            $vlrParcela = round($valorFinal / $qtdParcelas, 2);

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
                    'id' => Uuid::uuid4()->toString(),
                    'idCompra' => $idCompra,
                    'numeroParcela' => $i,
                    'valorParcela' => $vlrParcela,
                    'dataVencimento' => $dataVencimento->format('Y-m-d')
                ];
            }

            $compraDao->inserirParcelas($parcelas);

            return $response->withStatus(201); // Sucesso sem corpo

        } catch (Exception $e) {
            return $response->withStatus(500); // Erro interno
        }
    }
}
