<?php

namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;

/**
 * Serviço de validação para permissões de níveis de acesso.
 *
 * Esta classe é responsável por validar os dados recebidos para permissões de níveis de acesso,
 * utilizando a biblioteca Rakit Validation. Gera mensagens de erro personalizadas em caso de falha na validação.
 *
 * @package App\adms\Controllers\Services\Validation
 */
class ValidationAccessLevelPermissionService
{

    /**
     * Validar permissões de nível de acesso.
     *
     * Este método valida os dados de permissões de nível de acesso, verificando se o campo `adms_access_level_id`
     * está presente e é um valor inteiro. Em caso de erro, uma lista de mensagens de erro é retornada.
     *
     * @param array $data Dados a serem validados, incluindo `adms_access_level_id`.
     * @return array Retorna um array com as mensagens de erro, ou um array vazio se não houver erros.
     */

    public function validate(array $data): array
    {
        // Criar o array para receber as mensagens de erro
        $errors = [];

        // Instanciar a classe Validator para validar o formulário
        $validator = new Validator();

        // Definir as regras de validação
        $validation = $validator->make($data, [
            'adms_access_level_id' => 'required|integer',
        ]);

        // Definir mensagens personalizadas
        $validation->setMessages([
            'adms_access_level_id:required' => 'Dados inválidos.',
            'adms_access_level_id:integer' => 'Dados inválidos.',
        ]);

        // Validar os dados
        $validation->validate();

        // Retornar erros se houver
        if ($validation->fails()) {
            // Recuperar os erros 
            $arrayErrors = $validation->errors();

            // Percorrer o array de erros e armazenar a primeira mensagem de erro para cada campo validado
            foreach ($arrayErrors->firstOfAll() as $key => $message) {
                $errors[$key] = $message;
            }
        }

        // Retornar o array de erros, se houver
        return $errors;
    }
}
