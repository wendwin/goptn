<x-mail::message>
# 🎓 Reminder Jadwal SNPMB

Halo {{ $student->name }},  
Kamu mengaktifkan pengingat untuk jadwal SNBP di GoPTN.

Berikut event penting yang akan dimulai:

---

### 📌 {{ $item->name }}
- 🗓 Mulai: **{{ $item->start_date }}**
- ⏳ Status: **{{ ucfirst($item->status) }}**

---

<x-mail::button :url="'https://goptn.id/login'">
Lihat Detail di GoPTN
</x-mail::button>

Terus pantau jadwalmu agar tidak terlewat.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
