<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável em buscar e manipular freqwuencias de pagametno no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar freqwuencias de pagametno no banco de dados.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 * @author Rafael Mendes
 */
class FrequencyRepository extends DbConnection
{

    /**
     * Recuperar todos os freqwuencias de pagametno com paginação.
     *
     * Este método retorna uma lista de freqwuencias de pagametno da tabela `adms_frequency`, com suporte à paginação.
     *
     * @param int $page Número da página para recuperação de freqwuencias de pagametno (começa do 1).
     * @param int $limitResult Número máximo de resultados por página.
     * @return array Lista de freqwuencias de pagametno recuperados do banco de dados.
     */
    public function getAllFrequencies(int $page = 1, int $limitResult = 10): array
    {
        // Calcular o registro inicial, função max para garantir valor mínimo 0
        $offset = max(0, ($page - 1) * $limitResult);

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name, days
                FROM adms_frequency               
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
     * Recuperar a quantidade total de freqwuencias de pagametno para paginação.
     *
     * Este método retorna a quantidade total de freqwuencias de pagametno na tabela `adms_frequency`, útil para a paginação.
     *
     * @return int Quantidade total de freqwuencias de pagametno encontrados no banco de dados.
     */
    public function getAmountFrequencies(): int
    {
        // QUERY para recuperar a quantidade de registros
        $sql = 'SELECT COUNT(id) as amount_records
                FROM adms_frequency';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    /**
     * Recuperar um Frequência específico pelo ID.
     *
     * Este método retorna os detalhes de um Frequência específico identificado pelo ID.
     *
     * @param int $id ID do Frequência a ser recuperado.
     * @return array|bool Detalhes do Frequência recuperado ou `false` se não encontrado.
     */
    public function getFrequency(int $id): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT id, name, days, created_at, updated_at
                FROM adms_frequency
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
     * Cadastrar um novo Frequência
     *
     * Este método insere um novo Frequência na tabela `adms_frequency`. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados do Frequência a ser cadastrado, incluindo `name`.
     * @return bool|int `true` se o Frequência foi criado com sucesso ou `false` em caso de erro.
     */
    public function createFrequency(array $data): bool|int
    {
        try {

            // QUERY para cadastrar Frequência
            $sql = 'INSERT INTO adms_frequency (name, days, created_at) VALUES (:name, :days, :created_at)';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':days', $data['days'], PDO::PARAM_STR);
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            // Executar a QUERY
            $stmt->execute();

            // Retornar o ID do departamento recém cadastrado
            return $this->getConnection()->lastInsertId();

        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Frequência não cadastrada.", ['name' => $data['name'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualizar os dados de um Frequência existente.
     *
     * Este método atualiza as informações de um Frequência existente. Se a senha for fornecida, ela também será atualizada.
     * Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do Frequência, incluindo `id`, `name`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updateFrequency(array $data): bool
    {
        try {
            // QUERY para atualizar Frequência
            $sql = 'UPDATE adms_frequency SET name = :name, days = :days, updated_at = :updated_at';

            // Condição para indicar qual registro editar
            $sql .= ' WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':days', $data['days'], PDO::PARAM_STR);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Frequência não editada.", ['id' => $data['id'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Deletar um Frequência pelo ID.
     *
     * Este método remove um Frequência específico da tabela `adms_frequency`. Em caso de erro, um log é gerado.
     *
     * @param int $id ID do Frequência a ser deletado.
     * @return bool `true` se o Frequência foi deletado com sucesso ou `false` em caso de erro.
     */
    public function deleteFrequency(int $id): bool
    {
        try {
            // QUERY para deletar Frequência
            $sql = 'DELETE FROM adms_frequency WHERE id = :id LIMIT 1';

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
                GenerateLog::generateLog("error", "Frequência não apagada.", ['id' => $id]);

                return false;
            }
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Frequência não apagada.", ['id' => $id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function getAllFrequencySelect(): array
    {
        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name, days
                FROM adms_frequency                
                ORDER BY id ASC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);


        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
