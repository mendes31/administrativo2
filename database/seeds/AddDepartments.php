<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AddDepartments extends AbstractSeed
{
    /**
     * Cadastrar nível de Acesso na tabela `adms_departments` se ainda não existir.
     * 
     * Este método é executado para popular a tabela `adms_departments` com registros iniciais de niveis de acesso.
     * 
     * Primeiro veirifica se já existe o nível de acesso na tabela com base no name.
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
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Assuntos Regulatório'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Assuntos Regulatório',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Comercial'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Comercial',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Compras'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Compras',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Contabilidade'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Contabilidade',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Controle de Qualidade'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Controle de Qualidade',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Diretoria'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Diretoria',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Estoque'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Estoque',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Expedição'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Expedição',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Financeiro'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Financeiro',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Garantia da Qualidade'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Garantia da Qualidade',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Higiene'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Higiene',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Manutenção'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Manutenção',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Pesquisa e Desenvolvimento'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Pesquisa e Desenvolvimento',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Produção'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Produção',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }

       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Recepção'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Recepção',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }
       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Recursos Humanos'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Recursos Humanos',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }
       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Refeitório'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Refeitório',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }
       // Verificar se o registro já existe no banco de dados
       $existingRecord = $this->query('SELECT id FROM adms_departments WHERE name=:name', ['name' => 'Tecnologia da Informação'])->fetch();

       // Se o registro não existir, insere os dados na veriável $data para em seguida cadastrar na tabela
       if (!$existingRecord) {

           // Criar o array com os dados do usuário
           $data[] = [
               'name' => 'Tecnologia da Informação',
               'create_at' => date("Y-m-d H:i:s"),
           ];
       }


       // Indicar em qual tabela deve salvar
       $adms_departments = $this->table('adms_departments');

       // Inserir os registros na tabela
       $adms_departments->insert($data)->save();
    }
}
