<x-mail::message>
# ⏰ Reminder Appointment Besok!

Halo **{{ $appointment->patient->name }}**,

Kami ingin mengingatkan Anda bahwa memiliki appointment besok dengan **Dr. {{ $appointment->doctor->name }}**.

## Detail Appointment Besok:

| Item | Keterangan |
|------|-----------|
| **Dokter** | Dr. {{ $appointment->doctor->name }} ({{ $appointment->doctor->specialization }}) |
| **Tanggal** | {{ \Carbon\Carbon::parse($appointment->appointment_date)->addDay(-1)->format('d-m-Y') }} (Besok) |
| **Jam** | {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }} |

## ⚠️ Persiapan Penting:

✅ **Datang tepat waktu, lebih baik 15 menit lebih awal**  
✅ **Bawa kartu identitas dan asuransi kesehatan**  
✅ **Persiapkan informasi kesehatan relevan**  
✅ **Hubungi kami jika ada perubahan**  

Jika Anda perlu membatalkan atau mengubah jadwal, silakan hubungi kami segera.

<x-mail::button :url="'http://127.0.0.1:8000/appointments/' . $appointment->id">
Lihat Detail Appointment
</x-mail::button>

Sampai jumpa besok!<br>
{{ config('app.name') }} - Tim Manajemen Rumah Sakit
</x-mail::message>
