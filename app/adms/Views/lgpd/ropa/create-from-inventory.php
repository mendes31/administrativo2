<?php

use App\adms\Helpers\CSRFHelper;

?>
<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Criar ROPA a partir do Inventário</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>lgpd-inventory" class="text-decoration-none">Inventário</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>lgpd-inventory-view/<?php echo $this->data['inventory']['id']; ?>" class="text-decoration-none">Visualizar</a>
            </li>
            <li class="breadcrumb-item">Criar ROPA</li>
        </ol>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-info-circle"></i> Dados do Inventário</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Departamento:</strong> <?php echo htmlspecialchars($this->data['inventory']['area']); ?></p>
                    <p><strong>Titular:</strong> <?php echo htmlspecialchars($this->data['inventory']['data_subject']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Local:</strong> <?php echo htmlspecialchars($this->data['inventory']['storage_location']); ?></p>
                    <p><strong>Acesso:</strong> <?php echo htmlspecialchars($this->data['inventory']['access_level']); ?></p>
                </div>
            </div>
            
            <?php if (!empty($this->data['data_groups'])): ?>
                <div class="mt-3">
                    <h6><i class="fa-solid fa-database"></i> Grupos de Dados Identificados:</h6>
                    <div class="row">
                        <?php foreach ($this->data['data_groups'] as $group): ?>
                            <div class="col-md-6 mb-2">
                                <span class="badge bg-<?php echo $group['data_category'] === 'Sensível' ? 'danger' : 'primary'; ?> me-1">
                                    <?php echo htmlspecialchars($group['name']); ?>
                                </span>
                                <small class="text-muted">(<?php echo $group['data_category']; ?> - Risco <?php echo $group['risk_level']; ?>)</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4 border-light shadow">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa-solid fa-plus-circle"></i> Criar ROPA</h5>
        </div>
        <div class="card-body">
            <?php include './app/adms/Views/partials/alerts.php'; ?>

            <form method="POST" action="">
                <?php CSRFHelper::generateCSRFToken('form_create_ropa'); ?>
                
                <div class="row">
                    <!-- Código -->
                    <div class="col-md-6 mb-3">
                        <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="codigo" name="codigo" 
                               value="<?php echo $this->data['prefilled_data']['codigo'] ?? 'ROPA-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT); ?>" required>
                    </div>

                    <!-- Atividade -->
                    <div class="col-md-6 mb-3">
                        <label for="atividade" class="form-label">Atividade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="atividade" name="atividade" 
                               value="<?php echo $this->data['prefilled_data']['atividade'] ?? ''; ?>" required>
                    </div>

                    <!-- Departamento -->
                    <div class="col-md-6 mb-3">
                        <label for="departamento_id" class="form-label">Departamento <span class="text-danger">*</span></label>
                        <select class="form-select" id="departamento_id" name="departamento_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($this->data['departamentos'] as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>" 
                                        <?php echo ($dept['id'] == $this->data['prefilled_data']['departamento_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Finalidade do Processamento -->
                    <div class="col-md-6 mb-3">
                        <label for="processing_purpose" class="form-label">Finalidade do Processamento</label>
                        <input type="text" class="form-control" id="processing_purpose" name="processing_purpose" 
                               value="<?php echo $this->data['prefilled_data']['processing_purpose'] ?? ''; ?>">
                    </div>

                    <!-- Titular -->
                    <div class="col-md-6 mb-3">
                        <label for="data_subject" class="form-label">Titular dos Dados</label>
                        <input type="text" class="form-control" id="data_subject" name="data_subject" 
                               value="<?php echo $this->data['prefilled_data']['data_subject'] ?? ''; ?>">
                    </div>

                    <!-- Dados Pessoais -->
                    <div class="col-md-6 mb-3">
                        <label for="personal_data" class="form-label">Dados Tratados</label>
                        <textarea class="form-control" id="personal_data" name="personal_data" rows="2"><?php echo $this->data['prefilled_data']['personal_data'] ?? ''; ?></textarea>
                    </div>

                    <!-- Base Legal -->
                    <div class="col-md-6 mb-3">
                        <label for="base_legal" class="form-label">Base Legal <span class="text-danger">*</span></label>
                        <select class="form-select" id="base_legal" name="base_legal" required>
                            <option value="">Selecione...</option>
                            <option value="Consentimento" <?php echo ($this->data['prefilled_data']['base_legal'] === 'Consentimento') ? 'selected' : ''; ?>>Consentimento</option>
                            <option value="Execução de contrato" <?php echo ($this->data['prefilled_data']['base_legal'] === 'Execução de contrato') ? 'selected' : ''; ?>>Execução de contrato</option>
                            <option value="Obrigação legal" <?php echo ($this->data['prefilled_data']['base_legal'] === 'Obrigação legal') ? 'selected' : ''; ?>>Obrigação legal</option>
                            <option value="Legítimo interesse" <?php echo ($this->data['prefilled_data']['base_legal'] === 'Legítimo interesse') ? 'selected' : ''; ?>>Legítimo interesse</option>
                            <option value="Proteção ao crédito" <?php echo ($this->data['prefilled_data']['base_legal'] === 'Proteção ao crédito') ? 'selected' : ''; ?>>Proteção ao crédito</option>
                        </select>
                    </div>

                    <!-- Compartilhamento -->
                    <div class="col-md-6 mb-3">
                        <label for="sharing" class="form-label">Compartilhamento</label>
                        <input type="text" class="form-control" id="sharing" name="sharing" 
                               value="<?php echo $this->data['prefilled_data']['sharing'] ?? ''; ?>">
                    </div>

                    <!-- Retenção -->
                    <div class="col-md-6 mb-3">
                        <label for="retencao" class="form-label">Prazo de Retenção</label>
                        <input type="text" class="form-control" id="retencao" name="retencao" 
                               value="<?php echo $this->data['prefilled_data']['retencao'] ?? ''; ?>">
                    </div>

                    <!-- Riscos -->
                    <div class="col-md-6 mb-3">
                        <label for="riscos" class="form-label">Riscos Identificados</label>
                        <input type="text" class="form-control" id="riscos" name="riscos" 
                               value="<?php echo $this->data['prefilled_data']['riscos'] ?? ''; ?>">
                    </div>

                    <!-- Medidas de Segurança -->
                    <div class="col-md-12 mb-3">
                        <label for="medidas_seguranca" class="form-label">Medidas de Segurança</label>
                        <textarea class="form-control" id="medidas_seguranca" name="medidas_seguranca" rows="3"><?php echo $this->data['prefilled_data']['medidas_seguranca'] ?? ''; ?></textarea>
                    </div>

                    <!-- Observações -->
                    <div class="col-md-12 mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo $this->data['prefilled_data']['observacoes'] ?? ''; ?></textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Ativo" selected>Ativo</option>
                            <option value="Revisão">Revisão</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i>
                    <strong>Dica:</strong> Os campos foram pré-preenchidos com base nos dados do inventário. 
                    Revise e ajuste conforme necessário antes de salvar.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="SendAddRopa" class="btn btn-success">
                        <i class="fa-solid fa-save"></i> Criar ROPA
                    </button>
                    <a href="<?php echo $_ENV['URL_ADM']; ?>lgpd-inventory-view/<?php echo $this->data['inventory']['id']; ?>" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div> 