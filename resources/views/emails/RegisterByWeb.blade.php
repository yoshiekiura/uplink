<div style="background-color: #f8f7f7;color: #555;font-family: Arial;">
    <div style="background: #02A9F1;text-align: center;">
        <img src="{{ asset('images/uplink-white.png') }}" height="150">
    </div>
    <div style="padding: 35px;text-align: center;">
        <div style="display: inline-block;text-align: left;max-width: 450px;background: #fff;border-radius: 6px;border: 1px solid #ddd;padding: 15px;line-height: 28px;">
            <p>Halo, {{ $user->name }}</p>
            <p>Anda telah terdaftar di Uplink.id dan sekarang Anda dapat mulai branding campaign seperti menambahkan link, video, atau menjual produk baik fisik secara digital melalui aplikasi yang bisa Anda download melalui Google Play</p>

            <div style="text-align: center;width: 100%;margin: 30px 0px;">
                <a 
                    href="https://uplink.id/app" target="_blank"
                    style="text-decoration: none;background: #02a9f1;color: #fff;padding: 15px 35px;border-radius: 6px;">
                        Download App
                </a>
            </div>

            <p>Silahkan login menggunakan email dan password yang sebelumnya Anda gunakan untuk mendaftar</p>

            <p>
                Terima kasih,<br />
                Tim Uplink.id
            </p>
        </div>
    </div>
    <div style="background: #fff;text-align: center;padding: 30px 0px;color: #aaa;border: 1px solid #f8f7f7;width: 100%;">
        <div>Copyright Uplink.id {{ date('Y') }}</div>
        <div style="font-size: 12px;margin-top: 15px;">All Rights Reserved</div>
    </div>
</div>