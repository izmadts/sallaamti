<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Certificate extends Model
{
    protected $fillable = ['certificate_number', 'user_id', 'course_id', 'type', 'title', 'issued_by', 'issued_at'];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'course_id' => 'integer',
            'issued_by' => 'integer',
            'issued_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function generateNumber(string $prefix = 'SLM'): string
    {
        return $prefix . '-' . now()->format('Y') . '-' . strtoupper(Str::random(10));
    }



    public function qrCodeBase64(): string
    {
        $url = url('/verify-certificate/' . $this->certificate_number);

        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getDataUri();
    }
}
