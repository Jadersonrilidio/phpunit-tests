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
    private $finalizado;
    /** @var \DateTimeInterface  */
    private $dataInicio;
    /** @var int */
    private $id;

    public function __construct(string $descricao, \DateTimeImmutable $dataInicio = null, int $id = null)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
        $this->dataInicio = $dataInicio ?? new \DateTimeImmutable();
        $this->id = $id;
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
     * 
     */
    public function getDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    /**
     * 
     */
    public function getDataInicio(): \DateTimeInterface
    {
        return $this->dataInicio;
    }

    /**
     * 
     */
    public function temMaisDeUmaSemana(): bool
    {
        $hoje = new \DateTime();
        $intervalo = $this->dataInicio->diff($hoje);

        return $intervalo->days > 7;
    }

    /**
     * 
     */
    public function getId(): int
    {
        return $this->id;
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
