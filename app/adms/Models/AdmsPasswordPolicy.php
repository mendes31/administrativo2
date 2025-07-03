<?php

namespace App\adms\Models;

class AdmsPasswordPolicy
{
    public int $id;
    public int $vencimento_dias;
    public int $comprimento_minimo;
    public int $min_maiusculas;
    public int $min_minusculas;
    public int $min_digitos;
    public int $min_nao_alfanumericos;
    public int $historico_senhas;
    public int $tentativas_bloqueio;
    public string $created_at;
    public string $updated_at;
    public string $nivel_seguranca;
    public string $exemplo_senha;
} 