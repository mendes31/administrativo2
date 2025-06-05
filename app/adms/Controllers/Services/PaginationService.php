<?php

namespace App\adms\Controllers\Services;

/**
 * Classe PaginationService
 * 
 * Esta classe fornece um serviço para gerar dados de paginação, incluindo o número total de registros,
 * o número total de páginas, a página atual, e a URL do controller. É útil para gerenciar a navegação 
 * entre diferentes páginas de resultados em uma aplicação web.
 * 
 * @package App\adms\Controllers\Services
 * @author Rafael Mendes
 */
class PaginationService
{
    /**
     * Gerar os dados de paginação
     * 
     * Este método calcula o número total de páginas com base no número total de registros e na quantidade
     * de registros por página. Ele também retorna a página atual e a URL do controller para facilitar a 
     * navegação entre as páginas.
     * 
     * @param int $totalRecords Total de registros
     * @param int $limitResult Registros por página
     * @param int $currentPage Página atual
     * @param string $urlController URL do controller
     * @return array Dados da paginação
     */
    public static function generatePagination(int $totalRecords, int $limitResult, int $currentPage, string $urlController): array
    {
        // Calcular o numero total de páginas
        $lastPage = (int) ceil($totalRecords / $limitResult);

        // Retornar os dados da paginação
        return [
            'amount_records' => $totalRecords,
            'last_page' => $lastPage,
            'current_page' => $currentPage == 0 ? 1 :$currentPage,
            'url_controller' => $urlController

        ];
    }
}
