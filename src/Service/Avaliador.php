<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;

class Avaliador
{
    /** @var float */
    private $menorValor;
    /** @var float */
    private $maiorValor;
    /** @var Lance[]|array */
    private $maioresLances;

    /**
     * 
     */
    public function avalia(Leilao $leilao): void
    {
        if ($leilao->getFinalizado()) {
            throw new \DomainException('leilao finalizado nao pode ser avaliado');
        }

        if (empty($leilao->getLances())) {
            throw new \DomainException('nao e possivel avaliar um leilao vazio');
        }

        $lances = $this->organizaLancesEmOrdemCrescente($leilao->getLances());

        $this->menorValor = $lances[0]->getValor();
        $this->maiorValor = $lances[count($lances) - 1]->getValor();
        $this->maioresLances = array_slice($lances, -3, 3);

        $leilao->finaliza();
    }

    /**
     * 
     */
    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    /**
     * 
     */
    public function getMenorValor(): float
    {
        return $this->menorValor;
    }

    /**
     * @return Lance[]
     */
    public function getMaioresLances(): array
    {
        return $this->maioresLances;
    }

    /**
     * @param Lance[] $lances
     */
    public function organizaLancesEmOrdemCrescente(array $lances): array
    {
        usort($lances, fn (Lance $a, Lance $b) => $a->getValor() <=> $b->getValor());

        return $lances;
    }
}
