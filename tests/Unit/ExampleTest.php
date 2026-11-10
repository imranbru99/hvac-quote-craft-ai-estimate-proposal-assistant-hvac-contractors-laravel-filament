<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_author_identity(): void
    {
        $this->assertSame('Imran Ahmed', 'Imran Ahmed');
        $this->assertSame('me@imrandev.bd', 'me@imrandev.bd');
    }
}
