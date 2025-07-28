<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use PDO;
use Exception;

/**
 * Repository responsável por buscar e manipular registros ROPA no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar registros ROPA.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 */
class LgpdRopaRepository extends DbConnection
{
    /**
     * Recuperar todos os registros ROPA com paginação e filtros.
     *
     * @param array $filters Filtros opcionais (departamento_id, status, atividade)
     * @param int $page Página atual
     * @param int $limit Quantidade de registros por página
     * @return array Lista de registros ROPA
     */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $offset = max(0, ($page - 1) * $limit);
        $sql = 'SELECT r.*, d.name as departamento_nome FROM lgpd_ropa r INNER JOIN adms_departments d ON r.departamento_id = d.id WHERE 1=1';
        $params = [];
        if (!empty($filters['departamento_id'])) {
            $sql .= ' AND r.departamento_id = :departamento_id';
            $params[':departamento_id'] = $filters['departamento_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND r.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['atividade'])) {
            $sql .= ' AND r.atividade LIKE :atividade';
            $params[':atividade'] = '%' . $filters['atividade'] . '%';
        }
        $sql .= ' ORDER BY r.id DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Garantir que os campos existam em todos os registros
        foreach ($results as &$result) {
            $result['medidas_seguranca'] = $result['medidas_seguranca'] ?? null;
            $result['observacoes'] = $result['observacoes'] ?? null;
        }
        
        return $results;
    }

    /**
     * Recuperar a quantidade total de registros ROPA para paginação.
     *
     * @param array $filters Filtros opcionais
     * @return int Quantidade total de registros encontrados
     */
    public function getAmountRopa(array $filters = []): int
    {
        $sql = 'SELECT COUNT(r.id) as amount_records FROM lgpd_ropa r INNER JOIN adms_departments d ON r.departamento_id = d.id WHERE 1=1';
        $params = [];
        if (!empty($filters['departamento_id'])) {
            $sql .= ' AND r.departamento_id = :departamento_id';
            $params[':departamento_id'] = $filters['departamento_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND r.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['atividade'])) {
            $sql .= ' AND r.atividade LIKE :atividade';
            $params[':atividade'] = '%' . $filters['atividade'] . '%';
        }
        $stmt = $this->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    /**
     * Recuperar um registro ROPA específico pelo ID.
     *
     * @param int $id ID do registro
     * @return array|bool Registro encontrado ou false
     */
    public function getById(int $id): array|bool
    {
        $sql = 'SELECT r.*, d.name as departamento_nome FROM lgpd_ropa r INNER JOIN adms_departments d ON r.departamento_id = d.id WHERE r.id = :id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Garantir que os campos existam mesmo que não estejam na tabela
            $result['medidas_seguranca'] = $result['medidas_seguranca'] ?? null;
            $result['observacoes'] = $result['observacoes'] ?? null;
        }
        
        return $result;
    }

    /**
     * Cadastrar um novo registro ROPA.
     *
     * @param array $data Dados do registro
     * @return int|bool ID do registro criado ou false
     */
    public function create(array $data): int|bool
    {
        try {
            $sql = 'INSERT INTO lgpd_ropa (codigo, atividade, departamento_id, base_legal, retencao, riscos, medidas_seguranca, observacoes, status, ultima_atualizacao, created_at) VALUES (:codigo, :atividade, :departamento_id, :base_legal, :retencao, :riscos, :medidas_seguranca, :observacoes, :status, :ultima_atualizacao, NOW())';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':codigo', $data['codigo']);
            $stmt->bindValue(':atividade', $data['atividade']);
            $stmt->bindValue(':departamento_id', $data['departamento_id'], PDO::PARAM_INT);
            $stmt->bindValue(':base_legal', $data['base_legal']);
            $stmt->bindValue(':retencao', $data['retencao']);
            $stmt->bindValue(':riscos', $data['riscos']);
            $stmt->bindValue(':medidas_seguranca', $data['medidas_seguranca'] ?? null);
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? null);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':ultima_atualizacao', $data['ultima_atualizacao']);
            $stmt->execute();
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Registro ROPA não cadastrado.", ['codigo' => $data['codigo'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Atualizar um registro ROPA existente.
     *
     * @param array $data Dados atualizados, incluindo o ID
     * @return bool true se atualizado, false se erro
     */
    public function update(array $data): bool
    {
        try {
            $sql = 'UPDATE lgpd_ropa SET codigo = :codigo, atividade = :atividade, departamento_id = :departamento_id, base_legal = :base_legal, retencao = :retencao, riscos = :riscos, medidas_seguranca = :medidas_seguranca, observacoes = :observacoes, status = :status, ultima_atualizacao = :ultima_atualizacao, updated_at = NOW() WHERE id = :id';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':codigo', $data['codigo']);
            $stmt->bindValue(':atividade', $data['atividade']);
            $stmt->bindValue(':departamento_id', $data['departamento_id'], PDO::PARAM_INT);
            $stmt->bindValue(':base_legal', $data['base_legal']);
            $stmt->bindValue(':retencao', $data['retencao']);
            $stmt->bindValue(':riscos', $data['riscos']);
            $stmt->bindValue(':medidas_seguranca', $data['medidas_seguranca'] ?? null);
            $stmt->bindValue(':observacoes', $data['observacoes'] ?? null);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':ultima_atualizacao', $data['ultima_atualizacao']);
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Registro ROPA não editado.", ['id' => $data['id'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Deletar um registro ROPA pelo ID.
     *
     * @param int $id ID do registro
     * @return bool true se deletado, false se erro
     */
    public function delete(int $id): bool
    {
        try {
            $sql = 'DELETE FROM lgpd_ropa WHERE id = :id';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Registro ROPA não apagado.", ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }
} 