<?php

use Phinx\Migration\AbstractMigration;

class AddFinalidadeRelatedToDataMapping extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lgpd_data_mapping');
        $table->addColumn('finalidade_relacionada', 'string', ['limit' => 255, 'null' => true, 'after' => 'ropa_atividade'])
              ->addColumn('prazo_retencao_relacionado', 'string', ['limit' => 100, 'null' => true, 'after' => 'finalidade_relacionada'])
              ->update();
    }
} 