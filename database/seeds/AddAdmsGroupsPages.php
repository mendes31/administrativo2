<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AddAdmsGroupsPages extends AbstractSeed
{
    /**
     * Cadastra pacote na tabela `adms_groups_pages` se ainda não existirem.
     *
     * Este método é executado para popular a tabela `adms_groups_pages` com registros iniciais dos pacotes.
     * Primeiro, verifica se já existe pacote na tabela com base no name. 
     * Se o pacote não existir, os dados são inseridos na tabela.
     * 
     * @return void
     */
    public function run(): void
    {

        // Variável para receber os dados a serem inseridos
        $data = [];

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Dashboard'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 1
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Dashboard',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe 
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Usuários'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 2
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Usuários',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Nível de Acesso'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 3
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Nível de Acesso',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }
       

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Pacote de Páginas'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 4
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Pacote de Páginas',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Grupo de Páginas'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 5
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Grupo de Páginas',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Páginas'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 6
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Páginas',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Login'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 7
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Login',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

         // Verifica se o grupo de página com o name especificado já existe
         $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Departamento'])->fetch();

         // Se o usuário não existir, adiciona seus dados ao array $data Nº 8
         if (!$existingRecord) {
             $data[] = [
                 'name' => 'Departamento',
                 'obs' => '',
                 'created_at' => date("Y-m-d H:i:s"),
             ];
         }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Erros'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 9
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Erros',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Cargo'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 10
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Cargo',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Permissões'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 11
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Permissões',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Bancos'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 12
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Bancos',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Pagar'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 13
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Pagar',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Receber'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 14
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Receber',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Centros de Custo'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 15
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Centros de Custo',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Plano de Contas'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 16
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Plano de Contas',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Frequências'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 17
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Frequências',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Clientes'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 18
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Clientes',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Fornecedores'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 19
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Forncedores',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Formas de Pagamento'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 20
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Formas de Pagamento',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Relatórios Financeiros'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 21
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Relatórios Financeiros',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Movimentos'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 22
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Movimentos',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Documentos'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 23
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Documentos',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }

        // Verifica se o grupo de página com o name especificado já existe
        $existingRecord = $this->query('SELECT id FROM adms_groups_pages WHERE name=:name', ['name' => 'Treinamentos'])->fetch();

        // Se o usuário não existir, adiciona seus dados ao array $data Nº 24
        if (!$existingRecord) {
            $data[] = [
                'name' => 'Treinamentos',
                'obs' => '',
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }



        // Obtém a tabela 'adms_groups_pages' para inserir os registros
        $adms_groups_pages = $this->table('adms_groups_pages');

        // Insere os registros na tabela
        $adms_groups_pages->insert($data)->save();

    }
}
