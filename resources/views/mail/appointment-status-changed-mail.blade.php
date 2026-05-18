<x-mail::message>
# Status Appointment Anda Telah Berubah

Halo **{{ $appointment->patient->name }}**,

Status appointment Anda telah diperbarui. Silakan lihat detail berikut:

## Status Appointment:

| Item | Keterangan |
|------|-----------|
| **Status Sebelumnya** | {{ ucfirst($oldStatus) }} |
| **Status Saat Ini** | **{{ ucfirst($newStatus) }}** |
| **Dokter** | Dr. {{ $appointment->doctor->name }} |
| **Tanggal** | {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d-m-Y H:i') }} |

## Penjelasan Status:

@if($newStatus === 'confirmed')
✅ **Appointment Dikonfirmasi** - Appointment Anda sudah dikonfirmasi oleh dokter. Harap datang tepat waktu.
@elseif($newStatus === 'cancelled')
❌ **Appointment Dibatalkan** - Appointment ini telah dibatalkan. Hubungi kami untuk membuat appointment baru.
@elseif($newStatus === 'completed')
✔️ **Appointment Selesai** - Terima kasih telah melakukan konsultasi dengan kami. Semoga cepat sembuh!
@endif

Jika ada pertanyaan, silakan hubungi tim kami.

<x-mail::button :url="'http://127.0.0.1:8000/appointments/' . $appointment->id">
Lihat Detail Lengkap
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }} - Tim Manajemen Rumah Sakit
</x-mail::message>
