<?php
use Phinx\Migration\AbstractMigration;

class AddSecurityAndResponsibleToRopa extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lgpd_ropa');

        // Adicionar campo para medidas de segurança
        $table->addColumn('medidas_seguranca', 'text', [
            'null' => true,
            'comment' => 'Medidas de segurança específicas para esta atividade'
        ]);

        // Adicionar campo para responsável
        $table->addColumn('responsavel', 'string', [
            'limit' => 255,
            'null' => true,
            'comment' => 'Responsável pela atividade/ROPA'
        ]);

        // Adicionar campo para observações
        $table->addColumn('observacoes', 'text', [
            'null' => true,
            'comment' => 'Observações específicas da atividade'
        ]);

        $table->update();
    }
} 