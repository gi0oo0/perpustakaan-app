<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'description',
        'cover_image',
        'stock',
        'kategori',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public static function getKategoriList(): array
    {
        return [
            'Fiksi' => 'Fiksi',
            'Non-Fiksi' => 'Non-Fiksi',
            'Sains & Teknologi' => 'Sains & Teknologi',
            'Sejarah' => 'Sejarah',
            'Pendidikan' => 'Pendidikan',
            'Agama' => 'Agama',
            'Komik' => 'Komik',
            'Novel' => 'Novel',
            'Biografi' => 'Biografi',
            'Lainnya' => 'Lainnya',
        ];
    }
}
