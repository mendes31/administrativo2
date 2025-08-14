<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use Generator;
use PDO;

/**
 * Repositório responsável pelas operações relacionadas às páginas associadas aos níveis de acesso.
 *
 * Esta classe gerencia a recuperação e inserção de páginas associadas a níveis de acesso no banco de dados.
 * Ela oferece métodos para obter as páginas vinculadas a um determinado nível de acesso e para realizar 
 * a inserção em massa de novas associações entre níveis de acesso e páginas.
 *
 * @package App\adms\Models\Repository
 */
class AccessLevelsPagesRepository extends DbConnection
{
    /**
     * Recupera as páginas associadas a um nível de acesso.
     *
     * Este método realiza uma consulta no banco de dados para obter todas as páginas associadas a um 
     * nível de acesso específico, retornando um array com os IDs das páginas.
     *
     * @param int $accessLevel ID do nível de acesso.
     * @return array|bool Retorna um array com os IDs das páginas ou `false` se não houver resultados.
     */
    public function getPagesAccessLevelsArray(int $accessLevel, bool $permission = false): array|bool
    {
        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT adms_page_id
                FROM adms_access_levels_pages
                WHERE adms_access_level_id = :adms_access_level_id';

        // Acessa o if quando retornar somente as paginas que tiverem permissão 1
        if ($permission) {
            $sql .= " AND permission = 1";
        }

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir os parâmetros da QUERY pelos valores
        $stmt->bindValue(':adms_access_level_id', $accessLevel, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retornar array indexado para facilitar verificação de permissões
        if ($result) {
            if ($permission) {
                // Para verificação de permissões, retornar array indexado
                $indexedArray = [];
                foreach ($result as $row) {
                    $indexedArray[$row['adms_page_id']] = true;
                }
                return $indexedArray;
            } else {
                // Para operações de atualização, retornar array simples
                return array_column($result, 'adms_page_id');
            }
        }
        
        return [];
    }

    /**
     * Insere em massa as páginas associadas a um nível de acesso.
     *
     * Este método insere em lote as permissões de acesso às páginas para diferentes níveis de acesso no banco de dados.
     * Ele utiliza uma transação SQL para garantir a integridade das operações e gera logs para monitoramento.
     *
     * @param array $data Dados contendo as páginas a serem associadas a cada nível de acesso.
     * @return bool Retorna `true` se a operação foi bem-sucedida, ou `false` em caso de erro.
     */
    public function createPagesAccessLevel(array $data): bool
    {
        try {
            // Marca o ponto inicial de uma transação SQL
            $this->getConnection()->beginTransaction();

            // Array para armazenar ID do nível de acesso para salvar no log
            $accessLevelArrayId = [];

            // Percorrer o array com nível de acesso e páginas
            foreach ($data as $accessLevelId => $accessLevelPages) {

                // Array para acumular os valores
                $values = [];
                $placeholders = [];

                // Percorrer o array de páginas que o nível de acesso não tem permissão de acessar
                foreach ($accessLevelPages as $pageId) {
                    $values[] = $accessLevelId == 1 ? 1 : 0;
                    $values[] = $accessLevelId;
                    $values[] = $pageId;
                    $values[] = date("Y-m-d H:i:s");
                    $placeholders[] = "(?, ?, ?, ?)";
                }

                // Criar QUERY somente se o nível de acesso não tem página cadastrada
                if ($accessLevelPages ?? false) {

                    // QUERY para cadastrar em massa as páginas para o nível de acesso
                    $sql = "INSERT INTO adms_access_levels_pages (permission, adms_access_level_id, adms_page_id, created_at) VALUES " . implode(", ", $placeholders);

                    // Preparar a QUERY
                    $stmt = $this->getConnection()->prepare($sql);

                    // Executar a QUERY
                    $stmt->execute($values);

                    // Criar o array com ID do nível de acesso para salvar no log
                    $accessLevelArrayId[] = $accessLevelId;
                }
            }

            // Gerar log de sucesso
            GenerateLog::generateLog("info", "Páginas cadastradas para o nível de acesso.", ['adms_access_level_id' => $accessLevelArrayId]);

            // Acessa somente o commit se cadastrou alguma página para o nível de acesso
            if ($accessLevelArrayId ?? false) {
                // Operação SQL concluída com êxito
                $this->getConnection()->commit();
            }

            return true;
        } catch (Exception $e) {

            // Operação SQL não é concluída com êxito
            $this->getConnection()->rollBack();

            // Gerar log de erro
            GenerateLog::generateLog("error", "Páginas não cadastradas para o nível de acesso.", ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualiza as permissões de páginas associadas a um nível de acesso.
     *
     * COMPORTAMENTO CORRIGIDO:
     * - ADICIONA novas permissões marcadas no formulário
     * - ATUALIZA permissões existentes que estavam bloqueadas
     * - MANTÉM permissões existentes que não foram alteradas
     * - NÃO remove permissões automaticamente (apenas adiciona/atualiza)
     *
     * @param array $data Dados contendo as permissões de páginas a serem atualizadas.
     * @return bool Retorna `true` se a operação foi bem-sucedida, ou `false` em caso de erro.
     */
    public function updateAccessLevelPages(array $data): bool
    {
        // Log de debug
        error_log('updateAccessLevelPages chamado com dados: ' . json_encode($data));

        if ($data['adms_access_level_id'] == 1) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Permissão para o Super Administrador não pode ser editada.", ['id' => $data['adms_access_level_id']]);

            $_SESSION['error'] = "Permissão para o Super Administrador não pode ser editada!";

            return false;
        }

        try {

            // Marca o ponto inicial de uma transação SQL
            $this->getConnection()->beginTransaction();

            // Criar o elemento accessLevelPage no array quando não vem nível de acesso do formulário
            $data['accessLevelPage'] = $data['accessLevelPage'] ?? [];
            
            // Garantir que accessLevelPage seja sempre um array
            if (!is_array($data['accessLevelPage'])) {
                error_log('accessLevelPage não é um array, convertendo...');
                if (is_string($data['accessLevelPage'])) {
                    // Se for string, tentar decodificar JSON
                    $decoded = json_decode($data['accessLevelPage'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data['accessLevelPage'] = $decoded;
                    } else {
                        // Se não for JSON válido, tentar separar por vírgulas
                        $data['accessLevelPage'] = array_filter(array_map('trim', explode(',', $data['accessLevelPage'])));
                        error_log('String convertida para array por vírgulas: ' . json_encode($data['accessLevelPage']));
                    }
                } else {
                    // Se for outro tipo, criar array vazio
                    $data['accessLevelPage'] = [];
                }
            }
            
            // Verificar se ainda não é um array válido
            if (!is_array($data['accessLevelPage']) || empty($data['accessLevelPage'])) {
                error_log('accessLevelPage ainda não é um array válido após conversão');
                $data['accessLevelPage'] = [];
            }
            
            // Log de debug
            error_log('accessLevelPage após processamento: ' . json_encode($data['accessLevelPage']));
            error_log('Tipo final de accessLevelPage: ' . gettype($data['accessLevelPage']));
            error_log('É array? ' . (is_array($data['accessLevelPage']) ? 'Sim' : 'Não'));
            error_log('Tamanho: ' . count($data['accessLevelPage']));
            error_log('Primeiros 5 elementos: ' . json_encode(array_slice($data['accessLevelPage'], 0, 5)));

            // Recuperar todas as páginas cadastradas para o nível de acesso
            $resultAccessLevelsPages = $this->getPagesAccessLevelsArray((int) $data['adms_access_level_id']);
            $resultAccessLevelsPages = $resultAccessLevelsPages ? $resultAccessLevelsPages : [];
            
            // Log de debug
            error_log('Páginas existentes no BD: ' . json_encode($resultAccessLevelsPages));

            // Recuperar as páginas que nível de acesso tem permissão de acessar
            $resultAccessLevelsPagesPermissions = $this->getPagesAccessLevelsArray((int) $data['adms_access_level_id'], true);
            $resultAccessLevelsPagesPermissions = $resultAccessLevelsPagesPermissions ? $resultAccessLevelsPagesPermissions : [];
            
            // Log de debug
            error_log('Páginas com permissão no BD: ' . json_encode($resultAccessLevelsPagesPermissions));

            // Percorrer o array com as páginas que devem ser liberadas para o usuário
            foreach ($data['accessLevelPage'] as $accessLevelPage) {
                // Validar se o ID da página é válido
                if (empty($accessLevelPage) || !is_numeric($accessLevelPage)) {
                    error_log('ID de página inválido ignorado: ' . $accessLevelPage);
                    continue;
                }
                
                // Converter para inteiro
                $pageId = (int) $accessLevelPage;
                error_log('Processando página ID: ' . $pageId);

                // Verificar se a página não está cadastrada para o nível de acesso
                if (!in_array($pageId, $resultAccessLevelsPages)) {

                    // QUERY para cadastrar página para o nível de acesso
                    $sql = 'INSERT INTO adms_access_levels_pages (permission, adms_access_level_id, adms_page_id, created_at) VALUES (:permission, :adms_access_level_id, :adms_page_id, :created_at)';

                    // Preparar a QUERY
                    $stmt = $this->getConnection()->prepare($sql);

                    // Substituir os parâmetros da QUERY pelos valores
                    $stmt->bindValue(':permission', 1, PDO::PARAM_INT);
                    $stmt->bindValue(':adms_access_level_id', $data['adms_access_level_id'], PDO::PARAM_INT);
                    $stmt->bindValue(':adms_page_id', $pageId, PDO::PARAM_INT);
                    $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

                    // Executar a QUERY
                    $stmt->execute();
                    
                    // Log de debug
                    error_log('Página inserida: ' . $pageId);
                } elseif (!in_array($pageId, $resultAccessLevelsPagesPermissions)) { // Verificar se a página está cadastrada no BD, mas não está liberada o nível de acesso

                    // QUERY para atualizar página para o nível de acesso
                    $sql = 'UPDATE adms_access_levels_pages 
                            SET permission = :permission, updated_at = :updated_at
                            WHERE adms_access_level_id = :adms_access_level_id 
                            AND adms_page_id = :adms_page_id';

                    // Preparar a QUERY
                    $stmt = $this->getConnection()->prepare($sql);

                    // Substituir os parâmetros da QUERY pelos valores 
                    $stmt->bindValue(':permission', 1, PDO::PARAM_INT);
                    $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
                    $stmt->bindValue(':adms_access_level_id', $data['adms_access_level_id'], PDO::PARAM_INT);
                    $stmt->bindValue(':adms_page_id', $pageId, PDO::PARAM_INT);

                    // Executar a QUERY
                    $stmt->execute();
                    
                    // Log de debug
                    error_log('Página atualizada: ' . $pageId);

                    // Usar array_diff para remover o valor
                    $resultAccessLevelsPagesPermissions = array_diff($resultAccessLevelsPagesPermissions, [$pageId]);
                } elseif (in_array($pageId, $resultAccessLevelsPagesPermissions)) { // Verificar se no banco de dados o registro indica que o usuário tem permissão de acessar a página, no formulário mantém a permissão de acesso a página

                    // Usar array_diff para remover o valor
                    $resultAccessLevelsPagesPermissions = array_diff($resultAccessLevelsPagesPermissions, [$pageId]);
                    
                    // Log de debug
                    error_log('Página mantida: ' . $pageId);
                }
            }

            // IMPORTANTE: NÃO remover permissões existentes automaticamente
            // O sistema deve apenas ADICIONAR novas permissões, não revogar as existentes
            // Se uma permissão não está no formulário, ela deve ser mantida como está
            
            if ($resultAccessLevelsPagesPermissions) {
                error_log('ATENÇÃO: ' . count($resultAccessLevelsPagesPermissions) . ' permissões existentes NÃO serão removidas automaticamente');
                error_log('Páginas que permanecerão com permissão: ' . json_encode($resultAccessLevelsPagesPermissions));
                
                // Log das permissões que serão mantidas
                foreach ($resultAccessLevelsPagesPermissions as $pageId) {
                    error_log('Permissão MANTIDA para página: ' . $pageId);
                }
            }

            // Operação SQL concluída com êxito
            $this->getConnection()->commit();
            
            // Log de debug
            error_log('updateAccessLevelPages concluído com sucesso');
            error_log('Total de páginas processadas: ' . count($data['accessLevelPage']));
            error_log('Permissões salvas para o nível de acesso: ' . $data['adms_access_level_id']);
            
            // Verificar se as permissões foram realmente salvas
            $verificacao = $this->getPagesAccessLevelsArray((int) $data['adms_access_level_id'], true);
            error_log('Verificação após salvamento - Páginas com permissão: ' . json_encode($verificacao));
            error_log('Total de páginas com permissão após salvamento: ' . count($verificacao));

            return true;
        } catch (Exception $e) {

            // Operação SQL não é concluída com êxito
            $this->getConnection()->rollBack();

            // Gerar log de erro
            GenerateLog::generateLog("error", "Permissão de acesso à página pelo nível de acesso não editada.", ['id' => $data['adms_access_level_id'], 'error' => $e->getMessage()]);
            
            // Log de debug
            error_log('Erro em updateAccessLevelPages: ' . $e->getMessage());

            return false;
        }
    }
}
