@php
    $url = $product->url;
@endphp
<div style="background: #f8f7f7;font-family: Arial;">
    <div style="background: #02A9F1;text-align: center;">
        <img src="{{ asset('images/uplink-white.png') }}" height="150">
    </div>
    <div style="padding: 30px;color: #666;line-height: 28px;">
        @if ($product->custom_message != null)
            <p>{{ $product->custom_message }}</p>
        @else
            Terima kasih telah membeli produk <b>{{ $product->name }}</b>. Silahkan klik tombol berikut ini untuk mengunduh file Anda
        @endif
    </div>
    <div style="text-align: center;width: 100%;margin: 30px 0px;">
        <a 
            href="{{ $url }}" target="_blank"
            style="text-decoration: none;background: #02a9f1;color: #fff;padding: 15px 35px;border-radius: 6px;">
                Unduh File
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