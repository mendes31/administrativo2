<?php
// ... existing code ...
?>
<div class="container mt-4">
    <h2>Vincular Cargos ao Treinamento</h2>
    <h4><?= htmlspecialchars($this->data['training']['nome'] ?? $this->data['training']['name'] ?? '') ?></h4>
    <form method="post" action="">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width:80px;" class="text-center">Obrigatório</th>
                    <th style="width:60px;" class="text-center">ID</th>
                    <th>Cargo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->data['positions'] as $position): ?>
                    <tr>
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center m-0">
                                <input class="form-check-input" type="checkbox" name="obrigatorio[<?= $position['id'] ?>]" value="1"
                                    id="pos<?= $position['id'] ?>" <?= in_array($position['id'], $this->data['linkedPositions'] ?? []) ? 'checked' : '' ?> >
                            </div>
                        </td>
                        <td class="text-center"><?= $position['id'] ?></td>
                        <td><?= htmlspecialchars($position['name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="<?= $_ENV['URL_ADM'] . 'list-trainings' ?>" class="btn btn-secondary">Voltar</a>

        
    </form>
</div> 