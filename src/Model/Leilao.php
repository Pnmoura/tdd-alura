<?php

namespace Alura\Leilao\Model;

class Leilao
{
    private array $lances;
    private string $descricao;
    private bool $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new \DomainException('Usuario não pode propor 2 lances consecutivos');
        }

        $usuario = $lance->getUsuario();
        $totalDeLancesUsuario = $this->quantidadeLancesPorUsuario($usuario);
        if ($totalDeLancesUsuario >= 5){
            throw new \DomainException('Usuario não pode propor 5 lances por leilão');
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function estaFinalizado() : bool
    {
        return $this->finalizado;
    }

    /**
     * @param Lance $lance
     * @return bool
     */
    private function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[array_key_last($this->lances)];
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    private function quantidadeLancesPorUsuario(Usuario $usuario): int
    {
        $totalDeLancesUsuario = array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }

                return $totalAcumulado;
            },
            0
        );
        return $totalDeLancesUsuario;
    }
}
