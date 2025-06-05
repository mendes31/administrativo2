<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;


class LoginRepository extends DbConnection
{

    public function getUser(string $username): array|bool
    {
        // QUERY para recuperar o registro selecionado do banco de dados
        // $sql = 'SELECT id, name, email, username, password
        //         FROM adms_users
        //         WHERE username = :username
        //         LIMIT 1';

        $sql = 'SELECT t0.id, t0.name, t0.email, t0.username, t0.image, t0.password, t0.user_department_id, t0.user_position_id, t0.created_at, 
                t0.updated_at, t1.name dep_name, t2.name pos_name
                FROM adms_users t0
                INNER JOIN adms_departments t1 ON t0.user_department_id = t1.id
                INNER JOIN adms_positions t2 ON t0.user_position_id = t2.id
                WHERE t0.username = :username
                LIMIT 1';

        // Preparar a QUERY
        $stmt = $this->getConnection()->prepare($sql);

        // Substituir o link da QUERY pelo valor / Evita SQL INJECTION
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);

        // Executar a QUERY
        $stmt->execute();

        // Ler o registro e retornar
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
