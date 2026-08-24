<?php

namespace App\Support;

// A curated, alphabetical list of major Pakistani cities/towns — used
// wherever a "select your city" field defaults to Pakistan (e.g. the Nikah
// Counselor application). Deliberately not exhaustive (no equivalent
// cities-database file is bundled the way App\Support\CountryStates bundles
// countries/states) — <x-searchable-select> still accepts free text beyond
// this list, so a smaller town not listed here is never a hard block.
class PakistanCities
{
    public static function all(): array
    {
        return [
            'Abbottabad', 'Ahmedpur East', 'Attock', 'Bahawalnagar', 'Bahawalpur',
            'Bannu', 'Batgram', 'Bhakkar', 'Bhimber', 'Chakwal', 'Chaman', 'Charsadda',
            'Chichawatni', 'Chiniot', 'Chishtian', 'Dadu', 'Daska', 'Dera Ghazi Khan',
            'Dera Ismail Khan', 'Faisalabad', 'Gilgit', 'Gojra', 'Gujranwala', 'Gujrat',
            'Gwadar', 'Hafizabad', 'Hangu', 'Haripur', 'Hyderabad', 'Islamabad',
            'Jacobabad', 'Jamshoro', 'Jampur', 'Jaranwala', 'Jhang', 'Jhelum', 'Kabirwala',
            'Kamalia', 'Kamoke', 'Karachi', 'Kasur', 'Khairpur', 'Khanewal', 'Khanpur',
            'Kharian', 'Khushab', 'Khuzdar', 'Kohat', 'Kotli', 'Kot Addu', 'Lahore',
            'Larkana', 'Layyah', 'Lodhran', 'Loralai', 'Mailsi', 'Mandi Bahauddin',
            'Mansehra', 'Mardan', 'Mianwali', 'Mingora', 'Mirpur', 'Mirpur Khas',
            'Multan', 'Muridke', 'Murree', 'Muzaffarabad', 'Muzaffargarh', 'Narowal',
            'Nawabshah', 'Nowshera', 'Okara', 'Pakpattan', 'Pattoki', 'Peshawar',
            'Quetta', 'Rahim Yar Khan', 'Rawalpindi', 'Sadiqabad', 'Sahiwal', 'Sanghar',
            'Sargodha', 'Sheikhupura', 'Shikarpur', 'Sialkot', 'Sibi', 'Skardu',
            'Sukkur', 'Swabi', 'Swat', 'Taxila', 'Toba Tek Singh', 'Turbat', 'Vehari',
            'Wah Cantt', 'Wazirabad',
        ];
    }
}
