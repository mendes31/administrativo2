<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável em buscar e manipular cargos no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar cargos no banco de dados.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 * @author Rafael Mendes
 */
class PositionsRepository extends DbConnection
{

    /**
     * Recuperar todos os cargos com paginação.
     *
     * Este método retorna uma lista de cargos da tabela `adms_positions`, com suporte à paginação.
     *
     * @param int $page Número da página para recuperação de cargos (começa do 1).
     * @param int $limitResult Número máximo de resultados por página.
     * @return array Lista de cargos recuperados do banco de dados.
     */
    public function getAllPositions(int $page = 1, int $limitResult = 10): array
    {
        // Calcular o registro inicial, função max para garantir valor mínimo 0
        $offset = max(0, ($page - 1) * $limitResult);

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name
                FROM adms_positions               
                ORDER BY id ASC
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
     * Recuperar a quantidade total de cargos para paginação.
     *
     * Este método retorna a quantidade total de cargos na tabela `adms_positions`, útil para a paginação.
     *
     * @return int Quantidade total de cargos encontrados no banco de dados.
     */
    public function getAmountPositions(): int
    {
        // QUERY para recuperar a quantidade de registros
        $sql = 'SELECT COUNT(id) as amount_records
                FROM adms_positions';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    /**
     * Recuperar um cargo específico pelo ID.
     *
     * Este método retorna os detalhes de um cargo específico identificado pelo ID.
     *
     * @param int $id ID do cargo a ser recuperado.
     * @return array|bool Detalhes do cargo recuperado ou `false` se não encontrado.
     */
    public function getPosition(int $id): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT id, name, created_at, updated_at
                FROM adms_positions
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
     * Cadastrar um novo cargo
     *
     * Este método insere um novo cargo na tabela `adms_positions`. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados do cargo a ser cadastrado, incluindo `name`.
     * @return bool|int `true` se o cargo foi criado com sucesso ou `false` em caso de erro.
     */
    public function createPosition(array $data): bool|int
    {
        try {

            // QUERY para cadastrar cargo
            $sql = 'INSERT INTO adms_positions (name, created_at) VALUES (:name, :created_at)';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            // Executar a QUERY
            $stmt->execute();

            // Retornar o ID do cargo recém cadastrado
            return $this->getConnection()->lastInsertId();

        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Cargo não cadastrado.", ['name' => $data['name'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualizar os dados de um cargo existente.
     *
     * Este método atualiza as informações de um cargo existente. Se a senha for fornecida, ela também será atualizada.
     * Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do cargo, incluindo `id`, `name`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updatePosition(array $data): bool
    {
        try {
            // QUERY para atualizar cargo
            $sql = 'UPDATE adms_positions SET name = :name, updated_at = :updated_at';

            // Condição para indicar qual registro editar
            $sql .= ' WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Cargo não editado.", ['id' => $data['id'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Deletar um Cargo pelo ID.
     *
     * Este método remove um cargo específico da tabela `adms_positions`. Em caso de erro, um log é gerado.
     *
     * @param int $id ID do Cargo a ser deletado.
     * @return bool `true` se o Cargo foi deletado com sucesso ou `false` em caso de erro.
     */
    public function deletePosition(int $id): bool
    {
        try {
            // QUERY para deletar cargo
            $sql = 'DELETE FROM adms_positions WHERE id = :id LIMIT 1';

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
                GenerateLog::generateLog("error", "Cargo não apagado.", ['id' => $id]);

                return false;
            }
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Cargo não apagado.", ['id' => $id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function getAllPositionsSelect(): array
    {
        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name 
                FROM adms_positions                
                ORDER BY name ASC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);


        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
