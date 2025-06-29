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
     * @return array Lista de usuários recuperados do banco de dados.
     */
    public function getAllUsers(int $page = 1, int $limitResult = 10)
    {
        // Calcular o registro inicial, função max para garantir valor minimo 0
        $offset = max(0, ($page - 1) * $limitResult);

        // QUERY para recuperar os registros do banco de dados
        $sql = 'SELECT usr.id, usr.name, usr.email, usr.username, usr.user_department_id, usr.user_position_id, dep.name name_dep, pos.name name_pos
                FROM adms_users usr
                INNER JOIN adms_departments dep ON usr.user_department_id = dep.id
                INNER JOIN adms_positions pos ON usr.user_position_id = pos.id 
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir o link da QUERY pelo valor / Evita SQL INJECTION
        $stmt->bindValue(':limit', $limitResult, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperar a quantidade total de usuários para paginação.
     *
     * Este método retorna a quantidade total de usuários na tabela `adms_users`, útil para a paginação.
     *
     * @return int Quantidade total de usuários encontrados no banco de dados.
     */
    public function getAmountUsers(): int|bool
    {
        // Query para recuperar quantiade de registros
        $sql = 'SELECT COUNT(id) as amount_records 
        FROM adms_users';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Executar a QUERY
        $stmt->execute();

        // Ler os registros e retornar
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
                    t0.user_department_id, 
                    t0.user_position_id, 
                    t0.created_at, 
                    t0.updated_at, 
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
        // Usar o try e catch para gerenciar exeções/erro
        try { // Permanece no try se não houver erro

            // QUERY cadastrar usuários
            $sql = 'INSERT INTO adms_users (name, email, username, user_department_id, user_position_id,  password, created_at ) VALUES (:name, :email, :username, :user_department_id,:user_position_id, :password, :created_at)';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os links da QUERY pelo valor
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindValue(':user_department_id', $data['user_department_id'], PDO::PARAM_INT);
            $stmt->bindValue(':user_position_id', $data['user_position_id'], PDO::PARAM_INT);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':created_at', date("Y-m-d H:i:s"));

            // Executar a QUERY
            $stmt->execute();

            // Retornar o ID do usuário recém cadastrado
            return $this->getConnection()->lastInsertId();
        } catch (Exception $e) { // Acessa o catch quando houver erro no try

            // Chamar o método para salvar o log
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
        // Usar try e catch para gerenciar exceção/erro

        try { // Permanece no try se não houver nenhum erro

            // QUERY para atualizar o usuário
            $sql = 'UPDATE adms_users SET name = :name, email = :email, username = :username, user_department_id = :user_department_id, user_position_id = :user_position_id, updated_at = :updated_at';

            // Verificar se a senha está incluida nos dados e, se sim, adicionar ao SQL
            if (!empty($data['password'])) {
                $sql .= ', password = :password';
            }

            // Condição para indicar qual registro editar
            $sql .= ' WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os links da QUERY pelo valor
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':username', $data['username'], PDO::PARAM_STR);
            $stmt->bindValue(':user_department_id', $data['user_department_id'], PDO::PARAM_INT);
            $stmt->bindValue(':user_position_id', $data['user_position_id'], PDO::PARAM_INT);
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Substituir o link da senha se a mesma estiver presente
            if (!empty($data['password'])) {
                $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            }

            // Retornar TRUE quando conseguir executar a QUERY SQL, não considerando se alterou dados do registro
            return $stmt->execute();
        } catch (Exception $e) { // Acessa o catch quando houver erro no try

            // Chamar o método para salvar o log
            GenerateLog::generateLog("error", "Usuário não editado, nenhum valor foi alterado.", ['id' => $data['id'], 'email' => $data['email'], 'username' => $data['username'], 'error' => $e->getMessage()]);

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

        // Usar try e catch para gerenciar exceção/erro
        try {  // Permanece no try se não houver nenhum erro

            // QUERY para atualizar usuário
            // Condição para indicar qual registro editar
            $sql = 'UPDATE adms_users SET password = :password, updated_at = :updated_at WHERE id = :id';

            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Substituir os links da QUERY pelo valor
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':updated_at', date("Y-m-d H:i:s"));
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);

            // Retornar TRUE quando conseguir executar a QUERY SQL, não considerando se alterou dados do registro
            return $stmt->execute();
        } catch (Exception $e) { // Acessa o catch quando houver erro no try

            // Chamar o método para salvar o log
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
        // Usar try e catch para gerenciar exceção/erro
        try {
            // QUERY para deletar usuário
            $sql = 'DELETE FROM adms_users WHERE id = :id LIMIT 1';

            // Preparar a QUERY
            $stms = $this->getConnection()->prepare($sql);

            // Substituir o link da QUERY pelo valor 
            $stms->bindValue(':id', $id, PDO::PARAM_INT);

            // Executar a QUERY
            $stms->execute();

            // Verificar o número de linhas afetadas
            $affectedRows = $stms->rowCount();

            // Verificar o número de linhas afetadas
            if ($affectedRows > 0) {
                return true;
            } else {

                // Chamar o método para salvar o log
                GenerateLog::generateLog("error", "Usuário não apagado.", ['id' => $id]);

                return false;
            }
        } catch (Exception $e) {

            // Chamar o método para salvar o log
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
}
