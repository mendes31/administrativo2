<?php

use App\adms\Helpers\CSRFHelper;

?>
<div class="container-fluid px-4">

    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Data Mapping LGPD</h2>

        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>lgpd-data-mapping" class="text-decoration-none">Data Mapping</a>
            </li>
            <li class="breadcrumb-item">Cadastrar</li>
        </ol>

    </div>

    <div class="card mb-4 border-light shadow">

        <div class="card-header hstack gap-2">
            <span>Cadastrar</span>

            <span class="ms-auto d-sm-flex flex-row">
            <?php
                if (in_array('LgpdDataMapping', $this->data['buttonPermission'])) {
                    echo "<a href='{$_ENV['URL_ADM']}lgpd-data-mapping' class='btn btn-info btn-sm me-1 mb-1'><i class='fa-solid fa-list'></i> Listar</a> ";
                }
                ?>
            </span>

        </div>

        <div class="card-body">

            <?php include './app/adms/Views/partials/alerts.php'; ?>

            <!-- Formulário para cadastrar um novo data mapping -->
            <form action="" method="POST" class="row g-3">

                <input type="hidden" name="csrf_token" value="<?php echo CSRFHelper::generateCSRFToken('form_create_data_mapping'); ?>">

                <div class="col-md-6 col-sm-12">
                    <label for="source_system" class="form-label">Sistema Origem</label>
                    <input type="text" name="source_system" class="form-control" id="source_system" placeholder="Ex: ERP, CRM, Sistema RH" value="<?php echo $this->data['form']['source_system'] ?? ''; ?>">
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="source_field" class="form-label">Campo Origem</label>
                    <input type="text" name="source_field" class="form-control" id="source_field" placeholder="Ex: cpf, email, nome" value="<?php echo $this->data['form']['source_field'] ?? ''; ?>">
                </div>

                <div class="col-12">
                    <label for="transformation_rule" class="form-label">Regra de Transformação</label>
                    <textarea name="transformation_rule" class="form-control" id="transformation_rule" placeholder="Ex: Remover pontos e traços, Validar formato, etc." rows="3"><?php echo $this->data['form']['transformation_rule'] ?? ''; ?></textarea>
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="destination_system" class="form-label">Sistema Destino</label>
                    <input type="text" name="destination_system" class="form-control" id="destination_system" placeholder="Ex: Financeiro, Marketing, Analytics" value="<?php echo $this->data['form']['destination_system'] ?? ''; ?>">
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="destination_field" class="form-label">Campo Destino</label>
                    <input type="text" name="destination_field" class="form-control" id="destination_field" placeholder="Ex: document_id, contact_email, full_name" value="<?php echo $this->data['form']['destination_field'] ?? ''; ?>">
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="ropa_id" class="form-label">ROPA</label>
                    <select name="ropa_id" class="form-select" id="ropa_id">
                        <option value="" selected>Selecione uma operação ROPA</option>
                        <?php if (isset($this->data['ropas'])): ?>
                            <?php foreach ($this->data['ropas'] as $ropa): ?>
                                <option value="<?= $ropa['id'] ?>" <?= (isset($this->data['form']['ropa_id']) && $this->data['form']['ropa_id'] == $ropa['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ropa['atividade']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="inventory_id" class="form-label">Inventário</label>
                    <select name="inventory_id" class="form-select" id="inventory_id">
                        <option value="" selected>Selecione um item do inventário</option>
                        <?php if (isset($this->data['inventories'])): ?>
                            <?php foreach ($this->data['inventories'] as $inventory): ?>
                                <option value="<?= $inventory['id'] ?>" <?= (isset($this->data['form']['inventory_id']) && $this->data['form']['inventory_id'] == $inventory['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($inventory['area'] . ' - ' . $inventory['data_type']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label for="observation" class="form-label">Observações</label>
                    <textarea name="observation" class="form-control" id="observation" placeholder="Observações sobre o mapeamento (LGPD)" rows="3"><?php echo $this->data['form']['observation'] ?? ''; ?></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Salvar</button>
                    <a href="<?php echo $_ENV['URL_ADM']; ?>lgpd-data-mapping" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
                </div>

            </form>

        </div>

    </div>
</div>