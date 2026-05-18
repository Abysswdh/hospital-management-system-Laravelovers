# 📋 STEP-BY-STEP TESTING GUIDE

## 🎯 What You Need to Do (3 Simple Steps)

---

## STEP 1️⃣: Configure Email Driver

Choose ONE of these options:

### Option A: LOG DRIVER (⭐ Recommended for Quick Testing)

**This is the EASIEST option - no setup needed!**

1. Open `.env` file in project root
2. Find line: `MAIL_MAILER=`
3. Change it to: `MAIL_MAILER=log`
4. Save the file

That's it! Emails will be logged to `storage/logs/laravel.log`

---

### Option B: MAILTRAP DRIVER (Professional Testing)

**If you want to see emails in a nice dashboard:**

1. Go to https://mailtrap.io
2. Click "Sign Up" and create free account
3. Create new "Inbox"
4. Click "SMTP Settings"
5. Copy these 4 values:
   - Username
   - Password
   - Host (smtp.mailtrap.io)
   - Port (465)

6. Open `.env` file and update:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=paste_your_username_here
MAIL_PASSWORD=paste_your_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@hospital.local"
MAIL_FROM_NAME="Hospital Management"
```

7. Save the file

---

## STEP 2️⃣: Make Sure Server is Running

```bash
# Terminal 1 - Start Laravel server
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan serve
```

You should see:
```
Laravel development server started: http://127.0.0.1:8000
```

---

## STEP 3️⃣: Test Email Sending

### Test A: Create Appointment (Triggers Confirmation Email)

Open **Postman** and create appointment:

**Request:**
```
POST http://127.0.0.1:8000/api/appointments
```

**Headers:**
```
Content-Type: application/json
```

**Body (RAW JSON):**
```json
{
  "patient_id": 1,
  "doctor_id": 1,
  "appointment_date": "2026-05-25 14:00:00",
  "status": "pending",
  "complaint": "Sakit kepala"
}
```

**Expected Response:**
```json
{
  "message": "Appointment created successfully.",
  "data": {...}
}
```

### Check Email Was Sent

**If using LOG driver:**
```bash
# In another terminal, check logs
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
Get-Content storage/logs/laravel.log -Tail 50
```

You should see email log with:
- `To: john@example.com` (patient email)
- `Subject: Konfirmasi Appointment - Hospital Management`
- Email body content

**If using MAILTRAP:**
1. Go to https://mailtrap.io
2. Open your Inbox
3. You should see the confirmation email
4. Click to preview HTML

---

### Test B: Update Appointment Status (Triggers Status Change Email)

**Request:**
```
PUT http://127.0.0.1:8000/api/appointments/1
```

**Body:**
```json
{
  "status": "confirmed"
}
```

**Check the logs again** for status change email.

---

### Test C: Test Scheduler (Reminder Email)

Create appointment for **TOMORROW**:

```bash
# Open Laravel Tinker
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan tinker
```

```php
# In Tinker, paste this:
$appointment = App\Models\Appointment::create([
    'patient_id' => 1,
    'doctor_id' => 1,
    'appointment_date' => Carbon\Carbon::tomorrow()->format('Y-m-d'),
    'status' => 'pending',
    'complaint' => 'Test reminder'
]);

# Press Enter, then:
exit
```

Now test the command:
```bash
php artisan appointments:send-reminders
```

You should see:
```
✅ Reminder sent to john@example.com for Dr. Smith
📊 Task completed: 1 sent, 0 failed.
```

---

## ✅ TESTING CHECKLIST

After completing all 3 tests above, check these:

- [ ] **Test A - Confirmation Email**
  - Appointment created successfully
  - Email appears in logs/Mailtrap
  - Contains doctor name, date, time

- [ ] **Test B - Status Change Email**
  - Appointment status updated
  - Status change email appears
  - Shows old status → new status

- [ ] **Test C - Reminder Email**
  - Tomorrow appointment created
  - `php artisan appointments:send-reminders` runs successfully
  - Reminder email appears

---

## 📱 WHAT YOU SHOULD SEE

### Email 1: Confirmation (When Creating Appointment)
```
From: noreply@hospital.local
To: patient_email@domain.com
Subject: Konfirmasi Appointment - Hospital Management

Halo John Doe,

Terima kasih telah melakukan booking appointment...

Detail Appointment Anda:
- Dokter: Dr. Smith (General Practitioner)
- Tanggal: 25-05-2026
- Jam: 14:00
- Status: Pending
- Keluhan: Sakit kepala
```

### Email 2: Status Change (When Updating Appointment)
```
From: noreply@hospital.local
To: patient_email@domain.com
Subject: Status Appointment Anda Telah Berubah

Status Sebelumnya: Pending
Status Saat Ini: Confirmed
```

### Email 3: Reminder (Via Scheduler)
```
From: noreply@hospital.local
To: patient_email@domain.com
Subject: Reminder Appointment Besok!

⏰ Kami ingin mengingatkan Anda bahwa memiliki appointment besok...

Harap datang 15 menit lebih awal!
```

---

## 🆘 Troubleshooting

### ❌ Problem: "SMTP connect() failed"
**Solution:** Check Mailtrap credentials in .env are correct

### ❌ Problem: "No logs appear"
**Solution:** 
```bash
# Make sure log file is writable
icacls C:\xampp\htdocs\hospital-management-system-Laravelovers\storage\logs /grant Users:F
```

### ❌ Problem: Appointment creates but no email appears
**Solution:**
1. Check MAIL_MAILER value in .env
2. Check patient email address exists in database
3. Check for errors in `storage/logs/laravel.log`

### ❌ Problem: Patient email not found
**Solution:** 
```bash
php artisan tinker
App\Models\Patient::first();
# Check if patient with id 1 exists and has email
exit
```

---

## 🚀 Quick Copy-Paste Commands

### Start fresh test:
```bash
# Terminal 1: Start server
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan serve

# Terminal 2: Create test appointment
php artisan tinker
$a = App\Models\Appointment::create(['patient_id'=>1,'doctor_id'=>1,'appointment_date'=>'2026-05-25','complaint'=>'test']);
exit

# Terminal 3: Check logs
Get-Content storage/logs/laravel.log -Tail 50

# Or test scheduler
php artisan appointments:send-reminders
```

---

## 📝 Summary of What Each Email Does

| Email Type | When | Who Receives | What It Contains |
|-----------|------|-------------|-----------------|
| **Confirmation** | New appointment created | Patient | Doctor, date, time, complaint |
| **Reminder** | Day before appointment | Patient | Reminder to arrive early |
| **Status Change** | Status is updated | Patient | Old status → new status |

All emails are personalized and professional! ✨

---

## Next Steps After Testing

✅ All emails working → Continue to PHPUnit testing  
✅ Tests passing → Push to GitHub  
✅ Ready for submission!

