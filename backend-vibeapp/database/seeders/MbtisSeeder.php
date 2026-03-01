<?php

namespace Database\Seeders;

use App\Models\Mbti;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MbtisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Mbti::create(['mbti_name' => 'ISTJ', 'mbti_desc' => 'Introversion, Sensing, Thinking, Judging']);
        Mbti::create(['mbti_name' => 'ISFJ', 'mbti_desc' => 'Introversion, Sensing, Feeling, Judging']);
        Mbti::create(['mbti_name' => 'INFJ', 'mbti_desc' => 'Introversion, Intuition, Feeling, Judging']);
        Mbti::create(['mbti_name' => 'INTJ', 'mbti_desc' => 'Introversion, Intuition, Thinking, Judging']);
        Mbti::create(['mbti_name' => 'ISTP', 'mbti_desc' => 'Introversion, Sensing, Thinking, Perceiving']);
        Mbti::create(['mbti_name' => 'ISFP', 'mbti_desc' => 'Introversion, Sensing, Feeling, Perceiving']);
        Mbti::create(['mbti_name' => 'INFP', 'mbti_desc' => 'Introversion, Intuition, Feeling, Perceiving']);
        Mbti::create(['mbti_name' => 'INTP', 'mbti_desc' => 'Introversion, Intuition, Thinking, Perceiving']);
        Mbti::create(['mbti_name' => 'ESTP', 'mbti_desc' => 'Extraversion, Sensing, Thinking, Perceiving']);
        Mbti::create(['mbti_name' => 'ESFP', 'mbti_desc' => 'Extraversion, Sensing, Feeling, Perceiving']);
        Mbti::create(['mbti_name' => 'ENFP', 'mbti_desc' => 'Extraversion, Intuition, Feeling, Perceiving']);
        Mbti::create(['mbti_name' => 'ENFJ', 'mbti_desc' => 'Extraversion, Intuition, Feeling, Judging']);
        Mbti::create(['mbti_name' => 'ENTP', 'mbti_desc' => ' Extraversion, Intuition, Thinking, Perceiving']);
        Mbti::create(['mbti_name' => 'ENTJ', 'mbti_desc' => 'Extraversion, Intuition, Thinking, Judging']);
        Mbti::create(['mbti_name' => 'ESTJ', 'mbti_desc' => 'Extraversion, Sensing, Thinking, Judging']);
        Mbti::create(['mbti_name' => 'ESFJ', 'mbti_desc' => 'Extraversion, Sensing, Feeling, Judging']);
    }
}
