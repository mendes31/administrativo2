<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável por buscar e manipular grupos no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar grupos no banco de dados.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 * @author Rafael Mendes
 */
class GroupsPagesRepository extends DbConnection
{

    /**
     * Recuperar todos os grupos com paginação.
     *
     * Este método retorna uma lista de grupos da tabela `adms_groups_pages`, com suporte à paginação.
     *
     * @param int $page Número da página para recuperação de grupos (começa do 1).
     * @param int $limitResult Número máximo de resultados por página.
     * @return array Lista de grupos recuperados do banco de dados.
     */
    public function getAllGroupsPages(int $page = 1, int $limitResult = 10): array
    {
        // Calcular o registro inicial, função max para garantir valor mínimo 0
        $offset = max(0, ($page - 1) * $limitResult);

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name 
                FROM adms_groups_pages                
                ORDER BY name ASC
                LIMIT :limit OFFSET :offset';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir os parâmetros da QUERY pelos valores
        $stmt->bindValue(':limit', $limitResult, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperar a quantidade total de grupos para paginação.
     *
     * Este método retorna a quantidade total de grupos na tabela `adms_groups_pages`, útil para a paginação.
     *
     * @return int Quantidade total de grupos encontrados no banco de dados.
     */
    public function getAmountGroupsPages(): int
    {
        // QUERY para recuperar a quantidade de registros
        $sql = 'SELECT COUNT(id) as amount_records
                FROM adms_groups_pages';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    /**
     * Recuperar um grupo específico pelo ID.
     *
     * Este método retorna os detalhes de um grupo específico identificado pelo ID.
     *
     * @param int $id ID do grupo a ser recuperado.
     * @return array|bool Detalhes do grupo recuperado ou `false` se não encontrado.
     */
    public function getGroupPage(int $id): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT id, name, obs, created_at, updated_at
                FROM adms_groups_pages
                WHERE id = :id
                ORDER BY id DESC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cadastrar um novo grupo.
     *
     * Este método insere um novo grupo na tabela `adms_groups_pages`. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados do grupo a ser cadastrado, incluindo `name` e `obs`.
     * @return bool|int `true` se o grupo foi criado com sucesso ou `false` em caso de erro. Retorna o ID do grupo criado em caso de sucesso.
     */
    public function createGroupPage(array $data): bool|int
    {
        try {            

            // QUERY para cadastrar grupo
            $sql = 'INSERT INTO adms_groups_pages (name, obs, created_at) VALUES (:name, :obs, :created_at)';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':obs', $data['obs'], PDO::PARAM_STR);
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            // Executar a QUERY
            $stmt->execute();

            // Retornar o ID do grupo recém-cadastrado
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Grupo não cadastrado.", ['name' => $data['name'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualizar os dados de um grupo existente.
     *
     * Este método atualiza as informações de um grupo existente. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do grupo, incluindo `id`, `name`, e `obs`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updateGroupPage(array $data): bool
    {
        try {
            // QUERY para atualizar grupo
            $sql = 'UPDATE adms_groups_pages SET name = :name, obs = :obs, updated_at = :updated_at';

            // Condição para indicar qual registro editar
            $sql .= ' WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores 
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':obs', $data['obs'], PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Grupo não editado.", ['id' => $data['id'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Deletar um grupo pelo ID.
     *
     * Este método remove um grupo específico da tabela `adms_groups_pages`. Em caso de erro, um log é gerado.
     *
     * @param int $id ID do grupo a ser deletado.
     * @return bool `true` se o grupo foi deletado com sucesso ou `false` em caso de erro.
     */
    public function deleteGroupPage(int $id): bool
    {
        try {
            // QUERY para deletar grupo
            $sql = 'DELETE FROM adms_groups_pages WHERE id = :id LIMIT 1';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            // Executar a QUERY
            $stmt->execute();

            // Verificar o número de linhas afetadas
            $affectedRows = $stmt->rowCount();

            if ($affectedRows > 0) {
                return true;
            } else {
                // Gerar log de erro
                GenerateLog::generateLog("error", "Grupo não apagado.", ['id' => $id]);

                return false;
            }
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Grupo não apagado.", ['id' => $id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function getAllGroupsPagesSelect(): array
    {

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name 
                FROM adms_groups_pages                
                ORDER BY name ASC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);


        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
