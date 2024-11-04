<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $enrolledCourses = $user->courses;
        $availableCourses = Course::whereNotIn('id', $user->courses->pluck('id'))->get();
        
        return view('student.dashboard', compact('enrolledCourses', 'availableCourses'));
    }

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        try {

            $user = Auth::user();
            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $validated['course_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully enrolled in course'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll in course'
            ], 500);
        }
    }

    public function unenroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        try {
            Enrollment::where('user_id', Auth::id())
                ->where('course_id', $validated['course_id'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unenrolled from course'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unenroll from course'
            ], 500);
        }
    }

    public function enrollments()
    {
        $enrollments = Auth::user()
            ->enrollments()
            ->with('course')
            ->latest()
            ->paginate(10);

        return view('student.enrollments', compact('enrollments'));
    }
}