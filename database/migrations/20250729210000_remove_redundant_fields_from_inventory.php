<?php

use Phinx\Migration\AbstractMigration;

class RemoveRedundantFieldsFromInventory extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lgpd_inventory');

        // Remover campos redundantes que agora são gerenciados na tabela de relacionamento
        $table->removeColumn('data_category');
        $table->removeColumn('risk_level');

        $table->update();
    }
} 