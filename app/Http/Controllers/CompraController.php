<?php

namespace App\Http\Controllers;

use App\Dao\CompraDAO;
use App\DAO\JurosDAO;
use App\Http\Resources\CompraResource;
use App\Model\Compra;
use App\Utils\Uuid\GeradorUuid;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


Class CompraController
{
    private CompraDAO $compraDAO;
    private CompraResource $compraResource;

    public function __construct ()
    {
        $this->compraDAO = new CompraDao();
        $this->compraResource = new compraResource();
    }
    public function index (Request $request, Response $response): Response
    {
        try {
            $dataCompraGet = $this->compraDAO->todasComprasGet();

            if(!$dataCompraGet){
                $response->getBody()->write(json_encode([
                    'error' => 'A API não encontrou compras'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            $dataCompraGetTratadoSucess = $this->compraResource->make($dataCompraGet);

            $response->getBody()->write(($dataCompraGetTratadoSucess));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Throwable $th) {
            $response->getBody()->write(json_encode([
                'error' => 'A API não encontrou compras, problema não esperado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404); // Bad Request - JSON inválido
        }
    }

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

            $compraDao = new CompraDAO();
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

            $gerarVariavel = new GeradorUuid();
            $idCompra = $gerarVariavel->gerarUuidPrimaryKey();

            $compra = new Compra($idCompra, $idProduto, $valorEntrada, $qtdParcelas, $vlrParcela);
            $compra->setJurosAplicado($taxa);
            $compra->setIdTaxaJuros($juros->getId());

            $compraDao->inserir($compra);

            $parcelas = [];
            $dataAtual = new \DateTime();
            for ($i = 1; $i <= $qtdParcelas; $i++) {
                $dataVencimento = clone $dataAtual;
                $dataVencimento->modify("+{$i} months");

                $gerarVariavel = new GeradorUuid();
                $id = $gerarVariavel->gerarUuidPrimaryKey();

                $parcelas[] = [
                    'id' => $id,
                    'idCompra' => $idCompra,
                    'numeroParcela' => $i,
                    'valorParcela' => $vlrParcela,
                    'dataVencimento' => $dataVencimento->format('Y-m-d')
                ];
            }

            $compraDao->inserirParcelas($parcelas);

            return $response->withStatus(201); // Sucesso sem corpo

        } catch (\Exception $e) {
            return $response->withStatus(500); // Erro interno
        }
    }
}