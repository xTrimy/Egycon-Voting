# Cosplayers Bulk Import Guide

## Complete Bulk Import Workflow

The bulk import feature allows you to import cosplayers along with their images and references in one go!

## File Format Requirements

### Excel/CSV Data File
- **Supported formats**: Excel (`.xlsx`, `.xls`) or CSV (`.csv`)
- **Required columns** (case-sensitive):

| Column Name | Description | Example |
|-------------|-------------|---------|
| `name` | Cosplayer's real name | John Doe |
| `character` | Character being cosplayed | Naruto Uzumaki |
| `anime` | Anime/Series name | Naruto |
| `number` | Unique participant number | 001 |
| `stage_name` | Cosplayer's stage/display name | Johnny Cosplay |

### Image Files (Optional)

#### Cosplayer Images
- **Purpose**: Photos of the actual cosplayers
- **Formats**: JPEG, PNG, JPG, GIF, WebP
- **Upload methods**: 
  - Individual files: 5MB per file, 20 files max, 50MB total
  - ZIP archive: Up to 500+ images, 100MB max
- **Naming convention**: `{number}[suffix].{extension}`

#### Character References  
- **Purpose**: Reference images of the characters being cosplayed
- **Formats**: JPEG, PNG, JPG, GIF, WebP
- **Upload methods**: 
  - Individual files: 5MB per file, 20 files max, 50MB total
  - ZIP archive: Up to 500+ images, 100MB max
- **Naming convention**: `{number}[suffix].{extension}`

#### Flexible Naming Examples
- **Single images**: `1.jpg`, `001.jpg`, `2.png`, `010.gif`
- **Multiple images**: `1-1.jpg`, `1-2.jpg`, `001-a.png`, `001-front.jpg`, `002-back.gif`
- **Mixed formats**: `1.jpg` + `001-2.png` (both link to cosplayer #1)

## Step-by-Step Import Process

### 1. Prepare Your Files

#### Option A: Individual Files (up to 20 files)
```
📁 Your Import Folder
├── cosplayers-data.xlsx          (Required: cosplayer data)
├── 📁 images/                    (Optional: cosplayer photos)
│   ├── 1.jpg                     (Single image for cosplayer #1)
│   ├── 002-1.jpg                 (Multiple images for cosplayer #2)
│   ├── 002-2.png
│   ├── 3-front.jpg               (Descriptive suffixes allowed)
│   └── 003-back.gif
└── 📁 references/                (Optional: character references)
    ├── 1-main.jpg                (Main character reference)
    ├── 001-alt.jpg               (Alternative pose/outfit)
    ├── 2.png                     (Single reference)
    └── 003-detail.jpg            (Detail shots)
```

#### Option B: ZIP Archives (500+ images)
```
📁 Your Import Folder  
├── cosplayers-data.xlsx          (Required: cosplayer data)
├── cosplayer-images.zip          (Optional: ZIP containing 500+ photos)
│   └── (contains: 1.jpg, 001-1.jpg, 002-front.png, etc.)
└── character-references.zip      (Optional: ZIP containing reference images)
    └── (contains: 1-pose1.jpg, 002-ref.jpg, 3-detail.png, etc.)
```

### 2. Upload Process
1. **Upload Excel/CSV**: Select your cosplayer data file
2. **Select Event**: Choose which event these cosplayers belong to
3. **Upload Images** (Optional): Select multiple cosplayer photos
4. **Upload References** (Optional): Select character reference images
5. **Submit**: All data and images will be processed together

### 3. Automatic Matching
- Images are automatically matched to cosplayers using the `number` field from filenames
- **Flexible matching**: `1.jpg`, `001.jpg`, `001-1.jpg` all match cosplayer #1
- **Multiple images**: `1-1.jpg`, `1-2.jpg`, `1-front.jpg` create multiple records for cosplayer #1
- **Smart normalization**: Leading zeros are handled automatically (001 = 1)
- **Unmatched images**: Files without valid numbers or no corresponding cosplayer are ignored

## Important Notes

- **Number field is key**: Ensure cosplayer numbers in Excel match image filenames
- **Unique numbers**: Each cosplayer number must be unique within the event
- **Optional images**: You can import just data, or data + images, or any combination
- **Error handling**: Missing images won't prevent cosplayer import
- **Format flexibility**: Mix different image formats (JPG, PNG) as needed
- **Size management**: For large image collections, import data first, then use separate bulk image upload

## Upload Limits & Solutions

### Server Limits
- **Per file**: 5MB maximum
- **Total upload**: 8MB combined (Excel + all images)
- **File count**: 20 images + 20 references maximum

### For Large Collections (500+ Images)

#### Recommended: ZIP File Method
1. **Create ZIP archives**: Organize images into ZIP files (max 100MB each)
2. **Proper naming**: Ensure images inside ZIP follow naming convention (1.jpg, 001-1.jpg, etc.)
3. **Upload with data**: Upload Excel + ZIP files in single operation
4. **Automatic processing**: System extracts and processes all images automatically

#### Alternative: Batch Upload Method
If ZIP files are too large:
1. **Import data only**: Upload Excel file without images
2. **Use bulk image upload**: Navigate to separate bulk upload pages for images/references
3. **Split archives**: Create multiple smaller ZIP files (under 100MB each)
4. **Optimize images**: Compress images before upload (system auto-resizes to 600px)

## Example Workflow

1. **Create Excel with cosplayers**: `1` (Naruto), `2` (Sasuke), `3` (Sakura)
2. **Name cosplayer photos**: 
   - `1-main.jpg`, `1-closeup.jpg` (2 photos of cosplayer #1)
   - `002.png` (1 photo of cosplayer #2)  
   - `3-front.jpg`, `3-back.gif` (2 photos of cosplayer #3)
3. **Name reference images**: 
   - `001-pose1.jpg`, `001-pose2.jpg` (2 references for character #1)
   - `2-ref.jpg` (1 reference for character #2)
4. **Upload all files** in the bulk add form
5. **System processes**:
   - Creates 3 cosplayers from Excel
   - Links 5 cosplayer images (2+1+2) 
   - Links 3 character references (2+1+0)

This streamlined process eliminates the need for manual image association after import!
