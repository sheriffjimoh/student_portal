<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
         $courses = [
                [
                    'name' => 'Introduction to Programming',
                    'description' => 'Learn the basics of programming'
                ],
                [
                    'name' => 'Web Development',
                    'description' => 'Learn web development with HTML, CSS, and JavaScript'
                ],
                [
                    'name' => 'Database Design',
                    'description' => 'Learn database design and SQL'
                ]
            ];
    
            foreach ($courses as $course) {
                Course::create($course);
            }
        
    }
}
