<?php

use App\adms\Helpers\CSRFHelper;

// Gera o token CSRF para proteger o formulário de deleção
$csrf_token = CSRFHelper::generateCSRFToken('form_delete_access_level');

?>

<div class="container-fluid px-4">

    <div class="mb-1 d-flex flex-column flex-sm-row gap-2">
        <h2 class="mt-3">Nívies de Acesso</h2>

        <ol class="breadcrumb mb-3 mt-0 mt-sm-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-access-levels" class="text-decoration-none">Nívies de Acesso</a>
            </li>
            <li class="breadcrumb-item">Visualizar</li>

        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header d-flex flex-column flex-sm-row gap-2">
            <span>Visualizar</span>

            <span class="ms-sm-auto d-sm-flex flex-row">

            <?php
                if (in_array('ListAccessLevels', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}list-access-levels' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-list'></i> Listar</a> ";
                }

                $id = ($this->data['accessLevel']['id'] ?? '');
                if (in_array('UpdateAccessLevel', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}update-access-level/$id' class='btn btn-warning btn-sm me-1 mb-1'><i class='fa-solid fa-pen-to-square'></i> Editar</a> ";
                }
                if (in_array('DeleteAccessLevel', $this->data['buttonPermission'])) {
                ?>

                    <!-- Formulário para deletar nível de acesso -->
                    <form id="formDelete<?php echo ($this->data['accessLevel']['id'] ?? ''); ?>" action="<?php echo $_ENV['URL_ADM']; ?>delete-access-level" method="POST">

                        <!-- Campo oculto para o token CSRF -->
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <!-- Campo oculto para o ID do nível de acesso -->
                        <input type="hidden" name="id" id="id" value="<?php echo ($this->data['accessLevel']['id'] ?? ''); ?>">

                        <!-- Botão para submeter o formulário -->
                        <button type="submit" class="btn btn-danger btn-sm me-1 mb-1" onclick="confirmDeletion(event, <?php echo ($this->data['accessLevel']['id'] ?? ''); ?>)"><i class="fa-regular fa-trash-can"></i> Apagar</button>

                    </form>
                <?php } ?>

            </span>
        </div>

        <div class="card-body">

            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';

            // Verifica se há usuários no array
            if (isset($this->data['accessLevel'])) {

                // Extrai variáveis do array $this->data['acessLevel'] para fácil acesso
                extract($this->data['accessLevel']);
            ?>

                <dl class="row">

                    <dt class="col-sm-3">ID: </dt>
                    <dd class="col-sm-9"><?php echo $id; ?></dd>

                    <dt class="col-sm-3">Nome: </dt>
                    <dd class="col-sm-9"><?php echo $name; ?></dd>

                    <dt class="col-sm-3">Cadastrado: </dt>
                    <dd class="col-sm-9"><?php echo ($create_at ? date('d/m/Y H:i:s', strtotime($create_at)) : ""); ?></dd>

                    <dt class="col-sm-3">Editado: </dt>
                    <dd class="col-sm-9"><?php echo ($update_at ? date('d/m/Y H:i:s', strtotime($update_at)) : ""); ?></dd>
                </dl>

            <?php
            } else { // Caso o nível de acesso não seja encontrado
                echo "<div class='alert alert-danger' role='alert'>Nível de acesso não encontrado!</div>";
            }
            ?>

        </div>

    </div>

</div>