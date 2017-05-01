<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AjaxTest extends TestCase
{
    use WithoutMiddleware;

    protected $baseUrl = 'https://admin.tatcoop.dev';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->json('post', '/ajax/membershareholding', ['date' => '2017-4-1'])
            ->seeJson();
    }
}
