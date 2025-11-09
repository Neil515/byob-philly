<?php
/**
 * Restaurant photo management template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check user permission
$user_id = get_current_user_id();
if (!$user_id) {
    echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; text-align: center;">';
    echo '<h3>❌ Please Log In</h3>';
    echo '<p>You must log in to manage restaurant photos.</p>';
    echo '</div>';
    return;
}

// Verify user role
$user = get_user_by('id', $user_id);
if (!in_array('restaurant_owner', $user->roles)) {
    echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; text-align: center;">';
    echo '<h3>❌ Insufficient Permissions</h3>';
    echo '<p>Only restaurant owners can access this page.</p>';
    echo '</div>';
    return;
}

// Get restaurants owned by the user
$user_restaurants = byob_get_user_restaurants($user_id);
if (empty($user_restaurants)) {
    echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 20px; border-radius: 8px; text-align: center;">';
    echo '<h3>⚠️ Notice</h3>';
    echo '<p>No restaurant is currently linked to this account. Please contact the administrator.</p>';
    echo '</div>';
    return;
}

$restaurant_id = $user_restaurants[0]->ID;

// Handle photo upload
if ($_POST['action'] === 'upload_photos') {
    $result = byob_handle_photo_upload($restaurant_id, $_FILES['photos']);
    if (is_wp_error($result)) {
        echo '<div class="error">' . $result->get_error_message() . '</div>';
    } else {
        echo '<div class="success">Photo uploaded successfully!</div>';
    }
}

// Handle photo deletion
if ($_POST['action'] === 'delete_photo') {
    $photo_id = intval($_POST['photo_id']);
    $result = byob_delete_restaurant_photo($restaurant_id, $photo_id);
    if (is_wp_error($result)) {
        echo '<div class="error">' . $result->get_error_message() . '</div>';
    } else {
        echo '<div class="success">Photo deleted successfully!</div>';
    }
}

// Retrieve existing photos (three group fields)
$photo_1 = get_field('restaurant_photo_1', $restaurant_id);
$photo_2 = get_field('restaurant_photo_2', $restaurant_id);
$photo_3 = get_field('restaurant_photo_3', $restaurant_id);

$existing_photos = array();
if ($photo_1 && !empty($photo_1['photo'])) {
    $existing_photos[] = $photo_1;
}
if ($photo_2 && !empty($photo_2['photo'])) {
    $existing_photos[] = $photo_2;
}
if ($photo_3 && !empty($photo_3['photo'])) {
    $existing_photos[] = $photo_3;
}

$photo_count = count($existing_photos);
$max_photos = 3;
$can_upload = $photo_count < $max_photos;
?>

<div class="restaurant-photos-management">
    <h2>Restaurant Photo Management</h2>
    
    <!-- Preview restaurant button -->
    <div style="text-align: center; margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px;">
        <a href="<?php echo get_permalink($restaurant_id); ?>" target="_blank" style="background-color: #8b2635; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">👁️ Preview Restaurant</a>
        <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">Click to preview your restaurant page in a new tab.</p>
        <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">Restaurant ID: <?php echo $restaurant_id; ?> | Link: <?php echo get_permalink($restaurant_id); ?></p>
    </div>
    
    <!-- Photo upload section -->
    <?php if ($can_upload): ?>
        <div class="photo-upload-section">
            <h3>Upload New Photo</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_photos">
                
                <div class="upload-instructions">
                    <p><strong>Upload Guidelines:</strong></p>
                    <ul>
                        <li>Maximum <?php echo $max_photos; ?> photos.</li>
                        <li>Upload representative photos of the dining area, ambiance, or signature dishes.</li>
                        <li>Photo description is optional.</li>
                        <li>Supported formats: JPG, PNG, WebP. Maximum file size 2MB per image.</li>
                    </ul>
                </div>
                
                <div class="photo-upload-fields">
                    <div class="photo-field">
                        <label>Select photo:</label>
                        <input type="file" name="photos[]" accept="image/*" required>
                    </div>
                    <div class="photo-field">
                        <label>Photo description (optional):</label>
                        <input type="text" name="photo_description" placeholder="e.g., Dining area">
                    </div>
                </div>
                
                <button type="submit" class="upload-button">Upload Photo</button>
            </form>
        </div>
    <?php else: ?>
        <div class="upload-limit-reached">
            <p>You have reached the maximum number of photos (<?php echo $max_photos; ?>). Delete an existing photo to upload a new one.</p>
        </div>
    <?php endif; ?>
    
    <!-- Existing photos -->
    <div class="existing-photos-section">
        <h3>Existing Photos (<?php echo $photo_count; ?>/<?php echo $max_photos; ?>)</h3>
        
        <?php if (empty($existing_photos)): ?>
            <p>No photos have been uploaded yet.</p>
        <?php else: ?>
            <div class="photos-grid">
                <?php foreach ($existing_photos as $index => $photo): ?>
                    <div class="photo-item">
                        <div class="photo-preview">
                            <img src="<?php echo esc_url($photo['photo']['sizes']['thumbnail']); ?>" 
                                 alt="<?php echo esc_attr($photo['description'] ?: 'Restaurant photo'); ?>">
                        </div>
                        <div class="photo-info">
                            <?php if ($photo['description']): ?>
                                <p class="photo-description"><?php echo esc_html($photo['description']); ?></p>
                            <?php endif; ?>
                            <form method="post" class="delete-photo-form">
                                <input type="hidden" name="action" value="delete_photo">
                                <input type="hidden" name="photo_id" value="<?php echo $photo['photo']['ID']; ?>">
                                <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this photo?')">Delete Photo</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.restaurant-photos-management {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.photo-upload-section, .existing-photos-section {
    background: #f9f9f9;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
}

.upload-instructions {
    background: #e7f3ff;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.upload-instructions ul {
    margin: 10px 0;
    padding-left: 20px;
}

.photo-field {
    margin: 15px 0;
}

.photo-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.photo-field input[type="file"],
.photo-field input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.upload-button, .delete-button {
    background: #0073aa;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.delete-button {
    background: #dc3232;
    padding: 5px 10px;
    font-size: 12px;
}

.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.photo-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.photo-preview img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 4px;
}

.photo-info {
    margin-top: 10px;
}

.photo-description {
    margin: 5px 0;
    font-style: italic;
    color: #666;
}

.upload-limit-reached {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 15px;
    border-radius: 5px;
    color: #856404;
}

.error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
}

.success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
}
</style>
