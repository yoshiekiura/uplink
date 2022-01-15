<style>
    .Introduction {
        position: fixed;
        top: -150%;left: 0px;right: 0px;bottom: auto;
        background: linear-gradient(180deg, #0285F1 0%, #00DEF3 77.08%);
        z-index: 3;
        color: #fff;
    }
    .Introduction.active { top: 0px;bottom: 0px; }
    .Introduction h2 {
        color: #fff;
        font-size: 45px;
        font-weight: 'Poppins','sans-serif';
        font-weight: 700;
        margin-bottom: 10px;
    }
    .Introduction p {
        font-weight: 'Poppins','sans-serif';
        font-weight: 600;
        font-size: 20px;
    }
    .Introduction .box {
        background: none;
        border: none;
        box-shadow: none;
        border-bottom: 2px solid #fff;
        border-radius: 0px;
        color: #fff;
        padding: 0px;
    }
    .Introduction .group {
        position: relative;
    }
    .Introduction .group label {
        position: absolute;
        top: 20px;left: 16px;
        transition: 0.4s;
    }
    .box:focus ~ label,.box:valid ~ label {
        top: -14px;left: 0px;
        font-size: 15px;
    }
    .Introduction button {
        width: 100%;
        margin-top: 25px;
        border-radius: 0px;
        font-family: 'Poppins';
        color: #00DEF3;
    }
    .Introduction button[type=button] {
        border: 2px solid #fff;
        color: #fff;
    }
</style>
<div class="Introduction">
    <div class="wrap">
        <h2>Halo...</h2>
        <p>Sebelum melanjutkan, kami ingin berkenalan dengan kamu dulu</p>

        <form action="#" class="mt-3">
            <div class="mt-2 group">
                <input type="text" class="box" name="nama" id="nama" required>
                <label for="name">Nama :</label>
            </div>
            <div class="mt-2 group">
                <input type="text" class="box" name="email" id="email" required>
                <label for="name">Email :</label>
            </div>

            <button class="bg-putih">SUBMIT</button>
            <button type="button" onclick="closeIntro()" class="lebar-100 teks-putih">Nanti saja</button>
        </form>
    </div>
</div>


<script>
    const sel = dom => document.querySelector(dom);
    sel(".Introduction form").onsubmit = function (e) {
        let name = sel(".Introduction form #nama").value;
        sel(".Introduction").classList.remove('active');
        e.preventDefault();
    }
    const closeIntro = () => {
        sel(".Introduction").classList.remove('active');
    }
</script>