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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/page.css') }}">
</head>
<body>
    
<div class="bege"></div>

<form class="SearchBox">
    <div class="bagi lebar-15">
        <button class="lebar-100 tinggi-50 p-0">
            <span class="material-icons-outlined">search</span>
        </button>
    </div>
    <div class="bagi lebar-80">
        <input type="text" name="q" class="box" placeholder="Cari sesuatu">
    </div>
</form>

<div class="container">
    <div class="Profile rata-tengah teks-putih">
        <div class="Image">
            <img src="{{ asset('images/icon.png') }}">
        </div>
        <h1>Dailyhotels.id</h1>
        <div class="bio">Indonesia Hospitality & Media Ecosystem</div>

        <div class="Socials mt-2">
            <div class="bagi bagi-4">
                <div class="wrap super">
                    <a href="#">
                        <div class="item bg-putih rounded-circle squarize use-lineHeight rata-tengah">
                            <img src="{{ asset('images/icon/instagram.png') }}">
                        </div>
                    </a>
                </div>
            </div>
            <div class="bagi bagi-4">
                <div class="wrap super">
                    <a href="#">
                        <div class="item bg-putih rounded-circle squarize use-lineHeight rata-tengah">
                            <img src="{{ asset('images/icon/whatsapp.png') }}">
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="Content smallPadding">
        <div class="wrap">
            <nav class="Menu">
                <a href="?item_type=links">
                    <div class="bagi bagi-6 item active">
                        <span class="material-icons" style="transform: rotate(-45deg)">link</span>
                    </div>
                </a>
                <a href="#">
                    <div class="bagi bagi-6 item">
                        <span class="material-icons">event</span>
                    </div>
                </a>
                <a href="#">
                    <div class="bagi bagi-6 item">
                        <span class="material-icons-outlined">shopping_bag</span>
                    </div>
                </a>
                <a href="#">
                    <div class="bagi bagi-6 item">
                        <span class="material-icons-outlined">inventory_2</span>
                    </div>
                </a>
                <a href="?item_type=support">
                    <div class="bagi bagi-6 item">
                        <span class="material-icons-outlined">volunteer_activism</span>
                    </div>
                </a>
                <a href="#">
                    <div class="bagi bagi-6 item">
                        <span class="material-icons-outlined">forum</span>
                    </div>
                </a>
            </nav>

            @foreach ($categories as $category)
                <div class="Categories">
                    <h3>{{ $category->name }}</h3>
                    <div class="Area bayangan-5 smallPadding rounded">
                        <div class="wrap">
                            @foreach ($category->{$type} as $item)
                                @php
                                    $meta = get_meta_tags($item->url);
                                @endphp
                                <a href="{{ $item->url }}" target="_blank">
                                    <div class="item">
                                        <div class="bagi lebar-30 d-none">
                                            <div class="cover squarize rounded" ></div>
                                        </div>
                                        <div class="bagi lebar-100">
                                            <div class="wrap mt-0">
                                                <h4>{{ $item->title }}</h4>
                                                <div class="description">{{ $meta['description'] }}</div>
                                                <button class="primer p-0 mt-1 tinggi-30 lebar-50 teks-kecil">Buka</button>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- <div class="Categories">
                <h3>Hotel</h3>
                <div class="Area bayangan-5 rounded smallPadding">
                    <div class="wrap">
                        @for ($a = 1; $a <= 3; $a++)
                            <div class="item">
                                <div class="bagi lebar-30">
                                    <div class="cover squarize rounded" bg-image="{{ asset('images/hotel.jpg') }}"></div>
                                </div>
                                <div class="bagi lebar-70">
                                    <div class="wrap mt-0">
                                        <h4>Adidas RUNNING Sepatu Lite Racer 2.0 Wanita Hitam EG3291</h4>
                                        <div class="description">
                                            Lorem ipsum dolor sit amet consectetur adipisicing elit eiusmod et dolore magna aliqua
                                        </div>
                                        <div class="mt-2 mb-0">
                                            <div class="bagi lebar-60">
                                                <div class="price">Rp 590.000</div>
                                            </div>
                                            <div class="bagi lebar-40">
                                                <button class="primer p-0 m-0 tinggi-30 lebar-100 teks-kecil" onclick="addToCart(this)">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@include('Components.Nav')
@include('Components.Intro')

<script src="{{ asset('js/base.js') }}"></script>
<script>
    const addToCart = btn => {
        btn.innerHTML = "<span class='material-icons-outlined' style='font-size: 12px'>done</span> Added";
    }
    document.addEventListener("keydown", e => {
        if (e.key == "x") {
            select(".Introduction").classList.toggle('active');
        }
    });
</script>

</body>
</html>