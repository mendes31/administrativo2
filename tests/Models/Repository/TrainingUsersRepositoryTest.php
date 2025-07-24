<?php
use PHPUnit\Framework\TestCase;
use App\adms\Models\Repository\TrainingUsersRepository;

class TrainingUsersRepositoryTest extends TestCase
{
    public function setUp(): void
    {
        $this->repo = new TrainingUsersRepository();
    }

    public function testStatusDentroDoPrazoPrimeiroCiclo()
    {
        $user = [
            'data_limite_primeiro_treinamento' => date('Y-m-d', strtotime('+20 days')),
            'data_realizacao' => null,
            'data_agendada' => null,
            'prazo_treinamento' => 20,
            'tipo_vinculo' => 'individual'
        ];
        $status = $this->invokeCalculateStatus($user);
        $this->assertEquals('dentro_do_prazo', $status);
    }

    public function testStatusProximoVencimentoPrimeiroCiclo()
    {
        $user = [
            'data_limite_primeiro_treinamento' => date('Y-m-d', strtotime('+9 days')),
            'data_realizacao' => null,
            'data_agendada' => null,
            'prazo_treinamento' => 20,
            'tipo_vinculo' => 'individual'
        ];
        $status = $this->invokeCalculateStatus($user);
        $this->assertEquals('proximo_vencimento', $status);
    }

    public function testStatusVencido()
    {
        $user = [
            'data_limite_primeiro_treinamento' => date('Y-m-d', strtotime('-1 days')),
            'data_realizacao' => null,
            'data_agendada' => null,
            'prazo_treinamento' => 20,
            'tipo_vinculo' => 'individual'
        ];
        $status = $this->invokeCalculateStatus($user);
        $this->assertEquals('vencido', $status);
    }

    public function testStatusConcluido()
    {
        $user = [
            'data_limite_primeiro_treinamento' => date('Y-m-d', strtotime('+20 days')),
            'data_realizacao' => date('Y-m-d'),
            'data_agendada' => null,
            'prazo_treinamento' => 20,
            'tipo_vinculo' => 'individual'
        ];
        $status = $this->invokeCalculateStatus($user);
        $this->assertEquals('concluido', $status);
    }

    public function testStatusAgendado()
    {
        $user = [
            'data_limite_primeiro_treinamento' => date('Y-m-d', strtotime('+20 days')),
            'data_realizacao' => null,
            'data_agendada' => date('Y-m-d', strtotime('+5 days')),
            'prazo_treinamento' => 20,
            'tipo_vinculo' => 'individual'
        ];
        $status = $this->invokeCalculateStatus($user);
        $this->assertEquals('agendado', $status);
    }

    public function testStatusProximoVencimentoReciclagem()
    {
        $user = [
            'data_limite_primeiro_treinamento' => date('Y-m-d', strtotime('+29 days')),
            'data_realizacao' => null,
            'data_agendada' => null,
            'prazo_treinamento' => 60,
            'tipo_vinculo' => 'reciclagem'
        ];
        $status = $this->invokeCalculateStatus($user);
        $this->assertEquals('proximo_vencimento', $status);
    }

    // Helper para acessar método privado
    private function invokeCalculateStatus($user)
    {
        $reflection = new \ReflectionClass($this->repo);
        $method = $reflection->getMethod('calculateStatus');
        $method->setAccessible(true);
        return $method->invoke($this->repo, $user);
    }
} 