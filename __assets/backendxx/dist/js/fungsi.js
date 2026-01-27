$(document).ready(function () {
  // alert("sadas");
  $(".description-header").click(function (e) {
    // e.preventDefault();
    alert("tes");
  });
});

// On Klik dashboard Umur
function cetak_usia(type) {
  var url = host + "backoffice-cetak/laporan_cetak_usia?type=" + type;
  window.open(url, "_blank");
}

function cetak_kelamin(type) {
  var url = host + "backoffice-cetak/laporan_cetak_kelamin?type=" + type;
  window.open(url, "_blank");
}

function cetak_kawin(type) {
  var url = host + "backoffice-cetak/laporan_cetak_kawin?type=" + type;
  window.open(url, "_blank");
}

function getlurah() {
  console.log("#kelurahan_" + $("#acakk").val());
  var selectElement = document.getElementById("nip");

  var valuee = $("#kelurahan_" + $("#acakk").val()).val();

  selectElement.options.length = 0;
  var firstOption = document.createElement("option");
  firstOption.value = "";
  firstOption.text = " -- Pilih TTD Untuk Cetakan -- ";
  selectElement.appendChild(firstOption);
  $.ajax({
    url: host + "Backendxx/get_opsi_ttd/" + valuee,
    dataType: "json",
    cache: false,
    success: function (data) {
      console.log(data);

      // Menambahkan opsi baru dari data yang diterima
      data.forEach(function (item) {
        var option = document.createElement("option");
        option.value = item.id; // Asumsikan data memiliki properti 'id'
        option.text = item.txt; // Asumsikan data memiliki properti 'txt'
        selectElement.appendChild(option);
      });
    },
    error: function (xhr, status, error) {
      $.messager.alert(status, xhr.responseText, "error");
    },
  });
}

var grid_nya;

var today = new Date();

var dd = today.getDate();

var mm = today.getMonth() + 1; //January is 0!

var yyyy = today.getFullYear();

if (dd < 10) {
  dd = "0" + dd;
}

if (mm < 10) {
  mm = "0" + mm;
}

today = yyyy + "-" + mm + "-" + dd;

function genPieChart(divnya, tipe, judul, data) {
  Highcharts.chart(divnya, {
    chart: {
      plotBackgroundColor: null,

      plotBorderWidth: null,

      plotShadow: false,

      type: "pie",
    },

    title: {
      text: judul,
    },

    tooltip: {
      pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
    },

    plotOptions: {
      pie: {
        allowPointSelect: false,

        cursor: "pointer",

        dataLabels: {
          enabled: false,

          format: "<b>{point.name}</b> : {point.percentage:.1f} %",

          style: {
            width: "100px",
          },
        },

        showInLegend: true,
      },
    },

    series: data,

    exporting: {
      buttons: {
        contextButton: {
          menuItems: ["downloadPNG", "downloadJPEG"],
        },
      },
    },

    credits: {
      enabled: false,
    },
  });
}

// function genLineChart(divnya, tipe, judul, data) {
//   Highcharts.chart(divnya, {
//     chart: {
//       plotBackgroundColor: null,

//       plotBorderWidth: null,

//       plotShadow: false,

//       type: "line",
//     },

//     title: {
//       text: judul,
//     },

//     tooltip: {
//       pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
//     },

//     plotOptions: {
//       pie: {
//         allowPointSelect: false,

//         cursor: "pointer",

//         dataLabels: {
//           enabled: false,

//           format: "<b>{point.name}</b> : {point.percentage:.1f} %",

//           style: {
//             width: "100px",
//           },
//         },

//         showInLegend: true,
//       },
//     },

//     series: data,

//     exporting: {
//       buttons: {
//         contextButton: {
//           menuItems: ["downloadPNG", "downloadJPEG"],
//         },
//       },
//     },

//     credits: {
//       enabled: false,
//     },
//   });
// }

function genLineChart(divnya, tipe, judul, data) {

  var tooltipOpt = {
    pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>"
  };

  // 🔥 KHUSUS SKM
  if (tipe === 'skm') {
    tooltipOpt = {
      useHTML: true,
      formatter: function () {
        return '<b>' + this.y + '</b><br/>' + this.point.mutu;
      },
      positioner: function (labelWidth, labelHeight, point) {
        return {
          x: point.plotX + this.chart.plotLeft - labelWidth / 2,
          y: point.plotY + this.chart.plotTop - labelHeight - 10
        };
      }
    };
  }

  Highcharts.chart(divnya, {
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: "line",
    },

    title: { text: judul },

    tooltip: tooltipOpt, // ✅ PAKAI HASIL KONDISI

    plotOptions: {
      pie: {
        allowPointSelect: false,
        cursor: "pointer",
        dataLabels: {
          enabled: false,
          format: "<b>{point.name}</b> : {point.percentage:.1f} %",
          style: { width: "100px" },
        },
        showInLegend: true,
      },
    },

    series: data,

    exporting: {
      buttons: {
        contextButton: {
          menuItems: ["downloadPNG", "downloadJPEG"],
        },
      },
    },

    credits: { enabled: false },
  });
}


function genColumnChart(divnya, type, xxChart, yyChart, judul, pointformat) {
  var tooltipOpt = {
    pointFormat: '{series.name}: <b>{point.y}</b>'
  };

  // 🔥 KHUSUS SKM SAJA
  if (type === 'skm') {
    tooltipOpt = {
      useHTML: true,
      formatter: function () {
        return '<b>' + this.point.name + '</b><br>'
          + 'NRR Per Unsur : <b>' + this.y.toFixed(2) + '</b><br>'
          + '<b>' + (this.point.mutu || '') + '</b>';
      }
    };
  }
  Highcharts.chart(divnya, {
    chart: {
      type: "column",
    },

    title: {
      text: judul,
    },

    xAxis: {
      categories: xxChart,
    },

    scrollbar: {
      enabled: false,
    },

    rangeSelector: {
      selected: 1,
    },

    yAxis: [
      {
        min: 0,

        title: {
          text: "",
        },

        allowDecimals: false,
      },
      {
        title: {
          text: "",
        },

        opposite: true,
      },
    ],

    legend: {
      shadow: false,

      enabled: false,
    },

    tooltip: tooltipOpt,

    plotOptions: {
      column: {
        pointPadding: 0.1,

        borderWidth: 0,
      },
    },

    series: yyChart,
  });
}

//Fungsi warna untuk chart SKM
const skmPastelColors = [
  '#6FE3E1',
  '#8AD7F5',
  '#FFD97D',
  '#FFA8A8',
  '#B9E769',
  '#C3B7FF',
  '#FFB6E1',
  '#A7E8D8',
  '#FFCF9D'
];

function genColumnChartSKM(divnya, type, xxChart, yyChart, judul, pointformat) {
  Highcharts.chart(divnya, {
      chart: {
          type: 'column',
          backgroundColor: '#ffffff',
          height: 360
      },

      title: {
          text: judul,
          style: {
              color: '#333',
              fontSize: '16px',
              fontWeight: '600'
          }
      },

      xAxis: {
          categories: xxChart,
          labels: {
              rotation: -45,
              style: {
                  color: '#333',
                  fontSize: '12px'
              }
          }
      },

      yAxis: {
          min: 85,
          max: 100,
          title: { text: null },
          labels: {
              style: { color: '#333' }
          },
          gridLineColor: '#e6e6e6'
      },

      legend: {
          enabled: false
      },

      tooltip: {
          pointFormat: '<b>{point.y:.2f}%</b>'
      },

      plotOptions: {
        column: {
            borderRadius: 6,
            pointPadding: 0.02,   // 🔴 lebih rapat
            groupPadding: 0.02,   // 🔴 lebih rapat
            maxPointWidth: 46,    // 🔴 sedikit lebih tebal
            dataLabels: {
                enabled: true,
                format: '{y:.2f}%',
                style: {
                    color: '#333',
                    fontSize: '12px',
                    fontWeight: '600',
                    textOutline: 'none'
                }
            }
        }
      },

    series: yyChart.map(s => ({
        name: s.name,
        data: s.data.map((v, i) => ({
            y: v.y,
            color: skmPastelColors[i % skmPastelColors.length]
        }))
    })),


      credits: {
          enabled: false
      }
  });
}
// end fungsi SKM

function loadUrl(urls) {
  if (group_user == 2) {
    if (setting == "") {
      $.messager.alert(
        nama_apps,
        "Anda Belum Melakukan Setting Identitas Desa.",
        "warning"
      );

      $("#main-konten").empty().addClass("loading");

      $.get(host + "backoffice-form/identitas_desa", function (html) {
        $("#main-konten").html(html).removeClass("loading");
      });
    } else {
      $("#main-konten").empty().addClass("loading");

      $.get(urls, function (html) {
        $("#main-konten").html(html).removeClass("loading");
      }).fail(function (xhr) {
        $("#main-konten").html("").removeClass("loading");
        $.messager.alert(
          xhr.status,
          xhr.statusText +
            '<details>\
            <summary style="color:blue;cursor:pointer">Detail&#8595;</summary>\
            <p>' +
            xhr.responseText +
            "</p>\
          </details>",
          "error"
        );
      });
    }
  } else {
    $("#main-konten").empty().addClass("loading");

    $.get(urls, function (html) {
      $("#main-konten").html(html).removeClass("loading");
    }).fail(function (xhr) {
      $("#main-konten").html("").removeClass("loading");
      $.messager.alert(
        xhr.status,
        xhr.statusText +
          '<details>\
          <summary style="color:blue;cursor:pointer">Detail&#8595;</summary>\
          <p>' +
          xhr.responseText +
          "</p>\
        </details>",
        "error"
      );
    });
  }
}

function getClientHeight() {
  var theHeight;

  if (window.innerHeight) theHeight = window.innerHeight;
  else if (document.documentElement && document.documentElement.clientHeight)
    theHeight = document.documentElement.clientHeight;
  else if (document.body) theHeight = document.body.clientHeight;

  return theHeight;
}

var divcontainer;

function windowFormPanel(html, judul, width, height) {
  divcontainer = $("#jendela");

  $(divcontainer).unbind();

  $("#isiJendela").html(html);

  $(divcontainer).window({
    title: judul,

    width: width,

    height: height,

    autoOpen: false,

    top: Math.round(getClientHeight() / 2) - height / 2,

    left: Math.round(getClientWidth() / 2) - width / 2,

    modal: true,

    maximizable: false,

    minimizable: false,

    collapsible: false,

    closable: true,

    resizable: false,

    onBeforeClose: function () {
      $(divcontainer).window("close", true);

      //$(divcontainer).window("destroy",true);

      //$(divcontainer).window('refresh');

      return true;
    },
  });

  $(divcontainer).window("open");
}

function windowFormClosePanel() {
  $(divcontainer).window("close");

  //$(divcontainer).window('refresh');
}

var container;

function windowForm(html, judul, width, height) {
  container = "win" + Math.floor(Math.random() * 9999);

  $("<div id=" + container + "></div>").appendTo("body");

  container = "#" + container;

  $(container).html(html);

  $(container).css("padding", "5px");

  $(container).window({
    title: judul,

    width: width,

    height: height,

    autoOpen: false,

    maximizable: false,

    minimizable: false,

    collapsible: false,

    resizable: false,

    closable: true,

    modal: true,

    onBeforeClose: function () {
      $(container).window("close", true);

      $(container).window("destroy", true);

      return true;
    },
  });

  $(container).window("open");
}

function closeWindow() {
  $(container).window("close");

  $(container).html("");
}

function getClientWidth() {
  var theWidth;

  if (window.innerWidth) theWidth = window.innerWidth;
  else if (document.documentElement && document.documentElement.clientWidth)
    theWidth = document.documentElement.clientWidth;
  else if (document.body) theWidth = document.body.clientWidth;

  return theWidth;
}

function genGrid(modnya, divnya, lebarnya, tingginya, par1) {
  if (lebarnya == undefined) {
    lebarnya = getClientWidth() - 250;
  }

  if (tingginya == undefined) {
    tingginya = getClientHeight() - 450;
  }

  var kolom = {};

  var frozen = {};

  var judulnya;

  var param = {};

  var urlnya;

  var urlglobal = "";

  var url_detil = "";

  var post_detil = {};

  var fitnya;

  var klik = false;

  var doble_klik = false;

  var pagesizeboy = 50;

  var singleSelek = true;

  var nowrap_nya = true;

  var footer = false;

  var row_number = true;

  var paging = true;

  switch (modnya) {
    
    case "data_permohonan":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;
      nowrap_nya = false;
      frozen[modnya] = [
        {
          field: "status_data",
          title: "Proses",
          width: 100,
          halign: "center",
          align: "center",
          formatter: function (value, rowData, rowIndex) {
            return (
              "<button href=\"javascript:void(0)\" onClick=\"kumpulAction('permohonan','" +
              rowData.id +
              '\')" class="easyui-linkbutton" data-options="iconCls:\'icon-save\'">Proses</button>'
            );
          },
        },
        {
          field: "jenis_surat",
          title: "Jenis Surat",
          width: 230,
          halign: "center",
          align: "left",
        },
        {
          field: "nik",
          title: "NIK",
          width: 100,
          halign: "center",
          align: "left",
        },
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "kecamatan",
          title: "Kecamatan",
          width: 150,
          halign: "center",
          align: "center",
          formatter: function (value, rowData, rowIndex) {
            return rowData.kec + ", " + rowData.desa;
          },
        },
      ];
      kolom[modnya] = [
        {
          field: "rt",
          title: "RT",
          width: 90,
          halign: "center",
          align: "center",
        },
        {
          field: "rw",
          title: "RW",
          width: 90,
          halign: "center",
          align: "center",
        },
        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "nama_agama",
          title: "Agama",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "nama_status_kawin",
          title: "Status Kawin",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "nama_pendidikan",
          title: "Pendidikan",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "tanggal_buat",
          title: "Tgl. Permohonan",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];
      break;

    case "laporan_penduduk":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      pagesizeboy = 200;

      frozen[modnya] = [
        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "no_kk",
          title: "No. KK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kecamatan",
          title: "Kecamatan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "rt",
          title: "RT",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "rw",
          title: "RW",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_agama",
          title: "Agama",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_status_kawin",
          title: "Status Kawin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_pendidikan",
          title: "Pendidikan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "status_data",
          title: "Status Data",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      break;

    case "laporan_persuratan":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      nowrap_nya = false;

      frozen[modnya] = [
        {
          field: "nama_kelurahan_desa",
          title: "Kelurahan/Desa",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "jenis_surat",
          title: "Nama Layanan Publik",
          width: 230,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_lengkap",
          title: "Nama Pengguna Layanan",
          width: 230,
          halign: "center",
          align: "left",
        },

        {
          field: "tanggal_layanan",
          title: "Tgl. Mengurus Layanan",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "no_hp",
          title: "No. HP (WA) Aktif",
          width: 180,
          halign: "center",
          align: "center",
        },

        {
          field: "email",
          title: "Email Aktif",
          width: 200,
          halign: "center",
          align: "center",
        },
      ];

      break;

    case "laporan_rekap_usaha":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      nowrap_nya = false;

      frozen[modnya] = [
        {
          field: "nama_kelurahan_desa",
          title: "Kelurahan/Desa",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "jenis_surat",
          title: "Nama Layanan Publik",
          width: 230,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_lengkap",
          title: "Nama Pengguna Layanan",
          width: 230,
          halign: "center",
          align: "left",
        },

        {
          field: "tanggal_layanan",
          title: "Tgl. Mengurus Layanan",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "no_hp",
          title: "No. HP (WA) Aktif",
          width: 180,
          halign: "center",
          align: "center",
        },

        {
          field: "email",
          title: "Email Aktif",
          width: 200,
          halign: "center",
          align: "center",
        },
      ];

      break;

    case "laporan_rekap_pengantar_kendaraan":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      nowrap_nya = false;

      frozen[modnya] = [
        {
          field: "asal_kelurahan",
          title: "Kelurahan",
          width: 250,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "no_surat",
          title: "Nomor Surat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "info_tambahan1",
          title: "Nama Sopir",
          width: 300,
          halign: "center",
          align: "left",
          formatter: function(value, row, index) {
            try {
              const parsed = JSON.parse(value);
              return parsed.nama_sopir || '-';
            } catch (e) {
              return '-';
            }
          }
        },

        {
          field: "info_tambahan2",
          title: "Nomor Polisi",
          width: 300,
          halign: "center",
          align: "center",
          formatter: function(value, row, index) {
            try {
              const parsed = JSON.parse(value);
              return parsed.nopol || '-';
            } catch (e) {
              return '-';
            }
          }
        },

        {
          field: "info_tambahan3",
          title: "Jenis Perbaikan",
          width: 300,
          halign: "center",
          align: "left",
          formatter: function(value, row, index) {
            try {
              const parsed = JSON.parse(value);
              return parsed.jenis_perbaikan || '-';
            } catch (e) {
              return '-';
            }
          }
        },
      ];

      break;

    case "data_surat":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      nowrap_nya = false;

      kolom[modnya] = [
        {
          field: "status_esign",
          title: "Status E-SIGN",
          width: 100,
          halign: "center",
          align: "center",
          formatter: function (value, rowData, rowIndex) {
            if (
              rowData.nip_pemeriksa_esign == nip_pegawai_user ||
              rowData.nip == nip_pegawai_user ||
              group_user == 2
            ) {
              if (value == 1) {
                var status = "Approved";
              } else if (value == 2) {
                var status = "Submit";
              } else if (value == 3) {
                var status = "Diverifikasi";
              } else if (value == 4) {
                var status = "Revisi";
              } else if (value == 5) {
                var status = "Ditolak";
              } else {
                var status = "-";
              }
              if (value > 1) {
                status +=
                  '<br><a href="' +
                  host +
                  rowData.file_src_esign +
                  '" target="_blank">Dokumen</a>';
              } else if (value == 1) {
                status +=
                  '<br><a href="' +
                  host +
                  rowData.file_approved_esign +
                  '" target="_blank">Dokumen</a>';
              }
            } else {
              var status = "-";
            }
            return status;
          },
          styler: function (value, rowData, rowIndex) {
            if (value == 1) {
              return "color:#00a65a;";
            } else if (value == 2) {
              return "color:#0073b7;";
            } else if (value == 3) {
              return "color:#00c0ef;";
            } else if (value == 4) {
              return "color:#f39c12;";
            } else if (value == 5) {
              return "color:#dd4b39;";
            } else {
              return "";
            }
          },
        },
        {
          field: "jenis_surat",
          title: "Jenis Surat",
          width: 230,
          halign: "center",
          align: "left",
        },

        {
          field: "no_surat",
          title: "No. & Tgl. Surat",
          width: 250,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            if (value == null) {
              value = "-";
            }

            var html = "";

            html += "No. Surat : " + value + "<br/>";

            html += "Tgl. Surat  : " + rowData.tanggal_surat;

            return html;
          },
        },

        {
          field: "nama_lengkap",
          title: "Nama Dalam Surat",
          width: 200,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            if (value == null) {
              value = "-";
            }

            var html = "";

            html += value;

            return html;
          },
        },
        {
          field: "nama_pemohon",
          title: "Nama Pemohon",
          width: 200,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            if (value == null) {
              value = "-";
            }

            var html = "";

            // html += rowData.nama_pemohon;
            html += value;

            return html;
          },
        },

        {
          field: "no_hp",
          title: "No. Telepon",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "info_tambahan",
          title: "Lainnya",
          width: 200,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            var data = JSON.parse(value);
            var s = "";
            if (rowData.cl_jenis_surat_id == 19) {
              if (
                typeof data.nama_toko !== "undefined" &&
                data.nama_toko != ""
              ) {
                s +=
                  '<div class="row mb-2"><div class="col-md-12 text-muted" style="font-style: italic;">Nama Usaha:</div><div class="col-md-12">' +
                  data.nama_toko +
                  "</div></div>";
              }
              if (
                typeof data.nama_usaha !== "undefined" &&
                data.nama_usaha != ""
              ) {
                s +=
                  '<div class="row mb-2"><div class="col-md-12 text-muted" style="font-style: italic;">Bidang Usaha:</div><div class="col-md-12">' +
                  data.nama_usaha +
                  "</div></div>";
              }
            }
            return s;
          },
        },
        {
          field: "create_by",
          title: "Petugas Input",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "arsip",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.arsip +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_esign":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      nowrap_nya = false;

      kolom[modnya] = [
        {
          field: "status_esign",
          title: "Status E-SIGN",
          width: 100,
          halign: "center",
          align: "center",
          formatter: function (value, rowData, rowIndex) {
            if (
              rowData.nip_pemeriksa_esign == nip_pegawai_user ||
              rowData.nip == nip_pegawai_user ||
              group_user == 2
            ) {
              if (value == 1) {
                var status = "Approved";
              } else if (value == 2) {
                var status = "Submit";
              } else if (value == 3) {
                var status = "Diverifikasi";
              } else if (value == 4) {
                var status = "Revisi";
              } else if (value == 5) {
                var status = "Ditolak";
              } else {
                var status = "-";
              }
              if (value > 1) {
                status +=
                  '<br><a href="' +
                  host +
                  rowData.file_src_esign +
                  '" target="_blank">Dokumen</a>';
              } else if (value == 1) {
                status +=
                  '<br><a href="' +
                  host +
                  rowData.file_approved_esign +
                  '" target="_blank">Dokumen</a>';
              }
            } else {
              var status = "-";
            }
            return status;
          },
          styler: function (value, rowData, rowIndex) {
            if (value == 1) {
              return "color:#00a65a;";
            } else if (value == 2) {
              return "color:#0073b7;";
            } else if (value == 3) {
              return "color:#00c0ef;";
            } else if (value == 4) {
              return "color:#f39c12;";
            } else if (value == 5) {
              return "color:#dd4b39;";
            } else {
              return "";
            }
          },
        },
        {
          field: "jenis_surat",
          title: "Jenis Surat",
          width: 230,
          halign: "center",
          align: "left",
        },

        {
          field: "no_surat",
          title: "No. & Tgl. Surat",
          width: 250,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            if (value == null) {
              value = "-";
            }

            var html = "";

            html += "No. Surat : " + value + "<br/>";

            html += "Tgl. Surat  : " + rowData.tanggal_surat;

            return html;
          },
        },

        {
          field: "nama_lengkap",
          title: "Nama Dalam Surat",
          width: 200,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            if (value == null) {
              value = "-";
            }

            var html = "";

            html += value;

            return html;
          },
        },
        {
          field: "nama_pemohon",
          title: "Nama Pemohon",
          width: 200,
          halign: "center",
          align: "left",

          formatter: function (value, rowData, rowIndex) {
            if (value == null) {
              value = "-";
            }

            var html = "";

            // html += rowData.nama_pemohon;
            html += value;

            return html;
          },
        },

        {
          field: "no_hp",
          title: "No. Telepon",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "arsip",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.arsip +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_keluarga":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "no_kk",
          title: "No. KK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_kepala_keluarga",
          title: "Nama Kepala Keluarga",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "jumlah_anggota_keluarga",
          title: "Jumlah Anggota Keluarga",
          width: 180,
          halign: "center",
          align: "right",
        },

        {
          field: "rw_penduduk",
          title: "RW",
          width: 100,
          halign: "center",
          align: "center",
        },
        
        {
          field: "rt_penduduk",
          title: "RT",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "";
            }
            return n;
          },
        },
      ];

      break;

    case "data_rekap_bulan":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "left",
        },

        // {
        //   field: "bulan_indo",
        //   title: "Bulan",
        //   width: 150,
        //   halign: "center",
        //   align: "center",
        // },

        {
          field: "bulan",
          title: "Periode",
          width: 150,
          halign: "center",
          align: "left",
          formatter: function(value, row, index) {
            if (!value) return "-";
            const namaBulan = [
              "", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
              "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            return namaBulan[parseInt(value)] || "-";
          }
        },
        {
          field: "jml_lk_wni",
          title: "Data Laki-laki",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "jml_pr_wni",
          title: "Data Perempuan",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "jml_lk_wna",
          title: "Data Laki-laki/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "jml_pr_wna",
          title: "Data Perempuan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "lahir_lk_wni",
          title: "Lahir Laki-laki",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "lahir_pr_wni",
          title: "Lahir Perempuan",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "lahir_lk_wna",
          title: "Lahir Laki-laki/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "lahir_pr_wna",
          title: "Lahir Perempuan/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "mati_lk_wni",
          title: "Kematian Laki-laki",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "mati_pr_wni",
          title: "Kematian Perempaun",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "mati_lk_wna",
          title: "Kematian Laki-laki/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "mati_pr_wna",
          title: "Kematian Perempaun/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "datang_lk_wni",
          title: "Datang Laki-laki",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "datang_pr_wni",
          title: "Datang Perempuan",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "datang_lk_wna",
          title: "Datang Laki-laki/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "datang_pr_wna",
          title: "Datang Perempuan/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "pindah_lk_wni",
          title: "Datang Laki-laki",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "pindah_pr_wni",
          title: "Datang Perempuan",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "pindah_lk_wna",
          title: "Datang Laki-laki/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "pindah_pr_wna",
          title: "Datang Perempuan/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "pend_lk_wni",
          title: "Pend Laki-laki",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "pend_pr_wni",
          title: "Pend Perempuan",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "pend_lk_wna",
          title: "Pend Laki-laki/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "pend_pr_wna",
          title: "Pend Perempuan/WNA",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      break;

    case "data_ekspedisi":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nama_pemilik_usaha",
          title: "Pemilik Usaha",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_ekspedisi",
          title: "Ekspedisi",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat_ekspedisi",
          title: "Alamat",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "pnj_ekspedisi",
          title: "Penanggung Jawab",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "tlp_ekspedisi",
          title: "TELP / HP",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "keg_ekspedisi",
          title: "Kegiatan Ekspedisi",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "data_rekap_imb":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nama_pemohon_imb",
          title: "Nama Pemohon",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat_pemohon_imb",
          title: "Alamat Pemohon",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat_bangunan_imb",
          title: "Alamat Bangunan",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "luas_tanah_imb",
          title: "Status/Luas Tanah",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_surat_tanah_imb",
          title: "Nama Pada Surat",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "fungsi_gedung_imb",
          title: "Penggunaan/Fungsi Gedung",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_umkm":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "pemilik",
          title: "Nama Pemilik",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "telp",
          title: "No. HP",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_umkm",
          title: "Nama UKM",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 300,
          halign: "center",
          align: "left",
        },
        {
          field: "rt",
          title: "RT",
          width: 70,
          halign: "center",
          align: "center",
        },
        {
          field: "rw",
          title: "RW",
          width: 70,
          halign: "center",
          align: "center",
        },

        {
          field: "jenis",
          title: "Sektor UMKM",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "laporan_persuratan_masuk":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      nowrap_nya = false;

      frozen[modnya] = [
        {
          field: "nama_kelurahan_desa",
          title: "Kelurahan/Desa",
          width: 150,
          halign: "center",
          align: "left",
        },
      ];

      kolom[modnya] = [
        {
          field: "no_surat",
          title: "No Surat",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_surat",
          title: "Jenis Surat",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "sifat_surat",
          title: "Sifat Surat",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "asal_surat",
          title: "Asal Surat",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "tgl_diterima",
          title: "Tanggal diterima",
          width: 150,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "surat_masuk":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "no_surat",
          title: "No Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "jenis_surat",
          title: "Jenis Surat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "sifat_surat",
          title: "Sifat Surat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "asal_surat",
          title: "Asal Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "tgl_diterima",
          title: "Tanggal diterima",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "";
            }
            return n;
          },
        },
      ];

      break;

    case "surat_lain":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "no_surat",
          title: "No Surat",
          width: 250,
          halign: "center",
          align: "center",
        },

        {
          field: "tgl_surat",
          title: "Tanggal Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "perihal_surat_reg",
          title: "Perihal",
          width: 500,
          halign: "center",
          align: "left",
        },

        {
          field: "tujuan_surat_reg",
          title: "Tujuan Surat",
          width: 400,
          halign: "center",
          align: "left",
        },

        {
          field: "file",
          title: "Arsip",
          width: 250,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "";
            }
            return n;
          },
        },
      ];

      break;

    case "surat_himbauan":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "no_surat",
          title: "No Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "sifat_surat",
          title: "Sifat Surat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "asal_surat",
          title: "Asal Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "tgl_surat",
          title: "Tanggal Surat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tujuan",
          title: "Tujuan Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "uraian",
          title: "Uraian",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "";
            }
            return n;
          },
        },
      ];

      break;

    case "broadcast":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "tgl_broadcast",
          title: "Tanggal Broadcast",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_kelurahan",
          title: "Tujuan Surat",
          width: 200,
          halign: "center",
          align: "center",
        },

        {
          field: "subjek",
          title: "Perihal",
          width: 250,
          halign: "center",
          align: "center",
        },

        {
          field: "pesan",
          title: "Pesan",
          width: 350,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip",
          width: 100,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "";
            }
            return n;
          },
        },
      ];

      break;

    case "data_pkl":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nama",
          title: "Nama",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "nik_pkl",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "no_telp",
          title: "No. Telp",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "rt",
          title: "RT",
          width: 70,
          halign: "center",
          align: "center",
        },
        {
          field: "rw",
          title: "RW",
          width: 70,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_usaha_pkl",
          title: "Nama Usaha",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_usaha",
          title: "Jenis Usaha",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_petugas_kebersihan":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nama_petugas_keb",
          title: "Nama",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "nik_petugas",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "unit_kerja",
          title: "Unit Kerja",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "centar",
        },

        {
          field: "pekerjaan",
          title: "Pekerjaan",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "lokasi",
          title: "Lokasi",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "status_pegawai",
          title: "Status",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 300,
          halign: "center",
          align: "left",
        },
        {
          field: "rt",
          title: "RT",
          width: 70,
          halign: "center",
          align: "center",
        },
        {
          field: "rw",
          title: "RW",
          width: 70,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_retribusi_sampah":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "jumlah_wajub_retribusi",
          title: "Jumlah Wajib Rertribusi",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_bulan",
          title: "Bulan",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "total2",
          title: "Total",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "keterangan",
          title: "Keterangan",
          width: 200,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "data_rt_rw":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_lengkap",
          title: "Nama",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "jab_rt_rw",
          title: "Jabatan",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "rw",
          title: "RW",
          width: 50,
          halign: "center",
          align: "center",
        },

        {
          field: "rt",
          title: "RT",
          width: 50,
          halign: "center",
          align: "center",
        },

        {
          field: "tgl_mulai_jabat",
          title: "Tgl Mulai",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "status",
          title: "Status",
          width: 80,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_keluahan_desa",
          title: "Alamat",
          width: 300,
          halign: "center",
          align: "left",
        },

        {
          field: "no_hp",
          title: "No Telp",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "no_rekening",
          title: "No Rekening",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "no_npwp",
          title: "No NPWP",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "penilaian_rt_rw":
      judulnya = "";

      param["bulan"] = $("#bulan").val();

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 300,
          halign: "center",
          align: "left",
        },
        {
          field: "jabatan_rt_rw",
          title: "Jabatan",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "nilai",
          title: "Nilai",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "standar_nilai",
          title: "Standar Nilai",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "usulan_insentif",
          title: "Usulan Nilai Insentif",
          width: 150,
          halign: "center",
          align: "right",
        },

        {
          field: "nama_bulan",
          title: "Periode Penilaian",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "lpj",
          title: "LPJ",
          width: 160,
          halign: "center",
          align: "center",
          formatter: function (value, row, index) {

            // jika LPJ sudah ada (ada file)
            if (row.lpj == 1) {
              return `
                <a href="javascript:void(0)"
                  class="btn btn-sm btn-success"
                  onclick="cetakLPJ('${row.nik}','${row.bulan}')">
                  <i class="fa fa-print"></i> Cetak
                </a> `;
            } else {
              return '';
            }
          }
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "rekap_penilaian_kelrtrw":
      judulnya = "";

      param["rw"] = $("#rw").val();

      param["bulan"] = $("#bulan").val();

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nik",
          title: "NIK",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jabatan_rt_rw",
          title: "JABATAN",
          width: 95,
          halign: "center",
          align: "left",
        },

        {
          field: "rw",
          title: "RW",
          width: 50,
          halign: "center",
          align: "center",
        },

        {
          field: "tgl_surat",
          title: "Tanggal",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_bulan",
          title: "Periode Penilaian",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nilai",
          title: "Nilai",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_keluahan_desa",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    // case "usulan_penilaian_rt_rw":
    //   judulnya = "";

    //   urlnya = modnya;

    //   fitnya = true;

    //   row_number = true;

    //   kolom[modnya] = [
    //     {
    //       field: "tgl_usulan",
    //       title: "Tanggal Usulan",
    //       width: 200,
    //       halign: "center",
    //       align: "left",

    //     },
    //     {
    //       field: "periode_bulan",
    //       title: "Periode Penilaian",
    //       width: 100,
    //       halign: "center",
    //       align: "left",
    //     },
    //     // {
    //     //   field: "",
    //     //   title: "Jumlah Orang",
    //     //   width: 200,
    //     //   halign: "center",
    //     //   align: "left",
    //     // },
    //     {
    //       field: "jab_rt_rw",
    //       title: "Jabatan",
    //       width: 200,
    //       halign: "center",
    //       align: "left",
    //     },
    //     {
    //       field: "no_npwp",
    //       title: "No NPWP",
    //       width: 200,
    //       halign: "center",
    //       align: "left",
    //     },
    //     {
    //       field: "no_rekening",
    //       title: "No Rekening",
    //       width: 200,
    //       halign: "center",
    //       align: "left",
    //     },
    //     {
    //       field: "no_hp",
    //       title: "No Telepon",
    //       width: 200,
    //       halign: "center",
    //       align: "left",
    //     },
    //     {
    //       field: "nama_keluahan_desa",
    //       title: "Kelurahan",
    //       width: 200,
    //       halign: "center",
    //       align: "left",
    //     },
    //     {
    //       field: "status",
    //       title: "Status",
    //       width: 200,
    //       halign: "center",
    //       align: "left",
    //     },

    //     // {
    //     //   field: "",
    //     //   title: "Status",
    //     //   width: 200,
    //     //   halign: "center",
    //     //   align: "left",
    //     // },

    //     {
    //       field: "file",
    //       title: "Arsip ",
    //       width: 150,
    //       halign: "center",
    //       align: "center",

    //       formatter: function (value, rowData, rowIndex) {
    //         var n = "";
    //         if (value != null && value != "") {
    //           n +=
    //             '<a class="btn btn-sm btn-info" target="_blank" href="' +
    //             rowData.file +
    //             '"><i class="fa fa-eye"></i> Lihat</a>';
    //         } else {
    //           n += "-";
    //         }
    //         return n;
    //       },
    //     },
    //   ];

    //   break;

    case "data_sekolah":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kel",
          title: "Kelurahan",
          width: 250,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_sekolah",
          title: "Nama Sekolah",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "bp",
          title: "Jenjang",
          width: 80,
          halign: "center",
          align: "center",
        },
        {
          field: "status",
          title: "Status",
          width: 80,
          halign: "center",
          align: "center",
        },
        {
          field: "alamat",
          title: "Alamat",
          width: 370,
          halign: "center",
          align: "left",
        },
        {
          field: "lat",
          title: "Latitude",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "long",
          title: "Longitude",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "file",
          title: "Action ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_detail_sekolah":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "npsn",
          title: "NPSN",
          width: 110,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_sekolah",
          title: "Nama Sekolah",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "thn_ajar",
          title: "Tahun Ajar",
          width: 80,
          halign: "center",
          align: "center",
        },
        {
          field: "jumlah_siswa",
          title: "Jumlah Siswa",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "jumlah_rombel",
          title: "Rombel",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "jumlah_guru",
          title: "Jumlah Guru",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "jumlah_pegawai",
          title: "Jumlah Pegawai",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "ruang_kelas",
          title: "Ruang Kelas",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "jumlah_ruang_lab",
          title: "Jumlah R. Lab",
          width: 100,
          halign: "center",
          align: "center",
        },
        {
          field: "ruang_perpus",
          title: "Jumlah R. Perpus",
          width: 110,
          halign: "center",
          align: "center",
        },
        {
          field: "file",
          title: "Action ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_tempat_ibadah":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jns_tempat_ibadah",
          title: "Tempat Ibadah",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_tempat_ibadah",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "rt",
          title: "RT",
          width: 70,
          halign: "center",
          align: "center",
        },
        {
          field: "rw",
          title: "RW",
          width: 70,
          halign: "center",
          align: "center",
        },

        {
          field: "ketua_pengurus",
          title: "Pengurus",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_faskes":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "nama",
          title: "Nama RS",
          width: 300,
          halign: "center",
          align: "left",
        },
       
        {
          field: "alamat",
          title: "Alamat",
          width: 300,
          halign: "center",
          align: "left",
        },
        {
          field: "rw",
          title: "RW",
          width: 50,
          halign: "center",
          align: "center",
        },
        {
          field: "rt",
          title: "RW",
          width: 50,
          halign: "center",
          align: "center",
        },
        {
          field: "jenis",
          title: "Jenis",
          width: 100,
          halign: "center",
          align: "left",
        },
        {
          field: "kelas",
          title: "Kelas",
          width: 100,
          halign: "center",
          align: "left",
        },
        {
          field: "jenis_pelayanan",
          title: "Jenis Pelayanan",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "akreditasi",
          title: "Akreditasi",
          width: 150,
          halign: "center",
          align: "left",
        },
        
        {
          field: "telp",
          title: "Telp",
          width: 150,
          halign: "center",
          align: "left",
        },
        
        {
          field: "file",
          title: "Action ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_wamis":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "nama",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "nik",
          title: "NIK",
          width: 250,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "rw",
          title: "RW",
          width: 50,
          halign: "center",
          align: "center",
        },
        {
          field: "rt",
          title: "RW",
          width: 50,
          halign: "center",
          align: "center",
        },

        {
          field: "no_peserta",
          title: "No. Peserta",
          width: 250,
          halign: "center",
          align: "center",
        },

        {
          field: "ket",
          title: "Keterangan",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "data_kunjungan_rumah":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "rt",
          title: "RT",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "rw",
          title: "RW",
          width: 100,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "no_kk",
          title: "No. KK",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_kk",
          title: "Nama Kepala Keluarga",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "vaksin",
          title: "Vaksin",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "nomor_hp",
          title: "No. HP",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jumlah",
          title: "Jumlah",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "masukan",
          title: "Masukan",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "data_kerja_bakti":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "tanggal",
          title: "tanggal",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "lokasi",
          title: "Lokasi",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "volume_sampah",
          title: "Volume Sampah",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jumlah_warga",
          title: "Jumlah Partisipasi Warga",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "data_notulen_rapat":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      kolom[modnya] = [
        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "tanggal",
          title: "tanggal",
          width: 200,
          halign: "center",
          align: "left",
        },

        {
          field: "agenda_rapat",
          title: "Agenda Rapat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "hasil_rapat",
          title: "Hasil Rapat",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];

      break;

    case "data_penduduk":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      frozen[modnya] = [
        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "no_kk",
          title: "No. KK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kecamatan",
          title: "Kecamatan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "rt",
          title: "RT",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "rw",
          title: "RW",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "gol_darah",
          title: "Gol. Darah",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_agama",
          title: "Agama",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_status_kawin",
          title: "Status Kawin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_pendidikan",
          title: "Pendidikan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "status_data",
          title: "Status Data",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_penduduk_asing":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      frozen[modnya] = [
        {
          field: "no_passport",
          title: "No. Passport",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "kecamatan",
          title: "Kecamatan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "rt",
          title: "RT",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "rw",
          title: "RW",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "file",
          title: "Arsip ",
          width: 150,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];

      break;

    case "data_ktp":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      frozen[modnya] = [
        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "tempat_lahir",
          title: "Tempat Lahir",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tgl_lahir",
          title: "Tanggal Lahir",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_agama",
          title: "Agama",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_status_kawin",
          title: "Status Kawin",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_pendidikan",
          title: "Pendidikan",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "rt",
          title: "RT",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "rw",
          title: "RW",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kecamatan",
          title: "Kecamatan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_date",
          title: "Tgl. Buat",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      break;

    case "data_pegawai_kel_kec":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      frozen[modnya] = [
        {
          field: "nip",
          title: "NIP",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "nama",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jabatan",
          title: "Jabatan",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_golongan",
          title: "GOL",
          width: 100,
          halign: "center",
          align: "center",
        },

        {
          field: "pangkat",
          title: "Pangkat",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "status",
          title: "Status Pegawai",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "no",
          title: "No. Urut",
          width: 100,
          halign: "center",
          align: "center",
        },

        // {
        //   field: "file",
        //   title: "Arsip ",
        //   width: 150,
        //   halign: "center",
        //   align: "center",
        //   formatter: function (value, rowData, rowIndex) {
        //     var n = "";
        //     if (value != null && value != "" && value != "[]") {
        //       n +=
        //         '<a class="btn btn-sm btn-info" target="_blank" href="' +
        //         rowData.file +
        //         '"><i class="fa fa-eye"></i> Lihat</a>';
        //     } else {
        //       n += "-";
        //     }
        //     return n;
        //   },

        //   // formatter: function (value, rowData, rowIndex) {
        //   //   var n = "";
        //   //   if(rowData.file != "" && rowData.file !== null){

        //   //     $.each(JSON.parse(rowData.file),function(index,ndata){
        //   //       n +=
        //   //       '<a class="btn btn-sm btn-info" target="_blank" href="' +
        //   //       ndata.files +
        //   //       '"><i class="fa fa-eye"></i> Lihat</a>';
        //   //     });

        //   //   }
        //   //   return n;
        //   // },
        // },
      ];

      break;

    case "data_dasawisma":
      judulnya = "";

      urlnya = modnya;

      fitnya = true;

      row_number = true;

      frozen[modnya] = [
        {
          field: "nik",
          title: "NIK",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "nama_lengkap",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "no_kk",
          title: "No. KK",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kecamatan",
          title: "Kecamatan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "kelurahan",
          title: "Kelurahan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "rt",
          title: "RT",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "rw",
          title: "RW",
          width: 90,
          halign: "center",
          align: "center",
        },

        {
          field: "alamat",
          title: "Alamat",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_agama",
          title: "Agama",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_status_kawin",
          title: "Status Kawin",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "nama_pendidikan",
          title: "Pendidikan",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "status_data",
          title: "Status Data",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },

        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      break;

    case "data_penandatanganan":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "nip",
          title: "NIP",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];
      kolom[modnya] = [
        {
          field: "nama",
          title: "Nama",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "jabatan",
          title: "Jabatan",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "pangkat",
          title: "Pangkat",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "status",
          title: "Status",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "no_telp",
          title: "No. Telp",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "tanggal_buat",
          title: "Tgl. Input",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];
      break;

    case "daftar_agenda_kegiatan":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
          {
              field: "tgl_kegiatan",
              title: "Hari Kegiatan",
              width: 150,
              halign: "center",
              align: "center",
              formatter: function(value, row) {
                  return formatTanggalIndonesia(value);
              }
          },
      ];

      kolom[modnya] = [
          {
              field: "waktu_kegiatan",
              title: "Jam",
              width: 100,
              halign: "center",
              align: "center",
              formatter: function(value,row){
                  if(!value) return "-";
                  let jam = value.substring(0,5).replace(":", ".");
                  return jam + " - Selesai";
              }
          },
          {
              field: "lokasi_kegiatan",
              title: "Lokasi",
              width: 250,
              halign: "center",
              align: "left",
          },
          {
              field: "instansi_pengirim",
              title: "Instansi Pengirim",
              width: 200,
              halign: "center",
              align: "left",
          },
          {
              field: "perihal_kegiatan",
              title: "Perihal",
              width: 400,
              halign: "center",
              align: "left",
          },
          {
              field: "pj_kegiatan",
              title: "Penanggung Jawab",
              width: 200,
              halign: "center",
              align: "left",
          },
          {
              field: "ket_kegiatan",
              title: "Keterangan",
              width: 200,
              halign: "center",
              align: "left",
          },

          {
          field: "file",
          title: "Arsip ",
          width: 100,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            var n = "";
            if (value != null && value != "") {
              n +=
                '<a class="btn btn-sm btn-info" target="_blank" href="' +
                rowData.file +
                '"><i class="fa fa-eye"></i> Lihat</a>';
            } else {
              n += "-";
            }
            return n;
          },
        },
      ];
      break;

    // case "laporan_hasil_kegiatan":
    //   judulnya = "";
    //   urlnya = modnya;
    //   fitnya = true;
    //   row_number = true;

    //   frozen[modnya] = [
    //       {
    //           field: "tgl_hasil_agenda",
    //           title: "Hari/Tanggal",
    //           width: 150,
    //           halign: "center",
    //           align: "center",
    //           formatter: function(value, row) {
    //               return formatTanggalIndonesia(value);
    //           }
    //       },
    //   ];

    //   kolom[modnya] = [
    //       {
    //           field: "agenda",
    //           title: "Agenda",
    //           width: 350,
    //           halign: "center",
    //           align: "left",
    //       },
    //       {
    //           field: "notulen_hasil_agenda",
    //           title: "Notulen",
    //           width: 700,
    //           halign: "center",
    //           align: "left",
    //       },
    //       {
    //           field: "ket_hasil_agenda",
    //           title: "Keterangan",
    //           width: 250,
    //           halign: "center",
    //           align: "left",
    //       },
    //       {
    //       field: "file",
    //       title: "Arsip ",
    //       width: 150,
    //       halign: "center",
    //       align: "center",

    //       formatter: function (value, rowData, rowIndex) {
    //         var n = "";
    //         if (value != null && value != "") {
    //           n +=
    //             '<a class="btn btn-sm btn-info" target="_blank" href="' +
    //             rowData.file +
    //             '"><i class="fa fa-eye"></i> Lihat</a>';
    //         } else {
    //           n += "-";
    //         }
    //         return n;
    //       },
    //     },
    //   ];
    // break;
  
    case "laporan_hasil_kegiatan":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "tanggal_tampil",
          title: "Hari / Tanggal",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];

      kolom[modnya] = [
        {
          field: "perihal_kegiatan",
          title: "Agenda",
          width: 350,
          halign: "center",
          align: "left",
        },
        {
          field: "notulen_hasil_agenda",
          title: "Notulen",
          width: 600,
          halign: "center",
          align: "left",
          formatter: function(value, row){
            return row.ada_hasil ? value : '<i>Belum ada hasil</i>';
          }
        },
        {
          field: "ket_hasil_agenda",
          title: "Keterangan",
          width: 450,
          halign: "center",
          align: "left",
          formatter: function(value, row){
            if (!row.ada_hasil) {
              return '<i class="text-muted">Belum ada hasil</i>';
            }
            return value;
          }
        },
        {
          field: "file_dokumentasi",
          title: "Arsip",
          width: 150,
          halign: "center",
          align: "center",
          formatter: function (value) {
            if (value) {
              return `
                <a class="btn btn-sm btn-info" target="_blank" href="${value}">
                  <i class="fa fa-eye"></i> Lihat
                </a>`;
            }
            return "-";
          },
        },
      ];
    break;

    case "data_kendaraan":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "jenis_barang",
          title: "Jenis Barang",
          width: 150,
          halign: "center",
          align: "left",
        },
      ];
      kolom[modnya] = [
        {
          field: "nama_sopir",
          title: "Nama Pengemudi",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "nopol",
          title: "No Polisi",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "no_rangka",
          title: "No Rangka",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "no_mesin",
          title: "No. Mesin",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "tahun_perolehan",
          title: "Tahun Perolehan",
          width: 150,
          halign: "center",
          align: "center",
        },
        {
          field: "type_merek",
          title: "Merek/Type",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "asal_kelurahan",
          title: "Wilayah Penugasan",
          width: 150,
          halign: "left",
          align: "left",
        },
      ];
      break;

    case "data_indikator_skm":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "uraian",
          title: "Uraian",
          width: 850,
          halign: "center",
          align: "left",
        },
      ];
      kolom[modnya] = [
        {
          field: "p1",
          title: "A",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "p2",
          title: "B",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "p3",
          title: "C",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "p4",
          title: "D",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "kecamatan",
          title: "Wilayah Penugasan",
          width: 150,
          halign: "left",
          align: "left",
        },
      ];
      break;

    case "form_sub_indikator_rt_rw":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "kategori",
          title: "Nama Kategori",
          width: 550,
          halign: "center",
          align: "left",
        },
      ];
      kolom[modnya] = [
        {
          field: "uraian",
          title: "Nama Sub Indikator",
          width: 850,
          halign: "center",
          align: "left",
        },
        {
          field: "satuan",
          title: "Satuan",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];
      break;

    case "data_sub_indikator_rt_rw":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "kategori",
          title: "Nama Kategori",
          width: 550,
          halign: "center",
          align: "left",
        },
      ];
      kolom[modnya] = [
        {
          field: "uraian",
          title: "Nama Sub Indikator",
          width: 850,
          halign: "center",
          align: "left",
        },
        {
          field: "satuan",
          title: "Satuan",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];
      break;

    case "data_penilaian_skm":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "jenis_kelamin",
          title: "Jenis Kelamin",
          width: 150,
          halign: "center",
          align: "left",
        },
      ];
      kolom[modnya] = [
        {
          field: "umur",
          title: "Umur",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "pendidikan",
          title: "Pendidikan",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "pekerjaan",
          title: "Pekerjaan",
          width: 250,
          halign: "center",
          align: "left",
        },
        {
          field: "jenis_surat",
          title: "Jenis Layanan",
          width: 350,
          halign: "center",
          align: "left",
        },
        {
          field: "nilai",
          title: "Jumlah Nilai",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "rata_rata",
          title: "Nilai Rata-Rata",
          width: 150,
          halign: "center",
          align: "left",
        },
        {
          field: "kelurahan",
          title: "Wilayah Penugasan",
          width: 150,
          halign: "left",
          align: "left",
        },
      ];
      break;

    case "data_jenis_persuratan":
      judulnya = "";
      urlnya = modnya;
      fitnya = true;
      row_number = true;

      frozen[modnya] = [
        {
          field: "jenis_surat",
          title: "Jenis Surat",
          width: 250,
          halign: "center",
          align: "left",
        },
      ];
      kolom[modnya] = [
        {
          field: "teks_1",
          title: "Teks 1",
          width: 200,
          halign: "center",
          align: "left",
        },
        {
          field: "teks_2",
          title: "Teks 2",
          width: 200,
          halign: "center",
          align: "center",
        },
        {
          field: "teks_3",
          title: "Teks 3",
          width: 200,
          halign: "center",
          align: "center",
        },
        {
          field: "create_by",
          title: "Petugas Input",
          width: 150,
          halign: "center",
          align: "center",
        },
      ];
      break;

    case "user_group":
      judulnya = "";

      urlnya = "cl_user_group";

      fitnya = true;

      param = par1;

      row_number = true;

      kolom[modnya] = [
        {
          field: "user_group",
          title: "Group User",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "id",
          title: "User Role Setting",
          width: 120,
          halign: "center",
          align: "center",

          formatter: function (value, rowData, rowIndex) {
            return (
              "<button href=\"javascript:void(0)\" onClick=\"kumpulAction('userrole','" +
              rowData.id +
              "','" +
              rowData.user_group +
              '\')" class="easyui-linkbutton" data-options="iconCls:\'icon-save\'">Setting</button>'
            );
          },
        },
      ];

      break;

    case "user_mng":
      judulnya = "";

      urlnya = "tbl_user";

      fitnya = true;

      param = par1;

      row_number = true;

      kolom[modnya] = [
        {
          field: "username",
          title: "Username",
          width: 150,
          halign: "center",
          align: "left",
        },

        {
          field: "nama_lengkap",
          title: "Real Name",
          width: 250,
          halign: "center",
          align: "left",
        },

        {
          field: "user_group",
          title: "Group User",
          width: 190,
          halign: "center",
          align: "left",
        },
      ];

      break;
  }

  urlglobal = host + "backoffice-data/" + urlnya;

  grid_nya = $("#" + divnya).datagrid({
    title: judulnya,

    height: tingginya,

    width: lebarnya,

    rownumbers: row_number,

    iconCls: "database",

    fit: fitnya,

    striped: true,

    pagination: paging,

    remoteSort: false,

    showFooter: footer,

    singleSelect: singleSelek,

    url: urlglobal,

    nowrap: nowrap_nya,

    pageSize: pagesizeboy,

    pageList: [10, 20, 30, 40, 50, 100, 200],

    queryParams: param,

    frozenColumns: [frozen[modnya]],

    columns: [kolom[modnya]],

    onLoadSuccess: function (d) {
      $(".btn_grid").linkbutton();
    },

    onClickRow: function (rowIndex, rowData) {
      if (modnya == "ldap_user") {
        $("#user_ldap").val(rowData.samaccountname);

        $("#user_na").html(rowData.samaccountname);

        $("#nama_na").html(rowData.displayname);
      }
    },

    onDblClickRow: function (rowIndex, rowData) {},

    toolbar: "#tb_" + modnya,

    rowStyler: function (index, row) {
      if (modnya == "list_work_order") {
        if (row.flag_opr) {
          if (row.flag_opr == 0) {
            return "background-color:#FDF0C5;";
          } else if (row.flag_opr == 1) {
            return "background-color:#D6E2F3;";
          }
        } else {
          if (row.agent_cc != null) {
            return "background-color:#CDF8D6;"; //warna ijo toska staff AGENT
          } else {
            return "background-color:#FFDDDD;"; // warna merah terang - staff BO
          }
        }
      
      /* ==== DATA SURAT ==== */
      } else if (modnya == "data_surat") {
        if (row.flag_reg == "Y") {
          return "background-color:#FDF0C5;";
        }

      /* ==== PENILAIAN RT RW ==== */
      } else if (modnya == "penilaian_rt_rw") {
        if (row.nilai != null) {
          return "background-color: lightgreen;";
        }
      
      /* ==== 🔥 Warna untuk Daftar Agenda ==== */
      } else if (modnya == "daftar_agenda_kegiatan") {
        if (parseInt(row.status) === 1) {
          return "background-color:#cfe2ff;color:#084298;";
        }
      
      /* ==== 🔥 Warna untuk Laporan Hasil ==== */
      } else if (modnya == "laporan_hasil_kegiatan") {
        if (parseInt(row.ada_hasil) === 1) {
          return "background-color:#cfe2ff;color:#084298;";
        }
      }
    },

    onLoadSuccess: function (data) {
      if (data.total == 0) {
        var $panel = $(this).datagrid("getPanel");

        var $info =
          '<div class="info-empty" style="margin-top:20%;">Data Tidak Tersedia</div>';

        $($panel).find(".datagrid-view").append($info);
      } else {
        $($panel).find(".datagrid-view").append("");

        $(".info-empty").remove();
      }
    },
  });
}

function genform(type, modulnya, submodulnya, stswindow, p1, p2, p3) {
  var urlpost = host + "backoffice-form/" + submodulnya;

  var urldelete = host + "backoffice-simpan/" + submodulnya;

  var id_tambahan = "";

  var nama_file = "";

  var table = submodulnya;

  var adafilenya = false;

  switch (submodulnya) {
    case "neraca":
      table = "konten_keuangan";

      id_tambahan = submodulnya;

      break;
  }

  urldelete = host + "backoffice-simpan/" + table;

  switch (type) {
    //TIDAK BOLEH DIHAPUS CASE ADD ASLI (YUNIA)
    case "add":
      if (stswindow == undefined) {
        $("#grid_nya_" + submodulnya).hide();

        $("#detil_nya_" + submodulnya)
          .empty()
          .show()
          .addClass("loading");
      }

      $.post(
        urlpost,
        {
          editstatus: "add",
          ts: table,
          id_tambahan: id_tambahan,
        },
        function (resp) {
          if (stswindow == "windowform") {
            windowForm(resp, judulwindow, lebar, tinggi);
          } else if (stswindow == "windowpanel") {
            windowFormPanel(resp, judulwindow, lebar, tinggi);
          } else {
            $("#detil_nya_" + submodulnya).show();

            $("#detil_nya_" + submodulnya)
              .html(resp)
              .removeClass("loading");
          }
        }
      );

      break;

    // case "add":
    //   if (stswindow == undefined) {
    //     $("#grid_nya_" + submodulnya).hide();

    //     $("#detil_nya_" + submodulnya)
    //       .empty()
    //       .show()
    //       .addClass("loading");
    //   }

    //   $.post(
    //     urlpost,
    //     {
    //       editstatus: "add",
    //       ts: table,
    //       id_tambahan: id_tambahan,
    //     },
    //     function (resp) {
    //       if (stswindow == "windowform") {
    //         windowForm(resp, judulwindow, lebar, tinggi);
    //       } else if (stswindow == "windowpanel") {
    //         windowFormPanel(resp, judulwindow, lebar, tinggi);
    //       } else {
    //         $("#detil_nya_" + submodulnya).show();
    //         $("#detil_nya_" + submodulnya).html(resp).removeClass("loading");

    //         // ✅ Tambahkan logika khusus untuk add_usulan_penilaian_rt_rw
    //         if (submodulnya == "usulan_penilaian_rt_rw") {
    //           // Sembunyikan gambar under construction
    //           $("img[src*='konstruksi-icon.png']").closest("div").parent().hide();

    //           // Data bulan
    //           const bulanArray = [
    //             "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    //             "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    //           ];
    //           const bulanSekarang = new Date().getMonth(); // 0-based index
    //           const tahunIni = new Date().getFullYear();
    //           const tanggalDefault = new Date().toISOString().split("T")[0];

    //           // Buat opsi <select>
    //           let selectBulan = `<select id="periode_bulan" name="periode_bulan"
    //                             class="form-control"
    //                             style="height:32px; width:280px; font-size:0.9rem;"
    //                             onfocus="tampilkanPetugasRTRW()">`;

    //           bulanArray.forEach((namaBulan, i) => {
    //             let selected = i === bulanSekarang ? "selected" : "";
    //             let nilai = `${tahunIni}-${(i + 1).toString().padStart(2, '0')}`; // format YYYY-02
    //             selectBulan += `<option value="${nilai}" ${selected}>${namaBulan}</option>`;
    //           });

    //           selectBulan += `</select>`;

    //           // Gabung dengan input tanggal
    //           let inputTambahan = `
    //             <div class="mb-2 mt-2 d-flex align-items-center gap-2" style="gap: 10px;">
    //               <div>
    //                 <label for="tanggal_usulan"><b>Tanggal Usulan</b></label><br>
    //                 <input type="date" id="tanggal_usulan" name="tanggal_usulan"
    //                       value="${tanggalDefault}"
    //                       min="${tahunIni}-01-01"
    //                       max="${tahunIni}-12-31"
    //                       style="height:32px; width:270px; font-size:0.9rem;"
    //                       class="form-control d-inline-block">
    //               </div>
    //               <div>
    //                 <label for="periode_bulan"><b>Periode Bulan</b></label><br>
    //                 ${selectBulan}
    //               </div>
    //               <div id="tabel_petugas_rt_rw" class="mt-3"></div>
    //             </div>
    //           `;

    //           // Sisipkan ke atas form
    //           $("#detil_nya_" + submodulnya).prepend(inputTambahan);
    //         }
    //       }
    //     }
    //   );
    // break;

    case "edit":
      var row = $("#grid_" + submodulnya).datagrid("getSelected");
      if (row) {
        if (
          row.status_esign == 0 ||
          row.status_esign == 2 ||
          row.status_esign == 4 ||
          row.status_esign == 5 ||
          typeof row.status_esign == "undefined"
        ) {
          if (stswindow == undefined) {
            $("#grid_nya_" + submodulnya).hide();

            $("#detil_nya_" + submodulnya)
              .empty()
              .show()
              .addClass("loading");
          }
          if (submodulnya == "data_esign") {
            urlpost = host + "backoffice-form/data_surat";
          }
          var bln = $("#bulan").val();

          var rt_rw_id = "";
          if (row.rt_rw_id != undefined) {
            rt_rw_id = row.rt_rw_id;
          }

          // === KHUSUS LAPORAN HASIL KEGIATAN ===
          if (
            table === "laporan_hasil_kegiatan" &&
            (row.id === null || row.id === "" || typeof row.id === "undefined")
          ) {
            row.id = row.agenda_id; // 🔥 fallback ke agenda
          }

          $.post(
            urlpost,
            {
              editstatus: "edit",
              ts: table,
              id: row.id,
              rt_rw_id: rt_rw_id,
              bulan: bln,
            },
            function (resp) {
              if (stswindow == "windowform") {
                windowForm(resp, judulwindow, lebar, tinggi);
              } else if (stswindow == "windowpanel") {
                windowFormPanel(resp, judulwindow, lebar, tinggi);
              } else {
                $("#detil_nya_" + submodulnya).show();

                $("#detil_nya_" + submodulnya)
                  .html(resp)
                  .removeClass("loading");
              }
            }
          );
        } else {
          $.messager.alert(
            nama_apps,
            "Data dalam pengajuan tandatangan elektronik",
            "error"
          );
        }
      } else {
        $.messager.alert(
          nama_apps,
          "Pilih Data Yang Akan Dihapus/Diedit",
          "error"
        );
      }
      break;

    case "edit_preview":
      var urlpost = host + "backoffice-form/data_surat_edit";
      var row = $("#grid_" + submodulnya).datagrid("getSelected");
      if (row) {
        if (
          row.status_esign == 0 ||
          row.status_esign == 2 ||
          row.status_esign == 4 ||
          row.status_esign == 5 ||
          typeof row.status_esign == "undefined"
        ) {
          if (stswindow == undefined) {
            $("#grid_nya_" + submodulnya).hide();

            $("#detil_nya_" + submodulnya)
              .empty()
              .show()
              .addClass("loading");
          }

          $.post(
            urlpost,
            {
              editstatus: "edit",
              ts: table,
              id: row.id,
            },
            function (resp) {
              if (stswindow == "windowform") {
                windowForm(resp, judulwindow, lebar, tinggi);
              } else if (stswindow == "windowpanel") {
                windowFormPanel(resp, judulwindow, lebar, tinggi);
              } else {
                $("#detil_nya_" + submodulnya).show();

                $("#detil_nya_" + submodulnya)
                  .html(resp)
                  .removeClass("loading");
              }
            }
          );
        } else {
          $.messager.alert(
            nama_apps,
            "Data dalam pengajuan tandatangan elektronik",
            "error"
          );
        }
      } else {
        $.messager.alert(
          nama_apps,
          "Pilih Data Yang Akan Dihapus/Diedit",
          "error"
        );
      }
      break;

    case "delete":
      var row = $("#grid_" + submodulnya).datagrid("getSelected");

      if (row) {

        // 🔥 KHUSUS LAPORAN HASIL KEGIATAN
        if (
          submodulnya === "laporan_hasil_kegiatan" &&
          (row.id === null || row.id === "" || typeof row.id === "undefined")
        ) {
          row.id = row.agenda_id;
        }

        if (
          submodulnya === "daftar_agenda_kegiatan" &&
          parseInt(row.status) === 1
        ) {
          $.messager.alert(
            nama_apps,
            "Agenda ini sudah memiliki hasil laporan dan tidak dapat dihapus.",
            "warning"
          );
          return false; // ⛔ STOP TOTAL
        }

        $.messager.confirm(
          nama_apps,
          "Anda Yakin Ingin Menghapus Data Ini ?",
          function (re) {
            if (re) {
              if (adafilenya) {
                nama_file = row.nama_file;
              }

              $.LoadingOverlay("show");

              $.post(
                urldelete,
                {
                  id: row.id,
                  editstatus: "delete",
                  id_tambahan: id_tambahan,
                },
                function (r) {
                  if (r == 1) {
                    $.messager.alert(nama_apps, "Data Terhapus", "info");

                    grid_nya.datagrid("reload");
                  } else if (r == 3) {
                    $.messager.alert(
                      nama_apps,
                      "Gagal Menghapus Data karena NIK sudah terdaftar di Kartu Keluarga",
                      "warning"
                    );
                  } else if (r == 4) {
                    $.messager.alert(
                      nama_apps,
                      "Gagal Menghapus Data karena sudah ada Surat dengan NIK ini",
                      "warning"
                    );
                  } else if (r == 5) {
                    $.messager.alert(
                      nama_apps,
                      "Data tidak boleh dihapus!!!",
                      "warning"
                    );
                  } else {
                    $.messager.alert(
                      nama_apps,
                      "Gagal Menghapus Data " + r,
                      "error"
                    );
                  }

                  $.LoadingOverlay("hide", true);
                }
              );
            }
          }
        );
      } else {
        $.messager.alert(
          nama_apps,
          "Pilih Data Yang Akan Dihapus/Diedit",
          "error"
        );
      }

      break;
  }
}

function genTab(div, mod, tab_array, height_tab, width_tab) {
  /*

var id_sub_mod=sub_mod.split("_");

if(typeof(div_panel)!= "undefined" || div_panel!=""){



$(div_panel).panel({

width:(typeof(width_panel) == "undefined" ? getClientWidth()-268 : width_panel),

height:(typeof(height_panel) == "undefined" ? getClientHeight()-100 : height_panel),

title:judul_panel,

//fit:true,

tools:[{

iconCls:'icon-cancel',

handler:function(){

$('#grid_nya_'+id_sub_mod[1]).show();

$('#detil_nya_'+id_sub_mod[1]).hide();

$('#grid_'+id_sub_mod[1]).datagrid('reload');

}

}]

});

//

}

*/

  $(div).tabs({
    title: "AA",

    height: getClientHeight() - 150,

    width: getClientWidth() - 280,

    plain: false,

    fit: true,

    onSelect: function (title, index) {
      var isi_tab = title.replace(/ /g, "_");

      var par = {};

      console.log(isi_tab);

      $("#" + isi_tab.toLowerCase())
        .html("")
        .addClass("loading");

      urlnya = host + "Basarnas-getmodul/" + mod + "/" + isi_tab.toLowerCase();

      switch (mod) {
        case "kasir":
          var lantainya = title.split(" ");

          var lantainya = lantainya.length - 1;

          par["posisi_lantai"] = lantainya;

          urlnya = host + "kasir-lantai/";

          break;
      }

      $.post(urlnya, par, function (r) {
        $("#" + isi_tab.toLowerCase())
          .removeClass("loading")
          .html(r);
      });
    },

    onUnselect: function (title, index) {
      var isi_tab = title.replace(/ /g, "_");

      $("#" + isi_tab.toLowerCase()).html("");
    },

    selected: 0,
  });

  if (tab_array.length > 0) {
    for (var x in tab_array) {
      var isi_tab = tab_array[x].replace(/ /g, "_");

      $(div).tabs("add", {
        title: tab_array[x],

        index: x,

        selected: x == 0 ? true : false,

        content:
          '<div style="padding: 5px;"><div id="' +
          isi_tab.toLowerCase() +
          '" style="height: 200px;">' +
          isi_tab.toLowerCase() +
          "zzzz</div></div>",
      });
    }

    var tab = $(div).tabs("select", 0);
  }
}
async function kumpulAction(type, p1, p2, p3, p4, p5) {
  var param = {};
  // alert('gsgsgsgsg');
  switch (type) {

    case "export_laporan_lorong":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_lorong?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_rekap_bulan":
      var row = $('#grid_data_rekap_bulan').datagrid('getSelected'); // ambil baris aktif
      if (!row) {
          $.messager.alert("SIMLURAH", "Pilih data rekap terlebih dahulu!", "error");
          return;
      }

      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nip"] = $("#nip").val();

      if (!$("#nip").val()) {
          $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
          return;
      }

      // kirim id rekap dari baris yang dipilih
      param["id"] = row.id;
      // alert(row.id);

      var url = host + "backoffice-cetak/laporan_rekap_bulan?" + $.param(param);
      window.open(url, "_blank");
    break;

    case "export_laporan_ekspedisi":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url = host + "backoffice-cetak/laporan_ekspedisi?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_penilaian_rt_rw":
      var row = $("#grid_penilaian_rt_rw").datagrid("getSelected");
      param["rt_rw_id"] = row.rt_rw_id;
      param["nik"] = row.nik;
      param["rt"] = row.rt;
      param["rw"] = row.rw;
      param["bulan"] = row.bulan;
      param["nip"] = $("#nip").val();
      param["nik_lsm"] = $("#nik_lsm").val();
      param["nik_pembuat"] = $("#nik_pembuat").val();

      var url =
        host + "backoffice-cetak/laporan_penilaian_rt_rw?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_rekap_penilaian_kelrtrw":
      param["rt"] = $("#rt_" + p1).val();
      // param["rw"] = $("#rw_" + p1).val();
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["bulan"] = $("#bulan" + p1).val();
      param["rw"] = $("#rw" + p1).val();
      param["tgl_cetak"] = $("#tgl_cetak" + p1).val();
      // param["kelurahan"] = $("#kelurahan").val();
      param["nik_pembuat"] = $("#nik_pembuat" + p1).val();
      param["nik_lsm"] = $("#nik_lsm" + p1).val();
      param["nip"] = $("#nip" + p1).val();
      param["tinggi_baris"] = $("#tinggi_baris" + p1).val();

      if ($("#bulan" + p1).val() == "" || $("#bulan" + p1).val() == null) {
        $.messager.alert("SIMLURAH", "Pilih bulan terlebih dahulu!", "error");
        $("#bulan").focus();
        return;
      }
      // if (group_user == 2) {
      //   if ($("#bulan" + p1).val() == "" || $("#bulan" + p1).val() == null) {
      //     $.messager.alert("SIMLURAH", "Pilih bulan terlebih dahulu!", "error");
      //     $("#bulan").focus();
      //     return;
      //   }
      // }

      var url =
        host +
        "backoffice-cetak/laporan_rekap_penilaian_rt_rw?" +
        $.param(param);

      window.open(url, "_blank");

      break;

    case "export_konsolidasi_penilaian_kelrtrw":
      param["rt"] = $("#rt_" + p1).val();
      // param["rw"] = $("#rw_" + p1).val();
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["bulan"] = $("#bulan" + p1).val();
      param["rw"] = $("#rw" + p1).val();
      param["tgl_cetak"] = $("#tgl_cetak" + p1).val();
      // param["kelurahan"] = $("#kelurahan").val();
      param["nik_pembuat"] = $("#nik_pembuat" + p1).val();
      param["nik_lsm"] = $("#nik_lsm" + p1).val();
      param["nip"] = $("#nip" + p1).val();

      if ($("#rw" + p1).val() == "" || $("#rw" + p1).val() == null) {
        $.messager.alert("SIMLURAH", "Pilih RW terlebih dahulu!", "error");
        $("#rw").focus();
        return;
      }

      if ($("#bulan" + p1).val() == "" || $("#bulan" + p1).val() == null) {
        $.messager.alert("SIMLURAH", "Pilih bulan terlebih dahulu!", "error");
        $("#bulan").focus();
        return;
      }

      var url =
        host +
        "backoffice-cetak/laporan_konsolidasi_penilaian_rt_rw?" +
        $.param(param);

      window.open(url, "_blank");

      break;

    // case "export_rekap_penilaian_kelrtrw":
    //   param["rt"] = $("#rt_" + p1).val();

    //   param["rw"] = $("#rw_" + p1).val();

    //   param["kelurahan_id"] = $("#kelurahan_" + p1).val();

    //   param["bulan"] = $("#bulan").val();

    //   param["nik_pembuat"] = $("#nik_pembuat").val();

    //   param["nik_lsm"] = $("#nik_lsm").val();

    //   param["nip"] = $("#nip").val();

    //   var url = host + "backoffice-cetak/laporan_rekap_penilaian_rt_rw?" + $.param(param);

    //   window.open(url, "_blank");
    // break;

    // case "export_usulan_penilaian_rt_rw":
    //   param["rt"] = $("#rt_" + p1).val();

    //   param["rw"] = $("#rw_" + p1).val();

    //   param["kelurahan_id"] = $("#kelurahan_" + p1).val();

    //   param["bulan"] = $("#bulan").val();

    //   param["status_penilaian"] = $("#status_penilaian").val();

    //   param["nip"] = $("#nip").val();

    //   if ($("#nip").val() == '' || $("#nip").val() == null) {
    //     $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
    //   } else {

    //     var url = host + "backoffice-cetak/laporan_rekap_penilaian_rt_rw?" + $.param(param);

    //     window.open(url, "_blank");
    //   }
    //   break;

    case "export_rekap_penilaian_rt_rw":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nip"] = $("#nip").val();

      // if ($("#nip").val() == '' || $("#nip").val() == null) {
      //   $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      // } else {

      var url =
        host +
        "backoffice-cetak/laporan_rekap_penilaian_rt_rw?" +
        $.param(param);

      window.open(url, "_blank");

      // }

      break;

    case "rekap_penilaian_kelrtrw":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host +
        "backoffice-cetak/laporan_rekap_penilaian_kelrtrw?" +
        $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_penandatanganan":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_penandatanganan?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_daftar_agenda":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_daftar_agenda?" + $.param(param);

        window.open(url, "_blank");
      }
      
      break;

    case "export_laporan_hasil_agenda":

      param["tgl_mulai"]   = $("#date_start_" + p1).val();
      param["tgl_selesai"] = $("#date_end_" + p1).val();
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if (!param["nip"]) {
          $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
          return;
      }

      var url = host + "backoffice-cetak/laporan_hasil_agenda?" + $.param(param);
      window.open(url, "_blank");
      break;

    case "export_laporan_kendaraan":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url = host + "backoffice-cetak/laporan_kendaraan?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_pkl":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_pkl?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_ibadah":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_ibadah?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_sekolah":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_sekolah?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_kebersihan":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url =
          host + "backoffice-cetak/laporan_kebersihan?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    // case "export_laporan_keluarga":

    //   param["rt"] = $("#rt_" + p1).val();
    //   param["rw"] = $("#rw_" + p1).val();
    //   param["kelurahan"] = $("#kelurahan_" + p1).val();


    //   var url = host + "backoffice-cetak/laporan_keluarga?" + $.param(param);
    //   window.open(url, "_blank");

    //   break;

    // case "export_laporan_keluarga":
    //      var rw = "";
    //      var kat = $("#kat_" + p1).val();

    //     if (kat == "B.rw_") {
    //        rw = $("#key_" + p1).val();
    //     }

    //     var param = {};
    //     param["rt"] = $("#rt_" + p1).val();
    //     param["rw"] = $("#rw_" + p1).val();
    //     param["kelurahan"] = $("#kelurahan_" + p1).val();

    //      if (kat == "B.rw_") {
    //       param["rw"] = rw;
    //     } else {
    //       param["rw"] = $("#rw_" + p1).val();
    //     }

    //     var url = host + "backoffice-cetak/laporan_keluarga?" + $.param(param);
    //     window.open(url, "_blank");

    //     break;

    case "export_laporan_keluarga":

        var rw = "";
        var rt = "";
        var kelurahan = "";

        var kat = $("#kat_" + p1).val();

      if (kat == "B.rw") {
        rw = $("#key_" + p1).val();
      } else if (kat == "B.rt") {
        nama_lengkap = $("#key_" + p1).val();
      }

       var param = {};
      param["rw"] = $("#rw_" + p1).val();
      param["rt"] = $("#rt_" + p1).val();
      param["kelurahan"] = $("#kelurahan_" + p1).val();

       // 👇 Bagian penting: rw disesuaikan dengan kategori
      if (kat == "B.rw") {
        param["rw"] = rw;
      } else {
        param["rw"] = $("#rw_" + p1).val();
      }

      var url = host + "backoffice-cetak/laporan_keluarga?" + $.param(param);
      window.open(url, "_blank");

      break;



    // case "export_laporan_persuratan":
    //   param["rt"] = $("#rt_" + p1).val();

    //   param["rw"] = $("#rw_" + p1).val();

    //   param["kelurahan_id"] = $("#kelurahan_" + p1).val();

    //   var url = host + "backoffice-cetak/laporan_persuratan?" + $.param(param);

    //   window.open(url, "_blank");

    //   break;

    case "export_laporan_retribusi":
      param["rt"] = $("#rt_" + p1).val();
      param["rw"] = $("#rw_" + p1).val();
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_retribusi?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_staff":
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();
      param["kat"] = $("#kat_" + p1).val();
      param["key"] = $("#key_" + p1).val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        modal_prompt(
          {
            container_id: "modal_prompt_id",
            title: "Export Laporan Staff",
            form_id: "modal_prompt_form_id",
            confirm_id: "modal_prompt_confirm_id",
          },
          [
            {
              label: "Tanggal:",
              type: "input",
              className: "form-control form-control-sm tanggalnya",
              attr: {
                id: "tanggal",
                name: "tanggal",
              },
            },
            {
              label: "Filter PNS/NON PNS:",
              type: "select",
              className: "form-control form-control-sm",
              attr: {
                id: "pns_nonpns",
                name: "pns_nonpns",
              },

              dataOption: [
                {
                  id: "",
                  text: "-- Pilih --",
                },
                {
                  id: "pns",
                  text: "PNS",
                },
                {
                  id: "nonpns",
                  text: "NON PNS",
                },
              ],
            },
            {
              label: "PILIH JENIS CETAK:",
              type: "select",
              className: "form-control form-control-sm",
              attr: {
                id: "jenis_cetak",
                name: "jenis_cetak",
              },

              dataOption: [
                {
                  id: "",
                  text: "-- Pilih --",
                },
                {
                  id: "laporan_staff",
                  text: "ABSENSI",
                },
                {
                  id: "laporan_bpjs",
                  text: "LAPORAN BPJS",
                },
              ],
            },
            {
              label: "Kegiatan:",
              type: "input",
              className: "form-control form-control-sm",
              attr: {
                id: "keg_pegawai",
                name: "keg_pegawai",
              },
            },
          ]
        );

        $(".tanggalnya").datepicker({
          format: "yyyy-mm-dd",
        });
        document
          .getElementById("modal_prompt_confirm_id")
          .addEventListener("click", function () {
            const formElements = Array.from(
              document.querySelectorAll(
                "#modal_prompt_form_id input,#modal_prompt_form_id select"
              )
            ).filter((element) => element.name);
            for (let i = 0; i < formElements.length; i++) {
              const element = formElements[i];
              param[element.id] = element.value;
            }
            if (param["jenis_cetak"] == "laporan_bpjs") {
              var url =
                host + "backoffice-cetak/laporan_bpjs?" + $.param(param);
            } else {
              var url =
                host + "backoffice-cetak/laporan_staff?" + $.param(param);
            }
            window.open(url, "_blank");
            document.getElementById("modal_prompt_id").remove();
          });
      }

      break;

    case "export_laporan_rt_rw":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["bulan"] = $("#bulan").val();

      param["nip"] = $("#nip").val();

      var url = host + "backoffice-cetak/laporan_rt_rw?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_dasawisma":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan"] = $("#kelurahan_" + p1).val();

      // param["nik"] = nik;

      // param["nama_lengkap"] = nama_lengkap;

      // param["no_kk"] = no_kk;

      // param["status_data"] = status_data;

      var url = host + "backoffice-cetak/laporan_dasawisma?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_faskes":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_faskes?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_umkm":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_umkm?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_wamis":
      var rw = "";
      var jenis_wamis = "";
      var no_peserta = "";
      var nama = "";

      var kat = $("#kat_" + p1).val();

      if (kat == "a.nama") {
        nama = $("#key_" + p1).val();
      } else if (kat == "a.no_peserta") {
        no_peserta = $("#key_" + p1).val();
      } else if (kat == "b.rw") {
        rw = $("#key_" + p1).val();
      } else if (kat == "a.jenis_wamis"){
        jenis_wamis = $("#key_" + p1).val();
      }else {
        status_data = $("#key_" + p1).val();
      }

      var param = {};

      param["rw"] = rw;               // ⬅️ INI KUNCI
      param["jenis_wamis"] = jenis_wamis;
      param["no_peserta"] = no_peserta;
      param["nama"] = nama;
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();
      param["nip"] = $("#nip").val();

      if (kat == "b.rw") {
        param["rw"] = rw;
      } else {
        param["rw"] = $("#rw_" + p1).val();
      }

      if ($("#nip").val() == "" || $("#nip").val() == null) {
        $.messager.alert("SIMLURAH", "Please Select TTD First!", "error");
      } else {
        var url = host + "backoffice-cetak/laporan_wamis?" + $.param(param);

        window.open(url, "_blank");
      }
      break;

    case "export_laporan_ktp":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url = host + "backoffice-cetak/laporan_ktp?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_rekap_imb":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url = host + "backoffice-cetak/laporan_rekap_imb?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_hasil_skm":
      var param = {};

      param["tahun"] = $("#tahun_" + p1).val() || new Date().getFullYear();
      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      console.log("Param terkirim:", p1); // Tambahkan ini

      var url = host + "backoffice-cetak/laporan_hasil_skm?" + $.param(param);
      window.open(url, "_blank");

      break;

    case "export_laporan_hasil_skm2":
        var param = {};

        param["tahun"] = $("#tahun_" + p1).val() || new Date().getFullYear();
        param["kelurahan_id"] = $("#kelurahan_" + p1).val();

        console.log("Param terkirim:", p1); // Tambahkan ini

        var url = host + "backoffice-cetak/laporan_hasil_skm2?" + $.param(param);
        window.open(url, "_blank");

        break;

    case "export_laporan_penduduk":
      var nik = "";
      var nama_lengkap = "";
      var no_kk = "";
      var status_data = "";
      var rw = "";

      var kat = $("#kat_" + p1).val();

      if (kat == "A.nik") {
        nik = $("#key_" + p1).val();
      } else if (kat == "A.nama_lengkap") {
        nama_lengkap = $("#key_" + p1).val();
      } else if (kat == "A.no_kk") {
        no_kk = $("#key_" + p1).val();
      } else if (kat == "A.rw") {
        rw = $("#key_" + p1).val();
      } else {
        status_data = $("#key_" + p1).val();
      }

      var param = {};
      param["rt"] = $("#rt_" + p1).val();
      param["kelurahan"] = $("#kelurahan_" + p1).val();
      param["nik"] = nik;
      param["nama_lengkap"] = nama_lengkap;
      param["no_kk"] = no_kk;
      param["status_data"] = status_data;

      // 👇 Bagian penting: rw disesuaikan dengan kategori
      if (kat == "A.rw") {
        param["rw"] = rw;
      } else {
        param["rw"] = $("#rw_" + p1).val();
      }

      var url = host + "backoffice-cetak/laporan_penduduk?" + $.param(param);
      window.open(url, "_blank");

      break;

    case "export_laporan_penduduk_asing":
      no_passport = "";
      nama_lengkap = "";
      no_pengenalan = "";

      var kat = $("#kat_" + p1).val();

      if (kat == "A.no_passport") {
        no_passport = $("#key_" + p1).val();
      } else if (kat == "A.nama_lengkap") {
        nama_lengkap = $("#key_" + p1).val();
      } else {
        no_pengenalan = $("#key_" + p1).val();
      }

      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan"] = $("#kelurahan_" + p1).val();

      param["no_passport"] = no_passport;

      param["nama_lengkap"] = nama_lengkap;

      param["no_pengenalan"] = no_pengenalan;

      var url =
        host + "backoffice-cetak/laporan_penduduk_asing?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "reload_laporan_penduduk":
      $("#rt_" + p1).val("");

      $("#rw_" + p1).val("");

      $("#kelurahan_" + p1).prop("selectedIndex", 0);

      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_penduduk").datagrid("reload", param);

      break;

    case "generate_laporan_penduduk":
      param["rt"] = $("#rt_" + p1).val();

      param["rw"] = $("#rw_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_penduduk").datagrid("reload", param);

      break;

    case "export_laporan_persuratan":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      param["nik"] = $("#nik_" + p1).val();

      param["nip"] = $("#nip").val();

      var url = host + "backoffice-cetak/laporan_persuratan?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_rekap_usaha":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      var url = host + "backoffice-cetak/laporan_rekap_usaha?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_rekap_pengantar_kendaraan":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      var url = host + "backoffice-cetak/laporan_rekap_pengantar_kendaraan?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_persuratan_excel":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      param["nik"] = $("#nik_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_persuratan_excel?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_rekap_usaha_excel":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_rekap_usaha_excel?" + $.param(param);

      window.open(url, "_blank");

      break;
      
    case "export_laporan_rt_rw_excel":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_rt_rw_excel?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_rekap_pengantar_kendaraan_excel":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_rekap_pengantar_kendaraan_excel?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_keluarga_excel":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_keluarga_excel?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "reload_laporan_persuratan":
      $("#date_start_" + p1).val("");

      $("#date_end_" + p1).val("");

      $("#nik_" + p1).val("");

      $("#kelurahan_" + p1).prop("selectedIndex", 0);

      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["nik"] = $("#nik_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_persuratan").datagrid("reload", param);

      break;

    case "reload_laporan_rekap_usaha":
      $("#date_start_" + p1).val("");

      $("#date_end_" + p1).val("");

      $("#kelurahan_" + p1).prop("selectedIndex", 0);

      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_rekap_usaha").datagrid("reload", param);

      break;

    case "reload_laporan_rekap_pengantar_kendaraan":
      $("#date_start_" + p1).val("");

      $("#date_end_" + p1).val("");

      $("#kelurahan_" + p1).prop("selectedIndex", 0);

      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_rekap_pengantar_kendaraan").datagrid("reload", param);

      break;

    case "generate_laporan_persuratan":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nik"] = $("#nik_" + p1).val();

      param["nip"] = $("#nip").val();

      $("#grid_laporan_persuratan").datagrid("reload", param);

      break;

    case "generate_daftar_agenda_kegiatan":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nip"] = $("#nip").val();

      $("#grid_daftar_agenda_kegiatan").datagrid("reload", param);

      break;

    case "generate_laporan_hasil_kegiatan":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      param["nip"] = $("#nip").val();

      $("#grid_laporan_hasil_kegiatan").datagrid("reload", param);

      break;

    case "generate_laporan_rekap_usaha":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_rekap_usaha").datagrid("reload", param);

      break;

    case "generate_laporan_rekap_pengantar_kendaraan":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["jenis_surat"] = $("#jenis_surat_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_rekap_pengantar_kendaraan").datagrid("reload", param);

      break;

    case "export_laporan_persuratan_masuk":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host + "backoffice-cetak/laporan_persuratan_masuk?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "export_laporan_persuratan_masuk_excel":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url =
        host +
        "backoffice-cetak/laporan_persuratan_masuk_excel?" +
        $.param(param);

      window.open(url, "_blank");

      break;

    case "export_data_penduduk":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      var url = host + "backoffice-cetak/laporan_penduduk?" + $.param(param);

      window.open(url, "_blank");

      break;

    case "reload_laporan_persuratan_masuk":
      $("#date_start_" + p1).val("");

      $("#date_end_" + p1).val("");

      $("#kelurahan_" + p1).prop("selectedIndex", 0);

      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_persuratan_masuk").datagrid("reload", param);

      break;

    case "generate_laporan_persuratan_masuk":
      param["tgl_mulai"] = $("#date_start_" + p1).val();

      param["tgl_selesai"] = $("#date_end_" + p1).val();

      param["kelurahan_id"] = $("#kelurahan_" + p1).val();

      $("#grid_laporan_persuratan_masuk").datagrid("reload", param);

      break;

    case "import_penduduk":
      $.LoadingOverlay("show");

      $("#modalencuk").html("");

      $.post(
        host + "backoffice-form/import_data_penduduk",
        {
          editstatus: "add",
        },
        function (resp) {
          $("#headernya").html("<b>Form Import Data Excel Penduduk</b>");

          $("#modalencuk").html(resp);

          $("#pesanModal").modal("show");

          $.LoadingOverlay("hide", true);
        }
      );

      break;

    case "import_ktp":
      $.LoadingOverlay("show");

      $("#modalencuk").html("");

      $.post(
        host + "backoffice-form/import_data_ktp",
        {
          editstatus: "add",
        },
        function (resp) {
          $("#headernya").html("<b>Form Import Data Excel KTP Tercetak</b>");

          $("#modalencuk").html(resp);

          $("#pesanModal").modal("show");

          $.LoadingOverlay("hide", true);
        }
      );

      break;

    case "import_data_pegawai_kel_kec":
      $.LoadingOverlay("show");

      $("#modalencuk").html("");

      $.post(
        host + "backoffice-form/import_data_pegawai_kel_kec",
        {
          editstatus: "add",
        },
        function (resp) {
          $("#headernya").html("<b>Form Import Data Excel KTP Tercetak</b>");

          $("#modalencuk").html(resp);

          $("#pesanModal").modal("show");

          $.LoadingOverlay("hide", true);
        }
      );

      break;

    case "import_dasawisma":
      $.LoadingOverlay("show");

      $("#modalencuk").html("");

      $.post(
        host + "backoffice-form/import_data_dasawisma",
        {
          editstatus: "add",
        },
        function (resp) {
          $("#headernya").html("<b>Form Import Data Excel Dasawisma</b>");

          $("#modalencuk").html(resp);

          $("#pesanModal").modal("show");

          $.LoadingOverlay("hide", true);
        }
      );

      break;

    case "cetak_surat":
      var row = $("#grid_data_surat").datagrid("getSelected");
      // alert(row.cl_jenis_surat_id);
      if (row) {
        window.open(
          host +
            "backoffice-cetak/cetak_surat/" +
            row.cl_jenis_surat_id +
            "/" +
            row.tbl_data_penduduk_id +
            "/" +
            row.id,
          "_blank"
        );
      } else {
        $.messager.alert("SIMLURAH", "Please Select Data First!", "error");
      }

      break;

    case "daftar_esign":
      var row = $("#grid_data_surat").datagrid("getSelected");
      if (row) {
        var handle = window
          .open(
            host +
              "backoffice-cetak/daftar_esign/" +
              row.cl_jenis_surat_id +
              "/" +
              row.tbl_data_penduduk_id +
              "/" +
              row.id +
              "/" +
              encodeURIComponent($("#catatan-esign").val()) +
              "?status_esign=" +
              $('[name="status_esign"]:checked').val(),
            "_blank"
          )
          .blur();
      } else {
        $.messager.alert("SIMLURAH", "Please Select Data First!", "error");
      }

      break;

    case "cetak_himbauan":
      // alert('huj8');
      var row = $("#grid_surat_himbauan").datagrid("getSelected");

      // alert(row.no_surat);
      if (row) {
        var no = row.no_surat;
        var no_surat = no.replace(/[^a-zA-Z0-9]/g, "xxx");
        window.open(
          host + "backoffice-cetak/cetak_himbauan/" + no_surat + "/" + row.id,
          "_blank"
        );
      } else {
        $.messager.alert("SIMDESA", "Please Select Data First!", "error");
      }

      break;

    case "broadcast":
      // alert('huj8');
      var row = $("#grid_broadcast").datagrid("getSelected");

      // alert(row.no_surat);
      if (row) {
        var no = row.no_surat;
        var no_surat = no.replace(/[^a-zA-Z0-9]/g, "xxx");
        window.open(
          host + "backoffice-cetak/cetak_broadcast/" + no_surat + "/" + row.id,
          "_blank"
        );
      } else {
        $.messager.alert("SIMDESA", "Please Select Data First!", "error");
      }

      break;

    case "userrole":
      $.post(
        host + "backoffice-form/form_user_role",
        {
          id: p1,
          editstatus: "add",
        },
        function (resp) {
          $("#headernya").html(
            "<b>Form User Group Role Setting - " + p2 + "</b>"
          );

          $("#modalencuk").html(resp);

          $("#pesanModal").modal("show");
        }
      );

      break;
    case "permohonan":
      $.LoadingOverlay("show");
      $("#modalencuk").html("");
      $.post(
        host + "backoffice-form/permohonan",
        {
          editstatus: "add",
          id: p1,
        },
        function (resp) {
          $("#headernya").html("<b>Informasi Permohonan Persuratan</b>");
          $("#modalencuk").html(resp);
          $("#pesanModal").modal("show");
          $.LoadingOverlay("hide", true);
        }
      );
      break;
  }
}

function get_data_riwayat_esign() {
  if ($("#grid_data_esign").length) {
    var row = $("#grid_data_esign").datagrid("getSelected");
  } else {
    var row = $("#grid_data_surat").datagrid("getSelected");
  }
  if (row) {
    if (row.status_esign == 0 && (group_user == 4 || group_user == 5)) {
      $.messager.alert(
        "SIMLURAH",
        "Data bukan untuk tanda tangan elektronik",
        "info"
      );
      return;
    }

    if (
      group_user != 2 &&
      row.status_esign > 0 &&
      row.nip_pemeriksa_esign != nip_pegawai_user &&
      row.nip != nip_pegawai_user
    ) {
      $.messager.alert(
        "SIMLURAH",
        "Tidak ada tindakan yang dibutuhkan",
        "info"
      );
      return;
    }

    $("#nama_ttd").text(row.nama_ttd + " - " + row.jabatan_ttd);
    $("#pemeriksa_id").val(row.nip_pemeriksa_esign);
    $.ajax({
      url: host + "Backendxx/get_data_riwayat_esign/" + row.id,
      dataType: "json",
      cache: false,
      success: function (data) {
        var html = "";
        var no = 1;
        var status = [
          "Batal",
          "Disetujui",
          "Submit",
          "Diverifikasi",
          "Revisi",
          "Ditolak",
        ];

        if (group_user == 2 && row.status_esign == 0) {
          $("#pemeriksa_id").prop("disabled", false);
        } else {
          $("#pemeriksa_id").prop("disabled", true);
        }

        if (group_user == 2) {
          if (row.status_esign == 0 || row.status_esign == 4) {
            $("#list-radio-status-esign").html(
              '<label for="status_esign_2"><input type="radio" id="status_esign_2" name="status_esign" value="2" style="margin-left: 10px;" checked> Submit</label>'
            );
            $(".btn-simpan-esign").show();
          } else {
            if (row.status_esign == 2) {
              $("#list-radio-status-esign").html(
                '<label for="status_esign_0"><input type="radio" id="status_esign_0" name="status_esign" value="0" style="margin-left: 10px;" checked> Batal</label>'
              );
              $(".btn-simpan-esign").show();
            } else {
              $("#list-radio-status-esign").html(
                '<input type="radio" name="status_esign" value="' +
                  row.status_esign +
                  '" style="margin-left: 10px;display:none" checked>'
              );
              $(".btn-simpan-esign").hide();
            }
          }
        } else if (
          row.status_esign == 2 &&
          row.nip_pemeriksa_esign == nip_pegawai_user
        ) {
          $("#list-radio-status-esign").html(
            '<label for="status_esign_3"><input type="radio" id="status_esign_3" name="status_esign" value="3" style="margin-left: 10px;" checked> Verifikasi</label>\
              <label for="status_esign_4"><input type="radio" id="status_esign_4" name="status_esign" value="4" style="margin-left: 10px;"> Revisi</label>\
              <label for="status_esign_5"><input type="radio" id="status_esign_5" name="status_esign" value="5" style="margin-left: 10px;"> Tolak</label>'
          );
          $(".btn-simpan-esign").show();
        } else if (row.status_esign == 3 && row.nip == nip_pegawai_user) {
          $("#list-radio-status-esign").html(
            '<label for="status_esign_1"><input type="radio" id="status_esign_1" name="status_esign" value="1" checked> Setujui</label>\
              <label for="status_esign_4"><input type="radio" id="status_esign_4" name="status_esign" value="4" style="margin-left: 10px;"> Revisi</label>\
              <label for="status_esign_5"><input type="radio" id="status_esign_5" name="status_esign" value="5" style="margin-left: 10px;"> Tolak</label>'
          );
          $(".btn-simpan-esign").show();
        } else {
          $("#list-radio-status-esign").html(
            '<input type="radio" name="status_esign" value="' +
              row.status_esign +
              '" style="margin-left: 10px;display:none" checked>'
          );
          $(".btn-simpan-esign").hide();
        }

        if (row.status_esign == 3 && row.nip == nip_pegawai_user) {
          $("#akun-esign").show();
          $('[name="status_esign"]').change(function (e) {
            if (this.value == 1) {
              $("#akun-esign").show();
            } else {
              $("#akun-esign").hide();
              $("#akun-esign input").val("");
            }
          });
        } else {
          $("#akun-esign").hide();
        }

        if (data.length > 0) {
          $.each(data, function (i, v) {
            html +=
              '<div class="row"><div class="col-lg-1" align="right">' +
              no +
              '</div><div class="col-lg-11">';
            if (v.cl_user_group_id == 2) {
              html +=
                '<label class="fw-normal">' +
                v.nama_pegawai +
                " - Operator - " +
                v.created_at +
                " - " +
                status[v.status_esign] +
                "</label>";
            } else if (v.nip_pegawai == row.nip) {
              html +=
                '<label class="fw-normal">' +
                v.nama_pegawai +
                " - Yang bertanda tangan - " +
                v.created_at +
                " - " +
                status[v.status_esign] +
                "</label>";
            } else if (v.nip_pegawai == row.nip_pemeriksa_esign) {
              html +=
                '<label class="fw-normal">' +
                v.nama_pegawai +
                " - Verifikator - " +
                v.created_at +
                " - " +
                status[v.status_esign] +
                "</label>";
            }
            html +=
              "<p>" +
              decodeURIComponent(v.catatan) +
              ' <a href="' +
              host +
              v.file_src +
              '" target="_blank">Dokumen</a></p></div></div>';
            no++;
          });
        } else {
          html = '<span class="text-center">Tidak ada riwayat</span>';
        }
        $("#list-riwayat-esign").html(html);

        $("#modal-esign").modal("show");
      },
      error: function (xhr, status, error) {
        $.messager.alert(status, error, "error");
      },
    });
  } else {
    $.messager.alert("SIMLURAH", "Please Select Data First!", "error");
  }
}

function tampilkanPetugasRTRW() {
  $.ajax({
    url: base_url + "Backendxx/get_petugas_rt_rw", // pastikan URL ini benar
    type: "POST",
    dataType: "json",
    success: function (res) {
      if (Array.isArray(res) && res.length > 0) {
        let html = `
          <form id="form_kirim_petugas">
            <table class="table table-bordered table-sm" style="margin-top: 15px; font-size: 0.9rem;">
              <thead class="thead-dark">
                <tr>
                  <th><input type="checkbox" id="cek_semua" /></th>
                  <th>No</th>
                  <th>Nama Lengkap</th>
                  <th>NIK</th>
                  <th>Jabatan</th>
                  <th>No. HP</th>
                  <th>Kelurahan</th>
                </tr>
              </thead>
              <tbody>
        `;

        res.forEach((item, i) => {
          html += `
            <tr>
              <td><input type="checkbox" name="data_terpilih[]" value="${
                item.id
              }"></td>
              <td>${i + 1}</td>
              <td>${item.nama_lengkap}</td>
              <td>${item.nik}</td>
              <td>${item.jab_rt_rw}</td>
              <td>${item.no_hp}</td>
              <td>${item.kelurahan}</td>
            </tr>
          `;
        });

        html += `
              </tbody>
            </table>
            <div style="margin-top:10px;">
              <button type="button" class="btn btn-primary btn-sm" id="kirim_ke_sekcam">Kirim ke Sekcam</button>
            </div>
          </form>
        `;

        $("#tabel_petugas_rt_rw").html(html);

        // Aktifkan checkbox "Pilih Semua"
        $(document)
          .off("click", "#cek_semua")
          .on("click", "#cek_semua", function () {
            $("input[name='data_terpilih[]']").prop("checked", this.checked);
          });

        // Kirim ke Sekcam
        $(document)
          .off("click", "#kirim_ke_sekcam")
          .on("click", "#kirim_ke_sekcam", function () {
            const formData = $("#form_kirim_petugas").serialize();

            $.ajax({
              url: base_url + "Backendxx/kirim_ke_sekcam",
              type: "POST",
              data: formData,
              dataType: "json",
              success: function (res) {
                console.log("RESPON SERVER:", res);
                if (res.status === "ok") {
                  alert("Data berhasil dikirim ke Sekcam.");
                  location.reload();
                } else {
                  alert(
                    "Gagal mengirim data: " + (res.message || "Tidak diketahui")
                  );
                }
              },
              error: function (xhr, status, error) {
                console.error("Error AJAX:", error);
                alert("Terjadi kesalahan dalam pengiriman data.");
              },
            });
          });
      } else {
        $("#tabel_petugas_rt_rw").html(
          "<div class='alert alert-warning'>Data petugas tidak ditemukan.</div>"
        );
      }
    },
    error: function (err) {
      console.log(err);
      $("#tabel_petugas_rt_rw").html(
        "<div class='alert alert-danger'>Gagal mengambil data petugas RT/RW.</div>"
      );
    },
  });
}

// Fungsi kirim data ke Sekcam
// function kirimKeSekcam() {
//   const terpilih = $("input[name='data_terpilih[]']:checked")
//     .map(function () {
//       return this.value;
//     })
//     .get();

//   if (terpilih.length === 0) {
//     alert("Silakan pilih data petugas terlebih dahulu.");
//     return;
//   }

//     $.ajax({
//       url: '<?= base_url("Backendxx/kirim_ke_sekcam") ?>',
//       method: 'POST',
//       data: { data_terpilih: dataTerpilih }, // pastikan ini formatnya benar
//       success: function(response) {
//           if (response.status) {
//               alert('Data berhasil dikirim ke Sekcam.');
//               location.reload(); // ini untuk refresh data
//           } else {
//               alert(response.pesan); // untuk tangani "tidak ada data dipilih"
//           }
//       },
//       error: function(xhr, status, error) {
//           alert('Terjadi kesalahan: ' + error);
//       }
//     });
// }

// Fungsi kirim data ke Sekcam
function kirimKeSekcam() {
  $("#btn-kirim-ke-sekcam").on("click", function () {
    var dataTerpilih = $("input[name='data_terpilih[]']:checked")
      .map(function () {
        return $(this).val();
      })
      .get();

    if (dataTerpilih.length === 0) {
      alert("Silakan pilih data terlebih dahulu.");
      return;
    }

    $.ajax({
      url: '<?= base_url("Backendxx/kirim_ke_sekcam") ?>',
      method: "POST",
      data: { data_terpilih: dataTerpilih },
      dataType: "json",
      success: function (response) {
        alert(response.pesan);
        if (response.status) {
          location.reload(); // reload halaman agar data & ceklis terupdate
        }
      },
      error: function (xhr, status, error) {
        alert("Terjadi kesalahan: " + error);
      },
    });
  });
}

function submit_form(frm, func) {
  var url = jQuery("#" + frm).attr("url");

  // $.messager.progress();

  jQuery("#" + frm).form("submit", {
    url: url,

    onSubmit: function () {
      var isValid = $(this).form("validate");

      if (!isValid) {
        //$.messager.progress('close');	// hide progress bar while the form is invalid
      }

      return isValid;
    },

    success: function (data) {
      if (func == undefined) {
        if (data == "1") {
          pesan("Data Sudah Disimpan ", "Sukses");
        } else {
          pesan(data, "Result");
        }
      } else {
        func(data);
      }

      //$.messager.progress('close');
    },

    error: function (data) {
      if (func == undefined) {
        pesan(data, "Error");
      } else {
        func(data);
      }
    },
  });
}

function fillCombo(url, SelID, value, value2, value3, value4) {
  //if(Ext.get(SelID).innerHTML == "") return false;

  if (value == undefined) value = "";

  if (value2 == undefined) value2 = "";

  if (value3 == undefined) value3 = "";

  if (value4 == undefined) value4 = "";

  $("#" + SelID).empty();

  $.post(
    url,
    {
      v: value,
      v2: value2,
      v3: value3,
      v4: value4,
    },
    function (data) {
      $("#" + SelID).append(data);
    }
  );
}

function formatDate(date) {
  console.log(date);

  var y = date.getFullYear();

  var m = date.getMonth() + 1;

  var d = date.getDate();

  return y + "-" + (m < 10 ? "0" + m : m) + "-" + (d < 10 ? "0" + d : d);
}

function parserDate(s) {
  if (!s) return new Date();

  var ss = s.split("-");

  var y = parseInt(ss[0], 10);

  var m = parseInt(ss[1], 10);

  var d = parseInt(ss[2], 10);

  if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
    return new Date(y, m - 1, d);
  } else {
    return new Date();
  }
}

function clear_form(id) {
  $("#" + id)
    .find("input[type=text], textarea,select")
    .val("");

  //$('.angka').numberbox('setValue',0);
}

var divcontainerz;

function windowLoading(html, judul, width, height) {
  divcontainerz = "win" + Math.floor(Math.random() * 9999);

  $("<div id=" + divcontainerz + "></div>").appendTo("body");

  divcontainerz = "#" + divcontainerz;

  $(divcontainerz).html(html);

  $(divcontainerz).css("padding", "5px");

  $(divcontainerz).window({
    title: judul,

    width: width,

    height: height,

    autoOpen: false,

    modal: true,

    maximizable: false,

    resizable: false,

    minimizable: false,

    closable: false,

    collapsible: false,
  });

  $(divcontainerz).window("open");
}

function winLoadingClose() {
  $(divcontainerz).window("close");

  //$(divcontainer).html('');
}

function loadingna() {
  windowLoading(
    "<img src='" +
      host +
      "__assets/backend/img/loading.gif' style='position: fixed;top: 50%;left: 50%;margin-top: -10px;margin-left: -25px;'/>",
    "Sedang Proses, Mohon Tunggu",
    200,
    100
  );
}

function NumberFormat(value) {
  var jml = new String(value);

  if (jml == "null" || jml == "NaN") jml = "0";

  jml1 = jml.split(".");

  jml2 = jml1[0];

  amount = jml2.split("").reverse();

  var output = "";

  for (var i = 0; i <= amount.length - 1; i++) {
    output = amount[i] + output;

    if ((i + 1) % 3 == 0 && amount.length - 1 !== i) output = "." + output;
  }

  //if(jml1[1]===undefined) jml1[1] ="00";

  // if(isNaN(output))  output = "0";

  return output; // + "." + jml1[1];
}

function showErrorAlert(reason, detail) {
  var msg = "";

  if (reason === "unsupported-file-type") {
    msg = "Unsupported format " + detail;
  } else {
    console.log("error uploading file", reason, detail);
  }

  $(
    '<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
      "<strong>File upload error</strong> " +
      msg +
      " </div>"
  ).prependTo("#alerts");
}

function konversi_pwd_text(id) {
  if ($("input#" + id)[0].type == "password") $("input#" + id)[0].type = "text";
  else $("input#" + id)[0].type = "password";
}

function gen_editor(id) {
  tinymce.init({
    selector: id,

    height: 200,

    plugins: [
      "advlist autolink lists link image charmap print preview anchor",

      "searchreplace visualblocks code fullscreen",

      "insertdatetime media table contextmenu paste jbimages",
    ],

    // ===========================================

    // PUT PLUGIN'S BUTTON on the toolbar

    // ===========================================

    menubar: true,

    toolbar:
      "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ",

    // ===========================================

    // SET RELATIVE_URLS to FALSE (This is required for images to display properly)

    // ===========================================

    relative_urls: false,
  });

  tinyMCE.execCommand("mceRemoveControl", true, id);

  tinyMCE.execCommand("mceAddControl", true, id);
}

function cariData(divnya, post_search, acaknya) {
  if (post_search == "") {
    var post_search = {};

    post_search["kat"] = $("#kat_" + acaknya).val();
    post_search["key"] = $("#key_" + acaknya).val();
    post_search["kelurahan"] = $("#kelurahan_" + acaknya).val();
    post_search["bulan"] = $("#bulan").val();
    post_search["status_tab"] = $("#status_tab_" + acaknya).val();

    if (divnya == "rekap_penilaian_kelrtrw") {
      post_search["rw"] = $("#rw").val();
    }
  }

  $("#grid_" + divnya).datagrid("reload", post_search);
}


function handleKeyPress(event) {
  // Cek apakah tombol yang ditekan adalah tombol "Enter" (kode 13)
  if (event.keyCode === 13) {
    // Panggil fungsi pencarian jika tombol "Enter" ditekan
    $("#cari").click();
  }
}

function proseslaporan(divnya, table, acak) {
  var post_report = {};

  switch (divnya) {
    case "laporan_tukin_persatker":
      post_report["periode"] = $("#periode_" + acak).val();

      post_report["satker"] = $("#filter_satker_" + acak).val();

      break;

    case "laporan_tukin_perkelas":
      post_report["periode"] = $("#periode_" + acak).val();

      post_report["kelas"] = $("#filter_kelas_" + acak).val();

      break;

    case "rekap_tukin_persatker":
      post_report["periode"] = $("#periode_" + acak).val();

      break;

    case "rekap_tukin_perkelas":
      post_report["periode"] = $("#periode_" + acak).val();

      break;
  }

  $("#grid_" + divnya).datagrid("reload", post_report);
}

function advancedSearch(divnya, table, acak, type) {
  var post_search = {};

  if (type == "balikin") {
    $("#no_dok_" + acak).val("");

    $("#jns_dok_" + acak).val("");

    $("#tgl_arsip_" + acak).val("");

    $("#perihal_" + acak).val("");

    $("#pengirim_" + acak).val("");
  } else {
    post_search["advanced_search"] = "advanced";

    post_search["no_dokumen"] = $("#no_dok_" + acak).val();

    post_search["jenis_dokumen"] = $("#jns_dok_" + acak).val();

    post_search["tanggal_arsip"] = $("#tgl_arsip_" + acak).val();

    post_search["perihal"] = $("#perihal_" + acak).val();

    post_search["pengirim"] = $("#pengirim_" + acak).val();

    post_search["table"] = table;
  }

  $("#grid_" + divnya).datagrid("reload", post_search);

  //$('#grid_'+divnya).datagrid('refreshRow');

  /*

if($('#kat_'+acak).val()!=''){

grid_nya.datagrid('reload',post_search);

}else{

$.messager.alert('Aldeaz Basarnas Tukin',"Pilih Kategori Pencarian",'error');

}

//$('#grid_'+typecari).datagrid('reload', post_search);

*/
}

function simpan_form(id_form, id_cancel, msg) {
  if ($("#" + id_form).form("validate")) {
    loadingna();

    submit_form(id_form, function (r) {
      //console.log(r)

      if (r == 1) {
        $.messager.alert("Basarnas Tukin", msg, "info");

        $("#" + id_cancel).trigger("click");

        grid_nya.datagrid("reload");

        winLoadingClose();
      } else {
        console.log(r);

        $.messager.alert(
          "Basarnas Tukin",
          "Tidak Dapat Menyimpan Data " + r,
          "error"
        );

        winLoadingClose();
      }
    });
  } else {
    $.messager.alert("Basarnas Tukin", "Isi Data Yang Kosong ", "info");
  }
}

function get_form(mod, sts, acak) {
  param = {};

  //if(sts=='edit_flag'){param['editstatus']='edit';}else{param['editstatus']=sts;}

  param["editstatus"] = sts;

  switch (mod) {
    case "pembayaran":
      if (sts == "edit" || sts == "delete") {
        var row = $("#list_voucher_" + acak).datagrid("getSelected");

        if (row) {
          if (sts == "edit") {
            $("#editstatus_" + acak).val("edit");

            $("#id_" + acak).val(row.id);

            $("#jumlah_bayar" + acak).val(row.jumlah_bayar);

            $("#tgl_pembayaran" + acak).val(row.tgl_pembayaran);

            $("#modal_nya").modal("show");

            if (row.file) {
              $("#upl_ex_" + acak).html("File Exist :" + row.file);
            }
          } else {
            $.messager.confirm(
              "PT. Dienka Utama",
              "Anda Yakin Ingin Menghapus Data Ini ?",
              function (re) {
                if (re) {
                  loadingna();

                  $.post(
                    host + "backoffice-simpan/tbl_pembayaran_invoice",
                    {
                      editstatus: "delete",
                      id: row.id,
                    },
                    function (r) {
                      if (r == 1) {
                        $.messager.alert(
                          "PT. Dienka Utama",
                          "Data Sudah Terhapus",
                          "info"
                        );

                        $("#editstatus_" + acak).val("add");

                        $("#id_" + acak).val("");

                        $("#jumlah_bayar" + acak).val("");

                        $("#tgl_pembayaran" + acak).val("");

                        $("#modal_nya").modal("hide");

                        $("#list_voucher_" + acak).datagrid("reload");
                      } else {
                        $.messager.alert(
                          "PT. Dienka Utama",
                          "Proses Simpan Data Gagal " + r,
                          "warning"
                        );
                      }

                      winLoadingClose();
                    }
                  );
                }
              }
            );
          }
        } else {
          $.messager.alert(
            "PT. Dienka Utama",
            "Pilih Data Dalam List Grid",
            "error"
          );
        }
      } else {
        $("#editstatus_" + acak).val("add");

        $("#id_" + acak).val("");

        $("#jumlah_bayar" + acak).val("");

        $("#tgl_pembayaran" + acak).val("");

        $("#modal_nya").modal("show");

        $("#upl_ex_" + acak).html("");
      }

      break;

    case "voucher_management":
      if (sts == "edit" || sts == "delete") {
        var row = $("#list_voucher_" + acak).datagrid("getSelected");

        if (row) {
          if (sts == "edit") {
            $("#editstatus_" + acak).val("edit");

            $("#id_" + acak).val(row.id);

            $("#nama_pengeluaran" + acak).val(row.nama_pengeluaran);

            $("#qty" + acak).val(row.qty);

            $("#jumlah" + acak).val(row.jumlah);

            $("#total" + acak).val(row.total);

            $("#modal_nya").modal("show");

            if (row.file) {
              $("#upl_ex_" + acak).html("File Exist :" + row.file);
            }

            /*

if(row.tipe == "DV"){

$('#invoice_voucher').hide();

$('#tab-2').hide();

$('#tab-2').removeClass("active");



$('#daily_voucher').show();

$('#tab-1').show();

$('#tab-1').addClass("active");

}else if(row.tipe == "IV"){

$('#daily_voucher').hide();

$('#tab-1').hide();

$('#tab-1').removeClass("active");



$('#invoice_voucher').show();

$('#tab-2').show();

$('#tab-2').addClass("active");

}

*/

            //$('#invoice_voucher').hide();

            //$('#tab-2').hide();
          } else {
            $.messager.confirm(
              "PT. Dienka Utama",
              "Anda Yakin Ingin Menghapus Data Ini ?",
              function (re) {
                if (re) {
                  loadingna();

                  $.post(
                    host + "backoffice-simpan/tbl_pengeluaran_invoice",
                    {
                      editstatus: "delete",
                      id: row.id,
                    },
                    function (r) {
                      if (r == 1) {
                        $.messager.alert(
                          "PT. Dienka Utama",
                          "Data Sudah Terhapus",
                          "info"
                        );

                        $("#editstatus_" + acak).val("add");

                        $("#id_" + acak).val("");

                        $("#nama_pengeluaran" + acak).val("");

                        $("#qty" + acak).val("");

                        $("#jumlah" + acak).val("");

                        $("#total" + acak).val("");

                        $("#modal_nya").modal("hide");

                        $("#list_voucher_" + acak).datagrid("reload");
                      } else {
                        $.messager.alert(
                          "PT. Dienka Utama",
                          "Proses Simpan Data Gagal " + r,
                          "warning"
                        );
                      }

                      winLoadingClose();
                    }
                  );
                }
              }
            );
          }
        } else {
          $.messager.alert(
            "PT. Dienka Utama",
            "Pilih Data Dalam List Grid",
            "error"
          );
        }
      } else {
        $("#editstatus_" + acak).val("add");

        $("#id_" + acak).val("");

        $("#nama_pengeluaran" + acak).val("");

        $("#qty" + acak).val("");

        $("#jumlah" + acak).val("");

        $("#total" + acak).val("");

        /*

$('#daily_voucher').show();

$('#invoice_voucher').show();

//$('#tab-2').css({"display":"inline"});

$('#tab-1').show();

$('#tab-1').addClass("active");

$('#tab-2').removeClass("active");

*/

        $("#modal_nya").modal("show");

        $("#upl_ex_" + acak).html("");
      }

      break;
  }
}

function formatDate(date) {
  var bulan = date.getMonth() + 1;

  var tgl = date.getDate();

  if (bulan < 10) {
    bulan = "0" + bulan;
  }

  if (tgl < 10) {
    tgl = "0" + tgl;
  }

  return date.getFullYear() + "-" + bulan + "-" + tgl;
}

function get_report(mod, acak) {
  var param = {};

  switch (mod) {
    case "report_inv_paid":

    case "report_inv_unpaid":
      param["start_date"] = $("#start_date_" + acak).datebox("getValue");

      param["end_date"] = $("#end_date_" + acak).datebox("getValue");

      param["type_trans"] = $("#type_transaction_" + acak).val();

      break;
  }

  $("#isi_report_" + acak)
    .addClass("loading")
    .html("");

  $.post(host + "Basarnas-Report/" + mod, param, function (r) {
    $("#isi_report_" + acak)
      .removeClass("loading")
      .html(r);
  });
}

function myparser(s) {
  if (!s) return new Date();

  var ss = s.split("-");

  var y = parseInt(ss[0], 10);

  var m = parseInt(ss[1], 10);

  var d = parseInt(ss[2], 10);

  if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
    return new Date(y, m - 1, d);
  } else {
    return new Date();
  }
}

function get_detil(mod, id_data, par1) {
  $("#grid_nya_" + mod).hide();

  $("#detil_nya_" + mod)
    .html("")
    .show()
    .addClass("loading");

  $.post(
    host + "Basarnas-GetDetil",
    {
      mod: mod,
      id: id_data,
    },
    function (r) {
      $("#detil_nya_" + mod)
        .html(r)
        .removeClass("loading");
    }
  );
}

function openWindowWithPost(url, params) {
  var newWindow = window.open(url, "winpost");

  if (!newWindow) return false;

  var html = "";

  html +=
    "<html><head></head><body><form  id='formid' method='post' action='" +
    url +
    "'>";

  $.each(params, function (key, value) {
    if (value instanceof Array || value instanceof Object) {
      $.each(value, function (key1, value1) {
        html +=
          "<input type='hidden' name='" +
          key +
          "[" +
          key1 +
          "]' value='" +
          value1 +
          "'/>";
      });
    } else {
      html += "<input type='hidden' name='" + key + "' value='" + value + "'/>";
    }
  });

  html +=
    "</form><script type='text/javascript'>document.getElementById(\"formid\").submit()</script></body></html>";

  newWindow.document.write(html);

  return newWindow;
}

function export_data(type, mod, acak) {
  var url = host + "Basarnas-Cetak";

  var params = {};

  switch (mod) {
    case "laporan_tukin_persatker":
      params["satker"] = $("#filter_satker_" + acak).val();

      break;

    case "laporan_tukin_perkelas":
      params["kelas"] = $("#filter_kelas_" + acak).val();

      break;
  }

  params["periode"] = $("#periode_" + acak).val();

  params["mod"] = mod;

  params["type"] = type;

  openWindowWithPost(url, params);
}

function cari_row_div(mod, param) {
  switch (mod) {
    case "bd_form_ppjk":
      var io_number = $("#io_number_" + param).val();

      var partial_no = $("#partial_no_" + param).val();

      $.post(
        host + "backend/getdisplay/getimporter",
        {
          io_number: io_number,
          partial_no: partial_no,
        },
        function (resp) {
          obj = JSON.parse(resp);

          if (obj.sts == "0") {
            $.messager.alert(
              "VPTI - Contact Center",
              "IO Number is already in system <br/> Tiket No : " + obj.tiket_no,
              "warning"
            );
          } else {
            $("#importer_id_" + param).val(obj.data.importer_id);

            $("#commodity_code_" + param).val(obj.data.commodity_code);

            $("#importer_name_" + param).val(
              obj.data.importer_type + " " + obj.data.importer_name
            );

            $("#commodity_name_" + param).val(obj.data.description);

            $("#ls_number_" + param).val(obj.data.ls_number);
          }
        }
      );

      break;
  }
}

function remove_row_div(mod, param) {
  switch (mod) {
    case "bd_form_ppjk":
      $("#tr_ppjk_" + param).remove();

      break;
  }
}

function tambah_row_div(mod, param) {
  html = "";

  switch (mod) {
    case "bd_form_ppjk":
      var no = idx_row_div;

      idx_row_div++;

      html +=
        '<div id="tr_ppjk_' +
        idx_row_div +
        '" idx="' +
        idx_row_div +
        '" style="border:1px solid #F0F0F0;padding:10px;">';

      html += '	<div class="row">';

      html += '  		<div class="col-md-12">';

      //html += '  			<b>'+idx_row_div+'</b>';

      html += '  			<hr style="margin-top:0px;"/>';

      html += "		</div>";

      html += '		<div class="col-md-2">';

      html += '			<div class="form-group">';

      html +=
        '				<label>VO / VR Number </label> <input type="text" name="io_number[]" id="io_number_' +
        idx_row_div +
        '" class="form-control " autocomplete="off">';

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-2">';

      html += '			<div class="form-group">';

      html +=
        '				<label>Partial No. </label> <input type="text" name="partial_no[]" id="partial_no_' +
        idx_row_div +
        '" class="form-control " autocomplete="off">';

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-1" style="margin-top:24px;">';

      html +=
        '			<a id="search_' +
        idx_row_div +
        '" onclick="cari_row_div(\'' +
        mod +
        "', '" +
        idx_row_div +
        '\');" class="btn btn-small btn-info search" href="javascript:void(0);">';

      html += "				Search";

      html += "			</a>";

      html += "		</div>";

      html += '		<div class="col-md-6">';

      html +=
        '			<input type="hidden" name="importer_id[]" id="importer_id_' +
        idx_row_div +
        '" /> <input type="hidden" name="commodity_name[]" id="commodity_name_' +
        idx_row_div +
        '" />';

      html += '			<div class="form-group">';

      html +=
        '				<label>Importer Name </label> <input type="text" name="importer_name[]" id="importer_name_' +
        idx_row_div +
        '" class="form-control importer_name_tyypehead" autocomplete="off">';

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-1" style="margin-top:24px;">';

      html +=
        '			<a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="remove_row_div(\'' +
        mod +
        "', '" +
        idx_row_div +
        '\');"><i class="fa fa-times"></i></a>';

      html += "		</div>";

      html += "	</div>";

      html += '	<div class="row">';

      html += '		<div class="col-md-5">';

      html += '			<div class="form-group">';

      html +=
        '				<label>Commodity </label> <select id="commodity_code_' +
        idx_row_div +
        '" idx="' +
        idx_row_div +
        '" name="commodity_code[]" class="form-control commoditynya"> ' +
        combo_commodity +
        " </select>";

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-2">';

      html += '			<div class="form-group">';

      html +=
        '				<label>LS No. </label> <input type="text" name="ls_number[]" id="ls_number_' +
        idx_row_div +
        '" class="form-control " autocomplete="off">';

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-2">';

      html += '			<div class="form-group">';

      html +=
        '				<label>Request Type <font color="red">*</font></label> <select id="cl_request_type_id_' +
        idx_row_div +
        '" name="cl_request_type_id[]" class="form-control"> ' +
        combo_cl_request_type +
        " </select>";

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-2">';

      html += '			<div class="form-group">';

      html +=
        '				<label>Request Status <font color="red">*</font></label> <select id="request_status_' +
        idx_row_div +
        '" name="request_status[]" class="form-control"> ' +
        combo_request_status +
        " </select>";

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-6">';

      html += '			<div class="form-group">';

      html +=
        '				<label>Request Information </label> <input type="text" name="request_information[]" id="request_information_' +
        idx_row_div +
        '" class="form-control" autocomplete="off">';

      html += "			</div>";

      html += "		</div>";

      html += '		<div class="col-md-5">';

      html += '			<div class="form-group">';

      html +=
        '				<label>Solution </label> <input type="text" name="solution[]" id="solution_' +
        idx_row_div +
        '" class="form-control" autocomplete="off">';

      html += "			</div>";

      html += "		</div>";

      html += "	</div>";

      html += "</div>";

      break;
  }

  $("#" + mod).append(html);

  $(".importer_name_tyypehead").typeahead({
    source: data_importer,

    autoSelect: true,

    items: 10,
  });

  $(".commoditynya").on("change", function () {
    var comm_text = $(this).find("option:selected").attr("desc");

    var idx = $(this).attr("idx");

    $("#commodity_name_" + idx).val(comm_text);
  });
}

function tambah_row(mod, param) {
  var tr_table;

  switch (mod) {
    case "anggota_keluarga":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya" idx="' +
        idx_row +
        '" id="nik_' +
        idx_row +
        '" name="nik[]" >';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control" idx="' +
        idx_row +
        '" id="cl_status_hubungan_keluarga_id_' +
        idx_row +
        '" name="cl_status_hubungan_keluarga_id[]" >';

      tr_table += combo_hubungan_keluarga;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "saksi_saksi":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_saksi_' +
        idx_row +
        '" name="nama_saksi[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="pekerjaan_saksi_' +
        idx_row +
        '" name="pekerjaan_saksi[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "saksi_mengetahui":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_mengetahui_' +
        idx_row +
        '" name="nama_mengetahui[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="pekerjaan_mengetahui_' +
        idx_row +
        '" name="pekerjaan_mengetahui[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "saksi_saksi_tanah":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_saksi_tanah_' +
        idx_row +
        '" name="nama_saksi_tanah[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="pekerjaan_saksi_tanah_' +
        idx_row +
        '" name="pekerjaan_saksi_tanah[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "dasar":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="dasar_' +
        idx_row +
        '" name="dasar[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "wajib_pihak2":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="wajib_pihak2_' +
        idx_row +
        '" name="wajib_pihak2[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "dasar_ketentuan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="dasar_ketentuan_' +
        idx_row +
        '" name="dasar_ketentuan[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "anggota_pkl":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="anggota_pkl_' +
        idx_row +
        '" name="anggota_pkl[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "perjanjian_damai":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control idx="' +
        idx_row +
        '" id="isi_perjanjian_' +
        idx_row +
        '" name="isi_perjanjian[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "pernyataan_tj":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control idx="' +
        idx_row +
        '" id="isi_pernyataan_' +
        idx_row +
        '" name="isi_pernyataan[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "lampiran_berkas":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control idx="' +
        idx_row +
        '" id="lampiran_berkas_' +
        idx_row +
        '" name="lampiran_berkas[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "dokumen_lampiran_pencairan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control idx="' +
        idx_row +
        '" id="isi_dokumen' +
        idx_row +
        '" name="isi_dokumen[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "lampiran_berkas":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control idx="' +
        idx_row +
        '" id="lampiran_berkas_' +
        idx_row +
        '" name="lampiran_berkas[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "ahli_waris":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_ahli_waris_' +
        idx_row +
        '" name="nama_ahli_waris[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="nik_ahli_waris_' +
        idx_row +
        '" name="nik_ahli_waris[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya" idx="' +
        idx_row +
        '" id="hubungan_waris_' +
        idx_row +
        '" name="hubungan_waris[]" >';

      tr_table += hubungan_waris;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya" idx="' +
        idx_row +
        '" id="status_waris_' +
        idx_row +
        '" name="status_waris[]" >';

      tr_table += status_waris;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "kuasa_waris":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_ahli_waris_' +
        idx_row +
        '" name="nama_ahli_waris[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "yth":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="yth_' +
        idx_row +
        '" name="yth[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "penugasan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_bertugas_' +
        idx_row +
        '" name="nama_bertugas[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="jabatan_' +
        idx_row +
        '" name="jabatan[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "tugas_kec":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_bertugas_' +
        idx_row +
        '" name="nama_bertugas[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="pangkat_petugas_' +
        idx_row +
        '" name="pangkat_petugas[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nip_petugas_' +
        idx_row +
        '" name="nip_petugas[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="jabatan_' +
        idx_row +
        '" name="jabatan[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "penerima_nota":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="nama_penerima_nota_' +
        idx_row +
        '" name="nama_penerima_nota[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="unit_kerja_lama_' +
        idx_row +
        '" name="unit_kerja_lama[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="perbantuan_pada_' +
        idx_row +
        '" name="perbantuan_pada[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "pergantian_personil":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="petugas_diganti_' +
        idx_row +
        '" name="petugas_diganti[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="petugas_pengganti_' +
        idx_row +
        '" name="petugas_pengganti[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "orang_sama":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_dalam_dokumen_' +
        idx_row +
        '" name="nama_dalam_dokumen[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="dokumen_pendukung_' +
        idx_row +
        '" name="dokumen_pendukung[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="no_dokumen_' +
        idx_row +
        '" name="no_dokumen[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="tempat_lahir_' +
        idx_row +
        '" name="tempat_lahir[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tgl_lahir_dokumen_' +
        idx_row +
        '" name="tgl_lahir_dokumen[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="alamat_dokumen_' +
        idx_row +
        '" name="alamat_dokumen[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="dikeluarkan_oleh_' +
        idx_row +
        '" name="dikeluarkan_oleh[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2"  idx="' +
        idx_row +
        '" id="tgl_dikeluarkan_' +
        idx_row +
        '" name="tgl_dikeluarkan[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "pernyataan_lahir":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="nama_anak_pernyataan_' +
          idx_row +
          '" name="nama_anak_pernyataan[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="tempat_lahir_anak_' +
          idx_row +
          '" name="tempat_lahir_anak[]">';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control tanggalnya2" idx="' +
          idx_row +
          '" id="tgl_lahir_anak_' +
          idx_row +
          '" name="tgl_lahir_anak[]">';
        tr_table +=
          "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control" id="asal_sk_' +
          idx_row +
          '" name="asal_sk[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "permohonan_pengadaan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="kd_rek_pengadaan_' +
          idx_row +
          '" name="kd_rek_pengadaan[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="keg_pengadaan_' +
          idx_row +
          '" name="keg_pengadaan[]">';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="sub_keg_pengadaan_' +
          idx_row +
          '" name="sub_keg_pengadaan[]">';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="belanja_pengadaan_' +
          idx_row +
          '" name="belanja_pengadaan[]">';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="nilai_pagu_pengadaan_' +
          idx_row +
          '" name="nilai_pagu_pengadaan[]">';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="calon_penyedia_pengadaan_' +
          idx_row +
          '" name="calon_penyedia_pengadaan[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "data_surat_sktm":
      idx_row++;
      tr_table +=`
        <tr class="tr_inv" id="tr_inv_${idx_row}" idx="${idx_row}" style="border-top: 2px solid #b8b8b8;">
          <td>
            <input type="text" class="form-control wajib" idx="${idx_row}"
              id="no_surat_sktm" name="no_surat_sktm[]" required>
          </td>
          <td>
            <input type="text" class="form-control wajib" idx="${idx_row}"
              id="no_pengantar_sktm" name="no_pengantar_sktm[]">
          </td>
          <td>
            <input type="text"  class="form-control tanggalnya2 wajib" placeholder="dd-mm-yyyy" idx="${idx_row}"
              id="tgl_pengantar_sktm" name="tgl_pengantar_sktm[]">
          </td>
          <td>
            <input type="text" class="form-control wajib nik-only" idx="${idx_row}" id="nik_sktm" name="nik_sktm[]" onchange="get_data_penduduk(event)" required>
          </td>
          <td>
            <input type="text" class="form-control wajib" idx="${idx_row}" id="nama_sktm" name="nama_sktm[]" required>
          </td>
          <td>
            <input type="text" class="form-control" placeholder="Isi Jika Warga Luar Kelurahan / Warga Berdomisili" idx="${idx_row}"
              id="alamat_domisili_sktm" name="alamat_domisili_sktm[]">
          </td>
          
          <td class="text-center">
            <a href="javascript:void(0);" class="btn btn-primary btn-circle"
														onclick="$(this).parents('tr').next().toggle();"><i
															class="fa fa-eye"></i></a>
            <a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents('tr').first().remove();"><i class="fa fa-times"></i></a>
          </td>
        </tr>
        <tr style="display: none;">
          <td colspan="6">
            <table class="table table-bordered table-striped table-hover" style="border: 2px dashed black;">
              <tr>
                <th class='text-center' style='vertical-align:middle;'>
                  Tempat Lahir</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Tgl. Lahir</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Jenis Kelamin</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Status</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Agama</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Pendidikan</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Pekerjaan</th>
                <th class='text-center' style='vertical-align:middle;'>
                  Alamat</th>
                <th class='text-center' style='vertical-align:middle;'>
                  RT/RW</th>
      
              </tr>
              <tbody>
                <tr>
                  
                  <td>
                    <input type="text" class="form-control wajib" idx="${idx_row}"
                      id="tempat_lahir_sktm" name="tempat_lahir_sktm[]">
                  </td>
                  <td>
                    <input type="text"  class="form-control tanggalnya2 wajib" placeholder="dd-mm-yyyy" idx="${idx_row}"
                      id="tgl_lahir_sktm" name="tgl_lahir_sktm[]">
                  </td>
                  <td>
                    <select class="form-control select2nya wajib" idx="${idx_row}" id="jns_kelamin_sktm"
                      name="jns_kelamin_sktm[]">
                      ${jns_kelamin_sktm}
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2nya wajib" idx="${idx_row}" id="status_sktm"
                      name="status_sktm[]">
                      ${status_sktm}
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2nya wajib" idx="${idx_row}" id="agama_sktm"
                      name="agama_sktm[]">
                      ${agama_sktm}
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2nya wajib" idx="${idx_row}" id="pendidikan_sktm"
                      name="pendidikan_sktm[]">
                      ${pendidikan_sktm}
                    </select>
                  </td>
                  <td>
                    <select class="form-control select2nya wajib" idx="${idx_row}" id="pekerjaan_sktm"
                      name="pekerjaan_sktm[]">
                      ${pekerjaan_sktm}
                    </select>
                  </td>
                  <td>
                    <input type="text" class="form-control wajib" idx="${idx_row}"
                      id="alamat_sktm" name="alamat_sktm[]">
                  </td>
                  <td style="display: flex;">
                    <input type="text" class="form-control" idx="${idx_row}" id="rt_sktm" name="rt_sktm[]" style="width: 52px;" placeholder="RT">
                    <input type="text" class="form-control" idx="${idx_row}" id="rw_sktm" name="rw_sktm[]" style="width: 52px;" placeholder="RW">
                  </td>
                  
                </tr>
              </tbody>
            </table>
            <script type="text/javascript"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>
          </td>
        </tr>
      
      `;
















      /* tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="no_surat_sktm_' +
          idx_row +
          '" name="no_surat_sktm[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tgl_surat_sktm_' +
        idx_row +
        '" name="tgl_surat_sktm[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

    tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="no_pengantar_sktm_' +
        idx_row +
        '" name="no_pengantar_sktm[]" />';

    tr_table += "</td>";

    tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tgl_pengantar_sktm_' +
        idx_row +
        '" name="tgl_pengantar_sktm[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

    tr_table += "</td>";

    tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="alamat_domisili_sktm_' +
        idx_row +
        '" name="alamat_domisili_sktm[]">';

    tr_table += "</td>";

    tr_table += "<td>";

        tr_table +=
          '<select class="form-control select2nya wajib" idx="' +
          idx_row +
          '" id="nama_tidak_mampu_' +
          idx_row +
          '" name="nama_tidak_mampu[]" style="width:100%">';

        tr_table += combo_penduduk;

        tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="tmt_lahir_skm_' +
          idx_row +
          '" name="tmt_lahir_skm[]">';

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control tanggalnya2" idx="' +
          idx_row +
          '" id="tgl_lahir_skm_' +
          idx_row +
          '" name="tgl_lahir_skm[]">';
        tr_table +=
          "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<select class="form-control select2nya wajib" idx="' +
          idx_row +
          '" id="jns_kelamin_skm_' +
          idx_row +
          '" name="jns_kelamin_skm[]" style="width:100%">';

        tr_table += combo_penduduk;

        tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<select class="form-control select2nya wajib" idx="' +
          idx_row +
          '" id="status_skm_' +
          idx_row +
          '" name="status_skm[]" style="width:100%">';

        tr_table += combo_penduduk;

        tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<select class="form-control select2nya wajib" idx="' +
          idx_row +
          '" id="agama_skm_' +
          idx_row +
          '" name="agama_skm[]" style="width:100%">';

        tr_table += combo_penduduk;

        tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<select class="form-control select2nya wajib" idx="' +
          idx_row +
          '" id="pekerjaan_skm_' +
          idx_row +
          '" name="pekerjaan_skm[]" style="width:100%">';

        tr_table += combo_penduduk;

        tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

        tr_table +=
          '<input type="text" class="form-control wajib" id="alamat_skm_' +
          idx_row +
          '" name="alamat_skm[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>"; */
      break;

    // case "nama_tidak_mampu_v2":
    //   idx_row++;

    //   tr_table +=
    //     '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<select class="form-control select2nya wajib" idx="' +
    //       idx_row +
    //       '" id="nama_tidak_mampu_' +
    //       idx_row +
    //       '" name="nama_tidak_mampu[]" style="width:100%">';

    //     tr_table += combo_penduduk;

    //     tr_table += "</select>";

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<input type="text" class="form-control wajib" id="tmt_lahir_skm_' +
    //       idx_row +
    //       '" name="tmt_lahir_skm[]">';

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<input type="text" class="form-control tanggalnya2" idx="' +
    //       idx_row +
    //       '" id="tgl_lahir_skm_' +
    //       idx_row +
    //       '" name="tgl_lahir_skm[]">';
    //     tr_table +=
    //       "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<select class="form-control select2nya wajib" idx="' +
    //       idx_row +
    //       '" id="jns_kelamin_skm_' +
    //       idx_row +
    //       '" name="jns_kelamin_skm[]" style="width:100%">';

    //     tr_table += combo_penduduk;

    //     tr_table += "</select>";

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<select class="form-control select2nya wajib" idx="' +
    //       idx_row +
    //       '" id="status_skm_' +
    //       idx_row +
    //       '" name="status_skm[]" style="width:100%">';

    //     tr_table += combo_penduduk;

    //     tr_table += "</select>";

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<select class="form-control select2nya wajib" idx="' +
    //       idx_row +
    //       '" id="agama_skm_' +
    //       idx_row +
    //       '" name="agama_skm[]" style="width:100%">';

    //     tr_table += combo_penduduk;

    //     tr_table += "</select>";

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<select class="form-control select2nya wajib" idx="' +
    //       idx_row +
    //       '" id="pekerjaan_skm_' +
    //       idx_row +
    //       '" name="pekerjaan_skm[]" style="width:100%">';

    //     tr_table += combo_penduduk;

    //     tr_table += "</select>";

    //   tr_table += "</td>";

    //   tr_table += "<td>";

    //     tr_table +=
    //       '<input type="text" class="form-control wajib" id="alamat_skm_' +
    //       idx_row +
    //       '" name="alamat_skm[]">';

    //   tr_table += "</td>";

    //   tr_table +=
    //     '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

    //   tr_table += "</tr>";

    //   break;
    
    case "peneliti_mhs":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="nama_peneliti_mhs_' +
        idx_row +
        '" name="nama_peneliti_mhs[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="nim_peneliti_mhs_' +
        idx_row +
        '" name="nim_peneliti_mhs[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="pekerjaan_peneliti_mhs_' +
        idx_row +
        '" name="pekerjaan_peneliti_mhs[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="alamat_peneliti_mhs_' +
        idx_row +
        '" name="alamat_peneliti_mhs[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "serah_terima_barang":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="kode_barang_' +
        idx_row +
        '" name="kode_barang[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_barang_' +
        idx_row +
        '" name="nama_barang[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="merek_barang_' +
        idx_row +
        '" name="merek_barang[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="nopol_barang_' +
        idx_row +
        '" name="nopol_barang[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="warna_barang_' +
        idx_row +
        '" name="warna_barang[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="tahun_serah_terima_' +
        idx_row +
        '" name="tahun_serah_terima[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="harga_perolehan_' +
        idx_row +
        '" name="harga_perolehan[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "perjalanan_dinas":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_perjalanan_dinas_' +
        idx_row +
        '" name="nama_perjalanan_dinas[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tgl_lahir_dinas_' +
        idx_row +
        '" name="tgl_lahir_dinas[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'dd-mm-yyyy', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="ket_dinas_' +
        idx_row +
        '" name="ket_dinas[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "pernyataan_tpp":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_pernyataan_tpp_' +
        idx_row +
        '" name="nama_pernyataan_tpp[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="pangkat_akhir_' +
        idx_row +
        '" name="pangkat_akhir[]" style="width:100%">';

      tr_table += combo_golongan;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="jab_pernyataan_tpp_' +
        idx_row +
        '" name="jab_pernyataan_tpp[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="skpd_pernyataan_tpp_' +
        idx_row +
        '" name="skpd_pernyataan_tpp[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "nama_rekap_kematian":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="nik_rekap_' +
        idx_row +
        '" name="nik_rekap[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_rekap_' +
        idx_row +
        '" name="nama_rekap[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tgl_rekap_kematian_' +
        idx_row +
        '" name="tgl_rekap_kematian[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'yyyy-mm-dd', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="alamat_rekap_' +
        idx_row +
        '" name="alamat_rekap[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_hibah":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_hibah_' +
        idx_row +
        '" name="nama_hibah[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nik_hibah_' +
        idx_row +
        '" name="nik_hibah[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="tempat_lahir_hibah_' +
        idx_row +
        '" name="tempat_lahir_hibah[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya3" idx="' +
        idx_row +
        '" id="ttl_hibah_' +
        idx_row +
        '" name="ttl_hibah[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya3').datepicker({format: 'yyyy-mm-dd', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="alamat_hibah_' +
        idx_row +
        '" name="alamat_hibah[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "perintah_keg":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" idx="' +
        idx_row +
        '" id="perintah_keg_' +
        idx_row +
        '" name="perintah_keg[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_pengantar_kec":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" idx="' +
        idx_row +
        '" id="uraian_kec_' +
        idx_row +
        '" name="uraian_kec[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" id="jml_kec_' +
        idx_row +
        '" name="jml_kec[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" id="ket_kec_' +
        idx_row +
        '" name="ket_kec[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "kenaikan_gaji":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" idx="' +
        idx_row +
        '" id="nama_pegawai_' +
        idx_row +
        '" name="nama_pegawai[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" id="nip_pegawai_' +
        idx_row +
        '" name="nip_pegawai[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="pangkat_akhir_' +
        idx_row +
        '" name="pangkat_akhir[]" style="width:100%">';

      tr_table += combo_golongan;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" id="jabatan_pegawai_' +
        idx_row +
        '" name="jabatan_pegawai[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tmt_' +
        idx_row +
        '" name="tmt[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'yyyy-mm-dd', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "nontender_kec":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" idx="' +
        idx_row +
        '" id="kegiatan_nontender_' +
        idx_row +
        '" name="kegiatan_nontender[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" id="pagu_nontender_' +
        idx_row +
        '" name="pagu_nontender[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" id="penyedia_nontender_' +
        idx_row +
        '" name="penyedia_nontender[]"></textarea>';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_usulan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" idx="' +
        idx_row +
        '" id="nama_usulan_' +
        idx_row +
        '" name="nama_usulan[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="pangkat_akhir_' +
        idx_row +
        '" name="pangkat_akhir[]" style="width:100%">';

      tr_table += combo_golongan;

      tr_table += "</select>";

      tr_table += "</td>";

      // tr_table += "<td>";

      // tr_table +=
      //   '<textarea class="form-control wajib" id="pangkat_akhir_' +
      //   idx_row +
      //   '" name="pangkat_akhir[]"></textarea>';

      // tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tmt1_' +
        idx_row +
        '" name="tmt1[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'yyyy-mm-dd', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      // tr_table += "<td>";

      // tr_table +=
      //   '<textarea class="form-control wajib" id="tmt1_' +
      //   idx_row +
      //   '" name="tmt1[]"></textarea>';

      // tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="pangkat_usulan_' +
        idx_row +
        '" name="pangkat_usulan[]" style="width:100%">';

      tr_table += combo_golongan;

      tr_table += "</select>";

      tr_table += "</td>";

      // tr_table += "<td>";

      // tr_table +=
      //   '<textarea class="form-control wajib" id="pangkat_usulan_' +
      //   idx_row +
      //   '" name="pangkat_usulan[]"></textarea>';

      // tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control tanggalnya2" idx="' +
        idx_row +
        '" id="tmt2_' +
        idx_row +
        '" name="tmt2[]">';
      tr_table +=
        "<script type=\"text/javascript\"> $('.tanggalnya2').datepicker({format: 'yyyy-mm-dd', orientation:'bottom', changeMonth: true, changeYear: true, yearRange: \"c-80:c+10\", dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'], monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], }); </script>";

      tr_table += "</td>";

      // tr_table += "<td>";

      // tr_table +=
      //   '<textarea class="form-control wajib" id="tmt2_' +
      //   idx_row +
      //   '" name="tmt2[]"></textarea>';

      // tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_perpanjangan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<textarea class="form-control wajib" idx="' +
        idx_row +
        '" id="nama_perpanjangan_' +
        idx_row +
        '" name="nama_perpanjangan[]"></textarea>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="pangkat_akhirp_' +
        idx_row +
        '" name="pangkat_akhirp[]" style="width:100%">';

      tr_table += combo_golongan;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="jab_awal_' +
        idx_row +
        '" name="jab_awal[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="jab_akhir_' +
        idx_row +
        '" name="jab_akhir[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="ket_perpanjangan_' +
        idx_row +
        '" name="ket_perpanjangan[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_kuasa_umum":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_kuasa_' +
        idx_row +
        '" name="nama_kuasa[]" />';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="umur_kuasa_' +
        idx_row +
        '" name="umur_kuasa[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="pekerjaan_kuasa_' +
        idx_row +
        '" name="pekerjaan_kuasa[]">';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="alamat_kuasa_' +
        idx_row +
        '" name="alamat_kuasa[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "suket_bepergian":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="nama_pengikut_' +
        idx_row +
        '" name="nama_pengikut[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_bukti":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="file" class="form-control wajib" id="file_foto_' +
        idx_row +
        '" name="file_foto[]" requared/>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control wajib" id="ket_foto_' +
        idx_row +
        '" name="ket_foto[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "surat_sanggahan":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="file" class="form-control" id="file_foto_' +
        idx_row +
        '" name="file_foto[]"/>';

      tr_table += "</td>";

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control" id="ket_foto_' +
        idx_row +
        '" name="ket_foto[]">';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";
      break;

    case "barang_terbakar":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<input type="text" class="form-control idx="' +
        idx_row +
        '" id="barang_terbakar' +
        idx_row +
        '" name="barang_terbakar[]" />';

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "nama_dalam_surat":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya" idx="' +
        idx_row +
        '" id="nama_dalam_surat_' +
        idx_row +
        '" name="nama_dalam_surat[]" >';

      tr_table += data_pindah_penduduk_id;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "nama_tidak_mampu":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="nama_tidak_mampu_' +
        idx_row +
        '" name="nama_tidak_mampu[]" style="width:100%">';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td><input type="text" class="form-control" name="keterangan[]"></td>';

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "sub_indikator_penilaian":

      // Hitung jumlah row yang sudah ada → menentukan idx_row berikutnya
      var idx_row = $(".sub_indikator_penilaian .tr_inv").length;
      idx_row++;

      tr_table  = "";
      tr_table += '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      // Kolom Sub Indikator
      tr_table += "<td>";
      tr_table += 
        '<textarea class="form-control wajib" idx="' + idx_row +
        '" id="uraian_' + idx_row +
        '" name="uraian[]"></textarea>';
      tr_table += "</td>";

      // Kolom Satuan
      tr_table += "<td>";
      tr_table += 
        '<textarea class="form-control" idx="' + idx_row +
        '" id="satuan_' + idx_row +
        '" name="satuan[]"></textarea>';
      tr_table += "</td>";

      // Tombol hapus
      tr_table +=
        '<td class="text-center">' +
        '<a href="javascript:void(0);" class="btn btn-danger btn-circle" ' +
        'onclick="$(this).parents(\'tr\').first().remove();">' +
        '<i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    // case "sub_indikator_penilaian":
    //   idx_row++;

    //   tr_table +=
    //     '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

    //   tr_table += "<td>";

    //   tr_table +=
    //     '<textarea class="form-control wajib" idx="' +
    //     idx_row +
    //     '" id="uraian_' +
    //     idx_row +
    //     '" name="uraian[]"></textarea>';

    //   tr_table += "</td>";

    //   tr_table +=
    //     '<td><input type="text" class="form-control" name="satuan[]"></td>';

    //   tr_table +=
    //     '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

    //   tr_table += "</tr>";

    //   break;

    case "nama_berdomisili":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="nama_berdomisili_' +
        idx_row +
        '" name="nama_berdomisili[]" style="width:100%">';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "nama_pindah_penduduk":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="nama_pindah_penduduk_' +
        idx_row +
        '" name="nama_pindah_penduduk[]" style="width:100%">';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td><input type="text" class="form-control" name="keterangan[]"></td>';

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "nama_pindah_penduduk2":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="nama_pindah_penduduk2_' +
        idx_row +
        '" name="nama_pindah_penduduk2[]" style="width:100%">';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      tr_table +=
        '<td><input type="text" class="form-control" name="keterangan[]"></td>';

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "nama_keterangan_umum2":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="nama_keterangan_umum2_' +
        idx_row +
        '" name="nama_keterangan_umum2[]" style="width:100%">';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      // tr_table += '<td><input type="text" class="form-control" name="keterangan[]"></td>';

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;

    case "nama_keterangan_umum":
      idx_row++;

      tr_table +=
        '<tr class="tr_inv" id="tr_inv_' + idx_row + '" idx="' + idx_row + '">';

      tr_table += "<td>";

      tr_table +=
        '<select class="form-control select2nya wajib" idx="' +
        idx_row +
        '" id="nama_keterangan_umum_' +
        idx_row +
        '" name="nama_keterangan_umum[]" style="width:100%">';

      tr_table += combo_penduduk;

      tr_table += "</select>";

      tr_table += "</td>";

      // tr_table += '<td><input type="text" class="form-control" name="keterangan[]"></td>';

      tr_table +=
        '<td class="text-center"><a href="javascript:void(0);" class="btn btn-danger btn-circle" onclick="$(this).parents(\'tr\').first().remove();"><i class="fa fa-times"></i></a></td>';

      tr_table += "</tr>";

      break;
  }

  $("." + mod).append(tr_table);

  $(".select2nya").select2();
}

function monthHPL(date) {
  var tglRumus = moment(date).format("M");

  if (tglRumus <= 3) {
    days = moment(date).add(7, "days").format("DD");

    month = moment(date).add(9, "month").format("MM");

    year = moment(date).format("YYYY");
  } else {
    days = moment(date).add(7, "days").format("DD");

    month = moment(date).subtract(3, "month").format("MM");

    year = moment(date).add(1, "year").format("YYYY");
  }

  return year + "-" + month + "-" + days;

  //return days+"-"+month+"-"+year;
}

function getWeeks(tipe, days) {
  if (tipe == "bulan_hari") {
    var value = {
      month: Math.floor(days / 30),

      days: days % 30,
    };

    return value.month + " Bulan, " + value.days + " Hari";
  } else if (tipe == "minggu_hari") {
    var value = {
      weeks: Math.floor(days / 7),

      days: days % 7,
    };

    return value.weeks + " Minggu, " + value.days + " Hari";
  } else if (tipe == "bulan") {
    var value = {
      month: Math.floor(days / 30),
    };

    return value.month;
  }
}

function modal_prompt(option = "", prompt = "") {
  if (typeof option !== "object") {
    option = {
      title: "Modal Prompt",
    };
  }

  const modalContainer = document.createElement("div");
  modalContainer.className = "modal show";
  modalContainer.setAttribute("id", option.container_id);

  const modalDialog = document.createElement("div");
  modalDialog.className = "modal-dialog";

  modalContainer.appendChild(modalDialog);

  const modalContent = document.createElement("div");
  modalContent.className = "modal-content";

  modalDialog.appendChild(modalContent);

  const modalHeader = document.createElement("div");
  modalHeader.className = "modal-header";

  modalContent.appendChild(modalHeader);

  const modalTitle = document.createElement("h5");
  modalTitle.className = "modal-title";
  modalTitle.textContent = option.title;

  modalHeader.appendChild(modalTitle);

  const modalClose = document.createElement("button");
  modalClose.className = "close";
  modalClose.setAttribute("type", "button");
  modalClose.textContent = "x";

  modalClose.addEventListener("click", function () {
    modalContainer.remove();
  });

  modalHeader.appendChild(modalClose);

  const modalBody = document.createElement("div");
  modalBody.className = "modal-body";

  modalContent.appendChild(modalBody);

  const modalForm = document.createElement("form");
  modalForm.setAttribute("id", option.form_id);

  if (typeof prompt === "object") {
    for (let i = 0; i < prompt.length; i++) {
      const formGroup = document.createElement("div");
      formGroup.className = "form-group";
      const formLabel = document.createElement("label");
      formLabel.className = "form-label";
      if (prompt[i].label.length > 0) {
        formLabel.textContent = prompt[i].label;
      }
      const promptContent = document.createElement(prompt[i].type);
      promptContent.className = "prompt-form " + prompt[i].className;
      for (var key of Object.keys(prompt[i].attr)) {
        promptContent.setAttribute(key, prompt[i].attr[key]);
        if (key == "label") {
          formLabel.setAttribute(key, prompt[i].attr[key]);
        }
      }
      if (prompt[i].type == "select") {
        for (let ii = 0; ii < prompt[i].dataOption.length; ii++) {
          const option = document.createElement("option");
          option.setAttribute("value", prompt[i].dataOption[ii].id);
          option.innerHTML = prompt[i].dataOption[ii].text;
          promptContent.appendChild(option);
        }
      }
      formGroup.appendChild(formLabel);
      formGroup.appendChild(promptContent);
      modalForm.appendChild(formGroup);
      modalBody.appendChild(modalForm);
    }
  }

  const modalFooter = document.createElement("div");
  modalFooter.className = "modal-footer";

  modalContent.appendChild(modalFooter);

  const modalFooterAccepst = document.createElement("button");
  modalFooterAccepst.className = "btn-flat btn-success btn-sm";
  modalFooterAccepst.setAttribute("type", "button");
  modalFooterAccepst.setAttribute("id", option.confirm_id);
  modalFooterAccepst.textContent = "Ok";
  /*  modalFooterAccepst.addEventListener('click', function() {
     modalContainer.remove();
   }); */

  modalFooter.appendChild(modalFooterAccepst);

  const modalFooterDismis = document.createElement("button");
  modalFooterDismis.className = "btn-flat btn-danger btn-sm";
  modalFooterDismis.setAttribute("type", "button");
  modalFooterDismis.textContent = "Batal";

  modalFooterDismis.addEventListener("click", function () {
    modalContainer.remove();
  });

  modalFooter.appendChild(modalFooterDismis);

  document.body.appendChild(modalContainer);
}

function get_data_penduduk(e){
  var $this = $(e.target);
  $.ajax({
    url:host + "get-data-penduduk",
    dataType:'json',
    type:'post',
    data:({
      nik:$this.val()
    }),
    success:function(data) {
      if (data.status==true) {
        data = data.data;
        $this.closest('tr').find('#nama_sktm').val(data.nama_lengkap).prop('readonly',true).prop('required',false);
        var tr = $this.closest('tr').next();
        tr.hide();
        tr.find('#tempat_lahir_sktm').val(data.tempat_lahir).prop('required',false);
        const date = data.tgl_lahir;
        const [y, m, d] = date.split("-");
        const formatted_tgl_lahir = `${d}-${m}-${y}`;
        tr.find('#tgl_lahir_sktm').val(formatted_tgl_lahir).prop('required',false);
        tr.find('#jns_kelamin_sktm').val(data.jenis_kelamin).trigger('change').prop('required',false);
        tr.find('#status_sktm').val(data.status_kawin).trigger('change').prop('required',false);
        tr.find('#agama_sktm').val(data.agama).trigger('change').prop('required',false);
        tr.find('#pendidikan_sktm').val(data.cl_pendidikan).trigger('change').prop('required',false);
        tr.find('#pekerjaan_sktm').val(data.cl_jenis_pekerjaan_id).trigger('change').prop('required',false);
        tr.find('#alamat_sktm').val(data.alamat);
        tr.find('#rt_sktm').val(data.rt);
        tr.find('#rw_sktm').val(data.rw);
        $.messager.alert('Info','Data ditemukan');
      }else{
        $this.closest('tr').find('#nama_sktm').val('').prop('readonly',false).prop('required',true);
        var tr = $this.closest('tr').next();
        tr.show();
        tr.find('#tempat_lahir_sktm').val('');
        tr.find('#tgl_lahir_sktm').val('');
        tr.find('#jns_kelamin_sktm').val(null).trigger('change');
        tr.find('#status_sktm').val(null).trigger('change');
        tr.find('#agama_sktm').val(null).trigger('change');
        tr.find('#pendidikan_sktm').val(null).trigger('change');
        tr.find('#pekerjaan_sktm').val(null).trigger('change');
        tr.find('#alamat_sktm').val('');
        tr.find('#rt_sktm').val('');
        tr.find('#rw_sktm').val('');
        $.messager.alert('Peringatan',`NIK ${$this.val()} belum terdaftar, silahkan lengkapi data dibawah ini!`,'error');
      }
    }
  });
}

// helper: nama hari & nama bulan bahasa Indonesia
function namaHariIndonesia(dayIndex) {
    var hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    return hari[dayIndex] || '';
}
function namaBulanIndonesia(monthIndex) {
    var bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return bulan[monthIndex] || '';
}

// helper: parsing fleksibel dan format ke "Hari, DD NamaBulan YYYY"
function formatTanggalIndonesia(raw) {
    if (!raw) return '-';
    raw = raw.toString().trim();

    // ignore zero-date values
    if (/^0{4}[-\/]0{2}[-\/]0{2}$/.test(raw) || /^0{2}[-\/]0{2}[-\/]0{4}$/.test(raw)) return '-';

    var y, m, d;
    // detect YYYY-MM-DD or YYYY/MM/DD
    var m1 = raw.match(/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/);
    // detect DD-MM-YYYY or DD/MM/YYYY
    var m2 = raw.match(/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/);

    if (m1) {
        y = parseInt(m1[1],10);
        m = parseInt(m1[2],10) - 1;
        d = parseInt(m1[3],10);
    } else if (m2) {
        y = parseInt(m2[3],10);
        m = parseInt(m2[2],10) - 1;
        d = parseInt(m2[1],10);
    } else {
        // coba parse Date (fallback)
        var dt = new Date(raw);
        if (isNaN(dt.getTime())) return '-';
        y = dt.getFullYear();
        m = dt.getMonth();
        d = dt.getDate();
        var dayIndex = dt.getDay();
        return namaHariIndonesia(dayIndex) + ', ' + String(d).padStart(2,'0') + ' ' + namaBulanIndonesia(m) + ' ' + y;
    }

    // buat objek Date untuk mendapatkan hari
    var dateObj = new Date(y, m, d);
    if (isNaN(dateObj.getTime())) return '-';

    var dayIndex = dateObj.getDay();
    return namaHariIndonesia(dayIndex) + ', ' + String(d).padStart(2,'0') + ' ' + namaBulanIndonesia(m) + ' ' + y;
}


function updateWaktu() {
  const sekarang = new Date();

  const hariList = [
    "Minggu","Senin","Selasa","Rabu",
    "Kamis","Jumat","Sabtu"
  ];

  const bulanList = [
    "Januari","Februari","Maret","April","Mei","Juni",
    "Juli","Agustus","September","Oktober","November","Desember"
  ];

  const hari = hariList[sekarang.getDay()];
  const tanggal = sekarang.getDate();
  const bulan = bulanList[sekarang.getMonth()];
  const tahun = sekarang.getFullYear();

  let jam = sekarang.getHours();
  let menit = sekarang.getMinutes();
  let detik = sekarang.getSeconds();

  jam = jam < 10 ? "0" + jam : jam;
  menit = menit < 10 ? "0" + menit : menit;
  detik = detik < 10 ? "0" + detik : detik;

  document.getElementById("tanggal").innerHTML =
    hari + ", " + tanggal + " " + bulan + " " + tahun;

  document.getElementById("jam").innerHTML = jam;
  document.getElementById("menit").innerHTML = menit;
  document.getElementById("detik").innerHTML = detik;
}

function cetakLPJ(nik) {
  var bulan = $('#bulan').val(); // ambil dari filter

  if (!bulan) {
    alert('Silakan pilih bulan terlebih dahulu');
    return;
  }

  window.open(
    window.BASE_URL + "penilaian_rt_rw/cetak_lpj?nik=" + nik + "&bulan=" + bulan,
    "_blank"
  );
}


//Fungsi untuk dashboar_Admin
function renderChart(id, type, categories, data) {
    Highcharts.chart(id, {
        chart: { type: type },
        title: { text: null },
        xAxis: { categories: categories },
        yAxis: { title: { text: 'Jumlah' } },
        series: [{
            name: 'Jumlah',
            data: data
        }],
        credits: { enabled: false }
    });
}



