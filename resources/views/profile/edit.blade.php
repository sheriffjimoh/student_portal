@extends('layouts.app')
@section('content')

<script>
// First verify script is loading
console.log('Script starting');

// Wrap in a function to avoid global scope issues
function initializeProfileForm() {
    console.log('Initializing profile form');
    
    // Get DOM elements
    const form = document.getElementById('profileForm');
    const alertMessage = document.getElementById('alertMessage');
    const photoInput = document.getElementById('photo');
    const currentPhoto = document.querySelector('img.img-thumbnail');
    const placeholderDiv = document.querySelector('.rounded-circle.bg-secondary');
    
    // Debug log
    console.log('Elements found:', { 
        form, 
        alertMessage, 
        photoInput, 
        currentPhoto, 
        placeholderDiv 
    });

    // Photo preview handler
    if (photoInput) {
        console.log('Setting up photo input handler');
        photoInput.onchange = function(e) {
            console.log('Photo changed', e);
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    console.log('File read complete');
                    if (currentPhoto) {
                        currentPhoto.src = e.target.result;
                    } else if (placeholderDiv) {
                        const newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.className = 'img-thumbnail rounded-circle';
                        newImg.style.width = '150px';
                        newImg.style.height = '150px';
                        newImg.style.objectFit = 'cover';
                        placeholderDiv.parentNode.replaceChild(newImg, placeholderDiv);
                    }
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        };
    }

    // Form submission handler
    if (form) {
        console.log('Setting up form submission handler');
        form.onsubmit = async function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            clearErrors();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('{{ route("profile.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                console.log('Response:', data);

                if (response.ok) {
                    showAlert('Profile updated successfully!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    if (response.status === 422) {
                        Object.keys(data.errors).forEach(key => {
                            const errorElement = document.getElementById(`${key}Error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                document.getElementById(key).classList.add('is-invalid');
                            }
                        });
                        showAlert('Please correct the errors below.', 'danger');
                    } else {
                        throw new Error(data.message || 'An error occurred');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert(error.message || 'An error occurred while updating the profile.', 'danger');
            }
        };
    }

    function clearErrors() {
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
        if (alertMessage) alertMessage.classList.add('d-none');
    }

    function showAlert(message, type) {
        if (alertMessage) {
            alertMessage.textContent = message;
            alertMessage.className = `alert alert-${type} mb-3`;
            alertMessage.classList.remove('d-none');
            alertMessage.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

// Try multiple ways to ensure the script runs
document.addEventListener('DOMContentLoaded', initializeProfileForm);
window.addEventListener('load', initializeProfileForm);
// Immediate execution as fallback
initializeProfileForm();
</script>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Profile</h5>
            </div>
            <div class="card-body">
                <!-- Remove the form action and add id -->
                <form id="profileForm" enctype="multipart/form-data">
                    @csrf
                    <!-- <input type="hidden" name="_method" value="PUT"> -->

                    <!-- Alert for showing messages -->
                    <div id="alertMessage" class="alert d-none mb-3"></div>

                    <!-- Rest of your form fields remain the same -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                @if($user->photo_path)
                                    <img src="{{ Storage::url($user->photo_path) }}" 
                                         alt="Profile Photo"
                                         class="img-thumbnail rounded-circle"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 150px; height: 150px; margin: 0 auto;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Update Photo</label>
                                <input type="file" class="form-control" 
                                       id="photo" name="photo" accept="image/*">
                                <div class="invalid-feedback" id="photoError"></div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}"
                                       required>
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}"
                                       required>
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" 
                                       id="date_of_birth" name="date_of_birth" 
                                       value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                       required>
                                <div class="invalid-feedback" id="dateOfBirthError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" 
                                          id="address" name="address" rows="3" 
                                          required>{{ old('address', $user->address) }}</textarea>
                                <div class="invalid-feedback" id="addressError"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Password fields remain the same -->

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection