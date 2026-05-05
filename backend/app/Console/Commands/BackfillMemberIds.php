<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BackfillMemberIds extends Command
{
    protected $signature = 'krwwf:backfill-member-ids';
    protected $description = 'Assign missing member_id values to existing users using KRWWF-mmyy-XXXX format.';

    public function handle(): int
    {
        $users = User::whereNull('member_id')->orWhere('member_id', '')->get();
        $this->info('Found ' . $users->count() . ' users without member_id.');

        foreach ($users as $user) {
            $now = now();
            $prefix = sprintf('KRWWF-%02d%02d-', $now->month, $now->year % 100);
            $count = User::where('member_id', 'like', $prefix . '%')->count();
            $sequence = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
            $user->member_id = $prefix . $sequence;
            $user->save();
            $this->info("Assigned {$user->member_id} to user id={$user->id}");
        }

        $this->info('Backfill complete.');
        return 0;
    }
}

