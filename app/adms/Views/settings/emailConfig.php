<?php
$config = $this->data['email_config'] ?? [];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Configuração de Servidor de E-mail (SMTP)</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= $_ENV['URL_ADM'] ?>email-config/save">
                        <div class="mb-3">
                            <label for="MAIL_HOST" class="form-label">Servidor SMTP (Host)</label>
                            <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST" value="<?= htmlspecialchars($config['MAIL_HOST'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="MAIL_USERNAME" class="form-label">Usuário SMTP (E-mail)</label>
                            <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" value="<?= htmlspecialchars($config['MAIL_USERNAME'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="MAIL_PASSWORD" class="form-label">Senha SMTP</label>
                            <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" value="<?= htmlspecialchars($config['MAIL_PASSWORD'] ?? '') ?>" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="MAIL_PORT" class="form-label">Porta</label>
                            <input type="text" class="form-control" id="MAIL_PORT" name="MAIL_PORT" value="<?= htmlspecialchars($config['MAIL_PORT'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="MAIL_ENCRYPTI" class="form-label">Criptografia</label>
                            <select class="form-select" id="MAIL_ENCRYPTI" name="MAIL_ENCRYPTI" required>
                                <option value="TLS" <?= ($config['MAIL_ENCRYPTI'] ?? '') == 'TLS' ? 'selected' : '' ?>>TLS</option>
                                <option value="SSL" <?= ($config['MAIL_ENCRYPTI'] ?? '') == 'SSL' ? 'selected' : '' ?>>SSL</option>
                                <option value="" <?= ($config['MAIL_ENCRYPTI'] ?? '') == '' ? 'selected' : '' ?>>Nenhuma</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="EMAIL_TI" class="form-label">E-mail Remetente (From)</label>
                            <input type="text" class="form-control" id="EMAIL_TI" name="EMAIL_TI" value="<?= htmlspecialchars($config['EMAIL_TI'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="NAME_EMAIL_TI" class="form-label">Nome Remetente</label>
                            <input type="text" class="form-control" id="NAME_EMAIL_TI" name="NAME_EMAIL_TI" value="<?= htmlspecialchars($config['NAME_EMAIL_TI'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Salvar Configurações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 