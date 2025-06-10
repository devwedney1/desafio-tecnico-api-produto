<?php

require_once './app/Dao/CompraDAO.php';
require_once './app/Dao/JurosDAO.php';

class JurosController
{
    public function atualizarJuros()
    {

        $json = file_get_contents('php://input');
        $dados = json_decode($json, true);

        $dataInicio = new DateTime($dados['dataInicio']);
        $dataFinal = new DateTime($dados['dataFinal']);
        $hoje = new DateTime();


        if ($dataFinal < $dataInicio || $dataFinal > $hoje || $dataInicio < '2010-01-01') {
            http_response_code(422);
            return;
        }
        try {
            $dataInicioFormat = $dataInicio->format('d/m/Y');
            $dataFinalFormat = $dataFinal->format('d/m/Y');

            $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json&dataInicial={$dataInicioFormat}&dataFinal={$dataFinalFormat}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $valoresApi = json_decode(curl_exec($ch), true);

            if (isset($valoresApi['erro'])) {
                http_response_code(400);
                return;
            }

            $valorTotal = 0;

            foreach ($valoresApi as $valor) {
                $valorTotal += $valor['valor'];
            }

            $dao = new ComprasDAO();
            $dao->atualizarBDCompras($valorTotal);

            $jurosModel = new Juros($dados['dataInicio'],$dados['dataFinal'],$valorTotal);
            $jurosDao = new JurosDAO();
            $jurosDao->salvarJuros($jurosModel);
            
            http_response_code(200);
            echo json_encode(["sucesso" => "A taxa de juros foi atualizada"]);
        } catch (Exception $e) {
            http_response_code(400);
        }
    }
}
