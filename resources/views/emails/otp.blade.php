<div style="background: #f8f7f7;font-family: Arial;">
    <div style="background: #02A9F1;text-align: center;">
        <img src="{{ asset('images/uplink-white.png') }}" height="150">
    </div>
    <div style="padding: 30px;color: #666;line-height: 28px;">
        <p>Berikut adalah kode OTP Anda</p>
        <div style="text-align: center;font-weight: bold;font-size: 35px;margin: 50px 0px;">{{ $otp->code }}</div>
        <p>Harap segera masukkan kode ini pada layar ponsel Anda. Kode ini akan kedaluwarsa selama 30 menit berikutnya.</p>
    </div>
    <div style="background: #fff;text-align: center;padding: 30px 0px;color: #aaa;border: 1px solid #f8f7f7;">
        <div>Copyright Uplink.id {{ date('Y') }}</div>
        <div style="font-size: 12px;margin-top: 15px;">All Rights Reserved</div>
    </div>
</div>