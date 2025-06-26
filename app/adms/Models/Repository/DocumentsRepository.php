<?php

namespace App\adms\Models\Repository;

use App\adms\Helpers\GenerateLog;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável por buscar e manipular documentos no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar documentos no banco de dados.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 * @author Cesar <cesar@celke.com.br>
 */
class DocumentsRepository extends DbConnection
{   

    /**
     * Recuperar todos os documentos com paginação.
     *
     * Este método retorna uma lista de documentos da tabela `adms_documents`, com suporte à paginação.
     *
     * @param int $page Número da página para recuperação de documentos (começa do 1).
     * @param int $limitResult Número máximo de resultados por página.
     * @return array Lista de documentos recuperados do banco de dados.
     */
    public function getAllDocuments(int $page = 1, int $limitResult = 10): array
    {
        // Calcular o registro inicial, função max para garantir valor mínimo 0
        $offset = max(0, ($page - 1) * $limitResult);

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, cod_doc, name_doc, version, active 
                FROM adms_documents                
                ORDER BY name_doc ASC
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
     * Recuperar a quantidade total de documentos para paginação.
     *
     * Este método retorna a quantidade total de documentos na tabela `adms_documents`, útil para a paginação.
     *
     * @return int Quantidade total de páginas encontradas no banco de dados.
     */
    public function getAmountDocuments(): int
    {
        // QUERY para recuperar a quantidade de registros
        $sql = 'SELECT COUNT(id) as amount_records
                FROM adms_documents
                WHERE active = :active';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':active', 1, PDO::PARAM_INT);
        $stmt->execute();

        return (int) ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records'] ?? 0);
    }

    /**
     * Recuperar um documento específico pelo ID.
     *
     * @param int $id ID do documento a ser recuperado.
     * @return array|bool Detalhes do documento recuperado ou `false` se não encontrado.
     */
    public function getDocument(int $id): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT id, cod_doc, name_doc, version, active, created_at, updated_at
                FROM adms_documents
                WHERE id = :id
                ORDER BY name_doc ASC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cadastrar um novo documento.
     *
     * Este método insere um novo documento na tabela `adms_documents`. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados do documento a ser cadastrado, incluindo `cod_doc`, `name_doc`, `version`, `active`.
     * @return bool|int `true` se o documento foi criado com sucesso ou `false` em caso de erro.
     */
    public function createDocument(array $data): bool|int
    {
        try {            

            // QUERY para cadastrar documento
            $sql = 'INSERT INTO adms_documents (cod_doc, name_doc, version, active, created_at) VALUES (:cod_doc, :name_doc, :version, :active, :created_at)';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores
            $stmt->bindValue(':cod_doc', $data['cod_doc'], PDO::PARAM_STR);
            $stmt->bindValue(':name_doc', $data['name_doc'], PDO::PARAM_STR);   
            $stmt->bindValue(':version', $data['version'], PDO::PARAM_STR);
            $stmt->bindValue(':active', $data['active'], PDO::PARAM_BOOL);
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            // Executar a QUERY
            $stmt->execute();

            // Retornar o ID do documento recém-cadastrado
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Documento não cadastrado.", ['cod_doc' => $data['cod_doc'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Atualizar os dados de um documento existente.
     *
     * Este método atualiza as informações de um documento existente. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do documento, incluindo `id`, `cod_doc`, `name_doc`, `version`, `active`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updateDocument(array $data): bool
    {
        try {
            // QUERY para atualizar documento
            $sql = 'UPDATE adms_documents SET 
                    cod_doc = :cod_doc, 
                    name_doc = :name_doc, 
                    version = :version, 
                    active = :active, 
                    updated_at = :updated_at';

            // Condição para indicar qual registro editar
            $sql .= ' WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os parâmetros da QUERY pelos valores 
            $stmt->bindValue(':cod_doc', $data['cod_doc'], PDO::PARAM_STR);
            $stmt->bindValue(':name_doc', $data['name_doc'], PDO::PARAM_STR);
            $stmt->bindValue(':version', $data['version'], PDO::PARAM_STR);
            $stmt->bindValue(':active', $data['active'], PDO::PARAM_BOOL);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Executar a QUERY
            return $stmt->execute();
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Documento não editado.", ['id' => $data['id'], 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Deletar um documento pelo ID.
     *
     * Este método remove um documento específico da tabela `adms_documents`. Em caso de erro, um log é gerado.
     *
     * @param int $id ID do documento a ser deletado.
     * @return bool `true` se o documento foi deletado com sucesso ou `false` em caso de erro.
     */
        public function deleteDocument(int $id): bool
    {
        try {
           
            $sql = 'DELETE FROM adms_documents WHERE id = :id LIMIT 1';

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
                GenerateLog::generateLog("error", "Documento não apagado.", ['id' => $id]);

                return false;
            }
        } catch (Exception $e) {
            // Gerar log de erro
            GenerateLog::generateLog("error", "Documento não apagado.", ['id' => $id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function getDocumentsArray(): array|bool
    {

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id
                FROM adms_documents';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retornar apenas os valores de 'id' como array simples
        return $result ? array_column($result, 'id') : false;
    }

    public function getAllDocumentsFull(): array
    {

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, cod_doc, name_doc, version, active
                FROM adms_documents
                ORDER BY name_doc ASC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
