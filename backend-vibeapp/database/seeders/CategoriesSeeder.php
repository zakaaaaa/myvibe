<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create(['title' => 'Music Vault', 'url' => 'https://api.deezer.com/search/artist?q=']);
        Category::create(['title' => 'ALBUM', 'url' => 'https://api.deezer.com/search/album?q=']);
        Category::create(['title' => 'MUSICIAN', 'url' => 'https://api.deezer.com/search/artist?q=']);
        Category::create(['title' => 'PODCAST', 'url' => 'https://api.spotify.com']);
        Category::create(['title' => 'MOVIE', 'url' => 'https://www.omdbapi.com', 'token' => '2fda0cf7']);
        Category::create(['title' => 'SERIES', 'url' => 'https://www.omdbapi.com', 'token' => '2fda0cf7']);
        Category::create(['title' => 'SPORT', 'url' => 'https://highlightly.net/?gad_source=1&gbraid=0AAAAAqeQ5HC4l2NdmRc2uiMYCAv27zWU6&gclid=CjwKCAiA3ZC6BhBaEiwAeqfvyv9tsV2PUBQdeOlXzCbAo3QFwy0I5MhuAUS1_R5My-fqR0HuTXjmLRoCjYIQAvD_BwE']);
        Category::create(['title' => 'BOOK', 'url' => 'https://developers.google.com/books']);
    }
}
