<!-- @extends('layouts.app') -->

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Admin Dashboard</h2>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Students</h5>
                <p class="card-text display-4">{{ $statistics['total_students'] }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Active Enrollments</h5>
                <p class="card-text display-4">
                    {{ $statistics['students_per_course']->sum('students_count') }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Unenrolled Students</h5>
                <p class="card-text display-4">{{ $statistics['unenrolled_students'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Course Enrollment Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Course Enrollment Statistics</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Enrolled Students</th>
                        <th>Enrollment Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statistics['students_per_course'] as $course)
                    <tr>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->students_count }}</td>
                        <td>
                            @if($statistics['total_students'] > 0)
                                {{ round(($course->students_count / $statistics['total_students']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection