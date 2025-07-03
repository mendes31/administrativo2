<?php

use App\adms\Helpers\CSRFHelper;
// Exibe alertas de sucesso/erro
include './app/adms/Views/partials/alerts.php';
$form = $this->data['form'] ?? null;

// Definir exemplos fixos conforme o nível
$exemplos = [
    'Baixo' => 'abcd',
    'Médio' => 'Abcde1',
    'Elevado' => 'Abcde12@',
    'Customizado' => ''
];
$nivel = $form->nivel_seguranca ?? 'Baixo';
$exemplo_senha = $nivel !== 'Customizado' ? $exemplos[$nivel] : ($form->exemplo_senha ?? '');
?>
<div class="col-lg-6 mx-auto">
    <div class="card shadow-lg border-0 rounded-lg mt-5">
        <div class="card-header">
            <h3 class="text-center font-weight-light my-4">Administração de Senhas</h3>
        </div>
        <div class="card-body">
            <div id="alert-area"></div>
            <form id="formPasswordPolicy" method="POST" action="<?= $_ENV['URL_ADM'] ?>ajax-password-policy">
                <input type="hidden" name="id" value="<?= $form->id ?? '' ?>">
                <div class="container-fluid">
                    <div class="row mb-3 align-items-center">
                        <label for="vencimento_dias" class="col-md-3 col-form-label text-end">Vencimento após</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="vencimento_dias" id="vencimento_dias" value="<?= $form->vencimento_dias ?? '' ?>">
                        </div>
                        <div class="col-md-2" id="vencimento-unidade">Dias</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="comprimento_minimo" class="col-md-3 col-form-label text-end">Comprimento mínimo</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="comprimento_minimo" id="comprimento_minimo" value="<?= $form->comprimento_minimo ?? '' ?>">
                        </div>
                        <div class="col-md-2">Caracteres</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="min_maiusculas" class="col-md-3 col-form-label text-end">Nº mínimo de caracteres maiúsculos</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="min_maiusculas" id="min_maiusculas" value="<?= $form->min_maiusculas ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="min_minusculas" class="col-md-3 col-form-label text-end">Nº mínimo de caracteres minúsculos</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="min_minusculas" id="min_minusculas" value="<?= $form->min_minusculas ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="min_digitos" class="col-md-3 col-form-label text-end">Nº mínimo de dígitos</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="min_digitos" id="min_digitos" value="<?= $form->min_digitos ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="min_nao_alfanumericos" class="col-md-3 col-form-label text-end">Nº mínimo de caracteres não alfanuméricos</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="min_nao_alfanumericos" id="min_nao_alfanumericos" value="<?= $form->min_nao_alfanumericos ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="historico_senhas" class="col-md-3 col-form-label text-end">Senha não pode coincidir</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="historico_senhas" id="historico_senhas" value="<?= $form->historico_senhas ?? '' ?>">
                        </div>
                        <div class="col-md-2">Senhas anteriores</div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="tentativas_bloqueio" class="col-md-3 col-form-label text-end">Autenticações antes da conta ser bloqueada</label>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="tentativas_bloqueio" id="tentativas_bloqueio" value="<?= $form->tentativas_bloqueio ?? '' ?>">
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="exemplo_senha" class="col-md-3 col-form-label text-end">Exemplo de senha</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="exemplo_senha" id="exemplo_senha" value="<?= $form->exemplo_senha ?? '' ?>" <?= ($form->nivel_seguranca ?? '') === 'Customizado' ? '' : 'readonly' ?>>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label for="nivel_seguranca" class="col-md-3 col-form-label text-end">Nível de segurança</label>
                        <div class="col-md-3">
                            <select class="form-select" name="nivel_seguranca" id="nivel_seguranca">
                                <option value="Baixo" <?= ($form->nivel_seguranca ?? '') === 'Baixo' ? 'selected' : '' ?>>Baixo</option>
                                <option value="Médio" <?= ($form->nivel_seguranca ?? '') === 'Médio' ? 'selected' : '' ?>>Médio</option>
                                <option value="Elevado" <?= ($form->nivel_seguranca ?? '') === 'Elevado' ? 'selected' : '' ?>>Elevado</option>
                                <option value="Customizado" <?= ($form->nivel_seguranca ?? '') === 'Customizado' ? 'selected' : '' ?>>Customizado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" id="btnSalvar" class="btn btn-success">Salvar Configurações</button>
                    <a href="<?= $_ENV['URL_ADM'] ?>dashboard" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nivelSelect = document.getElementById('nivel_seguranca');
    const vencimentoInput = document.getElementById('vencimento_dias');
    const vencimentoUnidade = document.getElementById('vencimento-unidade');
    const exemploSenhaInput = document.getElementById('exemplo_senha');

    // Valores padrão para cada nível
    const niveis = {
        'Baixo': {
            vencimento_dias: '',
            comprimento_minimo: 4,
            min_maiusculas: 0,
            min_minusculas: 0,
            min_digitos: 0,
            min_nao_alfanumericos: 0,
            historico_senhas: 0,
            tentativas_bloqueio: 5,
            exemplo_senha: 'abcd',
        },
        'Médio': {
            vencimento_dias: 90,
            comprimento_minimo: 6,
            min_maiusculas: 1,
            min_minusculas: 0,
            min_digitos: 1,
            min_nao_alfanumericos: 0,
            historico_senhas: 3,
            tentativas_bloqueio: 3,
            exemplo_senha: 'Abcde1',
        },
        'Elevado': {
            vencimento_dias: 30,
            comprimento_minimo: 8,
            min_maiusculas: 1,
            min_minusculas: 1,
            min_digitos: 1,
            min_nao_alfanumericos: 1,
            historico_senhas: 5,
            tentativas_bloqueio: 3,
            exemplo_senha: 'Abcde12@',
        },
        'Customizado': null // Não altera nada
    };

    function toggleCamposNivel() {
        const nivel = nivelSelect.value;
        if (nivel === 'Baixo') {
            vencimentoInput.value = '';
            vencimentoInput.disabled = true;
            if (vencimentoUnidade) vencimentoUnidade.style.display = 'none';
        } else {
            vencimentoInput.disabled = false;
            if (vencimentoUnidade) vencimentoUnidade.style.display = '';
        }
        if (nivel === 'Customizado') {
            exemploSenhaInput.readOnly = false;
        } else {
            exemploSenhaInput.readOnly = true;
        }
    }

    function atualizarCamposPorNivel() {
        const nivel = nivelSelect.value;
        if (niveis[nivel] && nivel !== 'Customizado') {
            for (const campo in niveis[nivel]) {
                const input = document.getElementById(campo);
                if (input) input.value = niveis[nivel][campo];
            }
        }
        toggleCamposNivel();
    }

    nivelSelect.addEventListener('change', function() {
        atualizarCamposPorNivel();
    });
    // Ao carregar a página, só ajusta os campos de acordo com o nível, mas mantém valores do banco
    toggleCamposNivel();

    // Após salvar, recarregar a página para exibir os novos valores
    const form = document.getElementById('formPasswordPolicy');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btnSalvar = document.getElementById('btnSalvar');
        btnSalvar.disabled = true;
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            btnSalvar.disabled = false;
            if (data.success) {
                location.reload();
            } else {
                document.getElementById('alert-area').innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
            }
        })
        .catch(() => {
            btnSalvar.disabled = false;
            document.getElementById('alert-area').innerHTML = `<div class='alert alert-danger'>Erro ao salvar. Tente novamente.</div>`;
        });
    });
});
</script> 