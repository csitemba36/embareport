<style>
.select2-container--default .select2-selection--single {
    height: 30px; /* Sesuaikan dengan tinggi input lain */
    padding: 2px 5px;
    border: 1px solid #ced4da; /* Warna border input */
    border-radius: 4px; /* Border radius sama */
    font-size: 12px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px; /* Tengahin text */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px; /* Tengahin icon arrow */
}

.select2-container {
    width: 100% !important; /* Lebar penuh kayak input */
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
  top: -3px; /* atau 1px, sesuaikan */
}

.dt-buttons {
        margin-left: 10px;
        display: flex;
        gap: 5px;
    }
	
.card-body {
	  font-size: 12px; /* Ukuran font kecil */
	}

	.card-body table th,
	.card-body table td {
	  padding: 4px 8px; /* Rapatkan padding tabel */
	}

	.card-body .btn {
	  font-size: 12px;
	  padding: 5px 6px; /* Perkecil tombol */
	}
	
	/* Paging */
	.card-body .pagination {
	  font-size: 12px;       /* Kecilkan ukuran font */
	}

	.card-body .page-item {
	  margin: 0 2px;         /* Kurangi jarak antar halaman */
	}

	.card-body .page-link {
	  padding: 2px 6px;      /* Perkecil padding tombol halaman */
	  font-size: 12px;       /* Ukuran font tombol */
	}
	.cilik{
		font-size: 12px;
	}
</style>
<div class="card mb-4 shadow-sm">
    <div class="card-header" style="background-color:#e0eeff;">
        <h6 class="mb-0">Filter Data</h6>
    </div>
    <div class="card-body"> 
        <form id="filterForm" class="row g-3">
			<div class="col-md-2">
				<div class="form-group mb-2">
					<label for="filter-merk">Merk</label>
					<select id="filter-merk" class="form-control select2 filter-input">
						<option value="">-- Choose --</option>
					</select>
				</div>
			</div>

			<!--<div class="col-md-2">
				<div class="form-group mb-2">
					<label for="filter-gudang">Gudang / Toko</label>
					<select id="filter-gudang" class="form-control select2 filter-input">
						<option value="">-- Choose --</option>
					</select>
				</div>
			</div>-->
            <div class="col-md-2">
                	<label class="">Periode Awal</label>
               		<input type="date" name="start_date" id="start_date" class="form-control cilik filter-input" value="<?= date('Y-m-01') ?>">
            </div>
            <div class="col-md-2">
                <label class="">Periode Akhir</label>
                <input type="date" name="end_date" id="end_date" class="form-control cilik mt-6" value="<?= date('Y-m-t') ?>">
            </div>
           
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary w-100 mb-2">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row">

    <!-- ================= LEFT (col-4) ================= -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#e0eeff;">
        <span class="d-flex align-items-center gap-2 text-success fw-semibold">
            <i class="bi bi-google"></i>
            <span>Source Google Drive</span>
        </span>
    </div>
            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap table-striped" id="fileTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Tanggal Download</th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            </div>
        </div>
    </div>

    <!-- ================= RIGHT (col-8) ================= -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color:#e0eeff;">
                <h6 class="mb-0">Data Sales Invoice</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table id="salesTable"
                           class="table table-bordered table-hover table-striped nowrap"
                           style="width:100%">
                        <thead class="table-primary">
                            <tr>
                                <th>Process Date</th>
                                <th>Transact Date</th>
                                <th>Supplier No</th>
                                <th>Supplier Name</th>
                                <th>Store No</th>
                                <th>Store Name</th>
                                <th>Class</th>
                                <th>Dept</th>
                                <th>Group</th>
                                <th>Brand</th>
                                <th>World</th>
                                <th>Colour</th>
                                <th>Size</th>
                                <th>Item</th>
                                <th>Barcode</th>
                                <th class="text-end">QTY</th>
                                <th class="text-end">Item Price</th>
                                <th class="text-end">Gross Amt</th>
                                <th class="text-end">Disc Auto</th>
                                <th class="text-end">Disc Promo</th>
                                <th class="text-end">Disc Employee</th>
                                <th class="text-end">Disc Free Item</th>
                                <th class="text-end">Disc Struk</th>
                                <th class="text-end">Disc Pct</th>
                                <th class="text-end">Net Amt</th>
                                <th>POS No</th>
                                <th>Transact No</th>
                                <th>Transact Line</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySales"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>


<script>


$(document).ready(function () {
    loadGDriveFiles();
    function loadGDriveFiles() {
        // destroy jika sudah ada
        if ($.fn.DataTable.isDataTable('#fileTable')) {
            $('#fileTable').DataTable().clear().destroy();
        }

        $('#fileTable').DataTable({
            processing: true,
            serverSide: false,
            scrollX: true,
            ajax: {
                url: "api/gdrive/listfile_gdrive_mcp.php",
                dataSrc: ""
            },
            columns: [
                {
                    data: null,
                    className: "text-center",
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: "name",
                    className: "text-center",
                    orderable: false,
                    render: function (data) {
                        return `
                            <i class="bi bi-arrow-up-square-fill text-primary btn-upload"
                            data-filename="${data}"
                            title="Upload"
                            style="cursor:pointer; font-size:0.9rem;">
                            </i>
                        `;
                    }
                },
                { data: "name" },
                {
                    data: "size",
                    className: "text-end",
                    render: function (data) {
                        return (data / 1024).toFixed(1) + " KB";
                    }
                },
                { data: "date" }
            ],
            language: {
                processing: "Loading Google Drive files..."
            }
        });
        }


    $(document).on("click", ".btn-upload", function () {
        let filename = $(this).data("filename");
        //alert(filename);
        Swal.fire({
            title: 'Upload file?',
            text: `File "${filename}" akan diproses`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, upload',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {

            if (!result.isConfirmed) return;

            // ===============================
            // LOADING
            // ===============================
            Swal.fire({
                title: 'Processing...',
                text: 'Sedang mengupload & memproses data',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // ===============================
            // AJAX
            // ===============================
            $.ajax({
                url: "api/gdrive/upload_sales_from_gdrive.php",
                type: "POST",
                dataType: "json",
                data: { filename: filename },

                success: function (res) {

                    Swal.close();

                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            html: `
                                <b>File:</b> ${res.filename}<br>
                                <b>Inserted:</b> ${res.inserted}<br>
                                <b>Failed:</b> ${res.failed}
                            `,
                            confirmButtonText: 'OK'
                        });
                        loadSalesTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message ?? 'Proses gagal',
                            confirmButtonText: 'OK'
                        });
                    }
                },

                error: function (xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server',
                        confirmButtonText: 'OK'
                    });
                }
            });

        });
    });

    $('#salesTable').DataTable({
        scrollX: true,
    });

    function loadSalesTable() {

        // destroy dulu kalau sudah pernah dibuat
        if ($.fn.DataTable.isDataTable('#salesTable')) {
            $('#salesTable').DataTable().clear().destroy();
        }

        $('#salesTable').DataTable({
            processing: true,
            serverSide: false,
            scrollX: true,
            ajax: {
                url: "api/get_sales_mcp.php",
                type: "GET",
                dataSrc: "data"
            },
            columns: [
                { data: "PROCESS_DATE" },
                { data: "TRANSACT_DATE" },
                { data: "SUPPLIER_NO" },
                { data: "SUPPLIER_NAME" },
                { data: "STORE_NO" },
                { data: "STORE_NAME" },
                { data: "CLASS" },
                { data: "DEPT" },
                { data: "GROUP" },
                { data: "BRAND" },
                { data: "WORLD" },
                { data: "COLOUR" },
                { data: "SIZE" },
                { data: "ITEM" },
                { data: "BARCODE" },
                { data: "QTY", className: "text-end" },
                { data: "ITEM_PRICE", className: "text-end" },
                { data: "GROSS_AMT", className: "text-end" },
                { data: "DISC_AUTO", className: "text-end" },
                { data: "DISC_PROMO", className: "text-end" },
                { data: "DISC_EMPLOYEE", className: "text-end" },
                { data: "DISC_FREE_ITEM", className: "text-end" },
                { data: "DISC_STRUK", className: "text-end" },
                { data: "DISC_PCT", className: "text-end" },
                { data: "NET_AMT", className: "text-end" },
                { data: "POS_NO" },
                { data: "TRANSACT_NO" },
                { data: "TRANSACT_LINE_NO" }
            ],
            language: {
                processing: "Loading data..."
            }
        });
        }

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            const start = $('#start_date').val();
            const end = $('#end_date').val();
            const brand = $('#filter-merk').val();
            //alert($('#filter-gudang').val());

            if (!brand) {
                alert('Merk harus dipilih!');
                return 0;
            }
            
            //loadData(start, end, brand, group, kodegudang);
            loadSalesTable();
        });


});

    






		

	// load pertama kali
	//$(document).ready(function () {
    //loadData($('#start_date').val(), $('#end_date').val(), $('#customer').val(), $('#brand').val());

	$(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: '-- Choose --',
            allowClear: true
        });

        $('#filter-merk').select2({
            placeholder: '-- Choose --',
            allowClear: true,
            ajax: {
                url: 'api/get_brand.php', // ganti dengan path yang benar
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term // kirim keyword pencarian ke server
                    };
                },
                processResults: function (data) {
                    return {
                        results: data // hasil sudah dalam format id & text
                    };
                },
                cache: true
            }
        });


        $('#filter-group').select2({
            placeholder: '-- Choose --',
            allowClear: true,
            ajax: {
                url: 'api/get_group_customer.php', // ganti dengan path yang benar
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term // kirim keyword pencarian ke server
                    };
                },
                processResults: function (data) {
                    return {
                        results: data // hasil sudah dalam format id & text
                    };
                },
                cache: true
            }
        });

        $('#filter-group').on('change', function () {
            let selectedId = $(this).val();
            // AJAX ambil data gudang/toko
            $.ajax({
                url: 'api/get_warehouses_by_customer.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    KodeLgn: selectedId,
                    term: '' // kosongkan jika ingin semua
                },
                success: function (data) {
                    // Kosongkan dan isi ulang dropdown
                    let $gudang = $('#filter-gudang');
                    $gudang.empty();

                    // Tambahkan placeholder/default option
                    $gudang.append('<option value="">-- Choose --</option>');

                    // Tambahkan opsi dari hasil AJAX
                    $.each(data, function (index, item) {
                        $gudang.append(
                            $('<option>', {
                                value: item.id,
                                text: item.text
                            })
                        );
                    });
                    // Trigger update Select2
                    $gudang.trigger('change');
                },
                error: function (xhr, status, error) {
                    console.error('Error loading gudang:', error);
                }
            });
        });	

});
</script>