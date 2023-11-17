<?php

namespace Tests\Feature\Trait;

trait PrepareForTestTrait
{
    protected function setUp(): void
    {
        parent::setUp();
        session(['user' => ['id' => 1, 'name' => '1']]);
    }
}
