<?php

namespace Tests\Unit\Services;

use App\Models\EvaluationRule;
use App\Models\Level;
use App\Services\SchoolLevelsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolLevelsServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var SchoolLevelsService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(SchoolLevelsService::class);
        $this->disableForeignKeys();
        Level::query()->truncate();
        EvaluationRule::query()->truncate();
    }

    public function tearDown()
    {
        $this->enableForeignKeys();
        parent::tearDown();
    }

    public function testRetornaRegrasAvaliacao()
    {
        $regraAvaliacaoFake = factory(EvaluationRule::class)->create();
        /** @var Level $level */
        $level = factory(Level::class)->create();

        $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);

        $evaluationRules = $this->service->getEvaluationRules($level->cod_serie);

        $this->assertCount(1, $evaluationRules);
        $this->assertEquals($regraAvaliacaoFake->all(), $evaluationRules->first()->all());
    }

    public function testSemRegrasDeveRetornarVazio()
    {
        $level = factory(Level::class)->create();
        $evaluationRules = $this->service->getEvaluationRules($level->cod_serie);
        $this->assertEmpty($evaluationRules);
    }

    public function testSemRegraAvaliacaoDeveRetornarFalse()
    {
        $result = $this->service->levelAllowDefineDisciplinePerStage(null, 2019);
        $this->assertFalse($result);

        $level = factory(Level::class)->create();
        $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2019);
        $this->assertFalse($result);

        $level = factory(Level::class)->create();
        $regraAvaliacaoFake = factory(EvaluationRule::class)->create([
            'definir_componente_etapa' => true,
        ]);
        $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
        $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2021);
        $this->assertFalse($result);
    }

    public function testRegraAvaliacaoPermiteDefinirComponentesEtapa()
    {
        $level = factory(Level::class)->create();
        $regraAvaliacaoFake = factory(EvaluationRule::class)->create([
            'definir_componente_etapa' => true,
        ]);

        $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
        $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2019);

        $this->assertTrue($result);

        $level = factory(Level::class)->create();
        $regraAvaliacaoFake = factory(EvaluationRule::class)->create([
            'definir_componente_etapa' => false,
        ]);

        $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
        $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2019);

        $this->assertFalse($result);
    }
}
