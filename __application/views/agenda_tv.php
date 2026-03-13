<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Agenda Kelurahan</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:"Segoe UI",Arial,sans-serif;
    height:100vh;
    color:white;
    
    /* Background Image */
    background:url("<?= base_url('__assets/images/bck.jpg') ?>") center/cover no-repeat fixed;
    
    /* Overlay gelap biar teks jelas */
    position:relative;
}

body::before{
    content:"";
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.35);
    z-index:-1;
}

/* Layout */

.container{
    width:92%;
    margin:auto;
    padding-top:30px;
}

/* Header */

.header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:20px;
    padding: 40px;
    background:rgba(20,92,58,0.85);
    backdrop-filter:blur(10px);
}

/* Area logo + judul */
.title-area{
    display:flex;
    align-items:center;
    gap:18px;
}

/* Logo */
.logo{
    height:155px;
    width:auto;
}

.title{
    font-size:57px;
    font-weight:700;
    letter-spacing:1px;
}

.subtitle{
    font-size:40px;
    opacity:.9;
}

/* Clock */

.clock{
    text-align:right;
}

.time{
    font-size:57px;
    font-weight:700;
}

.date{
    font-size:40px;
    opacity:.9;
}

/* Glass Card */

.card{
    padding:20px;
    border-radius:16px;
    
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    
    border:1px solid rgba(255,255,255,0.25);
    box-shadow:0 10px 40px rgba(0,0,0,0.25);
}

/* Table */

table{
    width:100%;
    border-collapse:collapse;
    font-size:33px;
}

thead{
    background:rgba(25,135,84,0.85);
}

th,td{
    padding:18px;
}

th{
    text-align:left;
}

tbody tr{
    border-bottom:1px solid rgba(255,255,255,0.15);
}

tbody tr:nth-child(even){
    background:rgba(255,255,255,0.06);
}

/* Smooth transition */

tbody{
    transition:opacity .6s ease, transform .6s ease;
}

.fade-out{
    opacity:0;
    transform:translateY(20px);
}

/* Running Text */

.running-text{
    position:fixed;
    bottom:0;
    width:100%;
    background:rgba(20,92,58,0.85);
    backdrop-filter:blur(10px);
    font-size:28px;
    padding:12px 0;
    overflow:hidden;
    white-space:nowrap;
}

/* teks berjalan */
.marquee-text{
    display:inline-block;
    padding-left:100%;          /* mulai dari luar kanan */
    animation:marquee 20s linear infinite;
}

@keyframes marquee{
    from{
        transform:translateX(0);
    }
    to{
        transform:translateX(-100%);
    }
}

.cursor{
    display:inline-block;
    margin-left:6px;
    animation:blink 1s infinite;
}

@keyframes blink{
    0%,50%,100%{opacity:1;}
    25%,75%{opacity:0;}
}
.empty-row{
    opacity:.45;
    font-style:italic;
}
</style>
</head>

<body>
<div class="header">
    <div class="title-area">
        <img src="<?= base_url('__assets/images/logo-makassar.png') ?>" class="logo">
        
        <div>
            <div class="title">
                <span id="typingText"></span><span class="cursor">|</span>
            </div>
            <div class="subtitle">Kelurahan Sambung Jawa</div>
        </div>
    </div>

    <div class="clock">
        <div class="time" id="clock"></div>
        <div class="date" id="date"></div>
    </div>
</div>

<div class="container">



<div class="card">
<table>
<thead>
<tr>
<th>Tanggal</th>
<th>Waktu</th>
<th>Kegiatan</th>
<th>Lokasi</th>
</tr>
</thead>
<tbody id="agendaBody"></tbody>
</table>
</div>

</div>


<div class="running-text">
    <span class="marquee-text">
        📢 Jam Pelayanan Kelurahan: Senin - Jumat 08:00 - 15:00 WITA |
        Mohon hadir tepat waktu |
        Mari bersama menjaga kebersihan lingkungan
    </span>
</div>


<script>

/* DATA AGENDA */
const agenda = [
{tanggal:"05 Mei 2026", waktu:"09:00", kegiatan:"Rapat RT / RW", lokasi:"Aula"},
{tanggal:"08 Mei 2026", waktu:"08:00", kegiatan:"Posyandu Balita", lokasi:"RW 03"},
{tanggal:"12 Mei 2026", waktu:"07:30", kegiatan:"Kerja Bakti Lingkungan", lokasi:"RW 02"},
{tanggal:"15 Mei 2026", waktu:"10:00", kegiatan:"Pelatihan UMKM", lokasi:"Aula"},
{tanggal:"18 Mei 2026", waktu:"19:30", kegiatan:"Sosialisasi Program", lokasi:"Aula"},
{tanggal:"21 Mei 2026", waktu:"08:30", kegiatan:"Gotong Royong", lokasi:"RW 04"},
{tanggal:"25 Mei 2026", waktu:"07:00", kegiatan:"Senam Sehat", lokasi:"Lapangan"},
{tanggal:"28 Mei 2026", waktu:"09:00", kegiatan:"Rapat PKK", lokasi:"Balai Warga"}
]

const perPage = 6
let page = 0

function renderAgenda(){
    const body = document.getElementById("agendaBody")
    body.classList.add("fade-out")

    setTimeout(()=>{
        body.innerHTML=""

        // Hitung index berdasarkan page saat ini
        const start = page * perPage
        const end   = start + perPage
        const data  = agenda.slice(start, end)

        // Render data
        data.forEach(item=>{
            body.innerHTML += `
            <tr>
                <td>${item.tanggal}</td>
                <td>${item.waktu}</td>
                <td>${item.kegiatan}</td>
                <td>${item.lokasi}</td>
            </tr>`
        })

        // Tambah baris kosong biar selalu 5
        const emptyRows = perPage - data.length
        for(let i=0;i<emptyRows;i++){
            body.innerHTML += `
            <tr class="empty-row">
                <td>-</td><td>-</td><td>-</td><td>-</td>
            </tr>`
        }

        body.classList.remove("fade-out")

        // ✅ pindah halaman SETELAH render
        page++
        if(page >= Math.ceil(agenda.length / perPage)){
            page = 0
        }

    },500)
}

setInterval(renderAgenda,5000)
renderAgenda()


/* CLOCK */
function updateClock(){
    const now = new Date()
    const h = String(now.getHours()).padStart(2,'0')
    const m = String(now.getMinutes()).padStart(2,'0')
    const s = String(now.getSeconds()).padStart(2,'0')
    document.getElementById("clock").innerHTML = h+":"+m+":"+s

    const hari = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"]
    const bulan = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"]

    document.getElementById("date").innerHTML =
        hari[now.getDay()]+" "+now.getDate()+" "+bulan[now.getMonth()]+" "+now.getFullYear()
}

setInterval(updateClock,1000)
updateClock()

const text = "AGENDA KEGIATAN KELURAHAN"
const typingEl = document.getElementById("typingText")

let i = 0
let mode = "typing" // typing | pauseEnd | deleting | pauseStart

function typeLoop(){

    if(mode === "typing"){
        typingEl.textContent = text.substring(0, i)
        i++
        if(i > text.length){
            i = text.length
            mode = "pauseEnd"
            setTimeout(typeLoop, 1200)
            return
        }
    }

    else if(mode === "deleting"){
        typingEl.textContent = text.substring(0, i)
        i--
        if(i < 0){
            i = 0
            mode = "pauseStart"
            setTimeout(typeLoop, 500)
            return
        }
    }

    else if(mode === "pauseEnd"){
        mode = "deleting"
    }

    else if(mode === "pauseStart"){
        mode = "typing"
    }

    const speed = (mode === "deleting") ? 40 : 80
    setTimeout(typeLoop, speed)
}

typeLoop()

</script>

</body>
</html>