<?php


namespace App\adms\Controllers\Services\Validation;

use Rakit\Validation\Validator;

/**
 * Classe ValidationUserImageRakitService
 * 
 * Esta classe é responsável por validar os dados de um formulário de imagem do usuário, aplicando regras de validação para criação e edição de usuários.
 * Ela utiliza o pacote `Rakit\Validation` para realizar as validações e inclui uma regra personalizada de unicidade em múltiplas colunas.
 * 
 * @package App\adms\Controllers\Services\Validation
 * @author Rafael Mendes
 */
class ValidationUserImageRakitService 
{
    /**
     * Validar os dados do formulário.
     * 
     * Este método valida os dados fornecidos no formulário de usuário, aplicando diferentes regras dependendo se é uma criação ou edição de usuário.
     * 
     * @param array $data Dados do formulário.
     * @return array Lista de erros. Se não houver erros, o array será vazio.
     */
    public function validate(array $data): array 
    {
        // Criar o array que deve receber as mensagens de erro 
        $errors = [];

        // Instanciar a classe validar formulário
        $validator = new Validator();

        $validator->addValidator('uniqueInColumns', new UniqueInColumnsRule());

        // Definir as regras de validação
        $rules = [
            'id'    => 'required',
            'new_image' => 'required',            
        ];

               
         // Definir mensagens personalizadas
         $messages = [
            'id:required'                   => 'Dados inválidos.',
            'id:integer'                    => 'Dados inválidos.',
            
            'new_image:required'            => 'Selecione uma imagem.',
        
        ];

        // Criar o validador com os dados e regras fornecidas
        $validation = $validator->make($data, $rules);

        // Definir as mensagens de erro personalizadas
        $validation->setMessages($messages);


        // Validar os dados
        $validation->validate();

        // Retornar erros se houver
        if($validation->fails()){

            // Recuperar os erros
            $arrayErrors = $validation->errors();

            // Percorrer o arraqy de erros
            // firstOfAll - obter a primeira mensagem de erro para cada campo invalido.
            foreach($arrayErrors->firstOfAll() as $key => $message){
                $errors[$key] =  $message;
            }
        }

        return $errors;
    }
}