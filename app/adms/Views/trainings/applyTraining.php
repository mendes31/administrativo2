<?php
use App\adms\Helpers\FormatHelper;
?>
<div class="container-fluid px-4">
    <div class="mb-1 hstack gap-2">
        <h2 class="mt-3">Registrar Aplicação de Treinamento</h2>
        <ol class="breadcrumb mb-3 mt-3 ms-auto">
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>dashboard" class="text-decoration-none">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo $_ENV['URL_ADM']; ?>list-training-status" class="text-decoration-none">Status de Treinamentos</a>
            </li>
            <li class="breadcrumb-item">Registrar Aplicação</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 border-light shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Registrar Aplicação
                    </h5>
                </div>
                <div class="card-body">
                    <?php include './app/adms/Views/partials/alerts.php'; ?>
                    
                    <?php
                    $tipoRegistro = $_GET['tipo_registro'] ?? $_POST['tipo_registro'] ?? null;
                    $editando = !empty($this->data['edit_id']);
                    if ($editando) {
                        if (!empty($this->data['trainingUser']['data_realizacao'])) {
                            $tipoRegistro = 'realizacao';
                        } elseif (!empty($this->data['trainingUser']['data_agendada'])) {
                            $tipoRegistro = 'agendamento';
                        }
                        if (isset($_GET['tipo_registro']) && $_GET['tipo_registro'] !== $tipoRegistro) {
                            header('Location: ' . $_ENV['URL_ADM'] . 'list-training-status');
                            exit;
                        }
                    } else {
                        if ($tipoRegistro !== 'realizacao' && $tipoRegistro !== 'agendamento') {
                            $tipoRegistro = 'realizacao';
                        }
                    }
                    $exigeReciclagem = !empty($this->data['training']['reciclagem']) && !empty($this->data['training']['reciclagem_periodo']);
                    $aplicacaoAntecipada = false;
                    if ($exigeReciclagem && !empty($this->data['trainingUser']['data_realizacao'])) {
                        $dataRealizacao = new DateTime($this->data['trainingUser']['data_realizacao']);
                        $dataVencimento = clone $dataRealizacao;
                        $dataVencimento->add(new DateInterval('P' . $this->data['training']['reciclagem_periodo'] . 'M'));
                        $hoje = new DateTime();
                        $aplicacaoAntecipada = ($hoje < $dataVencimento);
                    }
                    $inicioCiclo = $_POST['inicio_ciclo'] ?? $_GET['inicio_ciclo'] ?? 'aplicacao';
                    ?>

                    <form method="POST" action="<?php echo $_ENV['URL_ADM']; ?>apply-training">
                        <input type="hidden" name="training_id" value="<?php echo $this->data['training_id']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $this->data['user_id']; ?>">
<?php if (!empty($this->data['edit_id'])): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $this->data['edit_id']; ?>">
<?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Tipo de Registro *</strong></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_registro" id="radio_realizacao" value="realizacao" <?= $tipoRegistro === 'realizacao' ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label" for="radio_realizacao">Registrar Realização</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo_registro" id="radio_agendamento" value="agendamento" <?= $tipoRegistro === 'agendamento' ? 'checked' : '' ?> disabled>
                                    <label class="form-check-label" for="radio_agendamento">Agendar Treinamento</label>
                                </div>
                                <input type="hidden" name="tipo_registro" value="<?= $tipoRegistro ?>">
                            </div>
                        </div>

                        <div class="row mb-3" id="div_realizacao">
                            <div class="col-md-6">
                                <label for="data_realizacao" class="form-label">
                                    <strong>Data de Realização *</strong>
                                </label>
                                <input type="date" 
                                       name="data_realizacao" 
                                       class="form-control" 
                                       id="data_realizacao" 
                                       value="<?php echo $this->data['trainingUser']['data_realizacao'] ?? date('Y-m-d'); ?>"
                                       max="<?php echo date('Y-m-d'); ?>">
                                <div class="form-text">Data em que o treinamento foi realizado</div>
                            </div>
                            <div class="col-md-6">
                                <label for="nota" class="form-label">
                                    <strong>Nota</strong>
                                </label>
                                <input type="number" 
                                       name="nota" 
                                       class="form-control" 
                                       id="nota" 
                                       min="0" 
                                       max="10" 
                                       step="0.1"
                                       value="<?php echo $this->data['trainingUser']['nota'] ?? ''; ?>"
                                       placeholder="0.0 a 10.0">
                                <div class="form-text">Nota de 0 a 10 (opcional)</div>
                            </div>
                        </div>

                        <div class="row mb-3" id="div_agendamento" style="display:none;">
                            <div class="col-md-6">
                                <label for="data_agendada" class="form-label">
                                    <strong>Data Agendada *</strong>
                                </label>
                                <input type="date" 
                                       name="data_agendada" 
                                       class="form-control" 
                                       id="data_agendada" 
                                       value="<?php echo $this->data['trainingUser']['data_agendada'] ?? ''; ?>"
                                       min="<?php echo date('Y-m-d'); ?>">
                                <div class="form-text">Data em que o treinamento está agendado para ocorrer</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="observacoes" class="form-label">
                                    <strong>Observações</strong>
                                </label>
                                <textarea name="observacoes" 
                                          class="form-control" 
                                          id="observacoes" 
                                          rows="4" 
                                          placeholder="Observações sobre a aplicação ou agendamento do treinamento..."><?php echo $this->data['trainingUser']['observacoes'] ?? ''; ?></textarea>
                                <div class="form-text">Observações adicionais sobre o treinamento</div>
                            </div>
                        </div>

                        <?php if ($exigeReciclagem && $tipoRegistro === 'realizacao'): ?>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Início do Próximo Ciclo *</strong></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inicio_ciclo" id="ciclo_aplicacao" value="aplicacao" <?= $inicioCiclo === 'aplicacao' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ciclo_aplicacao">A partir da data de aplicação</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="inicio_ciclo" id="ciclo_vencimento" value="vencimento" <?= $inicioCiclo === 'vencimento' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ciclo_vencimento">A partir da data prevista de vencimento anterior<?php if (isset($dataVencimento)) echo ' (' . $dataVencimento->format('d/m/Y') . ')'; ?></label>
                                </div>
                                <div class="form-text">Escolha como será calculado o início do próximo ciclo de reciclagem.</div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo $_ENV['URL_ADM']; ?>list-training-status" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Voltar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Salvar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 border-light shadow">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informações do Treinamento
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Treinamento:</strong><br>
                        <span class="text-primary"><?php echo htmlspecialchars($this->data['training']['nome']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Código:</strong><br>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($this->data['training']['codigo']); ?></span>
                    </div>
                    
                    <?php if (!empty($this->data['training']['versao'])): ?>
                    <div class="mb-3">
                        <strong>Versão:</strong><br>
                        <span class="badge bg-info"><?php echo htmlspecialchars($this->data['training']['versao']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($this->data['training']['reciclagem']) && !empty($this->data['training']['reciclagem_periodo'])): ?>
                    <div class="mb-3">
                        <strong>Reciclagem:</strong><br>
                        <span class="badge bg-warning text-dark">
                            <?php echo FormatHelper::formatReciclagemPeriodo((int)$this->data['training']['reciclagem_periodo']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4 border-light shadow">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informações do Colaborador
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Nome:</strong><br>
                        <span class="text-primary"><?php echo htmlspecialchars($this->data['user']['name']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <span class="text-muted"><?php echo htmlspecialchars($this->data['user']['email']); ?></span>
                    </div>
                    
                    <?php if (!empty($this->data['trainingUser']['data_realizacao'])): ?>
                    <div class="mb-3">
                        <strong>Última Aplicação:</strong><br>
                        <span class="badge bg-info">
                            <?php echo (new DateTime($this->data['trainingUser']['data_realizacao']))->format('d/m/Y'); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($this->data['trainingUser']['nota'])): ?>
                    <div class="mb-3">
                        <strong>Última Nota:</strong><br>
                        <span class="badge bg-success"><?php echo number_format($this->data['trainingUser']['nota'], 1); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Exibe apenas o bloco do tipo correto
const tipoRegistro = '<?= $tipoRegistro ?>';
document.addEventListener('DOMContentLoaded', function() {
    if (tipoRegistro === 'realizacao') {
        document.getElementById('div_realizacao').style.display = '';
        document.getElementById('div_agendamento').style.display = 'none';
    } else {
        document.getElementById('div_realizacao').style.display = 'none';
        document.getElementById('div_agendamento').style.display = '';
    }
});
</script> 