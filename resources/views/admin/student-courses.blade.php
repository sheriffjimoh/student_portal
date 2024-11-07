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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const handleRemoveEnrollment = async (studentId, courseId, button) => {
        try {
            const response = await fetch(`/admin/students/${studentId}/courses/${courseId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Remove the table row
                const row = button.closest('tr');
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                
                setTimeout(() => {
                    row.remove();
                    
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    
                    const tableResponsive = document.querySelector('.table-responsive');
                    tableResponsive.parentNode.insertBefore(alert, tableResponsive);

                    // Remove message after 3 seconds
                    setTimeout(() => {
                        alert.style.transition = 'opacity 0.3s';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }, 3000);
                }, 300);
            } else {
                throw new Error(data.message || 'Failed to remove enrollment');
            }
        } catch (error) {
            console.error('Error:', error);
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger alert-dismissible fade show';
            errorAlert.innerHTML = `
                ${error.message || 'An error occurred while removing the enrollment.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            const tableResponsive = document.querySelector('.table-responsive');
            tableResponsive.parentNode.insertBefore(errorAlert, tableResponsive);
            
            setTimeout(() => {
                errorAlert.style.transition = 'opacity 0.3s';
                errorAlert.style.opacity = '0';
                setTimeout(() => errorAlert.remove(), 300);
            }, 3000);
        }
    };

    // Add click event listeners to remove buttons
    document.querySelectorAll('.remove-enrollment').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.dataset.studentId;
            const courseId = this.dataset.courseId;
            
            if (confirm('Are you sure you want to remove this enrollment?')) {
                handleRemoveEnrollment(studentId, courseId, this);
            }
        });
    });
});
</script>
@endsection