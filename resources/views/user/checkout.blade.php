<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dailyhotels.id - Lorem ipsum dolor sit amet</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
</head>
<body>
    
<header>
    <div class="bagi lebar-20 rata-tengah" id="back">
        <span class="material-icons-outlined">west</span>
    </div>
    <div class="bagi lebar-60 title">
        Checkout
    </div>
</header>

<div class="Content">
    <div class="bg-putih bayangan-5 smallPadding Shipping">
        <div class="wrap">
            <div class="bagi lebar-10">
                <span class="material-icons-outlined teks-primer">place</span>
            </div>
            <div class="bagi lebar-80">
                <h3 class="mt-0">Shipping Address</h3>
                <div class="Address">
                    <span id="name">Riyan Satria</span> - <span id="phone">085159772902</span>
                    <br />
                    <span id="road">Jalan Bumiarjo 5 No. 11, Wonokromo, Surabaya</span>
                </div>
            </div>
            <div class="bagi lebar-10">
                <span class="material-icons-outlined teks-primer">chevron_right</span>
            </div>
        </div>
    </div>
    
    <div class="bg-putih bayangan-5 smallPadding Product mt-3">
        <div class="border-bottom p-2">
            <h4 class="m-0">Order item(s)</h4>
        </div>

        <div class="smallPadding">
            <div class="wrap">
                <div class="wrap">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="item mb-2">
                            <div class="bagi lebar-30">
                                <div class="cover rounded squarize" bg-image="{{ asset('images/hotel.jpg') }}"></div>
                            </div>
                            <div class="bagi lebar-70">
                                <div class="wrap mt-0">
                                    <div class="title">Adidas RUNNING Sepatu Lite Racer 2.0 Wanita Hitam EG3291</div>
                                    <div class="mt-1">
                                        <div class="bagi bagi-2">
                                            <div class="price">Rp 850.000</div>
                                        </div>
                                        <div class="bagi bagi-2 qty rata-tengah">
                                            <div class="bagi lebar-25 btn teks-primer material-icons-outlined" id="decrease">remove</div>
                                            <div class="bagi lebar-50">
                                                <input type="text" class="box" id="qty" min="1" max="5" value="1">
                                            </div>
                                            <div class="bagi lebar-25 btn teks-primer material-icons-outlined" id="increase">add</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="bg-putih bayangan-5 smallPadding Shipment mt-3">
        <div class="border-bottom p-2">
            <h4 class="m-0">Shipping Options</h4>
        </div>
    </div>

    <div class="wrap">
        <div class="bg-putih rounded bayangan-5 smallPadding">
            <div class="wrap">
                <h4 class="border-bottom pt-1 pb-1 m-0">Notes</h4>
                <textarea name="notes" id="notes" class="box"></textarea>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/base.js') }}"></script>

</body>
</html>