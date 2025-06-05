<?php

namespace App\adms\Models\Repository;

use App\adms\Models\Services\DbConnection;
use PDO;

class MenuPermissionUserRepository extends DbConnection
{

    public function menuPermission(array $menu): array|bool
    {
        //     // Verificar se o array $button está vazio
            if(empty($menu)){
                return [];
            }

        // Criar uma string de placeholders do mesmo tamanho do array de controllers
        $placeholders = implode(', ', array_fill(0, count($menu), '?'));

        // QUERY para verificar a permissão do usuário em relação às páginas
        $sql = "SELECT
                    ap.controller
                FROM 
                    adms_users_access_levels AS aual
                LEFT JOIN
                    adms_access_levels_pages AS alp ON alp.adms_access_level_id = aual.adms_access_level_id
                LEFT JOIN 
                    adms_pages AS ap ON ap.id = alp.adms_page_id
                WHERE 
                    aual.adms_user_id = ?
                    AND ap.controller IN ($placeholders)
                    AND alp.permission = 1";
            // Preparar a QUERY
            $stmt = $this->getConnection()->prepare($sql);

            // Combinar o valor do 'user_id' com o array de botões (controllers)
            $params = array_merge([$_SESSION['user_id']], $menu);

            // Executar a QUERY com os parâmetros
            $stmt->execute($params);

            // Ler os registros
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retornar apenas os valores de 'controller' como array simples
            return $result ? array_column($result, 'controller') : [];

    }
}
