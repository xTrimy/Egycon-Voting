# Egycon Voting System - AI Agent Instructions

## Project Overview
This is a Laravel 8.x cosplay competition voting system that manages events, cosplayers, judges, and both public and administrative voting mechanisms. The system integrates Telegram notifications and uses weighted judge scoring.

## Core Domain Models & Relationships

### Primary Entities
- **Event**: Competition events containing cosplayers (`app/Models/Event.php`)
- **Cosplayer**: Participants with images, references, and voting data (`app/Models/Cosplayer.php`)
- **User**: Judges/admins with vote weights and Telegram integration (`app/Models/User.php`)
- **Poll**: Generic polling system separate from cosplayer voting (`app/Models/Poll.php`)

### Key Relationships
- Events have many cosplayers and many users (judges) through pivot table
- Cosplayers belong to events, have images/references, and receive votes
- Users can vote on cosplayers with weighted scores via `CosplayerVote` model
- Dual voting systems: judge voting (weighted) and public polls (generic)

## Authentication & Authorization

### Permission System
- Uses `spatie/laravel-permission` package
- Routes protected by `check_permissions:admin` middleware (`app/Http/Middleware/CheckPermission.php`)
- Helper function `getPermissionsTeamId()` used throughout for team-based permissions
- Vote weights stored in `users.vote_weight` column for judge score calculations

### Route Structure
```php
// Public voting
Route::get('/poll/{id}', [PollVoteController::class, 'index']);

// Admin panel (requires auth)
Route::prefix('/admin')->middleware('auth')->group(function(){
    // Most routes require admin role via check_permissions middleware
    Route::middleware('check_permissions:admin')->group(function () {
        // Admin-only routes for CRUD operations
    });
});
```

## Voting Systems Architecture

### Judge Voting (Weighted)
- Cosplayers receive votes from authenticated judges via `CosplayerVote` model
- Votes multiplied by judge's `vote_weight` in `calculateJudgeScore()` method
- Normalized to 100-point scale in `Cosplayer::calculateJudgeScore()`

### Public Polling
- Separate generic polling system using `Poll`, `PollData`, `PollVote` models
- QR code generation for public access via `QRHelper` class

## Telegram Integration

### Service Architecture
- `TelegramService` class handles all bot communications (`app/Services/Telegram/`)
- Users get unique telegram codes for bot linking via `User::getTelegramCode()`
- Chat IDs stored in `TelegramChat` model linked to users
- Notifications controlled by `ENABLE_TELEGRAM_NOTIFICATIONS` env variable

### Usage Patterns
```php
// Service instantiation
TelegramService::withUser($user)->sendMessage($text);
TelegramService::withChatID($chat_id)->sendMessage($text);

// User telegram setup
$user->getTelegramCodeQR(); // Returns base64 QR code for bot linking
```

## File Upload & Management

### Image Handling
- Uses `intervention/image` for processing cosplayer photos and references
- Images stored in `storage/app/public/` with public disk configuration
- Cosplayers have separate `CosplayerImage` and `CosplayerReference` models for different image types
- Bulk upload functionality in `CosplayerController::bulk_upload_references()`
- **Flexible naming convention**: Images matched using `{number}[suffix].{ext}` pattern (e.g., `1.jpg`, `001-1.jpg`, `2-front.png`)
- **Multiple images support**: Cosplayers can have multiple images/references via suffix system
- **Smart number matching**: Handles both padded (`001`) and unpadded (`1`) numbers automatically
- **Automatic processing**: Images resized to 600x600px and converted to JPEG (80% quality) for optimization
- **Unique storage**: Files stored with timestamp+hash to prevent conflicts: `{type}/{event_id}/{number}_{timestamp}_{hash}.jpg`

### Excel Import/Export
- `maatwebsite/excel` package for cosplayer data management
- Export classes: `CosplayersExport`, `CosplayersWithEvent`, `PollsDataExport`, `CosplayersSampleExport`
- Import class: `CosplayersImport` for bulk cosplayer creation with required columns: name, character, anime, number, stage_name
- Sample files available at `/public/samples/` with download route `cosplayers.download-sample`
- Bulk import uses `WithHeadingRow` and `WithSkipDuplicates` concerns for robust processing
- **Enhanced bulk import**: Supports simultaneous upload of Excel data + images + references
- **Dual upload methods**: Individual files (up to 20) or ZIP archives (500+ images, 100MB limit)
- **ZIP processing**: Automatic extraction and recursive processing of images in ZIP archives
- **Image matching**: Files named with cosplayer numbers (e.g., `001.jpg`) automatically link to corresponding cosplayers
- **Dual image types**: Separate handling for cosplayer photos (`CosplayerImage`) and character references (`CosplayerReference`)

## Development Workflow

### Build Commands
```bash
# Frontend assets
npm run dev          # Development build
npm run watch        # Watch mode
npm run production   # Production build

# Laravel commands
php artisan migrate  # Database migrations
php artisan db:seed  # Seed development data
php artisan serve    # Development server
```

### Database Structure
- Migration naming follows timestamp convention
- Key tables: events, cosplayers, users, cosplayer_votes, polls, telegram_chats
- Many-to-many relationships use pivot tables (events_users)

## Event-Centric Architecture

### Event Scoping
- Most operations are scoped by event ID
- Routes include event-specific variants: `/admin/{event_id}/cosplayers`
- User access controlled via `Event::user_has_access($user_id)` method
- Controllers have both global and event-scoped methods (e.g., `index()` vs `index_with_event_id()`)

## Frontend Technology Stack
- TailwindCSS for styling (configured via `tailwind.config.js`)
- Laravel Mix for asset compilation (`webpack.mix.js`)
- Blade templating with component-based architecture in `resources/views/`

## Key Conventions
- Controllers follow Laravel resource pattern but often split for nested resources
- Service classes use static factory methods for different instantiation modes
- Models include business logic methods (e.g., vote calculations, access checks)
- Custom helper classes in `app/Helpers/` for specialized functionality (QR codes)
- Exception handling via custom exceptions (`UserNotFoundException`)

## Configuration Notes
- Telegram bot settings in `.env`: `TELEGRAM_API_KEY`, `TELEGRAM_BOT_USERNAME`
- Image processing and file storage use Laravel's default storage configuration
- Permission system configured for team-based access control
