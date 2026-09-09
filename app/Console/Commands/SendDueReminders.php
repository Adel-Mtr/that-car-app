<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Notifications\MaintenanceDueNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reminders:send-due')]
#[Description('Send vehicle reminders that have entered their notification window')]
class SendDueReminders extends Command
{
    public function handle(): int
    {
        $sent = 0;

        Reminder::query()
            ->whereNull('completed_at')
            ->whereNull('last_sent_at')
            ->where('due_at', '<=', now()->addDays(60))
            ->with(['user', 'vehicle'])
            ->chunkById(100, function ($reminders) use (&$sent): void {
                foreach ($reminders as $reminder) {
                    if ($reminder->due_at->isAfter(now()->addDays($reminder->lead_days))) {
                        continue;
                    }

                    $reminder->user->notify((new MaintenanceDueNotification($reminder))->afterCommit());
                    $reminder->update(['last_sent_at' => now()]);
                    $sent++;
                }
            });

        $this->info($sent.' '.str('reminder')->plural($sent).' sent.');

        return self::SUCCESS;
    }
}
