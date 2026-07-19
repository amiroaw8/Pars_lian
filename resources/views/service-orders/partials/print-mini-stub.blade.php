@php
    use App\Support\CompanyProfile;
    use Illuminate\Support\Str;

    $createdAt = $createdAt ?? (class_exists(\Morilog\Jalali\Jalalian::class)
        ? \Morilog\Jalali\Jalalian::fromCarbon($serviceOrder->created_at)->format('Y/m/d H:i')
        : $serviceOrder->created_at->format('Y/m/d H:i'));
@endphp
<div class="prt-mini-stub">
    <div class="prt-mini-col">
        <strong>پارس لیان</strong>
        <span class="prt-mini-phone prt-ltr">{{ CompanyProfile::PHONE }}</span>
    </div>
    <div class="prt-mini-col prt-mini-order">
        <span class="prt-mini-label">سفارش</span>
        <span class="prt-mini-id"><x-hash-ref :value="$serviceOrder->id" /></span>
    </div>
    <div class="prt-mini-col prt-mini-device">
        <span class="prt-mini-strong">{{ $serviceOrder->customer->name }}</span>
        <span>{{ $serviceOrder->device->type ?? '—' }} — {{ $serviceOrder->device->model ?? '—' }}</span>
        <span>{{ $createdAt }}</span>
    </div>
    <div class="prt-mini-col prt-mini-faults">
        <span><strong>ایراد:</strong> {{ Str::limit($serviceOrder->fault ?: '—', 100) }}</span>
        <span><strong>لوازم:</strong> {{ Str::limit($serviceOrder->accessories ?: '—', 80) }}</span>
    </div>
</div>
