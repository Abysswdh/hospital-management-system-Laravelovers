# 🔧 TESTING THE MAILING IMPLEMENTATION

## Option 1: Use LOG Driver (Easiest - No Setup Required)

### Step 1: Update .env
```env
MAIL_MAILER=log
```

### Step 2: Create a Test Appointment

Use Postman to create an appointment:
```
POST http://127.0.0.1:8000/api/appointments
Content-Type: application/json

{
  "patient_id": 1,
  "doctor_id": 1,
  "appointment_date": "2026-05-25 14:00:00",
  "complaint": "Sakit kepala"
}
```

### Step 3: Check the Logs
```bash
# Option A: Follow logs in real-time
tail -f storage/logs/laravel.log

# Option B: View last log entries
cat storage/logs/laravel.log | tail -50
```

You should see the email output with:
- Email subject
- Recipient address
- Email body content

---

## Option 2: Use Mailtrap.io (Professional Testing)

### Step 1: Sign Up (Free)
1. Go to https://mailtrap.io
2. Create free account
3. Create new Inbox
4. Copy SMTP credentials

### Step 2: Update .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username_from_mailtrap
MAIL_PASSWORD=your_password_from_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@hospital.local"
MAIL_FROM_NAME="Hospital Management"
```

### Step 3: Create Test Data in Laravel Tinker
```bash
# Open Tinker shell
php artisan tinker

# Create a patient (if not exists)
$patient = App\Models\Patient::firstOrCreate([
    'id' => 1
], [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '081234567890',
    'date_of_birth' => '1990-01-01'
]);

# Create a doctor (if not exists)
$doctor = App\Models\Doctor::firstOrCreate([
    'id' => 1
], [
    'name' => 'Dr. Smith',
    'specialization' => 'General Practitioner',
    'email' => 'doctor@hospital.local',
    'phone' => '081234567891'
]);

# Create appointment
$appointment = App\Models\Appointment::create([
    'patient_id' => 1,
    'doctor_id' => 1,
    'appointment_date' => '2026-05-25',
    'status' => 'pending',
    'complaint' => 'Sakit kepala'
]);

# Exit
exit
```

### Step 4: Check Mailtrap Dashboard
- Go back to https://mailtrap.io
- You should see the confirmation email in your Inbox
- You can preview HTML, see attachments, etc.

---

## Option 3: Test Scheduler Command

### Test the reminder command manually:
```bash
# This will send reminders for appointments tomorrow
php artisan appointments:send-reminders
```

### Create appointment for tomorrow to test:
```bash
php artisan tinker

$appointment = App\Models\Appointment::create([
    'patient_id' => 1,
    'doctor_id' => 1,
    'appointment_date' => Carbon\Carbon::tomorrow()->format('Y-m-d'),
    'status' => 'pending',
    'complaint' => 'Test reminder'
]);

exit
```

Then run:
```bash
php artisan appointments:send-reminders
```

---

## 📋 Testing Checklist

### Test 1: Confirmation Email (on Create)
- [ ] Create appointment via API
- [ ] Check logs or Mailtrap for email
- [ ] Verify contains: doctor name, date, time, complaint

### Test 2: Status Change Email (on Update)
- [ ] Update appointment status to "confirmed"
- [ ] Check logs or Mailtrap for email
- [ ] Verify shows old & new status

### Test 3: Scheduler Command
- [ ] Create appointment for tomorrow
- [ ] Run: `php artisan appointments:send-reminders`
- [ ] Check logs for reminder email

### Test 4: Scheduler Automation (Production)
- [ ] In production, set up cron job to run scheduler
- [ ] Verify cron runs daily
- [ ] Monitor email sending logs

---

## 🔍 Troubleshooting

### Emails not appearing in logs?
```bash
# Check if logs directory is writable
ls -la storage/logs/

# Check .env file
cat .env | grep MAIL_
```

### Connection issues with Mailtrap?
```bash
# Test connection
php artisan tinker

use Illuminate\Support\Facades\Mail;
Mail::to('test@example.com')->send(new App\Mail\TestMail());

exit
```

### Check if scheduler is working (production):
```bash
# List scheduled tasks
php artisan schedule:list

# Run scheduler
php artisan schedule:run
```

---

## 📧 Email Content Samples

### Confirmation Email Preview
```
Subject: Konfirmasi Appointment - Hospital Management

Halo John Doe,

Terima kasih telah melakukan booking appointment di Hospital Management System.

Detail Appointment Anda:
- Dokter: Dr. Smith (General Practitioner)
- Tanggal: 25-05-2026
- Jam: 14:00
- Status: Pending
- Keluhan: Sakit kepala

Harap Datang 15 Menit Lebih Awal
```

### Reminder Email Preview
```
Subject: Reminder Appointment Besok!

Halo John Doe,

Kami ingin mengingatkan Anda bahwa memiliki appointment besok...
[similar format]
```

### Status Changed Email Preview
```
Subject: Status Appointment Anda Telah Berubah

Status Sebelumnya: Pending
Status Saat Ini: Confirmed
[appointment details...]
```

---

## 🚀 Quick Start Command

Run this to test everything at once:
```bash
# 1. Switch to log driver
sed -i 's/MAIL_MAILER=.*/MAIL_MAILER=log/' .env

# 2. Start Laravel server
php artisan serve &

# 3. In another terminal, create appointment
php artisan tinker
$a = App\Models\Appointment::create([...]);
exit

# 4. Check logs
tail -f storage/logs/laravel.log
```
