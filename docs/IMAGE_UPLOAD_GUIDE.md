# Image Upload Feature - Admin Product Management

## Overview
The admin product management page now supports direct image uploads instead of requiring manual file placement and filename entry.

## How It Works

### For Admins

1. **Navigate to Admin Panel**
   - Go to `admin/manage_products.php`
   - Click on "Manage Products" in the admin navigation

2. **Add New Product**
   - Fill in the product details (name, price, category, restaurant)
   - Click the "Choose File" button in the Product Image field
   - Select an image from your computer
   - Click "Add Product" to submit

3. **Supported Image Formats**
   - JPG / JPEG
   - PNG
   - GIF
   - WEBP
   - Maximum file size: 5MB (configurable in PHP settings)

4. **What Happens**
   - The image is uploaded to `assets/images/food pics/`
   - A unique filename is generated (timestamp + original name)
   - The filename is automatically saved to the database
   - The product appears immediately in the product list

### Technical Details

#### Upload Directory
```
assets/images/food pics/
```

#### Filename Format
```
{timestamp}_{sanitized_original_name}.{extension}
```
Example: `1709123456_Chicken_Burger.jpg`

#### Security Features
1. **File Type Validation** - Only allowed image formats are accepted
2. **Filename Sanitization** - Special characters are replaced with underscores
3. **Unique Naming** - Timestamp prefix prevents filename conflicts
4. **Extension Validation** - File extension is verified before upload

#### Error Handling
The system provides clear error messages for:
- Invalid file types
- Upload failures
- Missing files
- Database errors

#### Success/Error Messages
- **Success**: Green message - "Product added successfully!"
- **Error**: Red message - Specific error description

## Code Changes

### PHP Upload Handler
```php
// Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../assets/images/food pics/';
    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            // Success - save to database
        }
    }
}
```

### HTML Form
```html
<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Product Image</label>
        <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
        <small>Accepted formats: JPG, JPEG, PNG, GIF, WEBP (Max 5MB)</small>
    </div>
</form>
```

## Troubleshooting

### Upload Fails
1. Check directory permissions: `assets/images/food pics/` must be writable
2. Verify PHP upload settings in `php.ini`:
   ```ini
   upload_max_filesize = 5M
   post_max_size = 6M
   ```

### Image Not Displaying
1. Verify the image was uploaded to the correct directory
2. Check file permissions (should be readable)
3. Verify the filename in the database matches the actual file

### File Too Large
- Increase `upload_max_filesize` in `php.ini`
- Increase `post_max_size` in `php.ini`
- Restart web server after changes

## Future Enhancements

Potential improvements:
1. Image preview before upload
2. Image resizing/optimization on upload
3. Multiple image support per product
4. Drag-and-drop upload interface
5. Image cropping tool
6. Bulk image upload
7. Image gallery management

## File Permissions

Ensure the upload directory has proper permissions:
```bash
chmod 755 assets/images/food\ pics/
```

Or on Windows, ensure the web server user has write permissions to the directory.
