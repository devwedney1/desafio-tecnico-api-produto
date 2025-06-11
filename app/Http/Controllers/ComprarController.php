<?php

namespace App\Http\Controllers;

use App\DAO\ComprarDAO;
use App\DAO\ProdutoDao;
use App\DAO\JurosDAO;
use App\Model\Compra;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

class ComprarController
{
    public function criar(Request $request, Response $response): Response
    {
        $dados = json_decode($request->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $response->getBody()->write(json_encode(['erro' => 'JSON inválido']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validação básica
        if (
            !isset($dados['idProduto']) ||
            !isset($dados['valorEntrada']) ||
            !isset($dados['qtdParcelas'])
        ) {
            $response->getBody()->write(json_encode(['erro' => 'Campos obrigatórios ausentes.']));
            return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
        }

        $idProduto = (int)$dados['idProduto'];
        $entrada = (float)$dados['valorEntrada'];
        $parcelas = (int)$dados['qtdParcelas'];

        try {
            $produtoDao = new ProdutoDao();
            $compraDao = new ComprarDAO();
            $jurosDao = new JurosDAO();

            // Verifica se produto existe
            if (!$produtoDao->existsById($idProduto)) {
                $response->getBody()->write(json_encode(['erro' => 'Produto não encontrado.']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Busca o produto para pegar o valor
            $produto = $produtoDao->findById($idProduto);
            $valorProduto = $produto->getValor();

            if ($entrada < 0 || $parcelas < 0 || $entrada > $valorProduto) {
                $response->getBody()->write(json_encode(['erro' => 'Valores de entrada ou parcelas inválidos.']));
                return $response->withStatus(422)->withHeader('Content-Type', 'application/json');
            }

            // Cálculo dos valores
            $valorParcela = 0.0;
            $jurosAplicado = 0.0;

            if ($parcelas > 0) {
                $valorParcela = ($valorProduto - $entrada) / $parcelas;
            }

            if ($parcelas > 6) {
                $taxaSelic = $jurosDao->mostrarJuros(); // Deve retornar um float como 13.75
                $jurosDecimal = $taxaSelic / 100;
                $valorFinanciado = $valorProduto - $entrada;
                $valorComJuros = $valorFinanciado * (1 + $jurosDecimal);
                $jurosAplicado = $valorComJuros - $valorFinanciado;
                $valorParcela = $valorComJuros / $parcelas;
            }

            $compra = new Compra($idProduto, $entrada, $parcelas, $valorParcela);
            $compra->setJurosAplicado($jurosAplicado);

            $compraDao->inserir($compra);

            $response->getBody()->write(json_encode(['mensagem' => 'Compra registrada com sucesso.']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['erro' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
