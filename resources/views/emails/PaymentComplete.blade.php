@php
    $sumQuantity = 0;
    function toIdr($angka) {
        return 'Rp. '.strrev(implode('.',str_split(strrev(strval($angka)),3)));
    }
@endphp
<div style="background: #f8f7f7;font-family: Arial;">
    <div style="background: #02A9F1;text-align: center;">
        <img src="{{ asset('images/uplink-white.png') }}" height="150">
    </div>
    <div style="padding: 30px;color: #666;line-height: 28px;">
        <div style="text-align: center;font-weight: bold;font-size: 35px;color: #333;margin: 60px 20px;">#123123</div>
        <div style="display: flex;flex-direction: row;flex-wrap: wrap;font-size: 20px;margin-bottom: 30px;">
            <div style="width: 50%;">
                <b>Riyan Satria</b>
                <p style="font-size: 15px;line-height: 25px;">Jalan Bumiarjo V No. 11, Surabaya</p>
                <p style="font-size: 15px;line-height: 18px;">0881036183076</p>
            </div>
            <div style="width: 50%;text-align: right;">
                <b>Riyan Satria</b>
                <p style="font-size: 15px;line-height: 25px;">Jalan Bumiarjo V No. 11, Surabaya</p>
                <p style="font-size: 15px;line-height: 18px;">0881036183076</p>
            </div>
        </div>
        <table style="width: 100%;text-align: left;font-size: 16px;">
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid #666;padding: 15px;">Produk</th>
                    <th style="border-bottom: 1px solid #666;padding: 15px;">Qty</th>
                    <th style="border-bottom: 1px solid #666;padding: 15px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart->details as $item)
                    <tr>
                        <td style="border-bottom: 1px solid #999;padding: 15px;">{{ $item->product->{$classToCall[$item->product_type]['name_column']} }}</td>
                        <td style="border-bottom: 1px solid #999;padding: 15px;">{{ $item->quantity }}</td>
                        <td style="border-bottom: 1px solid #999;padding: 15px;">{{ toIdr($item->total_price) }}</td>
                    </tr>
                @endforeach
                @if ($cart->voucher_id != null)
                    @php
                        $voucher = $cart->voucher;
                        if ($voucher->discount_type == 'percentage') {
                            $calculate = ($voucher->amount / 100) * $cart->grand_total;
                        }
                    @endphp
                    <tr>
                        <th style="border-bottom: 1px solid #999;padding: 15px;" colspan="2">Discount</td>
                        <td style="border-bottom: 1px solid #999;padding: 15px;">-{{ $voucher->discount_type == 'percentage' ? $voucher->amount.'%' : toIdr($voucher->amount) }}</td>
                    </tr>
                @endif
                <tr>
                    <th style="border-bottom: 1px solid #999;padding: 15px;">Total</td>
                    <th style="border-bottom: 1px solid #999;padding: 15px;">{{ $cart->details->count() }}</td>
                    <th style="border-bottom: 1px solid #999;padding: 15px;">{{ toIdr($cart->grand_total) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="background: #fff;text-align: center;padding: 30px 0px;color: #aaa;border: 1px solid #f8f7f7;">
        <div>Copyright Uplink.id {{ date('Y') }}</div>
        <div style="font-size: 12px;margin-top: 15px;">All Rights Reserved</div>
    </div>
</div>