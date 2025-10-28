# Egycon Voting System

A Laravel 8.x cosplay competition voting system that manages events, cosplayers, judges, and both public and administrative voting mechanisms with Telegram integration.

## 🚀 Features

### Core Functionality
- **Event Management**: Create and manage cosplay competition events
- **Cosplayer Management**: Register participants with images, references, and custom data
- **Dual Voting System**: Weighted judge scoring + public polling
- **Image Processing**: Automatic resize, optimization, and flexible naming support
- **Bulk Import**: Excel/CSV import with ZIP image upload support
- **Telegram Integration**: Real-time notifications and bot linking

### 🆕 NEW: Dynamic Custom Columns
- **Flexible Data Import**: Add any custom columns to your Excel/CSV files
- **Automatic Processing**: All additional columns are automatically detected and stored
- **Easy Frontend Rendering**: Custom data displayed in cosplayer details and voting pages
- **Export Support**: Custom fields included in data exports
- **Examples**: `gender`, `age`, `location`, `notes`, `social_media`, `experience_level`, etc.

## 🎯 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL/MariaDB
- Composer
- Node.js & NPM

### Installation
1. Clone the repository
2. Install dependencies: `composer install && npm install`
3. Configure environment: `cp .env.example .env`
4. Generate key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Build assets: `npm run dev`

## 📊 Custom Columns Usage

### Excel/CSV Import Format
```csv
name,character,anime,number,stage_name,gender,age,location,notes
John Doe,Naruto,Naruto Shippuden,001,Johnny Cosplay,Male,25,Tokyo Japan,First time participant
Jane Smith,Sakura,Naruto Shippuden,002,Moon Princess,Female,23,Osaka Japan,Professional cosplayer
```

### Supported Custom Fields
- **Personal Info**: `gender`, `age`, `birthday`, `location`, `country`
- **Social Media**: `instagram`, `twitter`, `facebook`, `tiktok`
- **Experience**: `years_cosplaying`, `competitions_won`, `skill_level`
- **Event Specific**: `notes`, `special_requirements`, `performance_time`
- **Any Custom Field**: The system accepts any column name you add!

### API Access
```php
// Get custom data
$cosplayer->getCustomData('gender'); // Returns: 'Female'
$cosplayer->getAllCustomData(); // Returns: ['gender' => 'Female', 'age' => 23, ...]

// Set custom data
$cosplayer->setCustomData('social_media', '@johndoe')->save();

// Check if exists
$cosplayer->hasCustomData('location'); // Returns: true/false
```

## 🖼️ Image Management

### Flexible Naming Convention
- **Single images**: `1.jpg`, `001.jpg`, `2.png`
- **Multiple images**: `1-1.jpg`, `1-2.jpg`, `001-front.png`, `2-back.jpg`
- **Automatic matching**: Images matched to cosplayers by number

### Upload Methods
- **Individual Files**: Up to 20 files, 5MB each
- **ZIP Archives**: Up to 100MB, 500+ images supported
- **Automatic Processing**: Resize to 600x600px, JPEG optimization

### Bulk Upload Workflow
1. **Prepare Excel/CSV**: Include required + custom columns
2. **Prepare Images**: Name files with cosplayer numbers (e.g., `001.jpg`, `002-1.jpg`)
3. **Upload**: Choose individual files or ZIP archives
4. **Process**: System automatically matches and processes everything

## 🔧 Advanced Features

### Telegram Integration
- **Bot Linking**: Users scan QR codes to link Telegram accounts
- **Notifications**: Real-time voting updates and announcements
- **Admin Controls**: Enable/disable notifications per user

### Voting System
- **Judge Voting**: Weighted scoring system (0-100 points)
- **Public Polling**: QR code-based public voting
- **Score Calculation**: Normalized to 100% scale with vote weighting
- **Real-time Updates**: Live score tracking and reporting

### Data Export
- **Excel Export**: Full cosplayer data including custom fields
- **Event-Specific**: Export data filtered by event
- **Custom Columns**: All custom data included automatically
- **Sample Files**: Download templates with examples

## 🛠️ Technical Architecture

### Database Schema
- **cosplayers**: Core participant data + `custom_data` JSON field
- **events**: Competition events with participant relationships
- **cosplayer_votes**: Judge scoring with user weighting
- **polls**: Public voting system with QR code generation

### Key Technologies
- **Backend**: Laravel 8.x, MySQL, Intervention Image
- **Frontend**: TailwindCSS, Blade templating, FontAwesome
- **Import/Export**: Maatwebsite Excel, CSV processing
- **Permissions**: Spatie Laravel Permission
- **Image Processing**: Intervention Image with automatic optimization

## 📝 Usage Examples

### Basic Bulk Import
```csv
name,character,anime,number,stage_name
John Doe,Naruto,Naruto,001,Johnny Cosplay
```

### Advanced Import with Custom Data
```csv
name,character,anime,number,stage_name,gender,age,instagram,experience_level,notes
John Doe,Naruto,Naruto,001,Johnny Cosplay,Male,25,@johndoe,Beginner,First competition
Jane Smith,Sakura,Naruto,002,Moon Princess,Female,23,@janesmith,Expert,Won 5 competitions
```

### Custom Data in Templates
```blade
@foreach($cosplayer->getAllCustomData() as $key => $value)
    <tr>
        <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
        <td>{{ $value }}</td>
    </tr>
@endforeach
```

## 📚 API Documentation

### Custom Data Methods
- `getCustomData($key, $default)`: Get specific custom field
- `setCustomData($key, $value)`: Set custom field value  
- `getAllCustomData()`: Get all custom fields as array
- `hasCustomData($key)`: Check if custom field exists
- `removeCustomData($key)`: Remove specific custom field

### File Upload Limits
- **Individual files**: 5MB per file, 20 files max
- **ZIP files**: 100MB max, 500+ images supported
- **Total upload**: 200MB combined limit for ZIP uploads
- **Server limits**: Check `/admin/cosplayers/upload-limits` for current PHP settings

## 🔐 Security Features
- **Role-based Access**: Admin-only management with permission middleware
- **File Validation**: Strict image type and size validation
- **ZIP Processing**: Safe extraction with cleanup and error handling
- **SQL Injection Protection**: Laravel's built-in ORM protection

## 🐛 Troubleshooting

### Upload Issues
- **File too large**: Increase `upload_max_filesize` and `post_max_size` in php.ini
- **ZIP processing fails**: Check server permissions and disk space
- **Images not matching**: Verify filename format (numbers only: `001.jpg`, `1-2.png`)

### Custom Column Issues
- **Data not importing**: Check column names don't conflict with reserved names
- **Missing data**: Verify Excel/CSV encoding and format
- **Display issues**: Check blade template syntax for custom data loops

## 🤝 Contributing
1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open pull request

## 📄 License
This project is licensed under the MIT License.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
