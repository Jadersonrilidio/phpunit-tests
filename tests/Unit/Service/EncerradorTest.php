<?php

namespace Alura\Leilao\Tests\Unit\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    private Encerrador $encerrador;
    private MockObject|EnviadorEmail $enviadorEmail;
    private Leilao $leilaoFiat147;
    private Leilao $leilaoVariant;

    /**
     * 
     */
    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariant = new Leilao('Variant 0Km', new \DateTimeImmutable('10 days ago'));

        /**
         * @see Utilizamos Mocks ou dubles de teste para executar metodos
         * e funcoes que nao queremos que sejam executados de verdade.
         */
        $leilaoDao = $this->createMock(LeilaoDao::class);
        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);

        $leilaoDao->method('getNaoFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);

        $leilaoDao->method('getFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);

        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza');

        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    /**
     * 
     */
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $this->encerrador->encerra();

        $leiloes = [$this->leilaoFiat147, $this->leilaoVariant];

        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->getFinalizado());
        self::assertTrue($leiloes[1]->getFinalizado());
        self::assertEquals('Fiat 147 0Km', $leiloes[0]->getDescricao());
        self::assertEquals('Variant 0Km', $leiloes[1]->getDescricao());
    }

    /**
     * 
     */
    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $this->enviadorEmail
            ->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException(new \DomainException('erro ao enviar email'));

        $this->encerrador->encerra();
    }

    /**
     * 
     */
    public function testSoDeveEnviarEmailDeTerminoAposLeilaoFinalizado()
    {
        $this->enviadorEmail
            ->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                static::assertTrue($leilao->getFinalizado());
            });

        $this->encerrador->encerra();
    }
}
