# 🧪 How to Test Laravel Mailing - Step by Step

## ✅ Prerequisites

Make sure you have:
1. MySQL running (XAMPP MySQL started)
2. Test data in database (at least 1 patient and 1 doctor)
3. The project working

---

## 🔍 Quick Status Check

First, verify the mail configuration is correct:

```bash
cat .env | grep MAIL_
```

You should see:
```
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@hospital.local"
MAIL_FROM_NAME="Hospital Management System"
```

---

## 📊 Test 1: Confirmation Email (When Creating Appointment)

### Step 1: Start Laravel Server
```bash
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan serve
```

Wait until you see:
```
INFO  Server running on [http://127.0.0.1:8000].
```

### Step 2: Open NEW terminal and check logs live
```bash
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
Get-Content storage/logs/laravel.log -Tail 20 -Wait
```

This will show real-time log updates.

### Step 3: Create an Appointment

Open **Postman** (or use curl) and make this request:

```
POST http://127.0.0.1:8000/api/appointments
```

**Headers:**
```
Content-Type: application/json
```

**Body (Raw JSON):**
```json
{
  "patient_id": 1,
  "doctor_id": 1,
  "appointment_date": "2026-05-25 14:00:00",
  "status": "pending",
  "complaint": "Test confirmation email"
}
```

### Step 4: Check Response

You should see:
```json
{
  "message": "Appointment created successfully.",
  "data": {
    "id": 1,
    "patient_id": 1,
    "doctor_id": 1,
    ...
  }
}
```

### Step 5: Check Email in Logs

In the terminal where you ran `Get-Content ... -Wait`, you should see email logs like:

```
[2026-05-20 11:20:00] local.DEBUG: Message sent: "From: noreply@hospital.local
To: patient_email@domain.com
Subject: Konfirmasi Appointment - Hospital Management

Content here...
```

✅ **If you see this, confirmation email is working!**

---

## 🔔 Test 2: Status Change Email (When Updating Status)

### Step 1: Update Appointment Status

Make this request in Postman:

```
PUT http://127.0.0.1:8000/api/appointments/1
```

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "status": "confirmed"
}
```

### Step 2: Check Response

You should see:
```json
{
  "message": "Appointment updated successfully.",
  "data": {
    "id": 1,
    "status": "confirmed",
    ...
  }
}
```

### Step 3: Check Email in Logs

Look for:
```
Subject: Status Appointment Berubah - Confirmed
```

✅ **If you see this, status change email is working!**

---

## ⏰ Test 3: Reminder Email (Via Scheduler)

### Step 1: Create Appointment for TOMORROW

Open a new terminal and run:

```bash
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan tinker
```

You'll see:
```
Psy Shell v0.11.x -- PHP 8.x.x
>
```

Copy and paste this (one line at a time):

```php
$tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
echo "Tomorrow: " . $tomorrow . "\n";
$appointment = App\Models\Appointment::create([
    'patient_id' => 1,
    'doctor_id' => 1,
    'appointment_date' => $tomorrow,
    'status' => 'pending',
    'complaint' => 'Test reminder email for tomorrow'
]);
echo "Created appointment ID: " . $appointment->id . "\n";
exit
```

You should see:
```
Tomorrow: 2026-05-21
Created appointment ID: 2
```

### Step 2: Run the Reminder Command

```bash
php artisan appointments:send-reminders
```

You should see output like:

```
🔄 Starting appointment reminder task...
✅ Reminder sent to patient_email@domain.com for Dr. Doctor Name
📊 Task completed: 1 sent, 0 failed.
```

### Step 3: Check Email in Logs

Look for:
```
Subject: Reminder Appointment - Besok Jam
```

✅ **If you see this, reminder email is working!**

---

## 📋 Complete Testing Checklist

Use this checklist to verify everything:

```
TEST 1: Confirmation Email
✅ Created appointment via POST /api/appointments
✅ Got 201 response with appointment data
✅ Email appears in logs with subject "Konfirmasi Appointment"
✅ Email contains doctor name, date, time, complaint

TEST 2: Status Change Email
✅ Updated appointment status via PUT /api/appointments/1
✅ Got 200 response with updated appointment
✅ Email appears in logs with subject "Status Appointment Berubah"
✅ Email shows old status → new status

TEST 3: Reminder Email
✅ Created appointment for tomorrow with php artisan tinker
✅ Ran php artisan appointments:send-reminders
✅ Command showed "1 sent, 0 failed"
✅ Email appears in logs with subject "Reminder Appointment"
✅ Email contains reminder message and appointment details
```

---

## 🔍 Troubleshooting

### ❌ Problem: "SQLSTATE Connection refused"
**Solution:** MySQL is not running. Start XAMPP and start MySQL service.

### ❌ Problem: "Patient/Doctor not found"
**Solution:** Database doesn't have test data. Use:
```bash
php artisan tinker
App\Models\Patient::first();
App\Models\Doctor::first();
exit
```

If empty, you need to create test data or run seeders.

### ❌ Problem: No email appears in logs
**Solution:** Check:
1. Is MAIL_MAILER=log in .env? ✅
2. Are storage/logs/ folder permissions correct?
3. Is Laravel server actually running?
4. Check full error in logs:
   ```bash
   Get-Content storage/logs/laravel.log | tail -100
   ```

### ❌ Problem: "Call to undefined method $patient->email"
**Solution:** The patient might not have an email. Check:
```bash
php artisan tinker
$patient = App\Models\Patient::find(1);
dd($patient);
exit
```

Look for the email field in the output.

---

## 📱 What You Should See in Logs

### Email 1: Confirmation
```
From: noreply@hospital.local
To: patient_email@domain.com
Subject: Konfirmasi Appointment - Hospital Management

Content:
Halo Patient Name,

Terima kasih telah melakukan booking appointment...
```

### Email 2: Status Change
```
From: noreply@hospital.local
To: patient_email@domain.com
Subject: Status Appointment Berubah - Confirmed

Content:
Halo Patient Name,

Status appointment Anda telah berubah menjadi Confirmed
```

### Email 3: Reminder
```
From: noreply@hospital.local
To: patient_email@domain.com
Subject: Reminder Appointment - Besok Jam 2026-05-21

Content:
Halo Patient Name,

Kami ingin mengingatkan Anda bahwa memiliki appointment besok...
```

---

## 🚀 Quick Copy-Paste Testing

### Terminal 1: Start Server
```bash
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan serve
```

### Terminal 2: Watch Logs
```bash
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
Get-Content storage/logs/laravel.log -Tail 20 -Wait
```

### Terminal 3: Run Tests

**Test 1 - Create Appointment:**
```bash
curl -X POST http://127.0.0.1:8000/api/appointments \
  -H "Content-Type: application/json" \
  -d '{
    "patient_id": 1,
    "doctor_id": 1,
    "appointment_date": "2026-05-25 14:00:00",
    "status": "pending",
    "complaint": "Test confirmation"
  }'
```

**Test 2 - Update Status:**
```bash
curl -X PUT http://127.0.0.1:8000/api/appointments/1 \
  -H "Content-Type: application/json" \
  -d '{"status": "confirmed"}'
```

**Test 3 - Send Reminders:**
```bash
cd C:\xampp\htdocs\hospital-management-system-Laravelovers
php artisan appointments:send-reminders
```

---

## ✅ Success Criteria

You'll know mailing is working when:

1. ✅ Confirmation email appears when creating appointment
2. ✅ Status change email appears when updating status
3. ✅ Reminder command runs without errors
4. ✅ All emails appear in `storage/logs/laravel.log`
5. ✅ Email subjects are in Indonesian (Konfirmasi, Status, Reminder)

---

## 📝 Next Steps After Testing

Once all tests pass:

1. ✅ Commit to GitHub
2. ✅ Document what you tested
3. ✅ Show logs as evidence
4. ✅ Consider this feature COMPLETE

**Grade Component**: Mailing (10% of final grade) ✅

---

**Last Updated**: 2026-05-20
