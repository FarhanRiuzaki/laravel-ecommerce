@extends('layouts.admin')

@section('title')
    Tambah Product
@endsection

@section('content')
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
        </div>
    </div>

    <div class="container-fluid">
        <!-- TAMBAHKAN ENCTYPE="" KETIKA MENGIRIMKAN FILE PADA FORM -->
        <form action="{{ route('product.store') }}" method="post" enctype="multipart/form-data" >
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Tambah Produk</h4>
                            <br>
                            <div class="form-group">
                                <label for="name">Nama Produk</label>
                                <input type="text" name="name" class="form-control  {{ isValid($errors->first('name')) }}" value="{{ old('name') }}" required>
                                <p class="invalid-feedback">{{ $errors->first('name') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                            
                                <!-- TAMBAHKAN ID YANG NNTINYA DIGUNAKAN UTK MENGHUBUNGKAN DENGAN CKEDITOR -->
                                <textarea name="description" id="description" class="form-control  {{ isValid($errors->first('description')) }}">{{ old('description') }}</textarea>
                                <p class="invalid-feedback">{{ $errors->first('description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" class="form-control  {{ isValid($errors->first('status')) }}" required>
                                    <option value="1" {{ old('status') == '1' ? 'selected':'' }}>Publish</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected':'' }}>Draft</option>
                                </select>
                                <p class="invalid-feedback">{{ $errors->first('status') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Kategori</label>
                                
                                <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                <select name="category_id" class="form-control  {{ isValid($errors->first('category_id')) }}">
                                    <option value="">Pilih</option>
                                    @foreach ($category as $row)
                                    <option value="{{ $row->id }}" {{ old('category_id') == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                    @endforeach
                                </select>
                                <p class="invalid-feedback">{{ $errors->first('category_id') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" name="price" class="form-control  {{ isValid($errors->first('price')) }}" value="{{ old('price') }}" required>
                                <p class="invalid-feedback">{{ $errors->first('price') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="weight">Berat</label>
                                <input type="number" name="weight" class="form-control  {{ isValid($errors->first('weight')) }}" value="{{ old('weight') }}" required>
                                <p class="invalid-feedback">{{ $errors->first('weight') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="image">Foto Produk</label>
                                <input type="file" name="image" class="form-control  {{ isValid($errors->first('image')) }}" value="{{ old('image') }}" required>
                                <p class="invalid-feedback">{{ $errors->first('image') }}</p>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm">Tambah</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        CKEDITOR.replace('description');
    </script>
@endsection