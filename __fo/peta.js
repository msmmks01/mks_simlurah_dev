var map = L.map("map", {
  scrollWheelZoom: false, // Menonaktifkan zoom saat scroll
  zoomControl: false, // Menonaktifkan kontrol zoom bawaan
}).setView([-5.133, 119.412], 12); // Set tampilan awal sementara

// === Tambahkan kontrol zoom secara manual ===
L.control
  .zoom({
    position: "topright",
  })
  .addTo(map);

// === Variabel untuk menyimpan batas area GeoJSON ===
let geojsonBounds;

// === Base Layer ===
var streetLayer = L.tileLayer(
  "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
  {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors",
  }
).addTo(map);

var satelliteLayer = L.tileLayer(
  "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
  { maxZoom: 19, attribution: "Tiles &copy; Esri" }
);

// === Inisialisasi tampilan awal ===
let currentLayer = "Satellite";

// === Elemen-elemen yang dibutuhkan ===
const thumb = document.getElementById("thumb");
const thumbImg = document.getElementById("thumbImg");
const thumbText = document.getElementById("thumbText");

// === Fungsi untuk mengganti tampilan ===
function switchView() {
  if (currentLayer === "street") {
    map.removeLayer(streetLayer);
    map.addLayer(satelliteLayer);
    thumbImg.src = "__fo/assets/street-thumb.png";
    thumbText.textContent = "Street";
    currentLayer = "satellite";
  } else {
    map.removeLayer(satelliteLayer);
    map.addLayer(streetLayer);
    thumbImg.src = "__fo/assets/satelit-thumb.png";
    thumbText.textContent = "Satellite";
    currentLayer = "street";
  }
}

// === Event listener untuk thumbnail ===
thumb.addEventListener("click", switchView);

// === Tombol Focus ===
const focusButton = L.control({ position: "topright" });

focusButton.onAdd = function (map) {
  const div = L.DomUtil.create("div", "leaflet-bar leaflet-control");
  div.innerHTML = `<a class="leaflet-control-zoom-in leaflet-bar-part leaflet-bar-part-top" href="#" title="Kembali ke Posisi Awal" role="button" aria-label="Kembali ke Posisi Awal">
                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-focus-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r=".5" fill="currentColor" /><path d="M12 12m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M12 3l0 2" /><path d="M3 12l2 0" /><path d="M12 19l0 2" /><path d="M19 12l2 0" /></svg>
                      </a>`;
  div.onclick = function (e) {
    e.preventDefault(); // Mencegah perilaku default tautan
    if (geojsonBounds) {
      map.flyToBounds(geojsonBounds, { padding: [40, 40], duration: 2.5 });
    } else {
      alert("Data kelurahan belum dimuat!");
    }
  };
  return div;
};

// === Membuat container untuk thumbnail ===
const thumbContainer = L.DomUtil.create("div", "leaflet-control");
thumbContainer.appendChild(thumb);

// === Menambahkan thumbnail ke peta sebagai kontrol ===
const thumbControl = L.control({ position: "topright" });
thumbControl.onAdd = function (map) {
  return thumbContainer;
};

// === Tambahkan kontrol ke peta ===
focusButton.addTo(map);
thumbControl.addTo(map);

// === Ambil GeoJSON dan tampilkan data kecamatan 008 ===
fetch("__fo/73.71_kelurahan.geojson")
  .then((res) => res.json())
  .then((data) => {
    const filtered = {
      ...data,
      features: data.features.filter(
        (f) => f.properties.kd_kecamatan === geoloc
      ),
    };

    const layerGeojson = L.geoJSON(filtered, {
      style: {
        color: "#ff6600",
        weight: 1.5,
        fillColor: "#80ff84ff",
        fillOpacity: 0.6,
      },
      onEachFeature: (feature, layer) => {
        const p = feature.properties;
        layer.bindPopup(`
          <b>${p.kd_kelurahan}</b><br>
          <b>Kelurahan: ${p.nm_kelurahan}</b><br>
          Kecamatan: Ujung Tanah<br>
          
        `);
      },
    }).addTo(map);

    // === Zoom animasi ke area ===
    if (filtered.features.length > 0) {
      geojsonBounds = layerGeojson.getBounds(); // Simpan batas area
      map.flyToBounds(geojsonBounds, { padding: [40, 40], duration: 2.5 });
    }
  });
