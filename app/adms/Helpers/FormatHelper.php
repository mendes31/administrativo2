<?php

namespace App\adms\Helpers;

class FormatHelper
{
    /**
     * Formata o período de reciclagem com singular/plural
     */
    public static function formatReciclagemPeriodo(?int $periodo): string
    {
        if (empty($periodo) || $periodo <= 0) {
            return 'N/A';
        }
        
        $texto = $periodo === 1 ? 'mês' : 'meses';
        return $periodo . ' ' . $texto;
    }
    
    /**
     * Formata o período de reciclagem para exibição em tabelas
     */
    public static function formatReciclagemPeriodoTable(?int $periodo): string
    {
        if (empty($periodo) || $periodo <= 0) {
            return 'N/A';
        }
        
        $texto = $periodo === 1 ? 'mês' : 'meses';
        return $periodo . ' - ' . $texto;
    }
} 