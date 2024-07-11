<?php

namespace Alura\Leilao\Tests;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Exception;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    /**
     * @dataProvider leilaoComQuatroLances
     * @dataProvider leilaoComDoisLances
     */
    public function testLeilaoDeveReceberLances(int $lancesCount, Leilao $leilao, array $valores)
    {
        $this->assertCount(expectedCount: $lancesCount, haystack: $leilao->getLances());

        foreach ($valores as $index => $valor) {
            $this->assertEquals(expected: $valor, actual: $leilao->getLances()[$index]->getValor());
        }
    }

    /**
     * 
     */
    public function testUsuarioNaoPodeDarDoisLancesConsecutivos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('usuario nao pode dar 2 lances consecutivos');

        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance($usuarioJoao, 2000));
        $leilao->recebeLance(new Lance($usuarioJorge, 2250));
        $leilao->recebeLance(new Lance($usuarioJorge, 2300));
        $leilao->recebeLance(new Lance($usuarioMarta, 2500));
    }

    /**
     * 
     */
    public function testUsuarioNaoPodeDarMaisDeCincoLancesPorLeilao()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('usuario nao pode dar mais de 5 lances por leilao');

        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');
        $usuarioMaria = new Usuario('Maria');

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance($usuarioMarta, 2100));
        $leilao->recebeLance(new Lance($usuarioJoao, 2200));
        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioMaria, 2400));
        $leilao->recebeLance(new Lance($usuarioMarta, 2500));
        $leilao->recebeLance(new Lance($usuarioJorge, 2600));
        $leilao->recebeLance(new Lance($usuarioMarta, 2700));
        $leilao->recebeLance(new Lance($usuarioJoao, 2800));
        $leilao->recebeLance(new Lance($usuarioMarta, 2900));
        $leilao->recebeLance(new Lance($usuarioMaria, 3000));
        $leilao->recebeLance(new Lance($usuarioMarta, 3100));
    }

    /**
     * 
     */
    public static function leilaoComQuatroLances()
    {
        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');
        $usuarioMaria = new Usuario('Maria');

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioJoao, 2000));
        $leilao->recebeLance(new Lance($usuarioMaria, 2500));
        $leilao->recebeLance(new Lance($usuarioJorge, 2250));

        return [
            'leilao-com-4-lances' => [4, $leilao, [2300, 2000, 2500, 2250]],
        ];
    }

    /**
     * 
     */
    public static function leilaoComDoisLances()
    {
        $usuarioJoao = new Usuario('Joao');
        $usuarioMarta = new Usuario('Marta');

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioJoao, 2000));

        return [
            'leilao-com-2-lances' => [2, $leilao, [2300, 2000]],
        ];
    }

    /**
     * 
     */
    public static function leilaoComLancesConsecutivos()
    {
        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance($usuarioJoao, 2000));
        $leilao->recebeLance(new Lance($usuarioJorge, 2250));
        $leilao->recebeLance(new Lance($usuarioJorge, 2300));
        $leilao->recebeLance(new Lance($usuarioMarta, 2500));

        return [
            'leilao-com-lances-consecutivos' => [3, $leilao, [2000, 2250, 2500]]
        ];
    }

    /**
     * 
     */
    public static function leilaoComSeisLancesDoMesmoUsuario()
    {
        $usuarioJoao = new Usuario('Joao');
        $usuarioJorge = new Usuario('Jorge');
        $usuarioMarta = new Usuario('Marta');
        $usuarioMaria = new Usuario('Maria');

        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->recebeLance(new Lance($usuarioMarta, 2100));
        $leilao->recebeLance(new Lance($usuarioJoao, 2200));
        $leilao->recebeLance(new Lance($usuarioMarta, 2300));
        $leilao->recebeLance(new Lance($usuarioMaria, 2400));
        $leilao->recebeLance(new Lance($usuarioMarta, 2500));
        $leilao->recebeLance(new Lance($usuarioJorge, 2600));
        $leilao->recebeLance(new Lance($usuarioMarta, 2700));
        $leilao->recebeLance(new Lance($usuarioJoao, 2800));
        $leilao->recebeLance(new Lance($usuarioMarta, 2900));
        $leilao->recebeLance(new Lance($usuarioMaria, 3000));
        $leilao->recebeLance(new Lance($usuarioMarta, 3100));

        return [
            'leilao-com-6-lances-do-mesmo-usuario' => [10, $leilao, [2100, 2200, 2300, 2400, 2500, 2600, 2700, 2800, 2900, 3000]],
        ];
    }
}
