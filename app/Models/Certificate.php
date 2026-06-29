<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Certificate extends Model
{
    protected $fillable = ['certificate_number', 'user_id', 'course_id', 'issued_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public static function generateNumber(): string
    {
        return 'SLM-' . now()->format('Y') . '-' . strtoupper(uniqid());
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
