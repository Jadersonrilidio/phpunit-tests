<?php

namespace Alura\Leilao\Model;

use DomainException;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    /** @var bool */
    private $finalizado = false;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->isMesmoUsuario($lance)) {
            throw new DomainException('usuario nao pode dar 2 lances consecutivos');
        }

        if ($this->countLancesUsuario($lance) >= 5) {
            throw new DomainException('usuario nao pode dar mais de 5 lances por leilao');
        }

        $this->lances[] = $lance;
    }

    /**
     * 
     */
    public function finaliza(): void
    {
        $this->finalizado = true;
    }

    /**
     * 
     */
    public function getFinalizado(): bool
    {
        return $this->finalizado;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    /**
     * @param Lance $lance
     */
    private function isMesmoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[array_key_last($this->lances)];

        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    /**
     * 
     */
    private function countLancesUsuario(Lance $lance): int
    {
        $usuario = $lance->getUsuario();

        return array_reduce($this->lances, function (int $ac, Lance $lanceAtual) use ($usuario) {
            return ($lanceAtual->getUsuario() == $usuario) ? ++$ac : $ac;
        }, 0);
    }
}
