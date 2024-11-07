@extends('layouts.app')

@section('content')


<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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
                    this.closest('tr').remove();
                    showAlert(data.message);
                } else {
                    showAlert(data.message, 'danger');
                }
            } catch (error) {
                showAlert('An error occurred while unenrolling.', 'danger');
            }
        });
    });

    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('main .container').insertBefore(alertDiv, document.querySelector('main .container').firstChild);
    }
});
</script>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">My Enrollments</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Enrolled Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->course->name }}</td>
                            <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm unenroll-btn" 
                                        data-course-id="{{ $enrollment->course_id }}">
                                    Unenroll
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $enrollments->links() }}
        </div>
    </div>
</div>


@endsection