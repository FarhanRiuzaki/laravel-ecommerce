@extends('layouts.admin')

@section('title')
    List Product
@endsection

@section('content')
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Upload Excel</h4>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×</button>
                </div>
                <form action="{{ route('product.saveBulk') }}" method="post" enctype="multipart/form-data" >
                    @csrf
                    <div class="modal-body">
                        <!-- SETIAP USER HARUS MEMILIH KATEGORI PRODUK TERKAIT -->
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select name="category_id" id="category_id" class="form-control {{ isValid($errors->first('category_id')) }}">
                                <option value="">Pilih</option>
                                @foreach ($category as $row)
                                <option value="{{ $row->id }}" {{ old('category_id') == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <p class="invalid-feedback">{{ $errors->first('category_id') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="file">File Excel</label>
                            <input type="file" id='file' name="file" class="form-control {{ isValid($errors->first('file')) }}" value="{{  old('file')   }}" required>
                            <p class="invalid-feedback">{{ $errors->first('file') }}</p>
                        </div>
                        <div class="form-group">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btnUpload">Upload</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h4 class="page-title text-truncate text-dark font-weight-medium mb-1">Product</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item">Home</li>
                            <li class="breadcrumb-item active">Product</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-right">
                    <button type="button" class="btn btn-warning custom-radius" data-toggle="modal" data-target="#myModal">Upload Excel</button>
                    <a href="{{  route('product.create')  }}" class="btn btn-primary custom-radius text-center"><span class="fas fa-plus"></span> Tambah Data</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Product</h4>
                        <br>
                        <div class="table-responsive">
                            <!-- BUAT FORM UNTUK PENCARIAN, METHODNYA ADALAH GET -->
                            <form action="{{ route('product.index') }}" method="get">
                                <div class="input-group mb-4 col-md-4 float-right">
                                    <!-- KEMUDIAN NAME-NYA ADALAH Q YANG AKAN MENAMPUNG DATA PENCARIAN -->
                                    <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <td>#</td>
                                        <td>Produk</td>
                                        <td>Harga</td>
                                        <td>Created At</td>
                                        <td>Status</td>
                                        <td width='220px'>Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                     <!-- LOOPING DATA TERSEBUT MENGGUNAKAN FORELSE -->
                                        <!-- ADAPUN PENJELASAN ADA PADA ARTIKEL SEBELUMNYA -->
                                        @forelse ($product as $row)
                                        <tr>
                                            <td>
                                                <!-- TAMPILKAN GAMBAR DARI FOLDER PUBLIC/STORAGE/PRODUCTS -->
                                                <img src="{{ asset('storage/products/' . $row->image) }}" width="100px" height="100px" alt="{{ $row->name }}">
                                            </td>
                                            <td>
                                                <strong>{{ $row->name }}</strong><br>
                                                <!-- ADAPUN NAMA KATEGORINYA DIAMBIL DARI HASIL RELASI PRODUK DAN KATEGORI -->
                                                <label>Kategori: <span class="badge badge-info">{{ $row->category->name }}</span></label><br>
                                                <label>Berat: <span class="badge badge-info">{{ $row->weight }} gr</span></label>
                                            </td>
                                            <td>Rp {{ number_format($row->price) }}</td>
                                            <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                            
                                            <!-- KARENA BERISI HTML MAKA KITA GUNAKAN { !! UNTUK MENCETAK DATA -->
                                            <td>{!! $row->status_label !!}</td>
                                            <td class="text-center">
                                                <a href="{{ route('product.show', $row->id) }}" class="btn btn-primary btn-sm">View</a>
                                                <a href="{{ route('product.edit', $row->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete" data-remote=" {{ route('product.destroy', $row->id)  }}" data-id="{{ $row->id }}">Hapus</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- MEMBUAT LINK PAGINASI JIKA ADA -->
                            <br>
                            <div class="row">
                                <div class="col-4">
                                    <div class="dataTables_info float-left" id="DataTables_Table_0_info" role="status" aria-live="polite">Total {{ number_format($count)}} Entries </div>
                                </div>
                                <div class="col-8">
                                    <div class="float-right">
                                        {!! $product->links() !!}
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
    $(document).ready(function(){
        // BERAT TERNYATA PAKE DATATABLE
        // DATA >= 10.000
        // option = {
        //     responsive: true,
        //     processing: true,
        //     serverSide: true,
        //     deferRender: true,
        //     ajax: {  
        //         url: "{{ route('product.index') }}",
        //         pages: 10,
        //     },
        //     columns: [
        //         {
        //         data: 'DT_RowIndex', 
        //         name: 'DT_RowIndex',
        //         className: "text-center",
        //         },
        //         {
        //         data: 'image',
        //         name: 'image'
        //         },
        //         {
        //         data: 'product',
        //         name: 'product'
        //         },
        //         {
        //         data: 'price',
        //         name: 'price'
        //         },
        //         {
        //         data: 'created_at',
        //         name: 'created_at',
        //         className: "text-center",
        //         },
        //         {
        //         data: 'action',
        //         name: 'action',
        //         orderable: false,
        //         searchable: false
        //         }
        //     ]
        // };
        // FUNCTION DATATABLE DISINI
        // $('.dataTable').DataTable().on( 'draw', function () {
        //     $('[data-toggle="tooltip"]').tooltip();
        // });

        // CEK ERROR
        category    = "{{ $errors->first('category_id') }}";
        file        = "{{ $errors->first('file') }}";

        if(category || file){
            $('#myModal').modal('show');
        }

        $('.btnUpload').on('click', function () {  
            file        = $('#file').val();
            category_id = $('#category_id').val();
            cek         = true;

            if(!file) cek = false
            
            if(!category_id) cek = false

            if(cek){
                swal.fire({
                    icon : 'info',
                    title: 'Harap menunggu',
                    text: 'Sedang menyimpan data',
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick : false,
                    allowEscapeKey : false,
                    allowEnterKey : false
                })
            }
        })
    });
    </script>
@endsection