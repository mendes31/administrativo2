<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AddAdmsPages extends AbstractSeed
{
    /**
     * Cadastra pagina na tabela `adms_pages` se ainda não existirem.
     *
     * Este método é executado para popular a tabela `adms_pages` com registros iniciais dos paginas.
     * Primeiro, verifica se já existe pagina na tabela com base no name. 
     * Se o pagina não existir, os dados são inseridos na tabela.
     * 
     * @return void
     */
    public function run(): void
    {

        // Variável para receber os dados a serem inseridos
        $data = [];

        // Variável para receber os dados que devem ser validados antes de cadastrar
        $pages = [
            ['name'=> 'Dashboard', 'controller' => 'Dashboard', 'controller_url' => 'dashboard', 'directory' => 'dashboard', 'obs' => 'Página inicial do administrativo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 1],

            ['name'=> 'Cadastrar Usuário', 'controller' => 'CreateUser', 'controller_url' => 'create-user', 'directory' => 'users', 'obs' => 'Página com o formulário cadastrar usuário.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],
            ['name'=> 'Listar Usuários', 'controller' => 'ListUsers', 'controller_url' => 'list-users', 'directory' => 'users', 'obs' => 'Página para listar o usuários.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],
            ['name'=> 'Visualizar Usuário', 'controller' => 'ViewUser', 'controller_url' => 'view-user', 'directory' => 'users', 'obs' => 'Página apresentar os detalhes do usuário.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],
            ['name'=> 'Editar Usuário', 'controller' => 'UpdateUser', 'controller_url' => 'update-user', 'directory' => 'users', 'obs' => 'Página com o formulário editar usuário.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],
            ['name'=> 'Editar Imagem do Usuário', 'controller' => 'UpdateUserImage', 'controller_url' => 'update-user-image', 'directory' => 'users', 'obs' => 'Página com o formulário editar imagem do usuário.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],
            ['name'=> 'Editar Senha do Usuário', 'controller' => 'UpdatePasswordUser', 'controller_url' => 'update-password-user', 'directory' => 'users', 'obs' => 'Página com o formulário editar senha do usuário.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],
            ['name'=> 'Apagar Usuário', 'controller' => 'DeleteUser', 'controller_url' => 'delete-user', 'directory' => 'users', 'obs' => 'Página para apagar o usuário do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 2],

            ['name'=> 'Cadastrar Nível de Acesso', 'controller' => 'CreateAccessLevel', 'controller_url' => 'create-access-level', 'directory' => 'accessLevels', 'obs' => 'Página com o formulário cadastrar nível de acesso.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],
            ['name'=> 'Listar Níveis de Acesso', 'controller' => 'ListAccessLevels', 'controller_url' => 'list-access-levels', 'directory' => 'accessLevels', 'obs' => 'Página para listar o níveis de acesso.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],
            ['name'=> 'Visualizar Nível de Acesso', 'controller' => 'ViewAccessLevel', 'controller_url' => 'view-access-level', 'directory' => 'accessLevels', 'obs' => 'Página apresentar os detalhes do nível de acesso.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],
            ['name'=> 'Editar Nível de Acesso', 'controller' => 'UpdateAccessLevel', 'controller_url' => 'update-access-level', 'directory' => 'accessLevels', 'obs' => 'Página com o formulário editar nível de acesso.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],           
            ['name'=> 'Apagar Nível de Acesso', 'controller' => 'DeleteAccessLevel', 'controller_url' => 'delete-access-level', 'directory' => 'accessLevels', 'obs' => 'Página para apagar o nível de acesso do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],
            ['name'=> 'Editar Nível de Acesso do usuário', 'controller' => 'UpdateUserAccessLevels', 'controller_url' => 'update-user-access-levels', 'directory' => 'accessLevels', 'obs' => 'Página para editar o nível de acesso do usuário no banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],
            ['name'=> 'Sincronizar Nível de Acesso', 'controller' => 'AccessLevelPageSync', 'controller_url' => 'access-level-page-sync', 'directory' => 'accessLevels', 'obs' => 'Sincronizar os níveis de acesso com as páginas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 3],

            ['name'=> 'Cadastrar Pacote', 'controller' => 'CreatePackage', 'controller_url' => 'create-package', 'directory' => 'packages', 'obs' => 'Página com o formulário cadastrar pacote.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 4],
            ['name'=> 'Listar Pacotes', 'controller' => 'ListPackages', 'controller_url' => 'list-packages', 'directory' => 'packages', 'obs' => 'Página para listar o pacotes.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 4],
            ['name'=> 'Visualizar Pacote', 'controller' => 'ViewPackage', 'controller_url' => 'view-package', 'directory' => 'packages', 'obs' => 'Página apresentar os detalhes do pacote.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 4],
            ['name'=> 'Editar Pacote', 'controller' => 'UpdatePackage', 'controller_url' => 'update-package', 'directory' => 'packages', 'obs' => 'Página com o formulário editar pacote.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 4],            
            ['name'=> 'Apagar Pacote', 'controller' => 'DeletePackage', 'controller_url' => 'delete-package', 'directory' => 'packages', 'obs' => 'Página para apagar o pacote do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 4],

            ['name'=> 'Cadastrar Grupo de Página', 'controller' => 'CreateGroupPage', 'controller_url' => 'create-group-page', 'directory' => 'groupsPages', 'obs' => 'Página com o formulário cadastrar grupo de página.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 5],
            ['name'=> 'Listar Grupos de Páginas', 'controller' => 'ListGroupsPages', 'controller_url' => 'list-groups-pages', 'directory' => 'groupsPages', 'obs' => 'Página para listar o grupos de páginas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 5],
            ['name'=> 'Visualizar Grupo de Página', 'controller' => 'ViewGroupPage', 'controller_url' => 'view-group-page', 'directory' => 'groupsPages', 'obs' => 'Página apresentar os detalhes do grupo de página.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 5],
            ['name'=> 'Editar Grupo de Página', 'controller' => 'UpdateGroupPage', 'controller_url' => 'update-group-page', 'directory' => 'groupsPages', 'obs' => 'Página com o formulário editar grupo de página.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 5],            
            ['name'=> 'Apagar Grupo de Página', 'controller' => 'DeleteGroupPage', 'controller_url' => 'delete-group-page', 'directory' => 'groupsPages', 'obs' => 'Página para apagar o grupo de página do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 5],

            ['name'=> 'Cadastrar Página', 'controller' => 'CreatePage', 'controller_url' => 'create-page', 'directory' => 'pages', 'obs' => 'Página com o formulário cadastrar página.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 6],
            ['name'=> 'Listar Páginas', 'controller' => 'ListPages', 'controller_url' => 'list-pages', 'directory' => 'pages', 'obs' => 'Página para listar o páginas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 6],
            ['name'=> 'Visualizar Página', 'controller' => 'ViewPage', 'controller_url' => 'view-group-page', 'directory' => 'pages', 'obs' => 'Página apresentar os detalhes do página.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 6],
            ['name'=> 'Editar Página', 'controller' => 'UpdatePage', 'controller_url' => 'update-group-page', 'directory' => 'pages', 'obs' => 'Página com o formulário editar página.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 6],            
            ['name'=> 'Apagar Página', 'controller' => 'DeletePage', 'controller_url' => 'delete-group-page', 'directory' => 'pages', 'obs' => 'Página para apagar o página do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 6],

            ['name'=> 'Página de Login', 'controller' => 'Login', 'controller_url' => 'login', 'directory' => 'login', 'obs' => 'Página com o formulário de login.', 'public_page' => 1, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 7],
            ['name'=> 'Cadastrar Novo Usuário', 'controller' => 'NewUser', 'controller_url' => 'new-user', 'directory' => 'login', 'obs' => 'Página com o formulário novo usuário na página de login.', 'public_page' => 1, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 7],
            ['name'=> 'Recuperar Senha', 'controller' => 'ForgotPassword', 'controller_url' => 'forgot-password', 'directory' => 'login', 'obs' => 'Página com o formulário para recuperar a senha.', 'public_page' => 1, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 7],
            ['name'=> 'Cadastrar Nova Senha', 'controller' => 'ResetPassword', 'controller_url' => 'reset-password', 'directory' => 'login', 'obs' => 'Página com o formulário cadastrar nova senha no login.', 'public_page' => 1, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 7],
            ['name'=> 'Sair do Administrativo', 'controller' => 'Logout', 'controller_url' => 'logout', 'directory' => 'login', 'obs' => 'Deslogar do sistema administrativo.', 'public_page' => 1, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 7],
            
            ['name'=> 'Cadastrar Departamento', 'controller' => 'CreateDepartment', 'controller_url' => 'create-department', 'directory' => 'departments', 'obs' => 'Página com o formulário cadastrar Departamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 8],
            ['name'=> 'Listar Departamentos', 'controller' => 'ListDepartments', 'controller_url' => 'list-departments', 'directory' => 'departments', 'obs' => 'Página para listar o Departamentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 8],
            ['name'=> 'Visualizar Departamento', 'controller' => 'ViewDepartment', 'controller_url' => 'view-department', 'directory' => 'departments', 'obs' => 'Página apresentar os detalhes do Departamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 8],
            ['name'=> 'Editar Departamento', 'controller' => 'UpdateDepartments', 'controller_url' => 'update-departments', 'directory' => 'departments', 'obs' => 'Página com o formulário editar Departamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 8],           
            ['name'=> 'Apagar Departamento', 'controller' => 'DeleteDepartment', 'controller_url' => 'delete-department', 'directory' => 'departments', 'obs' => 'Página para apagar o Departamento do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 8],

            ['name'=> 'Erro 403', 'controller' => 'Error403', 'controller_url' => 'logout', 'directory' => 'errors', 'obs' => 'Erro que deve apresentado quando não encontrar a página.', 'public_page' => 1, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 9],

            ['name'=> 'Cadastrar Cargo', 'controller' => 'CreatePosition', 'controller_url' => 'create-position', 'directory' => 'positions', 'obs' => 'Página com o formulário cadastrar Cargo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 10],
            ['name'=> 'Listar Cargos', 'controller' => 'ListPositions', 'controller_url' => 'list-positions', 'directory' => 'positions', 'obs' => 'Página para listar o Cargos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 10],
            ['name'=> 'Visualizar Cargo', 'controller' => 'ViewPosition', 'controller_url' => 'view-position', 'directory' => 'positions', 'obs' => 'Página apresentar os detalhes do Cargo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 10],
            ['name'=> 'Editar Cargo', 'controller' => 'UpdatePosition', 'controller_url' => 'update-positions', 'directory' => 'positions', 'obs' => 'Página com o formulário editar Cargo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 10],           
            ['name'=> 'Apagar Cargo', 'controller' => 'DeletePosition', 'controller_url' => 'delete-position', 'directory' => 'positions', 'obs' => 'Página para apagar o Cargo do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 10],

            ['name'=> 'Liberar Permissões', 'controller' => 'ListAccessLevelsPermissions', 'controller_url' => 'list-access-levels-permissions', 'directory' => 'permission', 'obs' => 'Página para liberar permissões para o nível de acesso.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 11],

            ['name'=> 'Cadastrar Banco', 'controller' => 'CreateBank', 'controller_url' => 'create-bank', 'directory' => 'banks', 'obs' => 'Página com o formulário cadastrar Banco.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 12],
            ['name'=> 'Listar Bancos', 'controller' => 'ListBanks', 'controller_url' => 'list-banks', 'directory' => 'banks', 'obs' => 'Página para listar o Bancos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 12],
            ['name'=> 'Visualizar Banco', 'controller' => 'ViewBank', 'controller_url' => 'view-bank', 'directory' => 'banks', 'obs' => 'Página apresentar os detalhes do Banco.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 12],
            ['name'=> 'Editar Banco', 'controller' => 'UpdateBank', 'controller_url' => 'update-bank', 'directory' => 'banks', 'obs' => 'Página com o formulário editar Banco.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 12],           
            ['name'=> 'Apagar Banco', 'controller' => 'DeleteBank', 'controller_url' => 'delete-bank', 'directory' => 'banks', 'obs' => 'Página para apagar o Banco do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 12],

            ['name'=> 'Cadastrar Contas à Pagar', 'controller' => 'CreatePay', 'controller_url' => 'create-pay', 'directory' => 'pay', 'obs' => 'Página com o formulário cadastrar Contas à Pagar.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Listar Contas à Pagar', 'controller' => 'ListPayments', 'controller_url' => 'list-payments', 'directory' => 'pay', 'obs' => 'Página para listar o Contas à Pagar.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Listar Pagamentos de uma Conta', 'controller' => 'ListPartialValues', 'controller_url' => 'list-partial-values', 'directory' => 'pay', 'obs' => 'Página para listar pagamentos parciais de uma Conta.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Visualizar Contas à Pagar', 'controller' => 'ViewPay', 'controller_url' => 'view-pay', 'directory' => 'pay', 'obs' => 'Página apresentar os detalhes do Contas à Pagar.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Editar Contas à Pagar', 'controller' => 'UpdatePay', 'controller_url' => 'update-pay', 'directory' => 'pay', 'obs' => 'Página com o formulário editar Contas à Pagar.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],           
            ['name'=> 'Apagar Contas à Pagar', 'controller' => 'DeletePay', 'controller_url' => 'delete-pay', 'directory' => 'pay', 'obs' => 'Página para apagar o Contas à Pagar do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Parcelar Contas à Pagar', 'controller' => 'Installments', 'controller_url' => 'installments', 'directory' => 'pay', 'obs' => 'Página com o formulário parcelar Conta à Pagar.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],           
            ['name'=> 'Pagar Conta', 'controller' => 'Payment', 'controller_url' => 'payment', 'directory' => 'pay', 'obs' => 'Página para pagar/baixar Conta do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Processar o arquivo de importação de pagamentos', 'controller' => 'ProcessFilePayments', 'controller_url' => 'process-file-payments', 'directory' => 'pay', 'obs' => 'Pagina para processar o arquivo de importaçãode pagamentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],           
            ['name'=> 'Obter o status dos pagamentos', 'controller' => 'GetPaymentsStatus', 'controller_url' => 'get-payments-status', 'directory' => 'pay', 'obs' => 'Página para obter o status dos pagamentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],
            ['name'=> 'Verificar se o pagamento está ocupado', 'controller' => 'CheckBusy', 'controller_url' => 'check-busy', 'directory' => 'pay', 'obs' => 'Página para verificar se o pagamento está ocupado.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],           
            ['name'=> 'Desocupar o pagamento ocupado', 'controller' => 'ClearBusyPay', 'controller_url' => 'clear-busyPay', 'directory' => 'pay', 'obs' => 'Página para limpar o pagamento ocupado.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 13],


            ['name'=> 'Cadastrar Contas à Receber', 'controller' => 'CreateReceive', 'controller_url' => 'create-receive', 'directory' => 'receive', 'obs' => 'Página com o formulário cadastrar Contas à Receber.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 14],
            ['name'=> 'Listar Contas à Receber', 'controller' => 'ListReceive', 'controller_url' => 'list-receive', 'directory' => 'receive', 'obs' => 'Página para listar o Contas à Receber.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 14],
            ['name'=> 'Visualizar Contas à Receber', 'controller' => 'ViewReceive', 'controller_url' => 'view-receive', 'directory' => 'receive', 'obs' => 'Página apresentar os detalhes do Contas à Receber.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 14],
            ['name'=> 'Editar Contas à Receber', 'controller' => 'UpdateReceive', 'controller_url' => 'update-receive', 'directory' => 'receive', 'obs' => 'Página com o formulário editar Contas à Receber.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 14],           
            ['name'=> 'Apagar Contas à Receber', 'controller' => 'DeleteReceive', 'controller_url' => 'delete-receive', 'directory' => 'receive', 'obs' => 'Página para apagar o Contas à Receber do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 14],

            ['name'=> 'Cadastrar Centros de Custo', 'controller' => 'CreateCostCenter', 'controller_url' => 'create-cost-center', 'directory' => 'costCenter', 'obs' => 'Página com o formulário cadastrar Centros de Custo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 15],
            ['name'=> 'Listar Centros de Custo', 'controller' => 'ListCostCenters', 'controller_url' => 'list-cost-centers', 'directory' => 'costCenter', 'obs' => 'Página para listar o Centros de Custo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 15],
            ['name'=> 'Visualizar Centros de Custo', 'controller' => 'ViewCostCenter', 'controller_url' => 'view-cost-center', 'directory' => 'costCenter', 'obs' => 'Página apresentar os detalhes do Centros de Custo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 15],
            ['name'=> 'Editar Centros de Custo', 'controller' => 'UpdateCostCenter', 'controller_url' => 'update-cost-center', 'directory' => 'costCenter', 'obs' => 'Página com o formulário editar Centros de Custo.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 15],           
            ['name'=> 'Apagar Centros de Custo', 'controller' => 'DeleteCostCenter', 'controller_url' => 'delete-cost-center', 'directory' => 'costCenter', 'obs' => 'Página para apagar o Centros de Custo do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 15],

            ['name'=> 'Cadastrar Plano de Contas', 'controller' => 'CreateAccountPlan', 'controller_url' => 'create-account-plan', 'directory' => 'accountsPlan', 'obs' => 'Página com o formulário cadastrar Plano de Contas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 16],
            ['name'=> 'Listar Plano de Contas', 'controller' => 'ListAccountsPlan', 'controller_url' => 'list-accounts-plan', 'directory' => 'accountsPlan', 'obs' => 'Página para listar o Plano de Contas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 16],
            ['name'=> 'Visualizar Plano de Contas', 'controller' => 'ViewAccountPlan', 'controller_url' => 'view-account-plan', 'directory' => 'accountsPlan', 'obs' => 'Página apresentar os detalhes do Plano de Contas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 16],
            ['name'=> 'Editar Plano de Contas', 'controller' => 'UpdateAccountPlan', 'controller_url' => 'update-account-plan', 'directory' => 'accountsPlan', 'obs' => 'Página com o formulário editar Plano de Contas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 16],           
            ['name'=> 'Apagar Plano de Contas', 'controller' => 'DeleteAccountPlan', 'controller_url' => 'delete-account-plan', 'directory' => 'accountsPlan', 'obs' => 'Página para apagar o Plano de Contas do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 16],
           
            ['name'=> 'Cadastrar Frequência para Pagamento', 'controller' => 'CreateFrequency', 'controller_url' => 'create-frequency', 'directory' => 'frequency', 'obs' => 'Página com o formulário cadastrar Plano de Contas.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 17],
            ['name'=> 'Listar Frequências para Pagamentos', 'controller' => 'ListFrequencies', 'controller_url' => 'list-frequencies', 'directory' => 'frequency', 'obs' => 'Página para listar o Frequências para Pagamentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 17],
            ['name'=> 'Visualizar Frequência para Pagamento', 'controller' => 'ViewFrequency', 'controller_url' => 'view-frequency', 'directory' => 'frequency', 'obs' => 'Página apresentar os detalhes do Frequências para Pagamentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 17],
            ['name'=> 'Editar Frequência para Pagamento', 'controller' => 'UpdateFrequency', 'controller_url' => 'update-frequency', 'directory' => 'frequency', 'obs' => 'Página com o formulário editar Frequências para Pagamentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 17],           
            ['name'=> 'Apagar Frequência para Pagamento', 'controller' => 'DeleteFrequency', 'controller_url' => 'delete-frequency', 'directory' => 'frequency', 'obs' => 'Página para apagar o Frequências para Pagamentos do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 17],

            ['name'=> 'Cadastrar Cliente', 'controller' => 'CreateCustomer', 'controller_url' => 'create-customer', 'directory' => 'customer', 'obs' => 'Página com o formulário cadastrar Clientes.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 18],
            ['name'=> 'Listar Clientes', 'controller' => 'ListCustomers', 'controller_url' => 'list-customers', 'directory' => 'customer', 'obs' => 'Página para listar o Clientes.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 18],
            ['name'=> 'Visualizar Cliente', 'controller' => 'ViewCustomer', 'controller_url' => 'view-customer', 'directory' => 'customer', 'obs' => 'Página apresentar os detalhes do Cliente.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 18],
            ['name'=> 'Editar Cliente', 'controller' => 'UpdateCustomer', 'controller_url' => 'update-customer', 'directory' => 'customer', 'obs' => 'Página com o formulário editar Cliente.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 18],           
            ['name'=> 'Apagar Cliente', 'controller' => 'DeleteCustomer', 'controller_url' => 'delete-customer', 'directory' => 'customer', 'obs' => 'Página para apagar o Cliente do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 18],

            ['name'=> 'Cadastrar Fornecedor', 'controller' => 'CreateSupplier', 'controller_url' => 'create-supplier', 'directory' => 'supplier', 'obs' => 'Página com o formulário cadastrar Fornecedor.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 19],
            ['name'=> 'Listar Fornecedores', 'controller' => 'ListSuppliers', 'controller_url' => 'list-suppliers', 'directory' => 'supplier', 'obs' => 'Página para listar o Fornecedores.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 19],
            ['name'=> 'Visualizar Fornecedor', 'controller' => 'ViewSupplier', 'controller_url' => 'view-supplier', 'directory' => 'supplier', 'obs' => 'Página apresentar os detalhes do Fornecedor.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 19],
            ['name'=> 'Editar Fornecedor', 'controller' => 'UpdateSupplier', 'controller_url' => 'update-supplier', 'directory' => 'supplier', 'obs' => 'Página com o formulário editar Fornecedor.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 19],           
            ['name'=> 'Apagar Fornecedor', 'controller' => 'DeleteSupplier', 'controller_url' => 'delete-supplier', 'directory' => 'supplier', 'obs' => 'Página para apagar o Fornecedor do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 19],
            ['name'=> 'Importar Fornecedor', 'controller' => 'ProcessFile', 'controller_url' => 'process-file', 'directory' => 'supplier', 'obs' => 'Página para importar o Fornecedor.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 19],

            ['name'=> 'Cadastrar Forma de Pagamento', 'controller' => 'CreatePaymentMethod', 'controller_url' => 'create-payment-method', 'directory' => 'paymentMethod', 'obs' => 'Página com o formulário cadastrar Forma de Pagamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 20],
            ['name'=> 'Listar Formas de Pagamento', 'controller' => 'ListPaymentMethods', 'controller_url' => 'list-payment-methods', 'directory' => 'paymentMethod', 'obs' => 'Página para listar o Formas de Pagamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 20],
            ['name'=> 'Visualizar Forma de Pagamento', 'controller' => 'ViewPaymentMethod', 'controller_url' => 'view-payment-method', 'directory' => 'paymentMethod', 'obs' => 'Página apresentar os detalhes do Forma de Pagamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 20],
            ['name'=> 'Editar Forma de Pagamento', 'controller' => 'UpdatePaymentMethod', 'controller_url' => 'update-payment-method', 'directory' => 'paymentMethod', 'obs' => 'Página com o formulário editar Forma de Pagamento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 20],           
            ['name'=> 'Apagar Forma de Pagamento', 'controller' => 'DeletePaymentMethod', 'controller_url' => 'delete-payment-method', 'directory' => 'paymentMethod', 'obs' => 'Página para apagar o Forma de Pagamento do banco de dados.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 20],

            ['name'=> 'Listar Movimentos', 'controller' => 'Movements', 'controller_url' => 'movements', 'directory' => 'financialReports', 'obs' => 'Página listar os Movimentos.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 21],
            ['name'=> 'Listar Fluxo de Caixa', 'controller' => 'CashFlow', 'controller_url' => 'cash-flow', 'directory' => 'financialReports', 'obs' => 'Página para listar o Fluxo de Caixa.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 21],
            ['name'=> 'Exportar o Fluxo de Caixa em PDF', 'controller' => 'ExportPdfCashFlow', 'controller_url' => 'export-pdf-cash-flow', 'directory' => 'financialReports', 'obs' => 'Página para exportar o Fluxo de Caixa em PDF.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 21],
    
            ['name'=> 'Editar Movimentos', 'controller' => 'EditMovement', 'controller_url' => 'edit-movement', 'directory' => 'movement', 'obs' => 'Página para editar o Movimento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 22],
            ['name'=> 'Deletar Movimentos', 'controller' => 'DeleteMovement', 'controller_url' => 'delete-movement', 'directory' => 'movement', 'obs' => 'Página para apagar o Movimento.', 'public_page' => 0, 'page_status' => 1, 'adms_packages_page_id' => 1, 'adms_groups_page_id' => 22],          
            
        ];

        // Percorrer o array com dados que devem ser validados antes de cadastrar
        foreach ($pages as $page) {

            // Verifica se a página com o name especificado já existe
            $existingRecord = $this->query('SELECT id FROM adms_pages WHERE name=:name', ['name' => $page['name']])->fetch();

             // Se a página não existir, adiciona seus dados ao array $data
            if (!$existingRecord) {
                $data[] = [
                    'name' => $page['name'],
                    'controller' => $page['controller'],
                    'controller_url' => $page['controller_url'],
                    'directory' => $page['directory'],
                    'obs' => $page['obs'],
                    'public_page' => $page['public_page'], 
                    'page_status' => $page['page_status'],
                    'adms_packages_page_id' => $page['adms_packages_page_id'],
                    'adms_groups_page_id' => $page['adms_groups_page_id'],
                    'created_at' => date("Y-m-d H:i:s"),
                ];
            }
        }

        // Obtém a tabela 'adms_pages' para inserir os registros
        $adms_pages = $this->table('adms_pages');

        // Insere os registros na tabela
        $adms_pages->insert($data)->save();

    }
}
