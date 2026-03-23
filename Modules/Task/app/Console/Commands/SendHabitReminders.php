<?php

namespace Modules\Task\Console\Commands;

use Illuminate\Console\Command;
use Modules\Task\Models\Habit;
use Modules\Notification\Services\OneSignalService;

class SendHabitReminders extends Command
{
    protected $signature = 'task:send-habit-reminders';
    protected $description = 'Send daily habit reminders to users';

    public function handle(OneSignalService $oneSignalService)
    {
        $now = now()->format('H:i');
        
        $habits = Habit::where('daily_reminder_at', 'LIKE', $now . '%')->get();

        foreach ($habits as $habit) {
            $user = $habit->user;
            if ($user) {
                $oneSignalService->sendNotificationToUser(
                    [$user->id],
                    "Habit Reminder",
                    "Don't forget to complete your habit: {$habit->name}"
                );
            }
        }

        $this->info('Habit reminders sent successfully.');
    }
}
