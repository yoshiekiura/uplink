@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
    $url = $event->platform_url;
    $date = Carbon::parse($event->date);
    $endDate = Carbon::parse($date->format('Y-m-d H:i:s'))->addHours($event->duration);
    if (strpos($url, "http") -1) {
        $url = "http://".$url;
    }
    $platform = $event->platform == "other" ? "Online Platform" : ucwords($event->platform);
@endphp
<div style="background: #f8f7f7;font-family: Arial;">
    <div style="background: #02A9F1;text-align: center;">
        <img src="{{ asset('images/uplink-white.png') }}" height="150">
    </div>
    <div style="padding: 30px;color: #666;line-height: 28px;">
        @if ($event->custom_message != null)
            <p>{{ $event->custom_message }}</p>
        @else
            Terima kasih telah mendaftar pada event {{ $event->title }} yang berlangsung pada
            {{ $date->isoFormat('DD MMMM YYYY') }}. Dan berikut adalah detail dari event yang akan berlangsung
        @endif
        <p>
            "{{ $event->title }}"<br />
            Tanggal : <b>{{ $date->isoFormat('DD MMMM YYYY') }}</b><br />
            Waktu : <b>{{ $date->format('H:i') }} - {{ $endDate->format('H:i') }}</b><br />
            Tempat : <b>{{ $platform }}</b>
        </p>
    </div>
    <div style="text-align: center;width: 100%;margin: 30px 0px;">
        <a 
            href="{{ $url }}" target="_blank"
            style="text-decoration: none;background: #02a9f1;color: #fff;padding: 15px 35px;border-radius: 6px;">
                Join Event
        </a>
    </div>
    <div style="padding: 30px;color: #666;line-height: 28px;">
        <p>
            Terima kasih,<br />
            {{ $user->name }}
        </p>
    </div>
    <div style="background: #fff;text-align: center;padding: 30px 0px;color: #aaa;border: 1px solid #f8f7f7;">
        <div>Copyright Uplink.id {{ date('Y') }}</div>
        <div style="font-size: 12px;margin-top: 15px;">All Rights Reserved</div>
    </div>
</div>