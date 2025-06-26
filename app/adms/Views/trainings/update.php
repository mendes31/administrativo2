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
                    <label for="instrutor" class="form-label">Instrutor</label>
                    <input type="text" name="instrutor" class="form-control" id="instrutor" value="<?php echo $this->data['training']['instrutor'] ?? ''; ?>">
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
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div> 