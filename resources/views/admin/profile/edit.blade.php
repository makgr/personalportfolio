@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title m-0">Edit Profile</h5>
                        <p class="card-text">Update your personal information and account settings</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-5">
                <!-- Profile Picture -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $profile->avatar_url ? asset($profile->avatar_url) : asset('assets/img/avatars/1.png') }}"
                                alt="user-avatar" class="d-block rounded" height="100" width="100"
                                id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <p class="text-muted mb-0">Allowed JPG, PNG. Max size of 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About Photo (stored as hero_image_url; shown in the home page "whoami" card below the hero) -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-1">About Photo</h6>
                        <p class="text-muted small mb-3">Shown in the "whoami" photo card on the home page, just
                            below the hero. A natural photo of you works best.</p>
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $profile->hero_image_url ? asset($profile->hero_image_url) : asset('assets/img/avatars/1.png') }}"
                                alt="about-photo" class="d-block rounded" height="100" width="100"
                                id="uploadedHeroImage" />
                            <div class="button-wrapper">
                                <label for="hero_upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload about photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <p class="text-muted mb-0">Allowed JPG, PNG. Max size of 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hero Cutout Portrait (large portrait in the frontend home hero) -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-1">Hero Cutout Portrait</h6>
                        <p class="text-muted small mb-3">The large portrait in the home page hero. Use a PNG or WebP
                            with a transparent background, or a photo on a plain black background, head and
                            shoulders, portrait orientation.</p>
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $profile->hero_cutout_url ? asset($profile->hero_cutout_url) : asset('assets/img/avatars/1.png') }}"
                                alt="hero-cutout" class="d-block rounded" height="100" width="100"
                                style="object-fit: cover; background: #0b0f17;" id="uploadedHeroCutout" />
                            <div class="button-wrapper">
                                <label for="hero_cutout_upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload cutout portrait</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <p class="text-muted mb-0">Allowed PNG, WebP, JPG. Max size of 4MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 col-md-7">
                <!-- Account Details -->
                <div class="card mb-4">
                    <form id="profile-form" data-ajax="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-header">
                            <h5 class="mb-0">Account Details</h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input class="form-control" type="text" id="full_name" name="full_name"
                                        value="{{ old('full_name', $profile->full_name) }}" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="title" class="form-label">Professional Title</label>
                                    <input class="form-control" type="text" id="title" name="title"
                                        value="{{ old('title', $profile->title) }}" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input class="form-control" type="email" id="email" name="email"
                                        value="{{ old('email', $profile->email) }}" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input class="form-control" type="text" id="phone" name="phone"
                                        value="{{ old('phone', $profile->phone) }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <input class="form-control" type="text" id="location" name="location"
                                        value="{{ old('location', $profile->location) }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="subtitle" class="form-label">Subtitle</label>
                                    <input class="form-control" type="text" id="subtitle" name="subtitle"
                                        value="{{ old('subtitle', $profile->subtitle) }}" />
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="4" required>{{ old('bio', $profile->bio) }}</textarea>
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="short_bio" class="form-label">Short Bio</label>
                                    <textarea class="form-control" id="short_bio" name="short_bio" rows="2">{{ old('short_bio', $profile->short_bio) }}</textarea>
                                </div>
                            </div>

                            <!-- Social Links -->
                            <h6 class="mt-4 mb-3">Social Links</h6>
                            <div class="row">
                                @php $socialLinks = $profile->social_links ?? [] @endphp
                                <div class="mb-3 col-md-6">
                                    <label for="github" class="form-label">GitHub</label>
                                    <input class="form-control" type="url" id="github"
                                        name="social_links[github]"
                                        value="{{ old('social_links.github', $socialLinks['github'] ?? '') }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="linkedin" class="form-label">LinkedIn</label>
                                    <input class="form-control" type="url" id="linkedin"
                                        name="social_links[linkedin]"
                                        value="{{ old('social_links.linkedin', $socialLinks['linkedin'] ?? '') }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="twitter" class="form-label">Twitter</label>
                                    <input class="form-control" type="url" id="twitter"
                                        name="social_links[twitter]"
                                        value="{{ old('social_links.twitter', $socialLinks['twitter'] ?? '') }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="facebook" class="form-label">Facebook</label>
                                    <input class="form-control" type="url" id="facebook"
                                        name="social_links[facebook]"
                                        value="{{ old('social_links.facebook', $socialLinks['facebook'] ?? '') }}" />
                                </div>
                            </div>

                            <!-- Profile Statistics -->
                            <h6 class="mt-4 mb-3">Profile Statistics</h6>
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="years_of_experience" class="form-label">Years of Experience</label>
                                    <input class="form-control" type="number" id="years_of_experience"
                                        name="years_of_experience" min="0"
                                        value="{{ old('years_of_experience', $profile->years_of_experience ?? 0) }}" />
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="total_projects" class="form-label">Total Projects</label>
                                    <input class="form-control" type="number" id="total_projects"
                                        name="total_projects" min="0"
                                        value="{{ old('total_projects', $profile->total_projects ?? 0) }}" />
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="happy_clients" class="form-label">Happy Clients</label>
                                    <input class="form-control" type="number" id="happy_clients"
                                        name="happy_clients" min="0"
                                        value="{{ old('happy_clients', $profile->happy_clients ?? 0) }}" />
                                </div>
                            </div>

                            <!-- Account Security -->
                            <h6 class="mt-4 mb-3">Account Security</h6>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="user_name" class="form-label">Username</label>
                                    <input class="form-control" type="text" id="user_name" name="user_name"
                                        value="{{ old('user_name', $user->name) }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="user_email" class="form-label">Account Email</label>
                                    <input class="form-control" type="email" id="user_email" name="user_email"
                                        value="{{ old('user_email', $user->email) }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input class="form-control" type="password" id="current_password"
                                        name="current_password" />
                                    <small class="text-muted">Required only if changing password</small>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input class="form-control" type="password" id="new_password" name="new_password" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input class="form-control" type="password" id="new_password_confirmation"
                                        name="new_password_confirmation" />
                                </div>
                            </div>
                        </div>

                        <!-- Hidden file inputs inside form -->
                        <input type="file" id="upload" name="upload" class="d-none"
                            accept="image/png, image/jpeg" />
                        <input type="file" id="hero_upload" name="hero_upload" class="d-none"
                            accept="image/png, image/jpeg" />
                        <input type="file" id="hero_cutout_upload" name="hero_cutout_upload" class="d-none"
                            accept="image/png, image/webp, image/jpeg" />

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="save-profile-btn">
                                <span class="btn-text">
                                    <i class="bx bx-save me-1"></i>
                                    Save Changes
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span>
                                    Saving...
                                </span>
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Profile Avatar Image Handler
        const fileInput = document.getElementById('upload');
        const previewImage = document.getElementById('uploadedAvatar');
        const originalSrc = previewImage ? previewImage.src : '';

        if (fileInput && previewImage) {
            fileInput.addEventListener('change', function(e) {
                console.log('File input changed:', e.target.files);
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        toastr.error('Please select a valid image file (JPG, PNG)');
                        fileInput.value = '';
                        return;
                    }

                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error('Image size must be less than 2MB');
                        fileInput.value = '';
                        return;
                    }

                    // Show instant preview
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        previewImage.src = ev.target.result;

                        // Visual feedback
                        previewImage.style.opacity = '0.7';
                        previewImage.style.border = '2px dashed #007bff';

                        toastr.info('Avatar image selected. Save changes to upload.', 'Image Ready');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // If no file selected, reset to original
                    previewImage.src = originalSrc;
                    previewImage.style.opacity = '1';
                    previewImage.style.border = 'none';
                }
            });
        }

        // Instant preview + client-side checks for the hero image uploads
        function bindImagePreview(inputId, previewId, allowedTypes, maxMb, label) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            if (!input || !preview) return;
            const originalSrc = preview.src;

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    preview.src = originalSrc;
                    preview.style.opacity = '1';
                    preview.style.border = 'none';
                    return;
                }
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Please select a valid image file (' + allowedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ') + ')');
                    input.value = '';
                    return;
                }
                if (file.size > maxMb * 1024 * 1024) {
                    toastr.error('Image size must be less than ' + maxMb + 'MB');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.opacity = '0.7';
                    preview.style.border = '2px dashed #007bff';
                    toastr.info(label + ' selected. Save changes to upload.', 'Image Ready');
                };
                reader.readAsDataURL(file);
            });
        }

        bindImagePreview('hero_upload', 'uploadedHeroImage', ['image/jpeg', 'image/jpg', 'image/png'], 2, 'About photo');
        bindImagePreview('hero_cutout_upload', 'uploadedHeroCutout', ['image/png', 'image/webp', 'image/jpeg', 'image/jpg'], 4, 'Hero cutout portrait');
    </script>
@endpush
