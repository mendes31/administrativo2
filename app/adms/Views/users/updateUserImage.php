<?php

use App\adms\Helpers\CSRFHelper;

?>

<div class="container-fluid px-4">

    <div class="mb-1 d-flex flex-column flex-sm-row gap-2">
        <h2 class="mt-3">Usuários</h2>

        <ol class="breadcrumb  mb-3 mt-0 mt-sm-3 ms-auto">
            <li class="breadcrumb-item"><a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo $_ENV['URL_ADM']; ?>list-users" class="text-decoration-none">Usuários</a></li>
            <li class="breadcrumb-item">Editar</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">

            <span>Editar Imagem</span>

            <span class="ms-auto d-sm-flex flex-row">

                <?php
                if (in_array('ListUsers', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}list-users' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-solid fa-list-ul'></i> Listar</a> ";
                }

                $id = ($this->data['form']['id'] ?? '');
                if (in_array('ViewUser', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}view-user/$id' class='btn btn-primary btn-sm me-1 mb-1'><i class='fa-regular fa-eye'></i> Visualizar</a> ";
                }
                ?>
            </span>

        </div>

        <div class="card-body">
            <?php
            // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';
            ?>

            <form action="" method="POST" class="row g-3" enctype="multipart/form-data">

                <!-- Token CSRF para segurança -->
                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_update_user_image'); ?>">

                <!-- ID do usuário -->
                <input type="hidden" name="id" id="id" value="<?php echo $this->data['form']['id'] ?? ''; ?>">

                <!-- Campo de Upload de Imagem -->
                <div class="col-12">
                    <label class="form-label">Imagem</label>
                    <input type="file" name="new_image" class="form-control" id="new_image"
                        accept="image/png, image/jpeg, image/jpg" aria-describedby="fileHelp">
                    <small id="fileHelp" class="form-text text-muted">Formatos permitidos: PNG, JPG, JPEG.</small>
                </div>

                <!-- Preview da Imagem Selecionada -->
                <div class="col-12">
                    <img id="image_preview" src="" class="img-thumbnail d-none" alt="Preview da Imagem" style="max-width: 200px;">
                </div>

                <!-- Botão de envio (desativado até que uma imagem seja selecionada) -->
                <div class="col-12">
                    <button type="submit" class="btn btn-warning btn-sm">Salvar</button>
                </div>

            </form>


        </div>
    </div>

</div>

<!-- Script para exibir preview da imagem e habilitar o botão -->
<script>
    document.getElementById('new_image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image_preview');
        const uploadBtn = document.getElementById('upload_btn');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
            uploadBtn.removeAttribute('disabled');
        } else {
            preview.classList.add('d-none');
            uploadBtn.setAttribute('disabled', 'true');
        }
    });
</script>