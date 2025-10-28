@extends('layouts.app')
@section('page')
cosplayers
@endsection
@section('title')
Bulk Add Cosplayer
@endsection
@section('content')

<main class="h-full pb-16 overflow-y-auto">
          <div class="container px-6 mx-auto grid">
            <h2
              class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
            >
              @isset($cosplayer)
                Bulk Add Cosplayers
              @endisset

            </h2>

            @if(Session::has('success'))
            <div
              class="flex items-center justify-between px-4 p-2 mb-8 text-sm font-semibold text-green-600 bg-green-100 rounded-lg focus:outline-none focus:shadow-outline-purple"
            >
              <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>{{ Session::get('success') }}</span>
              </div>
            </div>
            @endif
            @if(Session::has('error'))
            <div
              class="flex items-center justify-between px-4 p-2 mb-8 text-sm font-semibold text-red-600 bg-red-100 rounded-lg focus:outline-none focus:shadow-outline-purple"
            >
              <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>{{ Session::get('error') }}</span>
              </div>
            </div>
            @endif
            <!-- General elements -->

            <div class="px-4 py-3 mb-4 bg-blue-50 rounded-lg border border-blue-200 dark:bg-blue-900 dark:border-blue-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                  <span class="text-blue-700 dark:text-blue-300 text-sm font-medium">Need a template?</span>
                </div>
                <div class="flex space-x-2">
                  <a href="{{ route('cosplayers.download-sample') }}"
                     class="inline-flex items-center px-3 py-2 text-xs font-medium leading-4 text-blue-600 bg-white border border-blue-300 rounded-md shadow-sm hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-blue-800 dark:text-blue-200 dark:border-blue-600 dark:hover:bg-blue-700">
                    <i class="fas fa-download mr-1"></i>
                    Excel Sample
                  </a>
                  <a href="{{ asset('samples/cosplayers-sample.csv') }}"
                     download="cosplayers-sample.csv"
                     class="inline-flex items-center px-3 py-2 text-xs font-medium leading-4 text-green-600 bg-white border border-green-300 rounded-md shadow-sm hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-green-800 dark:text-green-200 dark:border-green-600 dark:hover:bg-green-700">
                    <i class="fas fa-download mr-1"></i>
                    CSV Sample
                  </a>
                </div>
              </div>
              <p class="text-blue-600 dark:text-blue-400 text-xs mt-2">
                Download a sample Excel file with the correct column format: name, character, anime, number, stage_name
              </p>
              <p class="text-blue-600 dark:text-blue-400 text-xs mt-1">
                💡 <strong>Pro tip:</strong> Use flexible naming: <code>1.jpg</code>, <code>001-1.jpg</code>, <code>2-front.png</code> for automatic matching!
              </p>
            </div>

            <!-- Custom Columns Info -->
            <div class="px-4 py-3 mb-4 bg-emerald-50 rounded-lg border border-emerald-200 dark:bg-emerald-900 dark:border-emerald-700">
              <div class="flex items-start">
                <i class="fas fa-magic text-emerald-500 mr-2 mt-0.5"></i>
                <div class="text-emerald-700 dark:text-emerald-300 text-sm">
                  <strong>🚀 NEW: Custom Columns Support!</strong><br>
                  • Add any custom columns to your Excel/CSV beyond the required ones<br>
                  • Examples: <code>gender</code>, <code>age</code>, <code>location</code>, <code>notes</code>, <code>social_media</code>, etc.<br>
                  • All custom data will be automatically imported and displayed in cosplayer details<br>
                  • Perfect for storing additional contestant information specific to your event<br>
                  • Download the updated sample file to see examples with custom columns
                </div>
              </div>
            </div>

            <form method="POST" enctype="multipart/form-data"
            action="{{ route('cosplayers.bulk-add') }}"
              class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800"
            >
            <span class="text-red-500 text-sm">* Is required</span>
            @csrf
            @if($errors->any())
                {!! implode('', $errors->all('<div class="text-red-500">:message</div>')) !!}
            @endif
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-sort-numeric-up-alt text-xl"></i>
                Cosplayers Sheet <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(or upload images only)</span>
                </span>
                <input
                type="file"
                name="sheet"
                accept=".xlsx,.xls,.csv"
                class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                placeholder="100"
                />
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                  <i class="fas fa-file-excel text-green-500 mr-1"></i>
                  Accepted formats: Excel (.xlsx, .xls) or CSV (.csv)
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                  <i class="fas fa-table text-blue-500 mr-1"></i>
                  Required columns: name, character, anime, number, stage_name
                </span>
                <span class="text-xs text-green-600 dark:text-green-400 mt-1 block">
                  <i class="fas fa-plus-circle text-green-500 mr-1"></i>
                  Custom columns: Add any additional columns (gender, age, location, notes, etc.) - they will be automatically imported!
                </span>
              </label>
              <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400">
                <i class="las la-calendar text-xl"></i>
                Event <span class="text-red-500">*</span>
                </span>
                <select
                value="{{ old('character')??$cosplayer->character??"" }}"
                name="event_id"
                  required
                  class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                >
                <option value="" selected disabled>Select Event</option>
                @foreach($events as $event)
                  <option {{ (old('event_id')==$event->id || (($cosplayer->event_id??0)==$event->id && old('event_id') == null))?'selected':"" }} value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach

                </select>
              </label>

              <div class="mt-6 border-t border-gray-200 dark:border-gray-600 pt-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                  <i class="fas fa-images text-purple-500 mr-2"></i>
                  Optional: Upload Images & References
                </h3>
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-4">
                  <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-500 mr-2 mt-0.5"></i>
                    <div class="text-yellow-700 dark:text-yellow-300 text-sm">
                      <strong>File Naming Rules:</strong><br>
                      • <strong>Single images:</strong> <code>1.jpg</code>, <code>001.jpg</code>, <code>2.png</code><br>
                      • <strong>Multiple images:</strong> <code>1-1.jpg</code>, <code>1-2.jpg</code>, <code>001-front.png</code><br>
                      • Images are automatically matched to cosplayers by number
                    </div>
                  </div>
                </div>
                <!-- Upload Options Info -->
                <div class="px-4 py-3 mb-4 bg-green-50 rounded-lg border border-green-200 dark:bg-green-900 dark:border-green-700">
                  <div class="flex items-start">
                    <i class="fas fa-info-circle text-green-500 mr-2 mt-0.5"></i>
                    <div class="text-green-700 dark:text-green-300 text-sm">
                      <strong>Choose Your Upload Method:</strong><br>
                      • <strong>Individual Files:</strong> Upload up to 20 images directly (5MB per file, 50MB total)<br>
                      • <strong>ZIP Files:</strong> For bulk uploads of 20+ images (max 100MB per ZIP)<br>
                      • ZIP files automatically extract and process all images inside<br>
                      • <a href="{{ route('cosplayers.upload-limits') }}" target="_blank" class="text-green-600 underline">Check server limits</a> for troubleshooting
                    </div>
                  </div>
                </div>
                <!-- Cosplayer Images Section -->
                <div class="mb-6 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                  <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-camera text-blue-500 mr-2"></i>
                    Cosplayer Images (Optional)
                  </h4>

                  <!-- Radio buttons for upload method -->
                  <div class="mb-3 space-y-2">
                    <label class="flex items-center">
                      <input type="radio" name="images_method" value="individual" checked class="mr-2">
                      <span class="text-sm text-gray-700 dark:text-gray-300">Individual files (up to 20 files)</span>
                    </label>
                    <label class="flex items-center">
                      <input type="radio" name="images_method" value="zip" class="mr-2">
                      <span class="text-sm text-gray-700 dark:text-gray-300">ZIP file (for 20+ images)</span>
                    </label>
                  </div>

                  <!-- Individual files upload -->
                  <div id="images-individual" class="upload-method">
                    <input
                      type="file"
                      name="images[]"
                      accept="image/*"
                      multiple
                      class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    />
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                      <i class="fas fa-file-image text-blue-500 mr-1"></i>
                      Max 5MB per file, 20 files total, 50MB combined limit
                    </span>
                  </div>

                  <!-- ZIP upload -->
                  <div id="images-zip" class="upload-method hidden">
                    <input
                      type="file"
                      name="images_zip"
                      accept=".zip"
                      class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    />
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                      <i class="fas fa-file-archive text-blue-500 mr-1"></i>
                      ZIP file containing images (max 100MB) - Images should follow naming convention: 1.jpg, 001-1.jpg, etc.
                    </span>
                  </div>
                </div>

                <!-- Character References Section -->
                <div class="mb-6 p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                  <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-image text-green-500 mr-2"></i>
                    Character References (Optional)
                  </h4>

                  <!-- Radio buttons for upload method -->
                  <div class="mb-3 space-y-2">
                    <label class="flex items-center">
                      <input type="radio" name="references_method" value="individual" checked class="mr-2">
                      <span class="text-sm text-gray-700 dark:text-gray-300">Individual files (up to 20 files)</span>
                    </label>
                    <label class="flex items-center">
                      <input type="radio" name="references_method" value="zip" class="mr-2">
                      <span class="text-sm text-gray-700 dark:text-gray-300">ZIP file (for 20+ images)</span>
                    </label>
                  </div>

                  <!-- Individual files upload -->
                  <div id="references-individual" class="upload-method">
                    <input
                      type="file"
                      name="references[]"
                      accept="image/*"
                      multiple
                      class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    />
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                      <i class="fas fa-file-image text-green-500 mr-1"></i>
                      Max 5MB per file, 20 files total, 50MB combined limit
                    </span>
                  </div>

                  <!-- ZIP upload -->
                  <div id="references-zip" class="upload-method hidden">
                    <input
                      type="file"
                      name="references_zip"
                      accept=".zip"
                      class="block w-full mt-1 text-sm border dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                    />
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                      <i class="fas fa-file-archive text-green-500 mr-1"></i>
                      ZIP file containing references (max 100MB) - Images should follow naming convention: 1.jpg, 001-1.jpg, etc.
                    </span>
                  </div>
                </div>
              </div>


              <!-- File preview sections -->
              <div id="image-preview" class="mt-4 hidden">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <i class="fas fa-eye text-blue-500 mr-1"></i>
                  Selected Cosplayer Images: <span id="image-count">0</span> files
                </h4>
                <div id="image-list" class="text-xs text-gray-600 dark:text-gray-400 max-h-20 overflow-y-auto"></div>
              </div>

              <div id="reference-preview" class="mt-4 hidden">
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <i class="fas fa-eye text-green-500 mr-1"></i>
                  Selected References: <span id="reference-count">0</span> files
                </h4>
                <div id="reference-list" class="text-xs text-gray-600 dark:text-gray-400 max-h-20 overflow-y-auto"></div>
              </div>

              <button type="submit" class="table items-center mt-6 justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
              @isset($cosplayer)
              Update Cosplayer
              @else
              Import Cosplayers
              @endisset
              <span class="ml-2" aria-hidden="true">
                  <i class='las la-arrow-right'></i>
              </span>
            </button>
        </form>

        <script>
        // File preview and validation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.querySelector('input[name="images[]"]');
            const referenceInput = document.querySelector('input[name="references[]"]');
            const form = document.querySelector('form');
            const submitButton = document.querySelector('button[type="submit"]');

            // File size limits (in bytes)
            const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB per file
            const MAX_TOTAL_SIZE = 50 * 1024 * 1024; // 50MB total (server limit)
            const MAX_ZIP_SIZE = 100 * 1024 * 1024; // 100MB for ZIP files

            // Handle upload method switching
            document.querySelectorAll('input[name="images_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const individual = document.getElementById('images-individual');
                    const zip = document.getElementById('images-zip');

                    if (this.value === 'individual') {
                        individual.classList.remove('hidden');
                        zip.classList.add('hidden');
                        zip.querySelector('input').value = ''; // Clear ZIP input
                    } else {
                        individual.classList.add('hidden');
                        zip.classList.remove('hidden');
                        individual.querySelector('input').value = ''; // Clear individual input
                    }
                });
            });

            document.querySelectorAll('input[name="references_method"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const individual = document.getElementById('references-individual');
                    const zip = document.getElementById('references-zip');

                    if (this.value === 'individual') {
                        individual.classList.remove('hidden');
                        zip.classList.add('hidden');
                        zip.querySelector('input').value = ''; // Clear ZIP input
                    } else {
                        individual.classList.add('hidden');
                        zip.classList.remove('hidden');
                        individual.querySelector('input').value = ''; // Clear individual input
                    }
                });
            });

            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    if (validateFiles(this.files)) {
                        showFilePreview(this.files, 'image');
                    }
                });
            }

            if (referenceInput) {
                referenceInput.addEventListener('change', function() {
                    if (validateFiles(this.files)) {
                        showFilePreview(this.files, 'reference');
                    }
                });
            }

            // Form submission validation
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateTotalSize()) {
                        e.preventDefault();
                    } else {
                        // Show loading state
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                    }
                });
            }

            function validateFiles(files) {
                for (let file of files) {
                    if (file.size > MAX_FILE_SIZE) {
                        alert(`File "${file.name}" is too large (${(file.size/1024/1024).toFixed(1)}MB). Maximum size per file is 5MB.`);
                        return false;
                    }
                }
                return true;
            }

            function validateTotalSize() {
                let totalSize = 0;

                // Add sheet file size
                const sheetInput = document.querySelector('input[name="sheet"]');
                if (sheetInput && sheetInput.files[0]) {
                    totalSize += sheetInput.files[0].size;
                }

                // Check if using ZIP uploads
                const imagesZipInput = document.querySelector('input[name="images_zip"]');
                const referencesZipInput = document.querySelector('input[name="references_zip"]');

                // Validate ZIP files separately (they have higher limits)
                if (imagesZipInput && imagesZipInput.files[0]) {
                    if (imagesZipInput.files[0].size > MAX_ZIP_SIZE) {
                        const fileMB = (imagesZipInput.files[0].size/1024/1024).toFixed(1);
                        alert(`Images ZIP file (${fileMB}MB) exceeds 100MB limit. Please compress or split the archive.`);
                        return false;
                    }
                    totalSize += imagesZipInput.files[0].size;
                }

                if (referencesZipInput && referencesZipInput.files[0]) {
                    if (referencesZipInput.files[0].size > MAX_ZIP_SIZE) {
                        const fileMB = (referencesZipInput.files[0].size/1024/1024).toFixed(1);
                        alert(`References ZIP file (${fileMB}MB) exceeds 100MB limit. Please compress or split the archive.`);
                        return false;
                    }
                    totalSize += referencesZipInput.files[0].size;
                }

                // Add individual image files size (only if not using ZIP)
                if (imageInput && imageInput.files && !document.querySelector('input[name="images_method"]:checked[value="zip"]')) {
                    for (let file of imageInput.files) {
                        totalSize += file.size;
                    }
                }

                // Add individual reference files size (only if not using ZIP)
                if (referenceInput && referenceInput.files && !document.querySelector('input[name="references_method"]:checked[value="zip"]')) {
                    for (let file of referenceInput.files) {
                        totalSize += file.size;
                    }
                }

                // Different limits for ZIP vs individual files
                const isUsingZip = (imagesZipInput && imagesZipInput.files[0]) || (referencesZipInput && referencesZipInput.files[0]);
                const effectiveLimit = isUsingZip ? 200 * 1024 * 1024 : MAX_TOTAL_SIZE; // 200MB if using ZIP, 50MB otherwise

                if (totalSize > effectiveLimit) {
                    const totalMB = (totalSize/1024/1024).toFixed(1);
                    const limitMB = (effectiveLimit/1024/1024).toFixed(0);
                    alert(`Total upload size (${totalMB}MB) exceeds server limit (${limitMB}MB). Please reduce the file sizes.`);
                    return false;
                }

                return true;
            }

            function showFilePreview(files, type) {
                const preview = document.getElementById(`${type}-preview`);
                const count = document.getElementById(`${type}-count`);
                const list = document.getElementById(`${type}-list`);

                if (files.length > 0) {
                    preview.classList.remove('hidden');
                    count.textContent = files.length;

                    let fileNames = [];
                    let totalSize = 0;

                    for (let file of files) {
                        const fileName = file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(1); // MB
                        const baseName = fileName.split('.')[0];
                        totalSize += file.size;

                        // Extract cosplayer number (handle formats like 001, 1, 001-1, 1-2, etc.)
                        const numberMatch = baseName.match(/^(\d+)/);
                        if (numberMatch) {
                            const number = numberMatch[1];
                            const suffix = baseName.includes('-') ? ` (${baseName.split('-').slice(1).join('-')})` : '';
                            const sizeWarning = file.size > MAX_FILE_SIZE ? ' ⚠️' : '';
                            fileNames.push(`${fileName} → Cosplayer #${number}${suffix} <span class="text-gray-500">(${fileSize}MB)${sizeWarning}</span>`);
                        } else {
                            fileNames.push(`${fileName} → ⚠️ Invalid format <span class="text-gray-500">(${fileSize}MB)</span>`);
                        }
                    }

                    // Add total size info
                    const totalMB = (totalSize / 1024 / 1024).toFixed(1);
                    const sizeClass = totalSize > MAX_FILE_SIZE ? 'text-red-600' : 'text-green-600';
                    fileNames.push(`<div class="mt-2 pt-2 border-t border-gray-300"><strong class="${sizeClass}">Total: ${totalMB}MB</strong></div>`);

                    list.innerHTML = fileNames.join('<br>');
                } else {
                    preview.classList.add('hidden');
                }
            }
        });
        </script>

          </div>
        </main>
@endsection
