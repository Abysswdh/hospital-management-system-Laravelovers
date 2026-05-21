# Laravel Mailing - Quick Reference

**Note**: It's **Laravel Mailing**, not Node mailing. Laravel has built-in email support.

---

## 🚀 Quick Start (5 Steps)

### Step 1: Configure .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@hospital.local"
MAIL_FROM_NAME="Hospital Management"
```

### Step 2: Create Mailable Classes
```bash
# Create 3 mailable classes
php artisan make:mail AppointmentConfirmationMail --markdown
php artisan make:mail AppointmentReminderMail --markdown
php artisan make:mail AppointmentStatusChangedMail --markdown
```

### Step 3: Create Email Templates
```bash
# Files will be in: resources/views/emails/
- appointment-confirmation.blade.php
- appointment-reminder.blade.php
- appointment-status-changed.blade.php
```

### Step 4: Send from Controller
```php
use App\Mail\AppointmentConfirmationMail;
use Illuminate\Support\Facades\Mail;

Mail::to($patient->email)->send(
    new AppointmentConfirmationMail($appointment)
);
```

### Step 5: Create Scheduler for H-1 Reminders
```bash
php artisan make:command SendAppointmentReminders
```

Update `app/Console/Kernel.php`:
```php
$schedule->command('appointments:send-reminders')->dailyAt('08:00');
```

---

## 📧 Email Types Required

| Email | Trigger | Content |
|-------|---------|---------|
| **Confirmation** | When appointment created | Doctor, date, time, queue # |
| **Reminder H-1** | 1 day before appointment | Reminder to come early |
| **Status Change** | When status changes | New status (confirmed/cancelled/completed) |

---

## 🔧 Commands Cheat Sheet

```bash
# Test email immediately
php artisan appointments:send-reminders

# Send test email from route
# Add to routes/api.php and visit URL

# Check logs (for MAIL_MAILER=log)
tail -f storage/logs/laravel.log

# Run queue worker (if using queues)
php artisan queue:work
```

---

## 📋 Files to Create

```
app/Mail/
├── AppointmentConfirmationMail.php
├── AppointmentReminderMail.php
└── AppointmentStatusChangedMail.php

app/Console/Commands/
└── SendAppointmentReminders.php

resources/views/emails/
├── appointment-confirmation.blade.php
├── appointment-reminder.blade.php
└── appointment-status-changed.blade.php
```

---

## ✅ Checklist

- [ ] Setup .env with mail credentials
- [ ] Create 3 Mailable classes
- [ ] Create 3 email templates
- [ ] Update AppointmentController to send emails
- [ ] Create SendAppointmentReminders command
- [ ] Configure scheduler in Kernel.php
- [ ] Test with Mailtrap or logs
- [ ] Verify all 3 email types work

---

## 🎯 PDF Requirements Met

✅ Konfirmasi booking appointment  
✅ Email reminder H-1 (Scheduler)  
✅ Email status berubah  
✅ Separate Mailable classes  
✅ Clean Blade templates  

**Full guide**: See `MAILING_IMPLEMENTATION.md`
