<?php
use App\adms\Helpers\CSRFHelper;
$csrf_token = CSRFHelper::generateCSRFToken('form_update_training');
?>
<div class="container-fluid px-4">
    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Editar Treinamento</h2>
        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-trainings" class="text-decoration-none">Treinamentos</a>
            </li>
            <li class="breadcrumb-item">Editar</li>
        </ol>
    </div>
    <div class="card mb-4 border-light shadow">
        <div class="card-header hstack gap-2">
            <span>Editar</span>
        </div>
        <div class="card-body">
            <?php include './app/adms/Views/partials/alerts.php'; ?>
            <form action="" method="POST" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" id="nome" value="<?php echo $this->data['training']['nome'] ?? ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" id="codigo" value="<?php echo $this->data['training']['codigo'] ?? ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="versao" class="form-label">Versão</label>
                    <input type="text" name="versao" class="form-control" id="versao" value="<?php echo $this->data['training']['versao'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="validade" class="form-label">Validade</label>
                    <input type="date" name="validade" class="form-control" id="validade" value="<?php echo $this->data['training']['validade'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="tipo" class="form-label">Tipo</label>
                    <input type="text" name="tipo" class="form-control" id="tipo" value="<?php echo $this->data['training']['tipo'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="instructor_type" class="form-label">Tipo de Instrutor</label>
                    <select name="instructor_type" id="instructor_type" class="form-select" onchange="toggleInstructorFields()">
                        <option value="">Selecione...</option>
                        <option value="internal" <?php echo (isset($this->data['training']['instructor_user_id']) && !empty($this->data['training']['instructor_user_id'])) ? 'selected' : ''; ?>>Colaborador Interno</option>
                        <option value="external" <?php echo (isset($this->data['training']['instructor_user_id']) && empty($this->data['training']['instructor_user_id']) && !empty($this->data['training']['instructor_email'])) ? 'selected' : ''; ?>>Instrutor Externo</option>
                    </select>
                </div>
                <div class="col-md-3" id="instructor_user_div" style="display: none;">
                    <label for="instructor_user_id" class="form-label">Instrutor (Colaborador Interno)</label>
                    <select name="instructor_user_id" id="instructor_user_id" class="form-select" onchange="fillInstructorEmail()">
                        <option value="">Selecione um usuário...</option>
                        <?php foreach (($this->data['listUsers'] ?? []) as $user): ?>
                            <option value="<?php echo $user['id']; ?>" data-email="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" <?php echo (isset($this->data['training']['instructor_user_id']) && $this->data['training']['instructor_user_id'] == $user['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($user['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3" id="instructor_name_div" style="display: none;">
                    <label for="instrutor" class="form-label">Nome do Instrutor Externo</label>
                    <input type="text" name="instrutor" class="form-control" id="instrutor" value="<?php echo $this->data['training']['instrutor'] ?? ''; ?>">
                </div>
                <div class="col-md-3" id="instructor_email_div" style="display: none;">
                    <label for="instructor_email" class="form-label">E-mail do Instrutor</label>
                    <input type="email" name="instructor_email" class="form-control" id="instructor_email" value="<?php echo $this->data['training']['instructor_email'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="carga_horaria" class="form-label">Carga Horária</label>
                    <input type="number" name="carga_horaria" class="form-control" id="carga_horaria" value="<?php echo $this->data['training']['carga_horaria'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="ativo" class="form-label">Status</label>
                    <select name="ativo" class="form-select" id="ativo">
                        <option value="1" <?php echo (isset($this->data['training']['ativo']) && $this->data['training']['ativo'] == 1) ? 'selected' : ''; ?>>Ativo</option>
                        <option value="0" <?php echo (isset($this->data['training']['ativo']) && $this->data['training']['ativo'] == 0) ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="reciclagem" class="form-label">Necessita Reciclagem?</label>
                    <select name="reciclagem" id="reciclagem" class="form-select" onchange="toggleReciclagemPeriodo()">
                        <option value="0" <?php echo (isset($this->data['training']['reciclagem']) && !$this->data['training']['reciclagem']) ? 'selected' : ''; ?>>Não</option>
                        <option value="1" <?php echo (isset($this->data['training']['reciclagem']) && $this->data['training']['reciclagem']) ? 'selected' : ''; ?>>Sim</option>
                    </select>
                </div>
                <div class="col-md-3" id="reciclagem_periodo_div" style="display: none;">
                    <label for="reciclagem_periodo" class="form-label">Período de Reciclagem</label>
                    <input type="number" name="reciclagem_periodo" class="form-control" id="reciclagem_periodo" min="1" value="<?php echo $this->data['training']['reciclagem_periodo'] ?? ''; ?>">
                </div>

                <script>
                function toggleInstructorFields() {
                    var typeSelect = document.getElementById('instructor_type');
                    var userDiv = document.getElementById('instructor_user_div');
                    var nameDiv = document.getElementById('instructor_name_div');
                    var emailDiv = document.getElementById('instructor_email_div');
                    var userSelect = document.getElementById('instructor_user_id');
                    var emailInput = document.getElementById('instructor_email');
                    var nameInput = document.getElementById('instrutor');

                    if (typeSelect.value === 'internal') {
                        userDiv.style.display = 'block';
                        nameDiv.style.display = 'none';
                        emailDiv.style.display = 'block';
                        emailInput.readOnly = true;
                        nameInput.value = '';
                        setTimeout(fillInstructorEmail, 10);
                    } else if (typeSelect.value === 'external') {
                        userDiv.style.display = 'none';
                        nameDiv.style.display = 'block';
                        emailDiv.style.display = 'block';
                        emailInput.readOnly = false;
                        userSelect.value = '';
                        emailInput.value = '';
                    } else {
                        userDiv.style.display = 'none';
                        nameDiv.style.display = 'none';
                        emailDiv.style.display = 'none';
                        userSelect.value = '';
                        emailInput.value = '';
                        nameInput.value = '';
                    }
                }
                
                function fillInstructorEmail() {
                    var userSelect = document.getElementById('instructor_user_id');
                    var emailInput = document.getElementById('instructor_email');
                    if (!userSelect || !emailInput) return;
                    var selectedOption = userSelect.options[userSelect.selectedIndex];
                    if (userSelect.value && selectedOption && selectedOption.getAttribute('data-email')) {
                        emailInput.value = selectedOption.getAttribute('data-email');
                    } else {
                        emailInput.value = '';
                    }
                }
                
                function toggleReciclagemPeriodo() {
                    var reciclagemSelect = document.getElementById('reciclagem');
                    var periodoDiv = document.getElementById('reciclagem_periodo_div');
                    if (reciclagemSelect.value == '1') {
                        periodoDiv.style.display = 'block';
                    } else {
                        periodoDiv.style.display = 'none';
                        document.getElementById('reciclagem_periodo').value = '';
                    }
                }
                
                document.addEventListener('DOMContentLoaded', function() {
                    var userSelect = document.getElementById('instructor_user_id');
                    if (userSelect) {
                        userSelect.addEventListener('change', fillInstructorEmail);
                    }
                    toggleInstructorFields();
                    var typeSelect = document.getElementById('instructor_type');
                    if (typeSelect && typeSelect.value === 'internal') {
                        fillInstructorEmail();
                    }
                    toggleReciclagemPeriodo();
                });
                </script>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div> 