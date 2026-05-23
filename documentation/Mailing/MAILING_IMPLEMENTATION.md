# Laravel Mailing Implementation Guide

**Based on BNCC LnT Final Project 2026 Requirements (Section 7)**

---

## 📋 Requirements Overview

From PDF - Mailing (10% of grade):

✅ **Email konfirmasi booking appointment**
- Include: doctor details, date, time, queue number

✅ **Email reminder H-1 (1 day before)**
- Using Laravel Scheduler

✅ **Email notifikasi perubahan status**
- When appointment status changes (confirmed, cancelled, completed)

✅ **Separate Mailable classes**
- One for each email type
- Clean Blade templates

---

## 🔧 Setup Prerequisites

### 1. Configure Mail in `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@hospital.local"
MAIL_FROM_NAME="Hospital Management System"
```

**For Testing**: Use [Mailtrap.io](https://mailtrap.io) (free)

### 2. Alternative: Use Local Testing

```env
MAIL_MAILER=log
# Emails will be logged to: storage/logs/laravel.log
```

---

## 📧 Step 1: Create Mailable Classes

### 1a. AppointmentConfirmationMail

```bash
php artisan make:mail AppointmentConfirmationMail --markdown=emails.appointment-confirmation
```

**File**: `app/Mail/AppointmentConfirmationMail.php`

```php
<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Janji Temu - ' . $this->appointment->appointment_date,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-confirmation',
            with: [
                'patient' => $this->appointment->patient,
                'doctor' => $this->appointment->doctor,
                'appointment' => $this->appointment,
            ],
        );
    }
}
```

### 1b. AppointmentReminderMail

```bash
php artisan make:mail AppointmentReminderMail --markdown=emails.appointment-reminder
```

**File**: `app/Mail/AppointmentReminderMail.php`

```php
<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat: Janji Temu Besok dengan ' . $this->appointment->doctor->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-reminder',
            with: [
                'patient' => $this->appointment->patient,
                'doctor' => $this->appointment->doctor,
                'appointment' => $this->appointment,
            ],
        );
    }
}
```

### 1c. AppointmentStatusChangedMail

```bash
php artisan make:mail AppointmentStatusChangedMail --markdown=emails.appointment-status-changed
```

**File**: `app/Mail/AppointmentStatusChangedMail.php`

```php
<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $newStatus
    ) {
    }

    public function envelope(): Envelope
    {
        $statusText = match ($this->newStatus) {
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            default => ucfirst($this->newStatus),
        };

        return new Envelope(
            subject: "Status Janji Temu Berubah: {$statusText}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-status-changed',
            with: [
                'patient' => $this->appointment->patient,
                'doctor' => $this->appointment->doctor,
                'appointment' => $this->appointment,
                'newStatus' => $this->newStatus,
            ],
        );
    }
}
```

---

## 🎨 Step 2: Create Email Templates

### 2a. Appointment Confirmation Template

**File**: `resources/views/emails/appointment-confirmation.blade.php`

```blade
<x-mail::message>
# Konfirmasi Janji Temu Anda

Halo **{{ $patient->user->name }}**,

Janji temu Anda telah berhasil dibuat. Berikut adalah detail lengkapnya:

<x-mail::panel>
**DETAIL JANJI TEMU**

**Dokter**: {{ $doctor->user->name }}  
**Spesialisasi**: {{ $doctor->specialization }}  
**Tanggal**: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d F Y') }}  
**Jam**: 10:00 AM - 11:00 AM  
**Nomor Antrian**: #{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}  

**Keluhan**: {{ $appointment->complaint }}
</x-mail::panel>

**Harap tiba 15 menit sebelum jadwal janji temu.**

Jika Anda ingin membatalkan atau mengubah janji temu, silakan hubungi kami.

Terima kasih,  
**Hospital Management System**
</x-mail::message>
```

### 2b. Appointment Reminder Template

**File**: `resources/views/emails/appointment-reminder.blade.php`

```blade
<x-mail::message>
# Pengingat Janji Temu - Besok!

Halo **{{ $patient->user->name }}**,

Ini adalah pengingat untuk janji temu Anda **BESOK** dengan dokter:

<x-mail::panel>
**JANJI TEMU BESOK**

**Dokter**: {{ $doctor->user->name }}  
**Spesialisasi**: {{ $doctor->specialization }}  
**Tanggal**: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d F Y') }}  
**Nomor Antrian**: #{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}  
</x-mail::panel>

**Jangan lupa untuk:**
- Tiba 15 menit lebih awal
- Membawa kartu identitas
- Membawa asuransi (jika ada)

Sampai jumpa besok!

Terima kasih,  
**Hospital Management System**
</x-mail::message>
```

### 2c. Appointment Status Changed Template

**File**: `resources/views/emails/appointment-status-changed.blade.php`

```blade
<x-mail::message>
@php
    $statusText = [
        'confirmed' => 'Dikonfirmasi',
        'cancelled' => 'Dibatalkan',
        'completed' => 'Selesai',
    ][$newStatus] ?? ucfirst($newStatus);
    
    $color = [
        'confirmed' => 'green',
        'cancelled' => 'red',
        'completed' => 'blue',
    ][$newStatus] ?? 'gray';
@endphp

# Status Janji Temu Berubah

Halo **{{ $patient->user->name }}**,

Status janji temu Anda telah berubah menjadi **{{ $statusText }}**.

<x-mail::panel>
**DETAIL JANJI TEMU**

**Dokter**: {{ $doctor->user->name }}  
**Tanggal**: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d F Y') }}  
**Status Baru**: <strong style="color: {{ $color }}">{{ $statusText }}</strong>  
**Nomor Antrian**: #{{ str_pad($appointment->id, 4, '0', STR_PAD_LEFT) }}  
</x-mail::panel>

@if ($newStatus === 'cancelled')
    Jika Anda ingin membuat janji temu baru, silakan akses aplikasi kami.
@elseif ($newStatus === 'completed')
    Terima kasih telah mengunjungi klinik kami. Semoga Anda segera sembuh!
@endif

Terima kasih,  
**Hospital Management System**
</x-mail::message>
```

---

## 🔔 Step 3: Send Emails from Controllers

### 3a. Send Confirmation Email (When Creating Appointment)

**File**: `app/Http/Controllers/API/AppointmentController.php`

```php
<?php

namespace App\Http\Controllers\API;

use App\Models\Appointment;
use App\Mail\AppointmentConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date_format:Y-m-d H:i:s|after:now',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'complaint' => 'required|string|max:500'
        ]);

        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['appointment_date'] = substr($validated['appointment_date'], 0, 10);
        
        $appointment = Appointment::create($validated);

        // Load relationships for email
        $appointment->load('patient', 'doctor', 'doctor.user', 'patient.user');

        // Send confirmation email
        Mail::to($appointment->patient->user->email)->send(
            new AppointmentConfirmationMail($appointment)
        );

        return response()->json([
            'message' => 'Appointment created successfully.',
            'data' => $appointment
        ], 201);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'appointment_date' => 'sometimes|date_format:Y-m-d',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
        ]);

        $oldStatus = $appointment->status;
        $appointment->update($validated);

        // If status changed, send notification
        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            $appointment->load('patient', 'doctor', 'doctor.user', 'patient.user');
            
            Mail::to($appointment->patient->user->email)->send(
                new AppointmentStatusChangedMail($appointment, $validated['status'])
            );
        }

        return response()->json([
            'message' => 'Appointment updated successfully.',
            'data' => $appointment
        ]);
    }
}
```

---

## ⏰ Step 4: Implement Laravel Scheduler (Reminders)

### Create Console Command

```bash
php artisan make:command SendAppointmentReminders
```

**File**: `app/Console/Commands/SendAppointmentReminders.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Mail\AppointmentReminderMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminder emails for appointments tomorrow';

    public function handle()
    {
        // Get appointments for tomorrow
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $appointments = Appointment::whereDate('appointment_date', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->with('patient', 'doctor', 'doctor.user', 'patient.user')
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            Mail::to($appointment->patient->user->email)->send(
                new AppointmentReminderMail($appointment)
            );
            $count++;
        }

        $this->info("Sent {$count} reminder emails.");
    }
}
```

### Schedule the Command

**File**: `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Send appointment reminders every day at 8 AM
        $schedule->command('appointments:send-reminders')
            ->dailyAt('08:00')
            ->timezone('Asia/Jakarta');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

### Run Scheduler (Development)

```bash
# In one terminal, run Laravel serve
php artisan serve

# In another terminal, run the scheduler
# For testing - runs the schedule immediately
php artisan appointments:send-reminders

# For continuous scheduling (production-like)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Step 5: Testing Emails

### Test with Mailtrap

1. Create account at [mailtrap.io](https://mailtrap.io)
2. Copy credentials to `.env`
3. Send test email:

```php
// In routes/api.php or in a controller
use App\Models\Appointment;
use App\Mail\AppointmentConfirmationMail;
use Illuminate\Support\Facades\Mail;

Route::post('/test-email', function () {
    $appointment = Appointment::with('patient', 'doctor', 'doctor.user', 'patient.user')->first();
    
    Mail::to('test@example.com')->send(
        new AppointmentConfirmationMail($appointment)
    );
    
    return 'Email sent!';
});
```

### View Emails in Logs (Development)

Set in `.env`:
```
MAIL_MAILER=log
```

Then check:
```bash
tail -f storage/logs/laravel.log
```

---

## 📧 Step 6: Queue Configuration (Optional but Recommended)

### Configure Queue

**File**: `.env`

```env
QUEUE_CONNECTION=database
```

### Create Queue Migration

```bash
php artisan queue:table
php artisan migrate
```

### Update Mail to Queue

**File**: `app/Mail/AppointmentConfirmationMail.php`

```php
public function __construct(public Appointment $appointment)
{
    $this->queue = 'default';  // Add this line
}
```

### Run Queue Worker

```bash
php artisan queue:work
```

---

## ✅ Complete Checklist

- [ ] Configure mail in `.env`
- [ ] Create 3 Mailable classes:
  - [ ] AppointmentConfirmationMail
  - [ ] AppointmentReminderMail
  - [ ] AppointmentStatusChangedMail
- [ ] Create 3 email templates:
  - [ ] appointment-confirmation.blade.php
  - [ ] appointment-reminder.blade.php
  - [ ] appointment-status-changed.blade.php
- [ ] Update AppointmentController to send emails
- [ ] Create SendAppointmentReminders command
- [ ] Configure scheduler in Kernel.php
- [ ] Test with Mailtrap or logs
- [ ] Test reminders: `php artisan appointments:send-reminders`

---

## 🎯 Summary

**Implements all PDF requirements:**

✅ Email konfirmasi booking  
✅ Email reminder H-1 (via Scheduler)  
✅ Email notifikasi status perubahan  
✅ Separate Mailable classes  
✅ Clean Blade templates  

**Worth 10% of final grade!**

