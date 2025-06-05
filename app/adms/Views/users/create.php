<?php

use App\adms\Helpers\CSRFHelper;

?>

<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Usuários</h2>

        <ol class="breadcrumb  mb-3 ms-auto">
            <li class="breadcrumb-item"><a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo $_ENV['URL_ADM']; ?>list-users" class="text-decoration-none">Usuários</a></li>
            <li class="breadcrumb-item">Cadastrar</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span>
                Cadastrar
            </span>

            <span class="ms-auto d-sm-flex flex-row">

            <?php
                if (in_array('ListUsers', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}list-users' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-list-ul'></i> Listar</a> ";
                }
                ?>
            </span>
        </div>

        <div class="card-body">
            <?php
            // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';
            ?>

            <!-- Formulário para cadastrar um novo usuário -->
            <form action="" method="POST" class="row g-3">

                <!-- Campo oculto para o token CSRF para proteger o formulário contra ataques de falsificação de solicitação entre sites -->
                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_create_user'); ?>">

                <div class="col-md-12">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Nome completo" value="<?php echo $this->data['form']['name'] ?? ''; ?>">
                </div>

                <div class="col-md-12">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Digite o seu melhor email" value="<?php echo $this->data['form']['email'] ?? ''; ?>">
                </div>

                <div class="col-md-12">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="Digite um usuário disponível" value="<?php echo $this->data['form']['username'] ?? ''; ?>">
                </div>

                <div class="col-md-6">
                    <label for="user_department_id" class="form-label">Departamento</label>
                    <select name="user_department_id" class="form-select" id="user_department_id">
                        <option value="" selected>Selecione</option>
                        <?php
                        // Verificar se existe pacotes
                        if ($this->data['listDepartments'] ?? false) {
                            // percorrer o array de pacotes
                            foreach ($this->data['listDepartments'] as $listDepartment) {
                                // Extrari as variáveis do array
                                extract($listDepartment);
                                // Verificar se deve manter selecionado a opção
                                $selected = isset($this->data['form']['user_department_id']) && $this->data['form']['user_department_id'] == $id ? 'selected' : '';
                                echo "<option value='$id' $selected >$name</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="user_position_id" class="form-label">Cargo</label>
                    <select name="user_position_id" class="form-select" id="user_position_id">
                        <option value="" selected>Selecione</option>
                        <?php
                        // Verificar se existe o cargo 
                        if ($this->data['listPositions'] ?? false) {
                            // percorrer o array de cargo
                            foreach ($this->data['listPositions'] as $listPosition) {
                                // Extrari as variáveis do array
                                extract($listPosition);
                                // Verificar se deve manter selecionado a opção
                                $selected = isset($this->data['form']['user_position_id']) && $this->data['form']['user_position_id'] == $id ? 'selected' : '';
                                echo "<option value='$id' $selected >$name</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Senha minímo 6 caracteres e deve conter letra, número e caractere especial." value="<?php echo $this->data['form']['password'] ?? ''; ?>">
                </div>

                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirmar Senha</label>
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirmar a senha." value="<?php echo $this->data['form']['confirm_password'] ?? ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </div>

            </form>

        </div>
    </div>
</div>