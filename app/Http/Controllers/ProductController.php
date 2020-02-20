<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use DataTables;
use SweetAlert;
use App\Category;
use Illuminate\Support\Str;
use File;
use App\Jobs\ProductJob;

class ProductController extends Controller
{
    public function index(Request $req)
    {
        //BUAT QUERY MENGGUNAKAN MODEL PRODUCT, DENGAN MENGURUTKAN DATA BERDASARKAN CREATED_AT
        //KEMUDIAN LOAD TABLE YANG BERELASI MENGGUNAKAN EAGER LOADING WITH()
        //ADAPUN CATEGORY ADALAH NAMA FUNGSI YANG NNTINYA AKAN DITAMBAHKAN PADA PRODUCT MODEL
        $product    = Product::with(['category'])->orderBy('created_at', 'DESC');
        $count      = Product::all()->count();
      
        //JIKA TERDAPAT PARAMETER PENCARIAN DI URL ATAU Q PADA URL TIDAK SAMA DENGAN KOSONG
        if (request()->q != '') {
            //MAKA LAKUKAN FILTERING DATA BERDASARKAN NAME DAN VALUENYA SESUAI DENGAN PENCARIAN YANG DILAKUKAN USER
            $product->where('name', 'LIKE', '%' . request()->q . '%')
                    ->orWhere('price', 'LIKE', '%' . request()->q . '%')
                    ->orWhere('weight', 'LIKE', '%' . request()->q . '%')
                    ->orWhereHas('category', function ($query) {
                        $query->where('name', 'LIKE',  '%' . request()->q . '%');
                    });
        }
        //TERAKHIR LOAD 10 DATA PER HALAMANNYA
        $product = $product->paginate(10);
        //LOAD VIEW INDEX.BLADE.PHP YANG BERADA DIDALAM FOLDER PRODUCTS
        //DAN PASSING VARIABLE $PRODUCT KE VIEW AGAR DAPAT DIGUNAKAN

        $category = Category::orderBy('name', 'DESC')->get();

        return view('products.index', compact('product','category', 'count'));
    }

    public function create()
    {
        //QUERY UNTUK MENGAMBIL SEMUA DATA CATEGORY
        $category = Category::orderBy('name', 'DESC')->get();
        //LOAD VIEW create.blade.php` YANG BERADA DIDALAM FOLDER PRODUCTS
        //DAN PASSING DATA CATEGORY
        return view('products.create', compact('category'));
    }

    public function store(Request $request)
    {
        //VALIDASI REQUESTNYA
        $this->validate($request, [
            'name'          => 'required|string|max:100',
            'description'   => 'required',
            'category_id'   => 'required|exists:categories,id', //CATEGORY_ID KITA CEK HARUS ADA DI TABLE CATEGORIES DENGAN FIELD ID
            'price'         => 'required|integer',
            'weight'        => 'required|integer',
            'image'         => 'required|image|mimes:png,jpeg,jpg' //GAMBAR DIVALIDASI HARUS BERTIPE PNG,JPG DAN JPEG
        ]);

        //JIKA FILENYA ADA
        if ($request->hasFile('image')) {
            //MAKA KITA SIMPAN SEMENTARA FILE TERSEBUT KEDALAM VARIABLE FILE
            $file = $request->file('image');
            //KEMUDIAN NAMA FILENYA KITA BUAT CUSTOMER DENGAN PERPADUAN TIME DAN SLUG DARI NAMA PRODUK. ADAPUN EXTENSIONNYA KITA GUNAKAN BAWAAN FILE TERSEBUT
            $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            //SIMPAN FILENYA KEDALAM FOLDER PUBLIC/PRODUCTS, DAN PARAMETER KEDUA ADALAH NAMA CUSTOM UNTUK FILE TERSEBUT
            $file->storeAs('public/products', $filename);

            //SETELAH FILE TERSEBUT DISIMPAN, KITA SIMPAN INFORMASI PRODUKNYA KEDALAM DATABASE
            $product = Product::create([
                'name'          => $request->name,
                'slug'          => $request->name,
                'category_id'   => $request->category_id,
                'description'   => $request->description,
                'image'         => $filename, //PASTIKAN MENGGUNAKAN VARIABLE FILENAM YANG HANYA BERISI NAMA FILE SAJA (STRING)
                'price'         => $request->price,
                'weight'        => $request->weight,
                'status'        => $request->status
            ]);
            //JIKA SUDAH MAKA REDIRECT KE LIST PRODUK
            return redirect(route('product.index'))->with(['success' => 'Produk Baru Ditambahkan']);
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id); //QUERY UNTUK MENGAMBIL DATA PRODUK BERDASARKAN ID
        //HAPUS FILE IMAGE DARI STORAGE PATH DIIKUTI DENGNA NAMA IMAGE YANG DIAMBIL DARI DATABASE
        File::delete(storage_path('app/public/products/' . $product->image));
        //KEMUDIAN HAPUS DATA PRODUK DARI DATABASE
        if($product->delete()){
            return response()->json(['code' => 'success', 'msg' => 'Data berhasil di hapus']);
        }else{
            return response()->json(['code' => 'error', 'msg' => 'Terjadi kesalahan!']);
        }
        //DAN REDIRECT KE HALAMAN LIST PRODUK
        // return redirect(route('product.index'))->with(['success' => 'Produk Sudah Dihapus']);
    }

    public function massUpload(Request $request)
    {
        //VALIDASI DATA YANG DIKIRIM
        $this->validate($request, [
            'category_id'   => 'required|exists:categories,id',
            'file'          => 'required|mimes:xlsx' //PASTIKAN FORMAT FILE YANG DITERIMA ADALAH XLSX
        ]);

        //JIKA FILE-NYA ADA
        if ($request->hasFile('file')) {
            $file       = $request->file('file');
            $filename   = time() . '-product.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads', $filename); //MAKA SIMPAN FILE TERSEBUT DI STORAGE/APP/PUBLIC/UPLOADS

            //BUAT JADWAL UNTUK PROSES FILE TERSEBUT DENGAN MENGGUNAKAN JOB
            //ADAPUN PADA DISPATCH KITA MENGIRIMKAN DUA PARAMETER SEBAGAI INFORMASI
            //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
            ProductJob::dispatch($request->category_id, $filename);
            return redirect()->back()->with(['success' => 'Upload Produk Dijadwalkan']);
        }
    }

    public function edit($id)
    {
        $product    = Product::find($id); //AMBIL DATA PRODUK TERKAIT BERDASARKAN ID
        $category   = Category::orderBy('name', 'DESC')->get(); //AMBIL SEMUA DATA KATEGORI
        return view('products.edit', compact('product', 'category')); //LOAD VIEW DAN PASSING DATANYA KE VIEW
    }

    public function update(Request $request, $id)
    {
    //VALIDASI DATA YANG DIKIRIM
        $this->validate($request, [
            'name'          => 'required|string|max:100',
            'description'   => 'required',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|integer',
            'weight'        => 'required|integer',
            'image'         => 'nullable|image|mimes:png,jpeg,jpg' //IMAGE BISA NULLABLE
        ]);

        $product    = Product::find($id); //AMBIL DATA PRODUK YANG AKAN DIEDIT BERDASARKAN ID
        $filename   = $product->image; //SIMPAN SEMENTARA NAMA FILE IMAGE SAAT INI
    
        //JIKA ADA FILE GAMBAR YANG DIKIRIM
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            //MAKA UPLOAD FILE TERSEBUT
            $file->storeAs('public/products', $filename);
            //DAN HAPUS FILE GAMBAR YANG LAMA
            File::delete(storage_path('app/public/products/' . $product->image));
        }

        //KEMUDIAN UPDATE PRODUK TERSEBUT
        $product->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'price'         => $request->price,
            'weight'        => $request->weight,
            'image'         => $filename
        ]);
        return redirect(route('product.index'))->with(['success' => 'Data Produk Diperbaharui']);
    }

    public function show($id)
    {
        $data = Product::find($id);

        return view('products.show', compact('data')); //LOAD VIEW DAN PASSING DATANYA KE VIEW
    }
}

