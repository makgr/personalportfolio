@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title m-0">Settings</h5>
                        <p class="card-text">Manage your portfolio website settings and configuration</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills nav-fill mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" 
                                        id="general-tab" data-bs-toggle="pill" data-bs-target="#general" 
                                        type="button" role="tab" aria-controls="general" aria-selected="true">
                                    <i class="bx bx-cog me-1"></i>
                                    General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'social' ? 'active' : '' }}" 
                                        id="social-tab" data-bs-toggle="pill" data-bs-target="#social" 
                                        type="button" role="tab" aria-controls="social" aria-selected="false">
                                    <i class="bx bx-share-alt me-1"></i>
                                    Social Media
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'seo' ? 'active' : '' }}" 
                                        id="seo-tab" data-bs-toggle="pill" data-bs-target="#seo" 
                                        type="button" role="tab" aria-controls="seo" aria-selected="false">
                                    <i class="bx bx-search me-1"></i>
                                    SEO
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab === 'advanced' ? 'active' : '' }}" 
                                        id="advanced-tab" data-bs-toggle="pill" data-bs-target="#advanced" 
                                        type="button" role="tab" aria-controls="advanced" aria-selected="false">
                                    <i class="bx bx-wrench me-1"></i>
                                    Advanced
                                </button>
                            </li>
                        </ul>

                        <form id="settings-form" data-ajax="{{ route('settings.update') }}">
                            @csrf
                            @method('POST')

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- General Settings Tab -->
                                <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" 
                                     id="general" role="tabpanel" aria-labelledby="general-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="site_name" class="form-label">Site Name</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                                   value="{{ old('site_name', $settingsGroups->get('general', collect())->where('key', 'site_name')->first()->value ?? '') }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="site_tagline" class="form-label">Site Tagline</label>
                                            <input type="text" class="form-control" id="site_tagline" name="site_tagline" 
                                                   value="{{ old('site_tagline', $settingsGroups->get('general', collect())->where('key', 'site_tagline')->first()->value ?? '') }}" />
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="site_description" class="form-label">Site Description</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3">{{ old('site_description', $settingsGroups->get('general', collect())->where('key', 'site_description')->first()->value ?? '') }}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                                   value="{{ old('contact_email', $settingsGroups->get('general', collect())->where('key', 'contact_email')->first()->value ?? '') }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" name="phone" 
                                                   value="{{ old('phone', $settingsGroups->get('general', collect())->where('key', 'phone')->first()->value ?? '') }}" />
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $settingsGroups->get('general', collect())->where('key', 'address')->first()->value ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media Tab -->
                                <div class="tab-pane fade {{ $activeTab === 'social' ? 'show active' : '' }}" 
                                     id="social" role="tabpanel" aria-labelledby="social-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="github_url" class="form-label">
                                                <i class="bx bxl-github me-1"></i> GitHub URL
                                            </label>
                                            <input type="url" class="form-control" id="github_url" name="github_url" 
                                                   value="{{ old('github_url', $settingsGroups->get('social', collect())->where('key', 'github_url')->first()->value ?? '') }}" 
                                                   placeholder="https://github.com/username" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="linkedin_url" class="form-label">
                                                <i class="bx bxl-linkedin me-1"></i> LinkedIn URL
                                            </label>
                                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                                   value="{{ old('linkedin_url', $settingsGroups->get('social', collect())->where('key', 'linkedin_url')->first()->value ?? '') }}" 
                                                   placeholder="https://linkedin.com/in/username" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="twitter_url" class="form-label">
                                                <i class="bx bxl-twitter me-1"></i> Twitter URL
                                            </label>
                                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                                   value="{{ old('twitter_url', $settingsGroups->get('social', collect())->where('key', 'twitter_url')->first()->value ?? '') }}" 
                                                   placeholder="https://twitter.com/username" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="facebook_url" class="form-label">
                                                <i class="bx bxl-facebook me-1"></i> Facebook URL
                                            </label>
                                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                                   value="{{ old('facebook_url', $settingsGroups->get('social', collect())->where('key', 'facebook_url')->first()->value ?? '') }}" 
                                                   placeholder="https://facebook.com/username" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="instagram_url" class="form-label">
                                                <i class="bx bxl-instagram me-1"></i> Instagram URL
                                            </label>
                                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                                   value="{{ old('instagram_url', $settingsGroups->get('social', collect())->where('key', 'instagram_url')->first()->value ?? '') }}" 
                                                   placeholder="https://instagram.com/username" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="youtube_url" class="form-label">
                                                <i class="bx bxl-youtube me-1"></i> YouTube URL
                                            </label>
                                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                                   value="{{ old('youtube_url', $settingsGroups->get('social', collect())->where('key', 'youtube_url')->first()->value ?? '') }}" 
                                                   placeholder="https://youtube.com/channel/your-channel" />
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO Tab -->
                                <div class="tab-pane fade {{ $activeTab === 'seo' ? 'show active' : '' }}" 
                                     id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <div class="alert alert-info">
                                                <i class="bx bx-info-circle me-2"></i>
                                                SEO settings help improve your website's visibility in search engines. These settings are managed separately for each page.
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <h6 class="mb-3">Default SEO Settings</h6>
                                            <p class="text-muted mb-4">These will be used as defaults for pages that don't have specific SEO settings.</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="default_meta_title" class="form-label">Default Meta Title</label>
                                            <input type="text" class="form-control" id="default_meta_title" name="default_meta_title" 
                                                   value="{{ old('default_meta_title', $seoSettings->get('default')->title ?? '') }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="default_meta_keywords" class="form-label">Default Keywords</label>
                                            <input type="text" class="form-control" id="default_meta_keywords" name="default_meta_keywords" 
                                                   value="{{ old('default_meta_keywords', $seoSettings->get('default')->keywords ?? '') }}" 
                                                   placeholder="keyword1, keyword2, keyword3" />
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="default_meta_description" class="form-label">Default Meta Description</label>
                                            <textarea class="form-control" id="default_meta_description" name="default_meta_description" rows="3">{{ old('default_meta_description', $seoSettings->get('default')->description ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Advanced Tab -->
                                <div class="tab-pane fade {{ $activeTab === 'advanced' ? 'show active' : '' }}" 
                                     id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="google_analytics" class="form-label">Google Analytics Tracking ID</label>
                                            <input type="text" class="form-control" id="google_analytics" name="google_analytics" 
                                                   value="{{ old('google_analytics', $settingsGroups->get('analytics', collect())->where('key', 'google_analytics')->first()->value ?? '') }}" 
                                                   placeholder="G-XXXXXXXXXX or UA-XXXXXXXXX-X" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="items_per_page" class="form-label">Items Per Page</label>
                                            <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                                                   value="{{ old('items_per_page', $settingsGroups->get('site', collect())->where('key', 'items_per_page')->first()->value ?? '10') }}" 
                                                   min="1" max="100" />
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                                       value="1" {{ old('maintenance_mode', $settingsGroups->get('site', collect())->where('key', 'maintenance_mode')->first()->value ?? false) ? 'checked' : '' }} />
                                                <label class="form-check-label" for="maintenance_mode">
                                                    <strong>Maintenance Mode</strong>
                                                    <small class="text-muted d-block">Enable this to show a maintenance page to visitors</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <hr>
                                            <h6>Cache Management</h6>
                                            <p class="text-muted">Clear application cache to refresh settings and improve performance.</p>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    data-ajax="{{ route('settings.clear-cache') }}"
                                                    data-loading="Clearing...">
                                                <i class="bx bx-refresh me-1"></i>
                                                Clear Cache
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary" id="save-settings-btn">
                                    <span class="btn-text">
                                        <i class="bx bx-save me-1"></i>
                                        Save Settings
                                    </span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        Saving...
                                    </span>
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="bx bx-x me-1"></i>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Settings page is now handled automatically by admin-ajax.js
// The form has data-ajax attribute and buttons have data-ajax attributes
// All AJAX functionality is handled automatically!

// Optional: Add any custom callbacks if needed
document.addEventListener('DOMContentLoaded', function () {
    // Example of custom success handling if needed
    const settingsForm = document.getElementById('settings-form');
    if (settingsForm) {
        settingsForm.dataset.onSuccess = 'console.log("Settings updated successfully!", data)';
    }
});
</script>
@endpush