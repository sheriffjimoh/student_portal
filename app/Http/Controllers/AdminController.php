<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $courses = $student->courses()
        ->withPivot('created_at')  
        ->orderBy('enrollments.created_at', 'desc')  
        ->paginate(10);

        return view('admin.student-courses', compact('student', 'courses'));
    }

    public function destroyCourse(User $student, Course $course)
{
    try {
        // Check if the student is enrolled in the course
        $enrollment = $student->courses()->where('course_id', $course->id)->first();
        
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Student is not enrolled in this course.'
            ], 404);
        }

        // Remove the enrollment
        $student->courses()->detach($course->id);

        return response()->json([
            'success' => true,
            'message' => "Successfully removed {$student->name} from {$course->name}."
        ]);

    } catch (\Exception $e) {
        Log::error('Error removing course enrollment', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while removing the enrollment.'
        ], 500);
    }
}
}
