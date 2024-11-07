@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">My Enrolled Courses</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($enrolledCourses as $course)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $course->name }}</h5>
                                    <p class="card-text">{{ $course->description }}</p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button class="btn btn-danger w-100 unenroll-btn" 
                                            data-course-id="{{ $course->id }}">
                                        Unenroll
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                You are not enrolled in any courses yet.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Available Courses</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($availableCourses as $course)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $course->name }}</h5>
                                    <p class="card-text">{{ $course->description }}</p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button class="btn btn-primary w-100 enroll-btn" 
                                            data-course-id="{{ $course->id }}">
                                        Enroll
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No available courses to enroll in.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF token for all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Common function for showing alerts
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('main .container').insertBefore(alertDiv, document.querySelector('main .container').firstChild);
    }

    // Enroll functionality
    document.querySelectorAll('.enroll-btn').forEach(button => {
        button.addEventListener('click', async function() {
            try {
                const response = await fetch('{{ route("student.enroll") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ course_id: this.dataset.courseId })
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert(data.message, 'danger');
                }
            } catch (error) {
                showAlert('An error occurred while enrolling.', 'danger');
            }
        });
    });

    document.querySelectorAll('.unenroll-btn').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to unenroll from this course?')) {
                return;
            }

            try {
                const response = await fetch('{{ route("student.unenroll") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ course_id: this.dataset.courseId })
                });

                const data = await response.json();
                
                if (data.success) {
                    showAlert(data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert(data.message, 'danger');
                }
            } catch (error) {
                showAlert('An error occurred while unenrolling.', 'danger');
            }
        });
    });
});
</script>
@endsection
