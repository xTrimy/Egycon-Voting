<?php

namespace App\Http\Controllers;

use App\Exports\CosplayersExport;
use App\Exports\CosplayersWithEvent;
use App\Exports\CosplayersSampleExport;
use App\Http\Controllers\API\CosplayerController as APICosplayerController;
use App\Http\Controllers\API\EventController;
use App\Imports\CosplayersImport;
use App\Models\Cosplayer;
use App\Models\CosplayerVote;
use App\Models\Event;
use App\Models\Poll;
use App\Models\PollData;
use App\Models\PollDataLine;
use App\Models\PollLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class CosplayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all cosplayers
        $cosplayers = (new APICosplayerController)->index();
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();

        return view('cosplayers.index', compact('cosplayers', 'judge_votes'));
    }

    public function index_with_event_id($event_id)
    {
        // get all cosplayers for event
        $event = Event::findOrFail($event_id);
        $cosplayers = Cosplayer::where('event_id', $event_id)->get();
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();

        return view('cosplayers.index', compact('cosplayers','judge_votes', 'event_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $events = (new EventController)->index();
        return view('cosplayers.create', compact('events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store cosplayer
        $cosplayer = (new APICosplayerController)->store($request);
        return redirect()->route('cosplayers.index');
    }

    public function bulk_add()
    {
        $events = (new EventController)->index();
        return view('cosplayers.bulk-add', compact('events'));
    }

    public function bulk_add_store(Request $request)
    {
        // Log request details for debugging
        \Illuminate\Support\Facades\Log::info('Bulk add store request received', [
            'has_sheet' => $request->hasFile('sheet'),
            'has_images' => $request->hasFile('images'),
            'has_references' => $request->hasFile('references'),
            'has_images_zip' => $request->hasFile('images_zip'),
            'has_references_zip' => $request->hasFile('references_zip'),
            'event_id' => $request->get('event_id'),
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 0,
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize')
        ]);

        // Check for PHP upload errors first
        if (!empty($_POST) && empty($_FILES) && empty($_GET) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $displayMaxSize = ini_get('post_max_size');
            $uploadMaxSize = ini_get('upload_max_filesize');
            \Illuminate\Support\Facades\Log::error('Upload size exceeded server limits', [
                'content_length' => $_SERVER['CONTENT_LENGTH'],
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize')
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['upload' => "Upload failed: Total file size exceeds server limit of {$displayMaxSize}. Please reduce file sizes or upload in smaller batches."]);
        }

        // Check if files are missing due to upload_max_filesize limit
        $uploadMaxBytes = $this->parseSize(ini_get('upload_max_filesize'));
        $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;

        if ($contentLength > 0 && empty($_FILES)) {
            $uploadMaxSize = ini_get('upload_max_filesize');
            \Illuminate\Support\Facades\Log::error('Files dropped due to upload_max_filesize limit', [
                'content_length' => $contentLength,
                'upload_max_filesize' => $uploadMaxSize
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['upload' => "Upload failed: Individual file size limit ({$uploadMaxSize}) exceeded. Your file appears to be " . round($contentLength/1024/1024, 1) . "MB. Please increase upload_max_filesize in PHP settings or use smaller files."]);
        }

        try {
            $rules = [
                'event_id' => 'required|exists:events,id',
                'sheet' => 'nullable|file|mimes:csv,xlsx,xls|max:2048', // 2MB for Excel files

                // Individual image uploads (limited)
                'images' => 'nullable|array|max:20',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB per file
                'references' => 'nullable|array|max:20',
                'references.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB per file

                // ZIP file uploads (for bulk images)
                'images_zip' => 'nullable|file|max:102400', // 100MB for ZIP files
                'references_zip' => 'nullable|file|max:102400', // 100MB for ZIP files
            ];

            // If only uploading ZIP files without sheet, make sure at least something is being uploaded
            if (!$request->hasFile('sheet') && ($request->hasFile('images_zip') || $request->hasFile('references_zip'))) {
                // Allow uploads with just ZIP files
            } elseif (!$request->hasFile('sheet') && !$request->hasFile('images_zip') && !$request->hasFile('references_zip') && !$request->hasFile('images') && !$request->hasFile('references')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sheet' => 'Either a cosplayers sheet or image files must be uploaded.']);
            }

            $request->validate($rules);

            // Custom validation for ZIP files
            if ($request->hasFile('images_zip')) {
                $file = $request->file('images_zip');
                if (!$this->isValidZipFile($file)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['images_zip' => 'The images zip file must be a valid ZIP archive.']);
                }
            }

            if ($request->hasFile('references_zip')) {
                $file = $request->file('references_zip');
                if (!$this->isValidZipFile($file)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['references_zip' => 'The references zip file must be a valid ZIP archive.']);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            \Illuminate\Support\Facades\Log::error('Validation failed in bulk_add_store', [
                'errors' => $e->errors(),
                'files' => [
                    'sheet' => $request->hasFile('sheet') ? $request->file('sheet')->getClientOriginalName() : null,
                    'images_zip' => $request->hasFile('images_zip') ? [
                        'name' => $request->file('images_zip')->getClientOriginalName(),
                        'size' => $request->file('images_zip')->getSize(),
                        'mime' => $request->file('images_zip')->getMimeType(),
                        'extension' => $request->file('images_zip')->getClientOriginalExtension()
                    ] : null,
                    'references_zip' => $request->hasFile('references_zip') ? [
                        'name' => $request->file('references_zip')->getClientOriginalName(),
                        'size' => $request->file('references_zip')->getSize(),
                        'mime' => $request->file('references_zip')->getMimeType(),
                        'extension' => $request->file('references_zip')->getClientOriginalExtension()
                    ] : null,
                ]
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        }

        $file = $request->file('sheet');
        $event_id = $request->get('event_id');

        // Import cosplayers from Excel/CSV if file provided
        if ($file) {
            (new CosplayersImport($event_id))->import($file);
        }

        $processedCounts = [
            'images' => 0,
            'references' => 0
        ];

        // Process images if provided (ZIP or individual files)
        try {
            if ($request->hasFile('images_zip')) {
                $processedCounts['images'] = $this->processZipFile($request->file('images_zip'), $event_id, 'images');
            } elseif ($request->hasFile('images')) {
                $processedCounts['images'] = $this->processImagesForEvent($request->file('images'), $event_id, 'images');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['images_zip' => $e->getMessage()]);
        }

        // Process references if provided (ZIP or individual files)
        try {
            if ($request->hasFile('references_zip')) {
                $processedCounts['references'] = $this->processZipFile($request->file('references_zip'), $event_id, 'references');
            } elseif ($request->hasFile('references')) {
                $processedCounts['references'] = $this->processImagesForEvent($request->file('references'), $event_id, 'references');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['references_zip' => $e->getMessage()]);
        }

        $message = 'Cosplayers imported successfully';
        if ($processedCounts['images'] > 0 || $processedCounts['references'] > 0) {
            $message .= sprintf(' with %d images and %d references', $processedCounts['images'], $processedCounts['references']);
        }

        return redirect()->route('cosplayers.index')->with('success', $message);
    }

    private function processImagesForEvent($files, $event_id, $type = 'images')
    {
        $processedCount = 0;

        foreach ($files as $file){
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cosplayer_number = $this->extractCosplayerNumber($filename);

            if ($cosplayer_number) {
                // Normalize the cosplayer number for database lookup (remove leading zeros)
                $normalized_number = ltrim($cosplayer_number, '0') ?: '0';

                // Try to find cosplayer by normalized number or original format
                $cosplayer = Cosplayer::where('event_id', $event_id)
                    ->where(function($query) use ($cosplayer_number, $normalized_number) {
                        $query->where('number', $cosplayer_number)
                              ->orWhere('number', $normalized_number)
                              ->orWhere('number', str_pad($normalized_number, 3, '0', STR_PAD_LEFT));
                    })->first();

                if($cosplayer){
                    $image = Image::read($file)->scaleDown(600, 600);

                    // Generate unique filename to handle multiple images
                    $timestamp = time();
                    $random = substr(md5($file->getClientOriginalName()), 0, 6);
                    $unique_filename = "{$cosplayer->number}_{$timestamp}_{$random}.jpg";
                    $path = "{$type}/{$event_id}/{$unique_filename}";

                    Storage::disk('public')->put($path, $image->toJpeg(80));

                    if ($type === 'images') {
                        $cosplayer->images()->create(['image' => $path]);
                    } else {
                        $cosplayer->references()->create(['image' => $path]);
                    }

                    $processedCount++;
                }
            }
        }

        return $processedCount;
    }

    private function processZipFile($zipFile, $event_id, $type = 'images')
    {
        $processedCount = 0;

        // Create temporary directory for extraction
        $tempPath = storage_path('app/temp/zip_extract_' . uniqid());
        if (!file_exists($tempPath)) {
            if (!mkdir($tempPath, 0777, true)) {
                throw new \Exception("Failed to create temporary directory for ZIP extraction");
            }
        }

        try {
            $zip = new \ZipArchive();
            $result = $zip->open($zipFile->getRealPath());

            if ($result !== TRUE) {
                $errorMessages = [
                    \ZipArchive::ER_OK => 'No error',
                    \ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
                    \ZipArchive::ER_RENAME => 'Renaming temporary file failed',
                    \ZipArchive::ER_CLOSE => 'Closing zip archive failed',
                    \ZipArchive::ER_SEEK => 'Seek error',
                    \ZipArchive::ER_READ => 'Read error',
                    \ZipArchive::ER_WRITE => 'Write error',
                    \ZipArchive::ER_CRC => 'CRC error',
                    \ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
                    \ZipArchive::ER_NOENT => 'No such file',
                    \ZipArchive::ER_EXISTS => 'File already exists',
                    \ZipArchive::ER_OPEN => 'Can\'t open file',
                    \ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
                    \ZipArchive::ER_ZLIB => 'Zlib error',
                    \ZipArchive::ER_MEMORY => 'Memory allocation failure',
                    \ZipArchive::ER_CHANGED => 'Entry has been changed',
                    \ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
                    \ZipArchive::ER_EOF => 'Premature EOF',
                    \ZipArchive::ER_INVAL => 'Invalid argument',
                    \ZipArchive::ER_NOZIP => 'Not a zip archive',
                    \ZipArchive::ER_INTERNAL => 'Internal error',
                    \ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                    \ZipArchive::ER_REMOVE => 'Can\'t remove file',
                    \ZipArchive::ER_DELETED => 'Entry has been deleted',
                ];
                $errorMsg = $errorMessages[$result] ?? "Unknown ZIP error code: {$result}";
                throw new \Exception("Failed to open ZIP file: {$errorMsg}");
            }

            // Check if ZIP contains any files
            if ($zip->numFiles === 0) {
                $zip->close();
                throw new \Exception("ZIP file is empty - no files found to process");
            }

            // Extract ZIP contents to temporary directory
            if (!$zip->extractTo($tempPath)) {
                $zip->close();
                throw new \Exception("Failed to extract ZIP file contents");
            }
            $zip->close();

            // Process all image files in the extracted directory
            $processedCount = $this->processDirectoryImages($tempPath, $event_id, $type);

            if ($processedCount === 0) {
                throw new \Exception("No valid image files found in ZIP archive. Supported formats: JPG, JPEG, PNG, GIF, WEBP");
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ZIP processing error: ' . $e->getMessage());
            // Clean up temporary directory before re-throwing
            $this->deleteDirectory($tempPath);
            throw new \Exception("ZIP upload failed: " . $e->getMessage());
        } finally {
            // Clean up temporary directory
            $this->deleteDirectory($tempPath);
        }

        return $processedCount;
    }

    private function processDirectoryImages($directory, $event_id, $type = 'images')
    {
        $processedCount = 0;
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());

                if (in_array($extension, $allowedExtensions)) {
                    $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $cosplayer_number = $this->extractCosplayerNumber($filename);

                    if ($cosplayer_number) {
                        // Normalize the cosplayer number for database lookup
                        $normalized_number = ltrim($cosplayer_number, '0') ?: '0';

                        // Try to find cosplayer by normalized number or original format
                        $cosplayer = Cosplayer::where('event_id', $event_id)
                            ->where(function($query) use ($cosplayer_number, $normalized_number) {
                                $query->where('number', $cosplayer_number)
                                      ->orWhere('number', $normalized_number)
                                      ->orWhere('number', str_pad($normalized_number, 3, '0', STR_PAD_LEFT));
                            })->first();

                        if ($cosplayer) {
                            try {
                                $image = Image::read($file->getRealPath())->scaleDown(600, 600);

                                // Generate unique filename to handle multiple images
                                $timestamp = time();
                                $random = substr(md5($file->getFilename()), 0, 6);
                                $unique_filename = "{$cosplayer->number}_{$timestamp}_{$random}.jpg";
                                $path = "{$type}/{$event_id}/{$unique_filename}";

                                Storage::disk('public')->put($path, $image->toJpeg(80));

                                if ($type === 'images') {
                                    $cosplayer->images()->create(['image' => $path]);
                                } else {
                                    $cosplayer->references()->create(['image' => $path]);
                                }

                                $processedCount++;

                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::warning("Failed to process image {$file->getFilename()}: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        return $processedCount;
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    private function extractCosplayerNumber($filename)
    {
        // Handle formats like: 001, 1, 001-1, 1-2, 001-a, etc.
        // Extract the base number before any dash or suffix
        if (preg_match('/^(\d+)/', $filename, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        return round($size);
    }

    private function isValidZipFile($file)
    {
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'zip') {
            \Illuminate\Support\Facades\Log::error('ZIP validation failed: Invalid extension', [
                'extension' => $extension,
                'filename' => $file->getClientOriginalName()
            ]);
            return false;
        }

        // Try to open with ZipArchive to verify it's a valid ZIP
        try {
            $zip = new \ZipArchive();
            $result = $zip->open($file->getRealPath());

            if ($result !== TRUE) {
                \Illuminate\Support\Facades\Log::error('ZIP validation failed: Cannot open as ZIP', [
                    'result' => $result,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType()
                ]);
                $zip->close();
                return false;
            }

            $zip->close();
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ZIP validation failed: Exception', [
                'exception' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);
            return false;
        }
    }

    public function bulk_upload_references()
    {
        $events = (new EventController)->index();
        return view('cosplayers.references.bulk-upload', compact('events'));
    }

    public function bulk_upload_references_store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $files = $request->file('images');
        $event_id = $request->get('event_id');

        // Use the same improved logic as the bulk add feature
        $this->processImagesForEvent($files, $event_id, 'references');

        return redirect()->route('cosplayers.index')->with('success', 'References uploaded successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get cosplayer
        $cosplayer = (new APICosplayerController)->show($id);
        $judge_vote = CosplayerVote::where('cosplayer_id', $id)->where('user_id', auth()->user()->id)->first();
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.show', ['cosplayer'=>$cosplayer, 'judge_vote'=>$judge_vote]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $events = (new EventController)->index();
        $cosplayer = (new APICosplayerController)->show($id);
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.create', compact('cosplayer', 'events'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // update cosplayer
        $cosplayer = (new APICosplayerController)->update($request, $id);
        return redirect()->route('cosplayers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete cosplayer
        $cosplayer = (new APICosplayerController)->destroy($id);
        return redirect()->route('cosplayers.index');
    }

    /**
     * Show the form for adding images to the cosplayer.
     */
    public function addImagesView($id)
    {
        // show form for adding images to cosplayer
        $cosplayer = (new APICosplayerController)->show($id);
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.images.create', compact('cosplayer'));
    }

    /**
     * Add new images to the cosplayer.
     *
     * @return \Illuminate\Http\Response
     */
    public function addImages(Request $request, $id, $collection = 'images')
    {
        // add images to cosplayer
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        foreach($request->file('images') as $image)
        {
            // don't use the API cosplayer controller here
            $cosplayer = Cosplayer::find($id);
            // resize image if more than 1920px wide

            $test = $cosplayer->addMedia($image)->withResponsiveImages()->toMediaCollection($collection);
        }
        return redirect()->route('cosplayers.index');
    }

    /**
     * Remove the specified image from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeImage($id, $imageId, $collection = 'images')
    {
        // remove image from cosplayer
        $cosplayer = Cosplayer::find($id);
        $cosplayer->getMedia($collection)->find($imageId)->delete();
        return redirect()->route('cosplayers.index');
    }
    /**
     * Show the form for adding references to the cosplayer.
     */

    public function addReferencesView($id)
    {
        // show form for adding references to cosplayer
        $cosplayer = (new APICosplayerController)->show($id);
        if(!$cosplayer->resource)
            abort(404);
        return view('cosplayers.references.create', compact('cosplayer'));
    }

    /**
     * Add new references to the cosplayer.
     *
     * @return \Illuminate\Http\Response
     */
    public function addReferences(Request $request, $id, $collection = 'references'){
        return $this->addImages($request, $id, $collection);
    }

    /**
     * Remove the specified reference from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeReference($id, $imageId, $collection = 'references')
    {
        return $this->removeImage($id, $imageId, $collection);
    }

    public function search_cosplayer_by_number(Request $request)
    {
        $q = $request->get('q');
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $cosplayers = $user->events()->with('cosplayers')->get()->pluck('cosplayers')->flatten()->where('number', $q);
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();
        return view('cosplayers.index', compact('cosplayers', 'judge_votes'));
    }

    public function search_cosplayer_by_number_with_event_id(Request $request, $event_id)
    {
        $q = $request->get('q');
        $cosplayers = Cosplayer::where('event_id', $event_id)->where('number', $q)->get();
        $judge_votes = CosplayerVote::where('user_id', auth()->user()->id)->pluck('cosplayer_id')->toArray();
        return view('cosplayers.index', compact('cosplayers', 'judge_votes', 'event_id'));
    }



    private function getTopCosplayersByJudgeScore($event_id){
        $cosplayers = Cosplayer::where('event_id', $event_id)->get();
        $top_cosplayers = [];
        $max_cosplayers = 10;
        foreach($cosplayers as $cosplayer){
            $cosplayer->score = $cosplayer->calculateJudgeScore();
            if($cosplayer->score > 0){
                $top_cosplayers[] = $cosplayer;
            }
        }
        usort($top_cosplayers, function($a, $b) {
            return $b->score <=> $a->score;
        });
        $top_cosplayers = array_slice($top_cosplayers, 0, $max_cosplayers);
        return $top_cosplayers;
    }

    public function top_cosplayers($event_id){
        $top_cosplayers = $this->getTopCosplayersByJudgeScore($event_id);
        return $top_cosplayers;
    }

    public function create_poll_from_top_cosplayers($event_id){
        $top_cosplayers = $this->getTopCosplayersByJudgeScore($event_id);
        $poll = new Poll();
        $poll->name = 'Top 10 Cosplayers';
        $poll->save();
        // create poll lines
        $poll_lines_types = [
            'text',
            'file',
            'text',
            'text',
            'text',
        ];
        foreach($poll_lines_types as $type){
            $poll_line = new PollLine();
            $poll_line->poll_id = $poll->id;
            $poll_line->type = $type;
            $poll_line->save();
        }

        $cosplayers_data = [
            'number',
            '',
            'name',
            'anime',
            'character',
        ];
        // add cosplayers as poll_data to poll
        foreach($top_cosplayers as $cosplayer){
            $poll_data = new PollData();
            $poll_lines = $poll->poll_lines;
            $poll_data->poll_id = $poll->id;
            $poll_data->save();
            foreach($poll_lines as $key => $line){
                $poll_data_line = new PollDataLine();
                $poll_data_line->poll_data_id = $poll_data->id;
                $poll_data_line->poll_line_id = $line->id;
                if($line->type == 'text'){
                    $poll_data_line->value = $cosplayer->{$cosplayers_data[$key]};
                }
                if($line->type == 'file'){
                    $poll_data_line->value = "generated/cosplayers/{$cosplayer->number}.jpg";
                }
                $poll_data_line->save();
            }
        }
        return redirect()->route('polls.index');
    }

    public function export_cosplayers(){
        return Excel::download(new CosplayersExport, 'cosplayers.xlsx');
    }

    public function export_cosplayers_with_event($evnet_id)
    {
        return Excel::download(new CosplayersWithEvent($evnet_id), 'cosplayers.xlsx');

    }

    public function download_sample()
    {
        return Excel::download(new \App\Exports\CosplayersSampleExport, 'cosplayers-sample.xlsx');
    }

    public function show_upload_limits()
    {
        $limits = [
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ];

        $postMaxBytes = $this->parseSize($limits['post_max_size']);
        $uploadMaxBytes = $this->parseSize($limits['upload_max_filesize']);

        return response()->json([
            'php_limits' => $limits,
            'php_limits_bytes' => [
                'post_max_size' => $postMaxBytes,
                'upload_max_filesize' => $uploadMaxBytes,
            ],
            'recommended' => [
                'post_max_size' => '200M or higher for ZIP uploads',
                'upload_max_filesize' => '100M for ZIP files',
                'max_file_uploads' => '50 for batch processing',
                'note' => 'CRITICAL: upload_max_filesize=' . $limits['upload_max_filesize'] . ' is too small for ZIP files. This must be increased to at least 100M.'
            ],
            'current_issue' => $uploadMaxBytes < (100 * 1024 * 1024) ?
                'Your upload_max_filesize (' . $limits['upload_max_filesize'] . ') is too small for ZIP uploads. Files larger than this limit are silently dropped by PHP.' :
                'Upload limits look sufficient for ZIP files.'
        ]);
    }
}
