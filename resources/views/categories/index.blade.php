<!-- MEMANGGIL MASTER TEMPLATE YANG SUDAH DIBUAT SEBELUMNYA, YAKNI admin.blade.php -->
@extends('layouts.admin')

@section('title')
    List Kategori
@endsection

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item">Home</li>
                            <li class="breadcrumb-item active">Kategori</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
              	<!-- BAGIAN INI AKAN MENG-HANDLE FORM INPUT NEW CATEGORY  -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Kategori Baru</h4>
                            <br>
                            <form action="{{ route('category.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Kategori</label>
                                    <input type="text" name="name" class="form-control {{ $errors->first('name') ? ' is-invalid' : '' }}" required>
                                    <p class="invalid-feedback">{{ $errors->first('name') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="parent_id">Parent</label>
                                      <!-- VARIABLE $PARENT PADA METHOD INDEX KITA GUNAKAN DISINI -->
                                    <!-- UNTUK MENAMPILKAN DATA CATEGORY YANG PARENT_ID NYA NULL -->
                                    <!-- UNTUK DIPILIH SEBAGAI PARENT TAPI SIFATNYA OPTIONAL -->
                                    <select name="parent_id" class="form-control {{ $errors->first('parent_id') ? ' is-invalid' : '' }}">
                                        <option value="">None</option>
                                        @foreach ($parent as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="invalid-feedback">{{ $errors->first('parent_id') }}</p>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- BAGIAN INI AKAN MENG-HANDLE FORM INPUT NEW CATEGORY  -->
              
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">List Kategori</h4>
                            <br>
                            
                            <div class="table-responsive">
                                <table class="table table-striped data-table dataTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th width='50px'>#</th>
                                            <th>Kategori</th>
                                            <th>Parent</th>
                                            <th>Created At</th>
                                            <th width='140px'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- ISININYA BY SERVER SIDE --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
    $(document).ready(function(){
        
        option = {
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('category.index') }}",
            columns: [
                {
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                className: "text-center",
                },
                {
                data: 'name',
                name: 'name'
                },
                {
                name: 'parent.name',
                className: "text-center",
                data: function (param) {  
                        if(param.parent){
                            return param.parent.name
                        }
                        return '-'
                    },
                },
                {
                data: 'created_at',
                name: 'created_at',
                className: "text-center",
                },
                {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
                }
            ]
        };
        // FUNCTION DATATABLE DISINI
        $('.dataTable').DataTable(option).on( 'draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
    </script>
@endsection