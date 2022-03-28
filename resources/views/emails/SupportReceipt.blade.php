@php
    $url = "";
@endphp
<div style="background: #f8f7f7;font-family: Arial;">
    <div style="background: #02A9F1;text-align: center;">
        <img src="{{ asset('images/uplink-white.png') }}" height="150">
    </div>
    <div style="padding: 30px;color: #666;line-height: 28px;">
        @if ($support->custom_message != null)
            <p>{{ $support->custom_message }}</p>
        @else
            Terima kasih telah mendukung <b>{{ $user->name }}</b> untuk {{ $support->button_text }} {{ $support->stuff }}
        @endif
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