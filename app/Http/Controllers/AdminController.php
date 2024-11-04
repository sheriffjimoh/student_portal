<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;



class AdminController extends Controller
{
    public function dashboard()
    {
        $statistics = [
            'total_students' => User::where('role_id', 2)->count(),
            'students_per_course' => Course::withCount('students')->get(),
            'unenrolled_students' => User::where('role_id', 2)
                ->whereDoesntHave('enrollments')
                ->count()
        ];

        return view('admin.dashboard', compact('statistics'));
    }

    public function students(Request $request)
    {
        $query = User::where('role_id', 2);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $students = $query->paginate(15);
        return view('admin.students', compact('students'));
    }

    public function studentCourses($id)
    {
        $student = User::findOrFail($id);
        $courses = $student->courses()->paginate(10);
        return view('admin.student-courses', compact('student', 'courses'));
    }
}
