<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    private static \PDO $pdo;
    private LeilaoDao $leilaoDao;

    /**
     * 
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$pdo = new \PDO('sqlite::memory:');

        self::$pdo->exec(
            'CREATE TABLE leiloes (
                id INTEGER primary key,
                descricao TEXT,
                finalizado BOOL,
                dataInicio TEXT
            );'
        );
    }

    /**
     * 
     */
    protected function setUp(): void
    {
        self::$pdo->beginTransaction();

        $this->leilaoDao = new LeilaoDao(self::$pdo);
    }

    /**
     * 
     */
    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }

    /**
     * @dataProvider leilaoFinalizadoENaoFinalizado
     */
    public function testBuscaLeiloesNaoFinalizados(Leilao $leilaoVariant, Leilao $leilaoFiat)
    {
        $this->leilaoDao->salva($leilaoVariant);
        $this->leilaoDao->salva($leilaoFiat);

        /** @var Leilao[] */
        $leiloes = $this->leilaoDao->getNaoFinalizados();

        $this->assertCount(1, $leiloes);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        $this->assertSame('Variant 0Km', $leiloes[0]->getDescricao());
    }

    /**
     * @dataProvider leilaoFinalizadoENaoFinalizado
     */
    public function testBuscaLeiloesFinalizados(Leilao $leilaoVariant, Leilao $leilaoFiat)
    {
        $this->leilaoDao->salva($leilaoVariant);
        $this->leilaoDao->salva($leilaoFiat);

        /** @var Leilao[] */
        $leiloes = $this->leilaoDao->getFinalizados();

        $this->assertCount(1, $leiloes);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        $this->assertSame('Fiat 147 0Km', $leiloes[0]->getDescricao());
    }

    /**
     * 
     */
    public function testStatusDoLeilaoDeveSerAlteradoAoAtualizar()
    {
        $leilao = new Leilao('Variant 0Km');

        $leilaoSalvo = $this->leilaoDao->salva($leilao);
        $leilaoSalvo->finaliza();

        $this->leilaoDao->atualiza($leilaoSalvo);

        $leiloes = $this->leilaoDao->getFinalizados();

        $this->assertCount(1, $leiloes);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        $this->assertSame('Variant 0Km', $leiloes[0]->getDescricao());
    }

    /**
     * 
     */
    public static function leilaoFinalizadoENaoFinalizado()
    {
        $leilaoVariant = new Leilao('Variant 0Km');

        $leilaoFiat = new Leilao('Fiat 147 0Km');
        $leilaoFiat->finaliza();

        return [
            [$leilaoFiat, $leilaoVariant]
        ];
    }
}
