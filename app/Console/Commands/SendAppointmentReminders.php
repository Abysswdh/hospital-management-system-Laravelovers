<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for appointments happening tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting appointment reminder task...');

        // Get appointments for tomorrow
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $appointments = Appointment::whereDate('appointment_date', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->with('patient', 'doctor')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('ℹ️ No appointments found for tomorrow.');
            return 0;
        }

        $sent = 0;
        $failed = 0;

        foreach ($appointments as $appointment) {
            try {
                Mail::to($appointment->patient->email)->send(
                    new AppointmentReminderMail($appointment)
                );
                $this->info("✅ Reminder sent to {$appointment->patient->email} for Dr. {$appointment->doctor->name}");
                $sent++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to send reminder to {$appointment->patient->email}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("📊 Task completed: {$sent} sent, {$failed} failed.");

        return 0;
    }
}
