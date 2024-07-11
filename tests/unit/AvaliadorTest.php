<?php

namespace Alura\Leilao\Tests;

use Alura\Leilao\Service\Avaliador;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Model\Lance;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private Avaliador $leiloeiro;

    /**
     * 
     */
    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatoria
     */
    public function testAvaliadorDeveRetornarOMaiorEMenorValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $this->assertEquals(expected: 2500, actual: $this->leiloeiro->getMaiorValor());
        $this->assertEquals(expected: 2000, actual: $this->leiloeiro->getMenorValor());
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatoria
     */
    public function testAvaliadorDeveBuscarOsTresMaioresLancesEmOrdemCrescente(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maioresValores = array_map(function (Lance $lance) {
            return $lance->getValor();
        }, $this->leiloeiro->getMaioresLances());

        $this->assertCount(3, $maioresValores);
        $this->assertEquals(expected: [2250, 2300, 2500], actual: $maioresValores);
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatoria
     */
    public function testAvaliadorSempreOrdenaLancesEmOrdemCrescente(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maioresValores = array_map(function (Lance $lance) {
            return $lance->getValor();
        }, $this->leiloeiro->getMaioresLances());

        $this->assertEquals(expected: [2250, 2300, 2500], actual: $maioresValores);
    }

    /**
     * @dataProvider leilaoVazio
     */
    public function testLeilaoVazioNaoPodeSerAvaliado(Leilao $leilao)
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('nao e possivel avaliar um leilao vazio');

        $this->leiloeiro->avalia($leilao);
    }

    /**
     * @dataProvider leilaoEmOrdemCrescente
     */
    public function testLeilaoFinalizadoNaoPodeSerAvaliado(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('leilao finalizado nao pode ser avaliado');

        $this->leiloeiro->avalia($leilao);
    }

    /**
     * 
     */
    public static function leilaoEmOrdemCrescente(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');
        $usuarioMaria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($usuarioJoao, 2000));
        $leilao->recebeLance(new Lance($usuarioJorge, 2250));
        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioMaria, 2500));

        return array(
            'ordem-crescente' => [$leilao]
        );
    }

    /**
     * 
     */
    public static function leilaoEmOrdemDecrescente(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');
        $usuarioMaria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($usuarioMaria, 2500));
        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioJorge, 2250));
        $leilao->recebeLance(new Lance($usuarioJoao, 2000));

        return array(
            'ordem-decrescente' => [$leilao]
        );
    }

    /**
     * 
     */
    public static function leilaoEmOrdemAleatoria(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');
        $usuarioMaria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioJoao, 2000));
        $leilao->recebeLance(new Lance($usuarioMaria, 2500));
        $leilao->recebeLance(new Lance($usuarioJorge, 2250));

        return array(
            'ordem-aleatoria' => [$leilao]
        );
    }

    /**
     * 
     */
    public static function leilaoVazio(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');

        return array(
            'leilao-vazio' => [$leilao]
        );
    }
}
