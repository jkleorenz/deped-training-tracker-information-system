@extends('layouts.app')

@section('title', 'Edit Profile - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="page-title mb-0">Edit Profile</h1>
    <a href="{{ route('profile.password') }}" class="btn btn-outline-primary">
        <i class="bi bi-key me-1"></i> Change Password
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <p class="text-muted small mb-4">Update your personal information. Email must be unique.</p>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <p class="small fw-semibold text-muted mb-2 pt-2 border-top">Personnel metadata</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                   value="{{ old('employee_id', $user->employee_id) }}">
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Position / Designation</label>
                            <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror"
                                   value="{{ old('designation', $user->designation) }}">
                            @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="school" class="form-label">School / Office</label>
                        <input type="text" name="school" id="school" class="form-control @error('school') is-invalid @enderror"
                               value="{{ old('school', $user->school) }}">
                        @error('school')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="theme" class="form-label">Theme <span class="badge bg-primary ms-2">Preview Available</span></label>
                        <div class="theme-selector-container">
                            <div class="theme-grid" id="themeGrid">
                                <div class="theme-option" data-theme="default" data-color="#1E35FF">
                                    <div class="theme-preview default-theme">
                                        <div class="preview-header"></div>
                                        <div class="preview-sidebar"></div>
                                        <div class="preview-content">
                                            <div class="preview-accent"></div>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name">Default Blue</div>
                                        <div class="theme-description">Classic blue theme</div>
                                    </div>
                                    <div class="theme-radio">
                                        <input type="radio" name="theme" value="default" 
                                               {{ old('theme', $user->theme) == 'default' ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                    </div>
                                </div>
                                
                                <div class="theme-option" data-theme="red" data-color="#dc2626">
                                    <div class="theme-preview red-theme">
                                        <div class="preview-header"></div>
                                        <div class="preview-sidebar"></div>
                                        <div class="preview-content">
                                            <div class="preview-accent"></div>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name">Red</div>
                                        <div class="theme-description">Bold red accent</div>
                                    </div>
                                    <div class="theme-radio">
                                        <input type="radio" name="theme" value="red" 
                                               {{ old('theme', $user->theme) == 'red' ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                    </div>
                                </div>
                                
                                <div class="theme-option" data-theme="green" data-color="#16a34a">
                                    <div class="theme-preview green-theme">
                                        <div class="preview-header"></div>
                                        <div class="preview-sidebar"></div>
                                        <div class="preview-content">
                                            <div class="preview-accent"></div>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name">Green</div>
                                        <div class="theme-description">Natural green theme</div>
                                    </div>
                                    <div class="theme-radio">
                                        <input type="radio" name="theme" value="green" 
                                               {{ old('theme', $user->theme) == 'green' ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                    </div>
                                </div>
                                
                                <div class="theme-option" data-theme="black" data-color="#000000">
                                    <div class="theme-preview black-theme">
                                        <div class="preview-header"></div>
                                        <div class="preview-sidebar"></div>
                                        <div class="preview-content">
                                            <div class="preview-accent"></div>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name">Dark Mode</div>
                                        <div class="theme-description">Elegant dark theme</div>
                                    </div>
                                    <div class="theme-radio">
                                        <input type="radio" name="theme" value="black" 
                                               {{ old('theme', $user->theme) == 'black' ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                    </div>
                                </div>
                                
                                <div class="theme-option" data-theme="deep-purple" data-color="#6B46C1">
                                    <div class="theme-preview deep-purple-theme">
                                        <div class="preview-header"></div>
                                        <div class="preview-sidebar"></div>
                                        <div class="preview-content">
                                            <div class="preview-accent"></div>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name">Deep Purple</div>
                                        <div class="theme-description">Rich purple accent</div>
                                    </div>
                                    <div class="theme-radio">
                                        <input type="radio" name="theme" value="deep-purple" 
                                               {{ old('theme', $user->theme) == 'deep-purple' ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                    </div>
                                </div>
                                
                                <div class="theme-option" data-theme="yellow" data-color="#EAB308">
                                    <div class="theme-preview yellow-theme">
                                        <div class="preview-header"></div>
                                        <div class="preview-sidebar"></div>
                                        <div class="preview-content">
                                            <div class="preview-accent"></div>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name">Yellow</div>
                                        <div class="theme-description">Warm yellow theme</div>
                                    </div>
                                    <div class="theme-radio">
                                        <input type="radio" name="theme" value="yellow" 
                                               {{ old('theme', $user->theme) == 'yellow' ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="theme-preview-section mt-3" id="livePreview" style="display: none;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-eye"></i>
                                    <span class="fw-semibold">Live Preview</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="closePreview">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div class="preview-container">
                                    <div class="mini-dashboard" id="miniDashboard">
                                        <div class="mini-sidebar"></div>
                                        <div class="mini-main">
                                            <div class="mini-header"></div>
                                            <div class="mini-content">
                                                <div class="mini-card"></div>
                                                <div class="mini-card"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('theme')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-deped">Save changes</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Enhanced Theme Selector Styles */
.theme-selector-container {
    background: #f8fafc;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
}

.theme-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.theme-option {
    position: relative;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.theme-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #cbd5e1;
}

.theme-option.selected {
    border-color: #1E35FF;
    box-shadow: 0 0 0 3px rgba(30, 53, 255, 0.1);
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
}

.theme-preview {
    width: 100%;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0.75rem;
    position: relative;
    border: 1px solid #e2e8f0;
    background: #fff;
}

.preview-header {
    height: 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.preview-sidebar {
    position: absolute;
    left: 0;
    top: 20px;
    width: 30%;
    height: 60px;
    background: #f1f5f9;
    border-right: 1px solid #e2e8f0;
}

.preview-content {
    position: absolute;
    left: 30%;
    top: 20px;
    width: 70%;
    height: 60px;
    background: #fff;
}

.preview-accent {
    width: 40px;
    height: 8px;
    border-radius: 4px;
    margin: 8px;
    transition: background 0.3s ease;
}

/* Theme-specific preview colors */
.default-theme .preview-accent { background: #1E35FF; }
.red-theme .preview-accent { background: #dc2626; }
.green-theme .preview-accent { background: #16a34a; }
.black-theme .preview-accent { background: #000000; }
.deep-purple-theme .preview-accent { background: #6B46C1; }
.yellow-theme .preview-accent { background: #EAB308; }

.theme-info {
    text-align: center;
    margin-bottom: 0.5rem;
}

.theme-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.theme-description {
    font-size: 0.75rem;
    color: #64748b;
}

.theme-radio {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.theme-radio input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.radio-mark {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #cbd5e1;
    border-radius: 50%;
    background: #fff;
    position: relative;
    transition: all 0.3s ease;
}

.theme-radio input[type="radio"]:checked + .radio-mark {
    border-color: #1E35FF;
    background: #1E35FF;
}

.theme-radio input[type="radio"]:checked + .radio-mark::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #fff;
}

/* Live Preview Section */
.theme-preview-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #e2e8f0;
}

.preview-container {
    background: #fff;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e2e8f0;
}

.mini-dashboard {
    display: flex;
    height: 120px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.mini-sidebar {
    width: 30%;
    background: #f8fafc;
    border-right: 1px solid #e2e8f0;
    transition: background 0.3s ease;
}

.mini-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.mini-header {
    height: 30px;
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.3s ease;
}

.mini-content {
    flex: 1;
    background: #fff;
    padding: 8px;
    display: flex;
    gap: 8px;
    align-items: center;
}

.mini-card {
    flex: 1;
    height: 40px;
    border-radius: 4px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .theme-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
    }
    
    .theme-selector-container {
        padding: 1rem;
    }
    
    .theme-preview {
        height: 60px;
    }
}

/* Animation for theme switching */
@keyframes themeSwitch {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.theme-option.selected {
    animation: themeSwitch 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme selector functionality
    const themeOptions = document.querySelectorAll('.theme-option');
    const livePreview = document.getElementById('livePreview');
    const miniDashboard = document.getElementById('miniDashboard');
    const closePreview = document.getElementById('closePreview');
    let previewTimeout;

    // Theme color mappings
    const themeColors = {
        'default': { primary: '#1E35FF', light: '#4d5fff', accent: '#93c5fd' },
        'red': { primary: '#dc2626', light: '#f87171', accent: '#fecaca' },
        'green': { primary: '#16a34a', light: '#4ade80', accent: '#bbf7d0' },
        'black': { primary: '#000000', light: '#404040', accent: '#d1d5db' },
        'deep-purple': { primary: '#6B46C1', light: '#9333EA', accent: '#E9D5FF' },
        'yellow': { primary: '#EAB308', light: '#FDE047', accent: '#FEF3C7' }
    };

    // Initialize selected state
    function initializeSelectedTheme() {
        const checkedRadio = document.querySelector('input[name="theme"]:checked');
        if (checkedRadio) {
            const themeOption = checkedRadio.closest('.theme-option');
            if (themeOption) {
                themeOption.classList.add('selected');
            }
        }
    }

    // Handle theme option click
    themeOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            // Don't trigger if clicking on the radio input directly
            if (e.target.type !== 'radio') {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            }
        });

        // Handle radio change
        const radio = option.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function() {
                // Remove selected class from all options
                themeOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selected class to current option
                option.classList.add('selected');
                
                // Show live preview
                showLivePreview(option.dataset.theme);
            });
        }

        // Hover effect for preview
        option.addEventListener('mouseenter', function() {
            clearTimeout(previewTimeout);
            showLivePreview(this.dataset.theme);
        });

        option.addEventListener('mouseleave', function() {
            previewTimeout = setTimeout(() => {
                // Hide preview if not selecting
                const checkedRadio = document.querySelector('input[name="theme"]:checked');
                if (checkedRadio && checkedRadio.closest('.theme-option') !== this) {
                    hideLivePreview();
                }
            }, 500);
        });
    });

    // Show live preview
    function showLivePreview(theme) {
        if (!livePreview || !miniDashboard) return;

        const colors = themeColors[theme];
        if (!colors) return;

        // Show preview section
        livePreview.style.display = 'block';
        
        // Apply theme colors to mini dashboard
        const miniCards = miniDashboard.querySelectorAll('.mini-card');
        const miniSidebar = miniDashboard.querySelector('.mini-sidebar');
        const miniHeader = miniDashboard.querySelector('.mini-header');

        // Apply colors with transitions
        miniSidebar.style.background = colors.light + '20';
        miniHeader.style.background = colors.primary;
        
        miniCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.background = colors.accent + '30';
                card.style.borderColor = colors.accent;
            }, index * 100);
        });

        // Add entrance animation
        livePreview.style.opacity = '0';
        livePreview.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            livePreview.style.transition = 'all 0.3s ease';
            livePreview.style.opacity = '1';
            livePreview.style.transform = 'translateY(0)';
        }, 50);
    }

    // Hide live preview
    function hideLivePreview() {
        if (livePreview) {
            livePreview.style.transition = 'all 0.3s ease';
            livePreview.style.opacity = '0';
            livePreview.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                livePreview.style.display = 'none';
            }, 300);
        }
    }

    // Close preview button
    if (closePreview) {
        closePreview.addEventListener('click', function() {
            hideLivePreview();
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideLivePreview();
        }
    });

    // Initialize
    initializeSelectedTheme();

    // Add smooth scrolling to theme selector
    const themeGrid = document.getElementById('themeGrid');
    if (themeGrid) {
        themeGrid.scrollBehavior = 'smooth';
    }

    // Enhanced visual feedback for selection
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Create ripple effect
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(30, 53, 255, 0.3)';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            const rect = this.getBoundingClientRect();
            ripple.style.left = (event.clientX - rect.left - 10) + 'px';
            ripple.style.top = (event.clientY - rect.top - 10) + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
