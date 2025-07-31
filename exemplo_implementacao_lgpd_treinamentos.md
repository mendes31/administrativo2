# 📋 EXEMPLO: Implementação Híbrida de Treinamentos LGPD

## 🗄️ 1. ESTRUTURA DO BANCO DE DADOS

### Modificação da Tabela `adms_trainings`:

```sql
-- Adicionar campos específicos para LGPD
ALTER TABLE adms_trainings 
ADD COLUMN categoria_lgpd ENUM('Awareness', 'Tratamento', 'Direitos', 'Incidentes', 'ROPA', 'Segurança') NULL,
ADD COLUMN nivel_risco ENUM('Alto', 'Médio', 'Baixo') NULL,
ADD COLUMN obrigatorio_lgpd BOOLEAN DEFAULT FALSE,
ADD COLUMN validade_lgpd INT NULL COMMENT 'Validade em meses para reciclagem LGPD',
ADD COLUMN base_legal VARCHAR(255) NULL COMMENT 'Base legal que fundamenta o treinamento';

-- Exemplo de dados inseridos:
INSERT INTO adms_trainings (
    nome, codigo, categoria_lgpd, nivel_risco, obrigatorio_lgpd, 
    validade_lgpd, base_legal, ativo, tipo
) VALUES 
('Awareness LGPD - Fundamentos', 'LGPD-001', 'Awareness', 'Baixo', TRUE, 12, 'Art. 37, §4º LGPD', 1, 'Ministrado'),
('Tratamento de Dados Pessoais', 'LGPD-002', 'Tratamento', 'Médio', TRUE, 6, 'Art. 7º LGPD', 1, 'Ministrado'),
('Direitos dos Titulares', 'LGPD-003', 'Direitos', 'Alto', TRUE, 6, 'Art. 18 LGPD', 1, 'Ministrado'),
('Gestão de Incidentes LGPD', 'LGPD-004', 'Incidentes', 'Alto', TRUE, 3, 'Art. 48 LGPD', 1, 'Ministrado'),
('ROPA - Registro de Operações', 'LGPD-005', 'ROPA', 'Médio', TRUE, 12, 'Art. 37 LGPD', 1, 'Ministrado'),
('Segurança da Informação', 'LGPD-006', 'Segurança', 'Alto', TRUE, 6, 'Art. 46 LGPD', 1, 'Ministrado');
```

## 🎯 2. CONTROLLER ESPECÍFICO LGPD

### `app/adms/Controllers/trainings/LgpdTrainingDashboard.php`:

```php
<?php

namespace App\adms\Controllers\trainings;

use App\adms\Models\Repository\TrainingsRepository;
use App\adms\Models\Repository\TrainingUsersRepository;
use App\adms\Controllers\Services\PageLayoutService;
use App\adms\Views\Services\LoadViewService;

class LgpdTrainingDashboard
{
    private TrainingsRepository $trainingsRepo;
    private TrainingUsersRepository $trainingUsersRepo;

    public function __construct()
    {
        $this->trainingsRepo = new TrainingsRepository();
        $this->trainingUsersRepo = new TrainingUsersRepository();
    }

    public function index(): void
    {
        $data = [
            'title_head' => 'Dashboard de Treinamentos LGPD',
            'menu' => 'lgpd-training-dashboard',
            'buttonPermission' => ['LgpdTrainingDashboard'],
        ];
        
        $pageLayout = new PageLayoutService();
        $data = array_merge($data, $pageLayout->configurePageElements($data));
        $data['dashboard'] = $this->getLgpdDashboardData();
        
        $loadView = new LoadViewService('adms/Views/trainings/lgpdTrainingDashboard', $data);
        $loadView->loadView();
    }

    private function getLgpdDashboardData(): array
    {
        return [
            'resumo_geral' => $this->getResumoGeral(),
            'treinamentos_por_categoria' => $this->getTreinamentosPorCategoria(),
            'compliance_por_departamento' => $this->getCompliancePorDepartamento(),
            'treinamentos_vencendo' => $this->getTreinamentosVencendo(),
            'matriz_risco' => $this->getMatrizRisco(),
            'evolucao_mensal' => $this->getEvolucaoMensal()
        ];
    }

    private function getResumoGeral(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_treinamentos,
                    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN obrigatorio_lgpd = 1 THEN 1 ELSE 0 END) as obrigatorios,
                    SUM(CASE WHEN nivel_risco = 'Alto' THEN 1 ELSE 0 END) as alto_risco,
                    SUM(CASE WHEN nivel_risco = 'Médio' THEN 1 ELSE 0 END) as medio_risco,
                    SUM(CASE WHEN nivel_risco = 'Baixo' THEN 1 ELSE 0 END) as baixo_risco
                FROM adms_trainings 
                WHERE categoria_lgpd IS NOT NULL";
        
        $stmt = $this->trainingsRepo->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    private function getTreinamentosPorCategoria(): array
    {
        $sql = "SELECT 
                    categoria_lgpd,
                    COUNT(*) as quantidade,
                    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos,
                    AVG(CASE WHEN nivel_risco = 'Alto' THEN 3 
                             WHEN nivel_risco = 'Médio' THEN 2 
                             ELSE 1 END) as risco_medio
                FROM adms_trainings 
                WHERE categoria_lgpd IS NOT NULL
                GROUP BY categoria_lgpd
                ORDER BY quantidade DESC";
        
        $stmt = $this->trainingsRepo->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCompliancePorDepartamento(): array
    {
        $sql = "SELECT 
                    d.nome as departamento,
                    COUNT(DISTINCT t.id) as total_treinamentos,
                    COUNT(DISTINCT tu.user_id) as colaboradores_treinados,
                    ROUND((COUNT(DISTINCT tu.user_id) / COUNT(DISTINCT t.id)) * 100, 2) as percentual_compliance
                FROM adms_trainings t
                LEFT JOIN training_users tu ON t.id = tu.training_id
                LEFT JOIN adms_users u ON tu.user_id = u.id
                LEFT JOIN adms_departments d ON u.department_id = d.id
                WHERE t.categoria_lgpd IS NOT NULL
                GROUP BY d.id, d.nome
                ORDER BY percentual_compliance DESC";
        
        $stmt = $this->trainingsRepo->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getTreinamentosVencendo(): array
    {
        $sql = "SELECT 
                    t.nome,
                    t.categoria_lgpd,
                    t.nivel_risco,
                    t.validade_lgpd,
                    COUNT(tu.user_id) as colaboradores_afetados,
                    DATE_ADD(tu.completed_at, INTERVAL t.validade_lgpd MONTH) as data_vencimento
                FROM adms_trainings t
                JOIN training_users tu ON t.id = tu.training_id
                WHERE t.categoria_lgpd IS NOT NULL 
                AND t.validade_lgpd IS NOT NULL
                AND tu.completed_at IS NOT NULL
                AND DATE_ADD(tu.completed_at, INTERVAL t.validade_lgpd MONTH) <= DATE_ADD(NOW(), INTERVAL 30 DAY)
                GROUP BY t.id
                ORDER BY data_vencimento ASC";
        
        $stmt = $this->trainingsRepo->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getMatrizRisco(): array
    {
        $sql = "SELECT 
                    categoria_lgpd,
                    nivel_risco,
                    COUNT(*) as quantidade,
                    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos
                FROM adms_trainings 
                WHERE categoria_lgpd IS NOT NULL
                GROUP BY categoria_lgpd, nivel_risco
                ORDER BY categoria_lgpd, 
                         CASE nivel_risco 
                             WHEN 'Alto' THEN 3 
                             WHEN 'Médio' THEN 2 
                             ELSE 1 END DESC";
        
        $stmt = $this->trainingsRepo->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getEvolucaoMensal(): array
    {
        $sql = "SELECT 
                    DATE_FORMAT(tu.completed_at, '%Y-%m') as mes,
                    COUNT(DISTINCT tu.user_id) as colaboradores_treinados,
                    COUNT(DISTINCT t.id) as treinamentos_concluidos
                FROM training_users tu
                JOIN adms_trainings t ON tu.training_id = t.id
                WHERE t.categoria_lgpd IS NOT NULL
                AND tu.completed_at IS NOT NULL
                AND tu.completed_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(tu.completed_at, '%Y-%m')
                ORDER BY mes DESC";
        
        $stmt = $this->trainingsRepo->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

## 🎨 3. VIEW DO DASHBOARD LGPD

### `app/adms/Views/trainings/lgpdTrainingDashboard.php`:

```php
<?php
// View do Dashboard de Treinamentos LGPD
$dashboard = $this->data['dashboard'] ?? [];
$resumo = $dashboard['resumo_geral'] ?? [];
$categorias = $dashboard['treinamentos_por_categoria'] ?? [];
$departamentos = $dashboard['compliance_por_departamento'] ?? [];
$vencendo = $dashboard['treinamentos_vencendo'] ?? [];
$matriz = $dashboard['matriz_risco'] ?? [];
$evolucao = $dashboard['evolucao_mensal'] ?? [];
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row justify-content-center mb-4">
        <div class="col-12">
            <div class="bg-primary bg-gradient rounded-4 p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-1">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Dashboard de Treinamentos LGPD
                        </h2>
                        <div class="text-white-50">
                            Gestão e monitoramento de capacitação em proteção de dados
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="text-white">
                            <small>Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h3 class="text-primary"><?php echo $resumo['total_treinamentos'] ?? 0; ?></h3>
                    <small class="text-muted">Total de Treinamentos</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h3 class="text-success"><?php echo $resumo['obrigatorios'] ?? 0; ?></h3>
                    <small class="text-muted">Obrigatórios LGPD</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h3 class="text-danger"><?php echo $resumo['alto_risco'] ?? 0; ?></h3>
                    <small class="text-muted">Alto Risco</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h3 class="text-warning"><?php echo $resumo['medio_risco'] ?? 0; ?></h3>
                    <small class="text-muted">Médio Risco</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h3 class="text-info"><?php echo $resumo['baixo_risco'] ?? 0; ?></h3>
                    <small class="text-muted">Baixo Risco</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <h3 class="text-secondary"><?php echo count($vencendo); ?></h3>
                    <small class="text-muted">Vencendo (30 dias)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="row mb-4">
        <!-- Treinamentos por Categoria -->
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Treinamentos por Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categoria</th>
                                    <th>Quantidade</th>
                                    <th>Ativos</th>
                                    <th>Risco Médio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $cat['categoria_lgpd']; ?></span>
                                    </td>
                                    <td><?php echo $cat['quantidade']; ?></td>
                                    <td><?php echo $cat['ativos']; ?></td>
                                    <td>
                                        <?php 
                                        $risco = round($cat['risco_medio'], 1);
                                        $cor = $risco >= 2.5 ? 'danger' : ($risco >= 1.5 ? 'warning' : 'success');
                                        ?>
                                        <span class="badge bg-<?php echo $cor; ?>"><?php echo $risco; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compliance por Departamento -->
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Compliance por Departamento
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Departamento</th>
                                    <th>Treinamentos</th>
                                    <th>Colaboradores</th>
                                    <th>Compliance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departamentos as $dept): ?>
                                <tr>
                                    <td><?php echo $dept['departamento']; ?></td>
                                    <td><?php echo $dept['total_treinamentos']; ?></td>
                                    <td><?php echo $dept['colaboradores_treinados']; ?></td>
                                    <td>
                                        <?php 
                                        $compliance = $dept['percentual_compliance'];
                                        $cor = $compliance >= 80 ? 'success' : ($compliance >= 60 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?php echo $cor; ?>"><?php echo $compliance; ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Treinamentos Vencendo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Treinamentos Vencendo (Próximos 30 dias)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($vencendo)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Treinamento</th>
                                    <th>Categoria</th>
                                    <th>Nível de Risco</th>
                                    <th>Colaboradores Afetados</th>
                                    <th>Data de Vencimento</th>
                                    <th>Validade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vencendo as $item): ?>
                                <tr>
                                    <td><strong><?php echo $item['nome']; ?></strong></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $item['categoria_lgpd']; ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $cor = $item['nivel_risco'] === 'Alto' ? 'danger' : 
                                               ($item['nivel_risco'] === 'Médio' ? 'warning' : 'success');
                                        ?>
                                        <span class="badge bg-<?php echo $cor; ?>"><?php echo $item['nivel_risco']; ?></span>
                                    </td>
                                    <td><?php echo $item['colaboradores_afetados']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($item['data_vencimento'])); ?></td>
                                    <td><?php echo $item['validade_lgpd']; ?> meses</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="mb-0">Nenhum treinamento vencendo nos próximos 30 dias</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Matriz de Risco -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Matriz de Risco por Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Categoria</th>
                                    <th class="text-center">Alto Risco</th>
                                    <th class="text-center">Médio Risco</th>
                                    <th class="text-center">Baixo Risco</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $categorias_unicas = array_unique(array_column($matriz, 'categoria_lgpd'));
                                foreach ($categorias_unicas as $cat):
                                    $alto = 0; $medio = 0; $baixo = 0;
                                    foreach ($matriz as $item) {
                                        if ($item['categoria_lgpd'] === $cat) {
                                            if ($item['nivel_risco'] === 'Alto') $alto = $item['quantidade'];
                                            elseif ($item['nivel_risco'] === 'Médio') $medio = $item['quantidade'];
                                            else $baixo = $item['quantidade'];
                                        }
                                    }
                                    $total = $alto + $medio + $baixo;
                                ?>
                                <tr>
                                    <td><strong><?php echo $cat; ?></strong></td>
                                    <td class="text-center">
                                        <?php if ($alto > 0): ?>
                                        <span class="badge bg-danger"><?php echo $alto; ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($medio > 0): ?>
                                        <span class="badge bg-warning"><?php echo $medio; ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($baixo > 0): ?>
                                        <span class="badge bg-success"><?php echo $baixo; ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo $total; ?></strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aqui você pode adicionar gráficos Chart.js para visualização
    console.log('Dashboard LGPD Treinamentos carregado');
});
</script>
```

## 🔗 4. INTEGRAÇÃO COM O MENU

### Adicionar no menu (`app/adms/Views/partials/menu.php`):

```php
// Dentro do submenu LGPD
[
    'label' => 'Treinamentos LGPD',
    'url' => $_ENV['URL_ADM'] . 'lgpd-training-dashboard',
    'permission' => 'LgpdTrainingDashboard'
],
[
    'label' => 'Relatório de Compliance',
    'url' => $_ENV['URL_ADM'] . 'lgpd-training-report',
    'permission' => 'LgpdTrainingReport'
],
```

## 📊 5. INTEGRAÇÃO COM DASHBOARD LGPD PRINCIPAL

### Modificar `LgpdDashboardRepository.php`:

```php
// Adicionar método para buscar dados de treinamentos LGPD
private function getTreinamentosLgpdIndicators(): array
{
    try {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN obrigatorio_lgpd = 1 THEN 1 ELSE 0 END) as obrigatorios,
                    SUM(CASE WHEN nivel_risco = 'Alto' THEN 1 ELSE 0 END) as alto_risco,
                    SUM(CASE WHEN nivel_risco = 'Médio' THEN 1 ELSE 0 END) as medio_risco,
                    SUM(CASE WHEN nivel_risco = 'Baixo' THEN 1 ELSE 0 END) as baixo_risco
                FROM adms_trainings 
                WHERE categoria_lgpd IS NOT NULL";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total' => (int) ($result['total'] ?? 0),
            'ativos' => (int) ($result['ativos'] ?? 0),
            'obrigatorios' => (int) ($result['obrigatorios'] ?? 0),
            'alto_risco' => (int) ($result['alto_risco'] ?? 0),
            'medio_risco' => (int) ($result['medio_risco'] ?? 0),
            'baixo_risco' => (int) ($result['baixo_risco'] ?? 0)
        ];
    } catch (Exception $e) {
        return [
            'total' => 0,
            'ativos' => 0,
            'obrigatorios' => 0,
            'alto_risco' => 0,
            'medio_risco' => 0,
            'baixo_risco' => 0
        ];
    }
}
```

## 🎯 6. RESULTADO FINAL

### Benefícios desta Implementação:

1. **✅ Unificação:** Um sistema de treinamentos
2. **✅ Especificidade:** Funcionalidades LGPD dedicadas
3. **✅ Compliance:** Rastreabilidade completa
4. **✅ Escalabilidade:** Fácil adicionar outras categorias
5. **✅ Manutenibilidade:** Código organizado e reutilizável
6. **✅ Experiência:** Interface consistente

### Funcionalidades Obtidas:

- **Dashboard específico** para treinamentos LGPD
- **Categorização** por tipo de treinamento
- **Matriz de risco** por categoria
- **Monitoramento** de vencimentos
- **Relatórios** de compliance por departamento
- **Integração** com dashboard LGPD principal
- **Rastreabilidade** completa para auditorias

Esta implementação oferece o melhor dos dois mundos: mantém a unificação do sistema mas adiciona funcionalidades específicas para LGPD, criando uma solução robusta e escalável. 