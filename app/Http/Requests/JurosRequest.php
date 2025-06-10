<?php

namespace App\Http\Requests;

use App\Models\Dao\JurosDao;
use App\Http\Resources\JurosResource;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JurosRequest
{
    public function executar(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $erros = $this->validar($body);

        if (!empty($erros)) {
            $response->getBody()->write(json_encode(['erros' => $erros]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        $inicio = $body['dataInicio'];
        $final = $body['dataFinal'];

        try {
            $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.4189/dados?formato=json&dataInicial={$inicio}&dataFinal={$final}";
            $apiResponse = file_get_contents($url);
            $selicData = json_decode($apiResponse, true);

            if (!$selicData || !is_array($selicData)) {
                return $response->withStatus(400);
            }

            $total = 0;
            foreach ($selicData as $item) {
                $total += floatval(str_replace(',', '.', $item['valor']));
            }

            $media = $total / count($selicData);

            $dao = new JurosDao();
            $dao->salvar($media, $inicio, $final);

            $response->getBody()->write(json_encode(JurosResource::toArray($media)));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            return $response->withStatus(400);
        }
    }

    public function validar(array $data): array
    {
        $erros = [];

        if (!isset($data['dataInicio']) || !isset($data['dataFinal'])) {
            $erros[] = 'Campos obrigatórios: dataInicio e dataFinal.';
            return $erros;
        }

        $inicio = $data['dataInicio'];
        $final = $data['dataFinal'];
        $hoje = date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $final)) {
            $erros[] = 'Formato de data inválido. Use YYYY-MM-DD.';
        }

        if ($inicio < '2010-01-01') {
            $erros[] = 'dataInicio deve ser ≥ 2010-01-01.';
        }

        if ($final > $hoje) {
            $erros[] = 'dataFinal não pode ser no futuro.';
        }

        if ($inicio > $final) {
            $erros[] = 'dataInicio deve ser menor ou igual a dataFinal.';
        }

        return $erros;
    }
}
