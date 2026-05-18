<x-mail::message>
# Konfirmasi Appointment Anda

Halo **{{ $appointment->patient->name }}**,

Terima kasih telah melakukan booking appointment di Hospital Management System.

## Detail Appointment Anda:

| Item | Keterangan |
|------|-----------|
| **Dokter** | Dr. {{ $appointment->doctor->name }} ({{ $appointment->doctor->specialization }}) |
| **Tanggal** | {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d-m-Y') }} |
| **Jam** | {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }} |
| **Status** | {{ ucfirst($appointment->status) }} |
| **Keluhan** | {{ $appointment->complaint }} |

## Informasi Penting:

📋 **Nomor Antrian**: Akan ditentukan saat hari H  
⏰ **Harap Datang 15 Menit Lebih Awal**  
📞 **Hubungi kami jika ada perubahan rencana**

Jika ada pertanyaan, silakan hubungi tim kami.

<x-mail::button :url="'http://127.0.0.1:8000/appointments/' . $appointment->id">
Lihat Detail Appointment
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }} - Tim Manajemen Rumah Sakit
</x-mail::message>
