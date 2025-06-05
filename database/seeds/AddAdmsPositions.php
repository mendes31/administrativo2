<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AddAdmsPositions extends AbstractSeed
{
    /**
     * Cadastrar cargo na tabela `adms_positions` se ainda não existir.
     * 
     * Este método é executado para popular a tabela `adms_positions` com registros iniciais de cargos.
     * 
     * Primeiro veirifica se já existe o cargo na tabela com base no name.
     * 
     * Se não existir, os dados serão inseridos na tabela.
     * 
     * @return void
     */
    public function run(): void
    {
       // Variável para receber os dados a serem inseridos
       $data = [];

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Administrador'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Administrador',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Analista'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Analista',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Auxiliar'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Auxiliar',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }

        // Verificar se o registro já existe no banco de dados
        $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Colaborador'])->fetch();

        // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
        if (!$existingRecord) {
 
            // Criar o array com os dados do usuário
            $data[] = [
                'name' => 'Colaborador',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }
 
        // Verificar se o registro já existe no banco de dados
        $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Consultor'])->fetch();
 
        // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
        if (!$existingRecord) {
 
            // Criar o array com os dados do usuário
            $data[] = [
                'name' => 'Consultor',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Coordenador'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Coordenador',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Diretor'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Diretor',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Gerente'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Gerente',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }


       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_positions WHERE name=:name', ['name' => 'Lider'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Lider',
               'created_at' => date("Y-m-d H:i:s"),
           ];
       }


       // Indicar em qual tabela deve salvar
       $adms_departments = $this->table('adms_positions');

       // Inserir os registros na tabela
       $adms_departments->insert($data)->save();
    }
}
