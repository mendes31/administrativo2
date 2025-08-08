<?php

namespace App\adms\Models\Repository;

use App\adms\Controllers\Services\Validation\ValidationEmptyField;
use App\adms\Helpers\GenerateLog;
use App\adms\Helpers\SlugImg;
use App\adms\Helpers\Upload;
use App\adms\Helpers\ValExtImg;
use App\adms\Models\Services\DbConnection;
use Exception;
use PDO;

/**
 * Repository responsável em buscar e manipular usuários no banco de dados.
 *
 * Esta classe fornece métodos para recuperar, criar, atualizar e deletar usuários no banco de dados.
 * Ela estende a classe `DbConnection` para gerenciar conexões com o banco de dados e utiliza o `GenerateLog`
 * para registrar erros que ocorrem durante as operações.
 *
 * @package App\adms\Models\Repository
 * @return Rafael Mendes
 */
class UsersRepository extends DbConnection
{
    /** @var array|string|null $data Recebe os dados que devem ser enviados para a VIEW */
    private array|string|null $data = null;

    /** @var array|string|null $data Recebe o nome da imagem*/
    private array|string|null $nameImg = null;

    /** @var array|string|null $data Recebe o nome do diretório  */
    private array|string|null $directory = null;

    /** @var string $delImg Recebe o endereço da imagem que deve ser excluida */
    private string $delImg;

    /** @var array|string|null $data Recebe os dados que devem ser enviados para a VIEW */
    private array|string|null $dataImage = null;

    /**
     * Recuperar todos os usuários com paginação.
     *
     * Este método retorna uma lista de usuários da tabela `adms_users`, com suporte à paginação.
     *
     * @param int $page Número da página para recuperação de usuários (começa do 1).
     * @param int $limitResult Número máximo de resultados por página.
     * @param array $filtros Filtros para aplicar nas consultas.
     * @return array Lista de usuários recuperados do banco de dados.
     */
    public function getAllUsers(int $page = 1, int $limitResult = 10, array $filtros = [])
    {
        $offset = max(0, ($page - 1) * $limitResult);
        $where = [];
        $params = [];
        if (!empty($filtros['nome'])) {
            $where[] = 'usr.name LIKE :nome';
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }
        if (!empty($filtros['email'])) {
            $where[] = 'usr.email LIKE :email';
            $params[':email'] = '%' . $filtros['email'] . '%';
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = 'SELECT usr.id, usr.name, usr.email, usr.username, usr.user_department_id, usr.user_position_id, usr.status, usr.bloqueado, usr.tentativas_login, usr.senha_nunca_expira, usr.modificar_senha_proximo_logon, dep.name name_dep, pos.name name_pos
                FROM adms_users usr
                INNER JOIN adms_departments dep ON usr.user_department_id = dep.id
                INNER JOIN adms_positions pos ON usr.user_position_id = pos.id 
                ' . $whereSql . '
                ORDER BY usr.id DESC
                LIMIT :limit OFFSET :offset';
        $stmt = $this->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limitResult, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperar a quantidade total de usuários para paginação.
     *
     * Este método retorna a quantidade total de usuários na tabela `adms_users`, útil para a paginação.
     *
     * @param array $filtros Filtros para aplicar nas consultas.
     * @return int|bool Quantidade total de usuários encontrados no banco de dados ou `false` em caso de erro.
     */
    public function getAmountUsers(array $filtros = []): int|bool
    {
        $where = [];
        $params = [];
        if (!empty($filtros['nome'])) {
            $where[] = 'name LIKE :nome';
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }
        if (!empty($filtros['email'])) {
            $where[] = 'email LIKE :email';
            $params[':email'] = '%' . $filtros['email'] . '%';
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = 'SELECT COUNT(id) as amount_records FROM adms_users ' . $whereSql;
        $stmt = $this->getConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return ($stmt->fetch(PDO::FETCH_ASSOC)['amount_records']) ?? 0;
    }

    /**
     * Recuperar um usuário específico pelo ID.
     *
     * Este método retorna os detalhes de um usuário específico identificado pelo ID.
     *
     * @param int $id ID do usuário a ser recuperado.
     * @return array|bool Detalhes do usuário recuperado ou `false` se não encontrado.
     */
    public function getUser(int $id): array|bool
    {
        // QUERY para recuperar o registro selecionado do banco de dados
        $sql = 'SELECT 
                    t0.id, 
                    t0.name, 
                    t0.email, 
                    t0.username, 
                    t0.image, 
                    t0.data_nascimento, 
                    t0.user_department_id, 
                    t0.user_position_id, 
                    t0.created_at, 
                    t0.updated_at, 
                    t0.status,
                    t0.bloqueado,
                    t0.tentativas_login,
                    t0.senha_nunca_expira,
                    t0.modificar_senha_proximo_logon,
                    t1.name dep_name, 
                    t2.name pos_name
                FROM adms_users t0
                INNER JOIN adms_departments t1 ON t0.user_department_id = t1.id
                INNER JOIN adms_positions t2 ON t0.user_position_id = t2.id
                WHERE t0.id = :id
                ORDER BY t0.id DESC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir o link da QUERY pelo valor / Evita SQL INJECTION
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cadastrar um novo usuário.
     *
     * Este método insere um novo usuário na tabela `adms_users`. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados do usuário a ser cadastrado, incluindo `name`, `email`, `username`, `password`.
     * @return bool|int `true` se o usuário foi criado com sucesso ou `false` em caso de erro.
     */
    public function createUser(array $data): bool|int
    {
        try {
            $sql = 'INSERT INTO adms_users (
                name, email, username, user_department_id, user_position_id, password, status, bloqueado, tentativas_login, senha_nunca_expira, modificar_senha_proximo_logon, created_at, image, data_nascimento
            ) VALUES (
                :name, :email, :username, :user_department_id, :user_position_id, :password, :status, :bloqueado, :tentativas_login, :senha_nunca_expira, :modificar_senha_proximo_logon, :created_at, :image, :data_nascimento
            )';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindValue(':user_department_id', $data['user_department_id'], PDO::PARAM_INT);
            $stmt->bindValue(':user_position_id', $data['user_position_id'], PDO::PARAM_INT);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':status', $data['status'] ?? 'Ativo', PDO::PARAM_STR);
            $stmt->bindValue(':bloqueado', $data['bloqueado'] ?? 'Não', PDO::PARAM_STR);
            $stmt->bindValue(':tentativas_login', $data['tentativas_login'] ?? 0, PDO::PARAM_INT);
            $stmt->bindValue(':senha_nunca_expira', $data['senha_nunca_expira'] ?? 'Não', PDO::PARAM_STR);
            $stmt->bindValue(':modificar_senha_proximo_logon', $data['modificar_senha_proximo_logon'] ?? 'Não', PDO::PARAM_STR);
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':image', $data['image'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':data_nascimento', $data['data_nascimento'] ?? null, PDO::PARAM_STR);
            $stmt->execute();
            $novoId = $this->getConnection()->lastInsertId();
            // Log de inserção
            if ($novoId) {
                $dadosDepois = [
                    'id' => $novoId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'user_department_id' => $data['user_department_id'],
                    'user_position_id' => $data['user_position_id'],
                    'status' => $data['status'] ?? 'Ativo',
                    'bloqueado' => $data['bloqueado'] ?? 'Não',
                    'tentativas_login' => $data['tentativas_login'] ?? 0,
                    'senha_nunca_expira' => $data['senha_nunca_expira'] ?? 'Não',
                    'modificar_senha_proximo_logon' => $data['modificar_senha_proximo_logon'] ?? 'Não',
                ];
                \App\adms\Models\Services\LogAlteracaoService::registrarAlteracao(
                    'adms_users',
                    $novoId,
                    $_SESSION['user_id'] ?? 0,
                    'insert',
                    [],
                    $dadosDepois
                );
            }
            return $novoId;
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Usuário não cadastrado.", ['username' => $data['username'], 'email' => $data['email'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Atualizar os dados de um usuário existente.
     *
     * Este método atualiza as informações de um usuário existente. Se a senha for fornecida, ela também será atualizada.
     * Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do usuário, incluindo `id`, `name`, `email`, `username`, e opcionalmente `password`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updateUser(array $data): bool
    {
        try {
            // Captura os dados antigos antes da alteração
            $dadosAntes = $this->getUser($data['id']);

            // QUERY para atualizar o usuário
            $sql = 'UPDATE adms_users SET name = :name, email = :email, username = :username, user_department_id = :user_department_id, user_position_id = :user_position_id, updated_at = :updated_at';
            if (isset($data['status'])) {
                $sql .= ', status = :status';
            }
            if (isset($data['bloqueado'])) {
                $sql .= ', bloqueado = :bloqueado';
            }
            if (isset($data['senha_nunca_expira'])) {
                $sql .= ', senha_nunca_expira = :senha_nunca_expira';
            }
            if (isset($data['modificar_senha_proximo_logon'])) {
                $sql .= ', modificar_senha_proximo_logon = :modificar_senha_proximo_logon';
            }
            if (!empty($data['image'])) {
                $sql .= ', image = :image';
            }
            if (!empty($data['data_nascimento'])) {
                $sql .= ', data_nascimento = :data_nascimento';
            }
            if (isset($data['bloqueado']) && $data['bloqueado'] === 'Não' && isset($dadosAntes['bloqueado']) && $dadosAntes['bloqueado'] === 'Sim') {
                $sql .= ', tentativas_login = 0, data_bloqueio_temporario = NULL';
            }
            $sql .= ' WHERE id = :id';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindValue(':user_department_id', $data['user_department_id'], PDO::PARAM_INT);
            $stmt->bindValue(':user_position_id', $data['user_position_id'], PDO::PARAM_INT);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            if (isset($data['status'])) {
                $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);
            }
            if (isset($data['bloqueado'])) {
                $stmt->bindValue(':bloqueado', $data['bloqueado'], PDO::PARAM_STR);
            }
            if (isset($data['senha_nunca_expira'])) {
                $stmt->bindValue(':senha_nunca_expira', $data['senha_nunca_expira'], PDO::PARAM_STR);
            }
            if (isset($data['modificar_senha_proximo_logon'])) {
                $stmt->bindValue(':modificar_senha_proximo_logon', $data['modificar_senha_proximo_logon'], PDO::PARAM_STR);
            }
            if (!empty($data['image'])) {
                $stmt->bindValue(':image', $data['image'], PDO::PARAM_STR);
            }
            if (!empty($data['data_nascimento'])) {
                $stmt->bindValue(':data_nascimento', $data['data_nascimento'], PDO::PARAM_STR);
            }
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
            if (!empty($data['password'])) {
                $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            }
            $result = $stmt->execute();
            // Se atualização bem-sucedida, registra o log de alteração
            if ($result) {
                // Monta os dados depois da alteração (agora com todos os campos relevantes)
                $dadosDepois = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'username' => $data['username'],
                    'user_department_id' => $data['user_department_id'],
                    'user_position_id' => $data['user_position_id'],
                    'status' => $data['status'] ?? $dadosAntes['status'] ?? null,
                    'bloqueado' => $data['bloqueado'] ?? $dadosAntes['bloqueado'] ?? null,
                    'tentativas_login' => isset($data['tentativas_login']) ? $data['tentativas_login'] : ($dadosAntes['tentativas_login'] ?? null),
                    'senha_nunca_expira' => $data['senha_nunca_expira'] ?? $dadosAntes['senha_nunca_expira'] ?? null,
                    'modificar_senha_proximo_logon' => $data['modificar_senha_proximo_logon'] ?? $dadosAntes['modificar_senha_proximo_logon'] ?? null,
                ];
                \App\adms\Models\Services\LogAlteracaoService::registrarAlteracao(
                    'adms_users',
                    $data['id'],
                    $_SESSION['user_id'] ?? 0,
                    'update',
                    $dadosAntes,
                    $dadosDepois
                );
            }
            return $result;
        } catch (Exception $e) {
            \App\adms\Helpers\GenerateLog::generateLog("error", "Usuário não editado, nenhum valor foi alterado.", ['id' => $data['id'], 'email' => $data['email'], 'username' => $data['username'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Atualizar os dados de um usuário existente.
     *
     * Este método atualiza as informações de um usuário existente. Se a senha for fornecida, ela também será atualizada.
     * Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do usuário, incluindo `id`, `name`, `email`, `username`, e opcionalmente `password`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updateUserImage(array $data): bool
    {
        $this->dataImage = $data['new_image'];
        unset($data['new_image']);

        $valExtImg = new ValExtImg();
        $valExtImg->validateExtImg($this->dataImage['type']);
        var_dump($valExtImg);

        if ((!empty($this->dataImage['name'])) and ($valExtImg->getResult())) {

            if ($this->upload($data, $this->dataImage)) {
                // Chama deleteImage() para remover a imagem antiga antes de retornar true
                $this->deleteImage($data);

                $directory = "app/adms/image/users/" . $data['id'] . "/";

                // Usar try e catch para gerenciar exceção/erro
                try { // Permanece no try se não houver nenhum erro

                    // QUERY para atualizar o usuário
                    $sql = 'UPDATE adms_users SET image = :image, updated_at = :updated_at WHERE id = :id';

                    // Preparar a QUERY
                    $stmt = $this->getConnection()->prepare($sql);

                    $slugImg = new SlugImg();
                    $nameImgFormatad =  $slugImg->slug($this->dataImage['name']);

                    // Substituir os links da QUERY pelo valor
                    $stmt->bindValue(':image', $nameImgFormatad, PDO::PARAM_STR);
                    $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
                    $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);


                    // Executar a QUERY SQL
                    $stmt->execute();



                    return true; // Retorna verdadeiro se a atualização for bem-sucedida


                } catch (Exception $e) { // Acessa o catch quando houver erro no try

                    // Chamar o método para salvar o log
                    GenerateLog::generateLog("error", "Imagem do Usuário não editada.", [
                        'id' => $data['id'],
                        'error' => $e->getMessage()
                    ]);

                    return false;
                }
            } else {
                // Criar a mensagem de erro
                $this->data['errors'][] = "Usuário não editado, Upload da imagem falhou!";
            }
        } else {
            // Criar a mensagem de erro
            $this->data['errors'][] = "Erro: Necessário selecionar uma imagem JPEG ou PNG!";
      
            return false; // Retorna falso se a função upload falhar
        }
        return false; // Retorna falso se a função upload falhar

    }

    /**
     * Metodo gera o slug da imagem com o helper SlugImg
     * Faz o upload da imagem usando o helper AdmsUploadImgRes
     * Chama o metodo edit para atualizar as informações no banco de dados
     * @return void
     */
    private function upload(array $data, array $dataImage): bool
    {
        $slugImg = new SlugImg();
        $this->nameImg =  $slugImg->slug($dataImage['name']);

        $directory = "app/adms/image/users/" . $data['id'] . "/";

        $uploadImgRes = new Upload();
        $uploadImgRes->upload($directory, $this->dataImage['tmp_name'], $this->nameImg, 300, 300);

        if ($uploadImgRes) {
            return true;
        }
        return false;
    }

    /**
     * Método para apagar a imagem antiga do usuário
     * @param array $data
     * @return bool
     */
    private function deleteImage(array $data): bool
    {
        // Garante que o ID do usuário foi passado corretamente
        if (!isset($data['id']) || empty($data['id'])) {
            $this->data['errors'][] = "Erro: ID do usuário não informado!";
            return false;
        }

        // Obtém os dados do usuário pelo método getUser()
        $user = $this->getUser($data['id']);

        // Verifique se o usuário existe
        if (!$user) {
            $this->data['errors'][] = "Erro: Usuário não encontrado!";
            return false;
        }

        // Verifique se a imagem antiga existe e se é diferente da nova
        if (!empty($user['image']) && $user['image'] !== $this->nameImg) {
            $this->delImg = "app/adms/image/users/" . $data['id'] . "/" . $user['image'];

            // Verifica se o arquivo realmente existe antes de tentar excluir
            if (file_exists($this->delImg)) {
                // Tentar excluir a imagem
                if (unlink($this->delImg)) {
                    return true; // Excluído com sucesso
                } else {
                    $this->data['errors'][] = "Aviso: Não foi possível excluir a imagem antiga.";
                    return false; // Não foi possível excluir
                }
            } else {
                $this->data['errors'][] = "Erro: Arquivo não encontrado para exclusão!";
                return false; // Arquivo não encontrado
            }
        } else {
            $this->data['errors'][] = "Erro: Nenhuma imagem encontrada para exclusão ou a imagem é a mesma!";
            return false; // Nenhuma imagem ou mesma imagem
        }
    }




    /**
     * Atualizar a senha de um usuário.
     *
     * Este método atualiza a senha de um usuário específico. Em caso de erro, um log é gerado.
     *
     * @param array $data Dados atualizados do usuário, incluindo `id` e `password`.
     * @return bool `true` se a atualização foi bem-sucedida ou `false` em caso de erro.
     */
    public function updatePasswordUser(array $data): bool
    {
        try {
            // Atualizar senha e modificar_senha_proximo_logon
            $sql = 'UPDATE adms_users SET password = :password, modificar_senha_proximo_logon = "Não", updated_at = :updated_at WHERE id = :id';
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Senha não editada.", ['id' => $data['id'], 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Deletar um usuário pelo ID.
     *
     * Este método remove um usuário específico da tabela `adms_users`. Em caso de erro, um log é gerado.
     *
     * @param int $id ID do usuário a ser deletado.
     * @return bool `true` se o usuário foi deletado com sucesso ou `false` em caso de erro.
     */
    public function deleteUser(int $id): bool
    {
        try {
            // Captura os dados antigos antes da exclusão
            $dadosAntes = $this->getUser($id);
            $sql = 'DELETE FROM adms_users WHERE id = :id LIMIT 1';
            $stms = $this->getConnection()->prepare($sql);
            $stms->bindValue(':id', $id, PDO::PARAM_INT);
            $stms->execute();
            $affectedRows = $stms->rowCount();
            if ($affectedRows > 0) {
                // Log de exclusão
                \App\adms\Models\Services\LogAlteracaoService::registrarAlteracao(
                    'adms_users',
                    $id,
                    $_SESSION['user_id'] ?? 0,
                    'delete',
                    $dadosAntes ?: [],
                    []
                );
                return true;
            } else {
                GenerateLog::generateLog("error", "Usuário não apagado.", ['id' => $id]);
                return false;
            }
        } catch (Exception $e) {
            GenerateLog::generateLog("error", "Usuário não apagado.", ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function getUserDepartments(int $id): array|bool
    {
        // QUERY para recuperar o registro do banco de dados
        $sql = 'SELECT t1.name
                FROM adms_users t0
                INNER JOIN adms_departments t1 ON t0.user_department_id = t1.id
                WHERE t1.id = :id
                ORDER BY t1.id DESC';

        // Preparar a quey
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir o link pelo valor
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // Executar a query
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsersSelect(): array
    {
        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT id, name, email FROM adms_users ORDER BY name ASC';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna usuários por cargo específico
     */
    public function getUsersByPosition(int $positionId): array
    {
        $sql = 'SELECT id, name, email, user_department_id, user_position_id 
                FROM adms_users 
                WHERE user_position_id = :position_id 
                ORDER BY name ASC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':position_id', $positionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retorna o total de usuários
     */
    public function getTotalUsers(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM adms_users';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    /**
     * Recupera todos os usuários para uso em formulários (select).
     *
     * @return array Lista de usuários para select
     */
    public function getAllUsersForSelect(): array
    {
        $sql = 'SELECT id, name, email FROM adms_users ORDER BY name ASC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
