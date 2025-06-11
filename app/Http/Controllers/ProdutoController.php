<?php

namespace App\Http\Controllers;

use App\Dao\ProdutoDao;
use App\Model\Produto;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProdutoController
{
    private ProdutoDao $produtoDao;

    public function __construct ()
    {
        $this->produtoDao = new ProdutoDao();
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function store (Request $request, Response $response): Response
    {
        try {
            // Pega o corpo da requisição
            $body = $request->getBody()->getContents();

            // Verifica se o JSON é válido
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $response->getBody()->write(json_encode(['error' => 'O formato que está sendo enviado a requisição, não e permitido. São e permitido formtado Json.']));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Bad Request - JSON inválido
            }

            // Verifica campos obrigatórios
            if (empty($data['id']) || empty($data['nome']) || !isset($data['valor'])) {
                $response->getBody()->write(json_encode(['error' => 'O campos do produto como id, nome e valor são obrigatorios.']));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(422); // Unprocessable Entity - campos obrigatórios
            }

            // Verifica formato UUID
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $data['id'])) {
                $response->getBody()->write(json_encode(['error' => 'O formato do Id, não corresponde ao formatod UUID.']));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(422); // UUID inválido
            }

            // Verifica valor negativo
            if (!is_numeric($data['valor']) || $data['valor'] < 0) {
                $response->getBody()->write(json_encode(['error' => 'O valor do Produto não e permitido negativo.']));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(422); // Valor negativo
            }

            // Verifica se ID já existe no banco
            if ($this->produtoDao->existsById($data['id'])) {
                $response->getBody()->write(json_encode(['error' => 'Produto com este ID já existe.']));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
            }

            // Cria produto
            $produto = new Produto($data['id'], $data['nome'], $data['tipo'] ?? null, (float)$data['valor']);

            // Salva no banco
            $produtoCriado = $this->produtoDao->create($produto);
            if (!$produtoCriado) {
                $response->getBody()->write(json_encode(['error' => 'Problema no processamento de criar o produto.']));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Falha ao salvar
            }

            $response->getBody()->write(json_encode(['sucess' => 'Produto criado']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(201); // Created

        } catch (Exception $e) {
            error_log($e->getMessage());

            $response->getBody()->write(json_encode(['error' => 'Problema não esperado']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Internal Server Error
        }
    }
}