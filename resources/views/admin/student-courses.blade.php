@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Courses for {{ $student->name }}
                <small class="text-muted">({{ $student->email }})</small>
            </h5>
            <a href="{{ route('admin.students') }}" class="btn btn-secondary">
                Back to Students
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($courses->isEmpty())
            <div class="alert alert-info">
                This student is not enrolled in any courses.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th>Enrollment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                        <tr>
                            <td>{{ $course->name }}</td>
                            <td>{{ Str::limit($course->description, 100) }}</td>
                            <td>{{ $course->pivot->created_at->format('M d, Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-enrollment" 
                                        data-student-id="{{ $student->id }}"
                                        data-course-id="{{ $course->id }}">
                                    Remove
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle enrollment removal
    document.querySelectorAll('.remove-enrollment').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to remove this enrollment?')) {
                return;
            }

            const studentId = this.dataset.studentId;
            const courseId = this.dataset.courseId;

            try {
                const response = await fetch(`/admin/students/${studentId}/courses/${courseId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.closest('tr').remove();
                    
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.textContent = data.message;
                    document.querySelector('.card-body').insertBefore(alert, document.querySelector('.table-responsive'));
                    
                    // Remove message after 3 seconds
                    setTimeout(() => alert.remove(), 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while removing the enrollment.');
            }
        });
    });
});
</script>
@endpush
@endsection