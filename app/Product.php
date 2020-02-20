<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    //JIKA FILLABLE AKAN MENGIZINKAN FIELD APA SAJA YANG ADA DIDALAM ARRAYNYA
    //MAKA GUARDED AKAN MEMBLOK FIELD APA SAJA YANG ADA DIDALAM ARRAY-NYA
    //JADI APABILA FIELDNYA BANYAK MAKA KITA BISA MANFAATKAN DENGAN HANYA MENULISKAN ARRAY KOSONG
    //YANG BERARTI TIDAK ADA FIELD YANG DIBLOCK SEHINGGA SEMUA FIELD TERSEBUT SUDAH DIIZINAKAN
    //HAL INI MEMUDAHKAN KITA KARENA TIDAK PERLU MENULISKANNYA SATU PERSATU
    protected $guarded = [];

    //SEDANGKAN INI ADALAH MUTATORS, PENJELASANNYA SAMA DENGAN ARTIKEL SEBELUMNYA
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value); 
    }

    //INI ADALAH ACCESSOR, JADI KITA MEMBUAT KOLOM BARU BERNAMA STATUS_LABEL
    //KOLOM TERSEBUT DIHASILKAN OLEH ACCESSOR, MESKIPUN FIELD TERSEBUT TIDAK ADA DITABLE PRODUCTS
    //AKAN TETAPI AKAN DISERTAKAN PADA HASIL QUERY
    public function getStatusLabelAttribute()
    {
        //ADAPUN VALUENYA AKAN MENCETAK HTML BERDASARKAN VALUE DARI FIELD STATUS
        if ($this->status == 0) {
            return '<span class="badge badge-secondary">Draft</span>';
        }
        return '<span class="badge badge-success">Aktif</span>';
    }

    //FUNGSI YANG MENG-HANDLE RELASI KE TABLE CATEGORY
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

