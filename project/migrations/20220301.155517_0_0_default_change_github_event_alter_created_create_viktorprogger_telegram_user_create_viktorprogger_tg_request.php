<?php

namespace Viktorprogger\YiisoftInform\Migration;

use Spiral\Migrations\Migration;

class OrmDefaultAeea5c0bccf2e49e536823d6c99eced8 extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('viktorprogger_telegram_user')
            ->addColumn('id', 'string', [
                'nullable' => false,
                'default' => null,
                'size' => 255,
            ])
            ->setPrimaryKeys(["id"])
            ->create();

        $this->table('tg_update')
            ->rename('viktorprogger_telegram_request');
    }

    public function down(): void
    {
        $this->table('viktorprogger_tg_request')->rename('tg_update');
        $this->table('viktorprogger_telegram_user')->drop();
    }
}
