<?php

namespace Database\Seeders;

use App\Models\Zodiac;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZodiacsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Zodiac::create(['zodiac_name' => 'Aries', 'zodiac_desc' => '(March 21 - April 19)']);
        Zodiac::create(['zodiac_name' => 'Taurus', 'zodiac_desc' => '(April 20 - May 20)']);
        Zodiac::create(['zodiac_name' => 'Gemini', 'zodiac_desc' => '(May 21 - June 20)']);
        Zodiac::create(['zodiac_name' => 'Cancer', 'zodiac_desc' => '(June 21 - July 22)']);
        Zodiac::create(['zodiac_name' => 'Leo', 'zodiac_desc' => '(July 23 - August 22)']);
        Zodiac::create(['zodiac_name' => 'Virgo', 'zodiac_desc' => '(August 23 - September 22)']);
        Zodiac::create(['zodiac_name' => 'Libra', 'zodiac_desc' => '(September 23 - October 22)']);
        Zodiac::create(['zodiac_name' => 'Scorpio', 'zodiac_desc' => '(October 23 - November 21)']);
        Zodiac::create(['zodiac_name' => 'Sagittarius', 'zodiac_desc' => '(November 22 - December 21)']);
        Zodiac::create(['zodiac_name' => 'Capricorn', 'zodiac_desc' => '(December 22 - January 19)']);
        Zodiac::create(['zodiac_name' => 'Aquarius', 'zodiac_desc' => '(January 20 - February 18)']);
        Zodiac::create(['zodiac_name' => 'Pisces', 'zodiac_desc' => '(February 19 - March 20)']);
    }
}
