<?php

use Phinx\Migration\AbstractMigration;

class CreateLgpdDataMappingFontes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lgpd_data_mapping_fontes', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false])
              ->addColumn('data_mapping_id', 'integer', ['null' => false, 'signed' => false])
              ->addColumn('fonte_coleta_id', 'integer', ['null' => false, 'signed' => false])
              ->addColumn('observacoes', 'text', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['data_mapping_id'], ['name' => 'idx_data_mapping_id'])
              ->addIndex(['fonte_coleta_id'], ['name' => 'idx_fonte_coleta_id'])
              ->create();
              
        // Adicionar foreign keys
        $this->execute('ALTER TABLE lgpd_data_mapping_fontes ADD CONSTRAINT fk_data_mapping_fontes_data_mapping FOREIGN KEY (data_mapping_id) REFERENCES lgpd_data_mapping(id) ON DELETE CASCADE');
        $this->execute('ALTER TABLE lgpd_data_mapping_fontes ADD CONSTRAINT fk_data_mapping_fontes_fonte_coleta FOREIGN KEY (fonte_coleta_id) REFERENCES lgpd_fontes_coleta(id) ON DELETE CASCADE');
    }
} 