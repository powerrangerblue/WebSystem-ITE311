<?= $this->extend('template') ?>

<?= $this->section('title') ?>Upload Material<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">
                        <i class="fas fa-upload"></i> Upload Material for Course
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= site_url('admin/course/' . $course_id . '/upload') ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-4">
                            <label for="file" class="form-label fw-bold">
                                <i class="fas fa-file"></i> Select File
                            </label>
                            <input type="file" class="form-control form-control-lg" id="file" name="file" required 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt" onchange="showFileInfo(this)">
                            <div class="form-text">
                                <strong>Allowed file types:</strong> PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT<br>
                                <strong>Maximum file size:</strong> 10MB
                            </div>
                            <div id="fileInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <span id="fileName"></span> 
                                    (<span id="fileSize"></span>)
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?php 
                            $role = strtolower((string) session()->get('role'));
                            $backUrl = 'dashboard'; // Default to dashboard
                            
                            // Set appropriate back URL based on user role
                            if ($role === 'admin') {
                                $backUrl = 'admin/materials';
                            } elseif ($role === 'teacher') {
                                $backUrl = 'teacher/dashboard';
                            } elseif ($role === 'student') {
                                $backUrl = 'dashboard';
                            }
                            ?>
                            <a href="<?= site_url($backUrl) ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left"></i> Back to <?= $role === 'admin' ? 'Materials Management' : 'Dashboard' ?>
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload"></i> Upload Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showFileInfo(input) {
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        fileName.textContent = file.name;
        
        // Format file size
        const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
        fileSize.textContent = sizeInMB + ' MB';
        
        // Show file info
        fileInfo.style.display = 'block';
        
        // Check file size
        if (file.size > 10 * 1024 * 1024) {
            fileInfo.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> File size exceeds 10MB limit!</div>';
        }
    } else {
        fileInfo.style.display = 'none';
    }
}

// Add form submission debugging
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('file');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please select a file to upload.');
        e.preventDefault();
        return false;
    }
    
    console.log('Form submitting with file:', fileInput.files[0].name);
    console.log('Form action:', document.getElementById('uploadForm').action);
    console.log('File size:', fileInput.files[0].size);
    console.log('File type:', fileInput.files[0].type);
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    submitBtn.disabled = true;
    
    // Don't prevent default - let the form submit normally
    return true;
});
</script>

<?= $this->endSection() ?>
