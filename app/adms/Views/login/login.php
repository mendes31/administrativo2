<?php

use App\adms\Helpers\CSRFHelper;

?>

<div class="col-lg-5">
    <div class="card shadow-lg border-0 rounded-lg mt-5">

        <div class="card-header">
            <h3 class="text-center font-weight-light my-4">Login</h3>
        </div>

        <div class="card-body">
            <?php // Inclui o arquivo que exibe mensagens de sucesso e erro
            include './app/adms/Views/partials/alerts.php';
            ?>

            <form action="" method="POST">
                <!-- Campo oculto para o token CSRF para proteger o formulário contra ataques de falsificação de solicitação entre sites -->
                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_login'); ?>">


                <!-- Campo usuário -->
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="username" placeholder="Digite seu usuário" value="<?php echo $this->data['form']['username'] ?? ''; ?>">
                    <label for="username">Usuário</label>
                </div>

                <!-- Campo para a senha do usuário -->
                <div class="form-floating mb-3">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Digite sua senha." value="<?php echo $this->data['form']['password'] ?? ''; ?>">
                    <label for="password">Senha</label>
                </div>

                <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                    <a href="<?php echo $_ENV['URL_ADM']; ?>forgot-password" class="small text-decoration-none">Esqueceu a Senha?</a>
                    <!-- Botão para submeter o formulário -->
                    <button type="submit" class="btn btn-primary btn-sm">Acessar</button>
                </div>
            </form>

        </div>

        <div class="card-footer text-center py-3">
            <div class="small">
                <a href="<?php echo $_ENV['URL_ADM']; ?>new-user" class="text-decoration-none">Cadastrar</a>
            </div>
        </div>

    </div>
</div>