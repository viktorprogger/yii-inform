<?php

namespace Viktorprogger\YiisoftInform\Migration;

use Spiral\Migrations\Migration;

class OrmDefault9c9558978ef16764a228ea474310cf9c extends Migration
{
    protected const DATABASE = 'default';

    public function up(): void
    {
        $this->table('subscriber')
            ->addColumn('id', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255
            ])
            ->addColumn('telegram_chat_id', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255
            ])
            ->addColumn('settings_realtime', 'text', [
                'nullable' => true,
                'default'  => null
            ])
            ->addColumn('settings_summary', 'text', [
                'nullable' => true,
                'default'  => null
            ])
            ->setPrimaryKeys(["id", "telegram_chat_id"])
            ->create();

        $this->table('github_event')
            ->addColumn('id', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255
            ])
            ->addColumn('type', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255
            ])
            ->addColumn('repo', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255
            ])
            ->addColumn('payload', 'text', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('created', 'timestamp', [
                'nullable' => false,
                'default'  => null
            ])
            ->setPrimaryKeys(["id"])
            ->create();

        $this->table('repository')
            ->addColumn('name', 'string', [
                'nullable' => false,
                'default'  => null,
                'size'     => 255
            ])
            ->setPrimaryKeys(["name"])
            ->create();

        $this->table('tg_update')
            ->addColumn('id', 'integer', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('created_at', 'timestamp', [
                'nullable' => false,
                'default'  => null
            ])
            ->addColumn('contents', 'longText', [
                'nullable' => false,
                'default'  => null
            ])
            ->setPrimaryKeys(["id"])
            ->create();
    }

    public function down(): void
    {
        $this->table('tg_update')->drop();
        $this->table('repository')->drop();
        $this->table('github_event')->drop();
        $this->table('subscriber')->drop();
    }
}
