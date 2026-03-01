<?php

namespace Database\Seeders;

use App\Models\Relationship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;

class RelationshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relationship::create(['relationship_name' => 'Single AF', 'relationship_desc' => 'Still single, no partner']);
        Relationship::create(['relationship_name' => 'Taken', 'relationship_desc' => 'In a relationship, already have a partner']);
        Relationship::create(['relationship_name' => "It's Complicated", 'relationship_desc' => 'The relationship is unclear or has many complexities']);
        Relationship::create(['relationship_name' => "Situationship", 'relationship_desc' => "Talking to someone, but there's no clear commitment or label"]);
        Relationship::create(['relationship_name' => 'In a Relationship ', 'relationship_desc' => 'Officially dating, in a committed relationship']);
        Relationship::create(['relationship_name' => 'Engaged', 'relationship_desc' => 'Officially engaged, planning to get married']);
        Relationship::create(['relationship_name' => 'Married', 'relationship_desc' => 'Officially married']);
        Relationship::create(['relationship_name' => 'Divorced', 'relationship_desc' => 'Ended a marriage']);
        Relationship::create(['relationship_name' => 'Talking', 'relationship_desc' => 'In the early stages of getting to know someone, no official label yet']);
    }
}
