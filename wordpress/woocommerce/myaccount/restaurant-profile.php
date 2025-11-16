<?php

/**
 * 餐廳資料編輯頁面模板
 * 
 * 這個檔案會顯示餐廳業者的資料編輯表單
 * 包含基本資料編輯和 LOGO 上傳功能
 */

// 防止直接訪問
if (!defined('ABSPATH')) {
    exit;
}

// 檢查使用者是否為餐廳業者
$user_id = get_current_user_id();
if (!$user_id) {
    echo '<div style="text-align: center; padding: 50px;">';
    echo '<h2>Please Login First</h2>';
    echo '<p>You need to login to edit restaurant information.</p>';
    echo '<a href="' . wp_login_url(get_permalink()) . '" class="button">Login</a>';
    echo '</div>';
    return;
}

$user = get_user_by('id', $user_id);
if (!in_array('restaurant_owner', $user->roles)) {
    echo '<div style="text-align: center; padding: 50px;">';
    echo '<h2>Insufficient Permissions</h2>';
    echo '<p>Only restaurant owners can access this page.</p>';
    echo '</div>';
    return;
}

// 獲取使用者擁有的餐廳
$user_restaurants = byob_get_user_restaurants($user_id);
if (empty($user_restaurants)) {
    echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 30px; border-radius: 8px; text-align: center;">';
    echo '<h3>⚠️ Notice</h3>';
    echo '<p>You currently have no associated restaurants.</p>';
    echo '<p>This could be because:</p>';
    echo '<ul style="text-align: left; display: inline-block; margin: 20px 0;">';
    echo '<li>Restaurant information has not been created</li>';
    echo '<li>Restaurant is not yet linked to your account</li>';
    echo '<li>Restaurant status is not "Published"</li>';
    echo '</ul>';
    echo '<p>Please contact the administrator for assistance.</p>';
    echo '</div>';
    return;
}

$restaurant = $user_restaurants[0]; // 取第一個餐廳
$restaurant_id = $restaurant->ID;

// 獲取當前餐廳資料
$current_logo_id = get_post_meta($restaurant_id, '_restaurant_logo', true);
$current_logo_url = $current_logo_id ? wp_get_attachment_image_url($current_logo_id, 'thumbnail') : '';

// ACF 欄位資料載入除錯（僅在開發環境且管理員登入時顯示）
if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) {
    echo '<div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 20px; font-family: monospace; font-size: 12px;">';
    echo '<h4 style="margin: 0 0 15px 0; color: #495057;">🔍 ACF 欄位資料載入除錯資訊</h4>';
    
    // 檢查 ACF 外掛是否啟用
    if (function_exists('get_field')) {
        echo '<p style="color: #28a745; margin: 5px 0;">✅ ACF 外掛已啟用</p>';
        
        // 檢查各個 ACF 欄位的資料
        $debug_fields = array(
            'restaurant_type' => 'Restaurant Type',
            'is_charged' => 'Corkage Fee',
            'corkage_fee_amount' => 'Corkage Fee Amount',
            'corkage_fee_note' => 'Corkage Fee Other Note',
            'equipment' => 'Wine Equipment',
            'byob_service_level' => 'BYOB Service Level',
            'open_bottle_service' => 'Wine Service (legacy)',
            'open_bottle_service_other_note' => 'Wine Service Other Note',
            'website' => 'Official Website',
            'social_links' => 'Social Media Links',
            'phone' => 'Phone Number',
            'address' => 'Address',
            'business_hours' => 'Business Hours'
        );
        
        foreach ($debug_fields as $field_name => $field_label) {
            $field_value = get_field($field_name, $restaurant_id);
            if ($field_value !== false && $field_value !== null && $field_value !== '') {
                if (is_array($field_value)) {
                    echo '<p style="color: #28a745; margin: 5px 0;">✅ ' . $field_label . ': ' . implode(', ', $field_value) . '</p>';
                } else {
                    echo '<p style="color: #28a745; margin: 5px 0;">✅ ' . $field_label . ': ' . esc_html($field_value) . '</p>';
                }
            } else {
                echo '<p style="color: #dc3545; margin: 5px 0;">❌ ' . $field_label . ': No data or field does not exist</p>';
            }
        }
    } else {
        echo '<p style="color: #dc3545; margin: 5px 0;">❌ ACF Plugin Not Enabled</p>';
    }
    
    echo '<p style="color: #6c757d; margin: 5px 0;">Restaurant ID: ' . $restaurant_id . '</p>';
    echo '<p style="color: #6c757d; margin: 5px 0;">Restaurant Title: ' . esc_html($restaurant->post_title) . '</p>';
    
    // 添加權限檢查除錯資訊
    $restaurant_owner_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    echo '<p style="color: #6c757d; margin: 5px 0;">Restaurant Owner ID: ' . $restaurant_owner_id . '</p>';
    echo '<p style="color: #6c757d; margin: 5px 0;">Current User ID: ' . $user_id . '</p>';
    echo '<p style="color: #6c757d; margin: 5px 0;">User Roles: ' . implode(', ', $user->roles) . '</p>';
    echo '<p style="color: ' . ($restaurant_owner_id == $user_id ? '#28a745' : '#dc3545') . '; margin: 5px 0;">Permission Check: ' . ($restaurant_owner_id == $user_id ? '✅ Has Permission' : '❌ No Permission') . '</p>';
    
    echo '</div>';
}

// 處理表單提交
if (isset($_POST['action']) && $_POST['action'] === 'update_restaurant_profile') {
    byob_handle_restaurant_profile_submit($restaurant_id);
}

// Logo deletion handler
if (isset($_POST['action']) && $_POST['action'] === 'delete_restaurant_logo') {
    $delete_restaurant_id = intval($_POST['restaurant_id']);
    
    // 添加除錯日誌
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('BYOB LOGO Delete: start processing');
        error_log('BYOB LOGO Delete: restaurant ID = ' . $restaurant_id . ' (type: ' . gettype($restaurant_id) . ')');
        error_log('BYOB LOGO Delete: submitted restaurant ID = ' . $delete_restaurant_id . ' (type: ' . gettype($delete_restaurant_id) . ')');
        error_log('BYOB LOGO Delete: user ID = ' . $user_id);
        error_log('BYOB LOGO Delete: user roles = ' . implode(', ', $user->roles));
    }
    
    // Permission check - restaurant owners should edit their own restaurant
    if ($delete_restaurant_id == $restaurant_id) { // use == to handle type difference
        // Verify the owner ID
        $restaurant_owner_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
        $user_has_restaurant = ($restaurant_owner_id == $user_id);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BYOB LOGO Delete: restaurant owner ID = ' . $restaurant_owner_id);
            error_log('BYOB LOGO Delete: current user ID = ' . $user_id);
            error_log('BYOB LOGO Delete: user owns this restaurant = ' . ($user_has_restaurant ? 'true' : 'false'));
        }
        
        if ($user_has_restaurant) {
        // Get current logo ID
        $current_logo_id = get_post_meta($restaurant_id, '_restaurant_logo', true);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BYOB LOGO Delete: current logo ID = ' . $current_logo_id);
        }
        
        if ($current_logo_id) {
            // Delete attachment from media library
            $delete_result = wp_delete_attachment($current_logo_id, true);
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BYOB LOGO Delete: wp_delete_attachment result = ' . ($delete_result ? 'success' : 'failure'));
            }
            
            if ($delete_result) {
                // Remove logo meta from restaurant
                $meta_delete_result = delete_post_meta($restaurant_id, '_restaurant_logo');
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('BYOB LOGO Delete: delete_post_meta result = ' . ($meta_delete_result ? 'success' : 'failure'));
                }
                
                // Clear current logo data to avoid redirect issues
                $current_logo_id = '';
                $current_logo_url = '';
                
                // Set success message
                $logo_delete_message = 'logo_deleted';
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('BYOB LOGO Delete: completed successfully');
                }
            } else {
                // Set error message
                $logo_delete_message = 'logo_delete_error';
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('BYOB LOGO Delete: attachment removal failed');
                }
            }
        } else {
            // No logo to delete
            $logo_delete_message = 'no_logo';
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BYOB LOGO Delete: no logo available to delete');
            }
        }
        } else {
            // User does not have permission for this restaurant
            $logo_delete_message = 'permission_denied';
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('BYOB LOGO Delete: user lacks permission for this restaurant');
                error_log('BYOB LOGO Delete: user restaurant list: ' . print_r(array_map(function($r) { return $r->ID; }, $user_restaurants), true));
            }
        }
    } else {
        // Restaurant ID mismatch
        $logo_delete_message = 'permission_denied';
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('BYOB LOGO Delete: restaurant ID mismatch');
            error_log('BYOB LOGO Delete: submitted restaurant ID = ' . $delete_restaurant_id . ', current restaurant ID = ' . $restaurant_id);
        }
    }
}

// 顯示成功/失敗訊息
$message_to_show = '';

// 檢查 GET 參數中的訊息
if (isset($_GET['message'])) {
    $message_to_show = sanitize_text_field($_GET['message']);
}

// 檢查 LOGO 刪除訊息
if (isset($logo_delete_message)) {
    $message_to_show = $logo_delete_message;
}

// 顯示訊息
if ($message_to_show) {
    if ($message_to_show === 'success') {
        echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">✅ Update Successful!</h3>';
        echo '<p style="margin: 0;">Restaurant information has been successfully updated.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'error') {
        echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">❌ Update Failed</h3>';
        echo '<p style="margin: 0;">Please check if the input data is correct.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'partial_success') {
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">⚠️ Partial Update Success</h3>';
        echo '<p style="margin: 0;">Basic information updated, but LOGO upload failed.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'logo_deleted') {
        echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">✅ LOGO Deleted</h3>';
        echo '<p style="margin: 0;">Restaurant LOGO has been successfully deleted.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'logo_delete_error') {
        echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">❌ LOGO Delete Failed</h3>';
        echo '<p style="margin: 0;">An error occurred while deleting LOGO, please try again later.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'no_logo') {
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">⚠️ No LOGO to Delete</h3>';
        echo '<p style="margin: 0;">No LOGO is currently set.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'permission_denied') {
        echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">';
        echo '<h3 style="margin: 0 0 10px 0;">❌ Insufficient Permissions</h3>';
        echo '<p style="margin: 0;">You do not have permission to perform this operation.</p>';
        echo '</div>';
    } elseif ($message_to_show === 'address_validation_error') {
        $address_errors = isset($_GET['address_errors']) ? explode('|', urldecode($_GET['address_errors'])) : array();
        echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px;">';
        echo '<h3 style="margin: 0 0 10px 0;">❌ Incomplete Address Format</h3>';
        if (!empty($address_errors)) {
            echo '<ul style="margin: 10px 0 0 0; padding-left: 20px;">';
            foreach ($address_errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul>';
        }
        echo '<p style="margin: 10px 0 0 0; font-size: 14px;">Please refer to the validation rules below the address field to ensure the address includes complete city, district, street and house number information.</p>';
        echo '</div>';
    }
}

// 頁面標題和說明
echo '<div class="restaurant-profile-header" style="margin-bottom: 30px;">';
echo '<h1 style="color: #333; margin-bottom: 10px; text-align: center;">Restaurant Information Edit</h1>';
echo '<p style="color: #666; font-size: 16px; text-align: left;">Edit your restaurant basic information and LOGO</p>';

// 預覽餐廳按鈕
echo '<div style="text-align: right; margin-top: 15px;">';
echo '<a href="' . get_permalink($restaurant_id) . '" class="button" target="_blank" style="background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 10px 20px; font-size: 14px; display: inline-block; text-decoration: none; color: white; border: none;">👁️ Preview Restaurant</a>';
echo '</div>';

echo '</div>';

// 主要表單
echo '<div class="restaurant-profile-form" style="max-width: 800px; margin: 0 auto;">';
echo '<form method="post" enctype="multipart/form-data" style="background: #f9f9f9; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
echo '<input type="hidden" name="action" value="update_restaurant_profile">';
echo '<input type="hidden" name="restaurant_id" value="' . esc_attr($restaurant_id) . '">';

// 餐廳基本資料區塊
echo '<div class="form-section" style="margin-bottom: 35px;">';
echo '<h3 style="color: #333; border-bottom: 3px solid rgba(139, 38, 53, 0.8); padding-bottom: 15px; margin-bottom: 25px;">Basic Information</h3>';

// 必填欄位說明
echo '<div style="background: rgba(212, 237, 218, 0.7); border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 25px;">';
echo '<p style="margin: 0; color: #856404; font-size: 14px;"><strong>📋 Important Notice:</strong> Fields marked with <span style="color: #dc3545; font-weight: bold;">*</span> are required. Only after completion will the restaurant be displayed on the front-end page for customers to browse.</p>';
echo '</div>';

// 餐廳名稱
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="restaurant_name" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Restaurant Name *</label>';
echo '<input type="text" id="restaurant_name" name="restaurant_name" value="' . esc_attr($restaurant->post_title) . '" required style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Restaurant name is a required field</p>';
echo '</div>';

// 餐廳類型
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Restaurant Type *</label>';
echo '<div class="checkbox-group" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">';

$restaurant_types = array(
    'Italian' => 'Italian',
    'French' => 'French',
    'American' => 'American',
    'Asian' => 'Asian',
    'Mediterranean' => 'Mediterranean',
    'Mexican' => 'Mexican',
    'Steakhouse' => 'Steakhouse',
    'Seafood' => 'Seafood',
    'Vegetarian/Vegan' => 'Vegetarian/Vegan',
    'Thai' => 'Thai',
    'Japanese' => 'Japanese',
    'Indian' => 'Indian',
    'Spanish' => 'Spanish',
    'Fine dining' => 'Fine dining',
    'Other' => 'Other'
);



$current_types = get_field('philly_restaurant_type', $restaurant_id);
if (!$current_types) {
    $current_types = get_field('restaurant_type', $restaurant_id);
}
$current_types = is_array($current_types) ? $current_types : array();
        
        // 除錯：檢查餐廳類型和其他類型說明
        $other_note = get_field('philly_restaurant_type_other_note', $restaurant_id);
        if (!$other_note) {
            $other_note = get_field('restaurant_type_other_note', $restaurant_id);
        }
        echo '<!-- DEBUG: philly_restaurant_type = ' . print_r($current_types, true) . ' -->';
        echo '<!-- DEBUG: philly_restaurant_type_other_note = ' . $other_note . ' -->';
        
        // 更詳細的除錯資訊
        echo '<!-- DEBUG: restaurant_id = ' . $restaurant_id . ' -->';
        echo '<!-- DEBUG: ACF 函數是否存在: ' . (function_exists('get_field') ? '是' : '否') . ' -->';
        
        // 檢查所有 ACF 欄位
        if (function_exists('get_field')) {
            $all_fields = get_fields($restaurant_id);
            echo '<!-- DEBUG: 所有 ACF 欄位: ' . print_r($all_fields, true) . ' -->';
        }

foreach ($restaurant_types as $value => $label) {
    $checked = in_array($value, $current_types) ? 'checked' : '';
    

    
    echo '<label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; padding: 10px; border: 1px solid #ddd; border-radius: 6px; transition: all 0.3s;">';
    echo '<input type="checkbox" name="restaurant_type[]" value="' . $value . '" ' . $checked . ' style="margin-right: 8px;" onchange="limitCheckboxes(this, 3, \'restaurant_type\')">';
    echo $label;
    echo '</label>';
}

echo '</div>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please select your restaurant type (maximum 3)</p>';
echo '</div>';

// 其他類型說明（條件式顯示）
echo '<div id="other_type_note_field" class="form-group" style="margin-bottom: 25px; display: none;">';
echo '<label for="restaurant_type_other_note" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Other Type Description</label>';
echo '<input type="text" id="restaurant_type_other_note" name="restaurant_type_other_note" value="' . esc_attr($other_note) . '" placeholder="Please describe your restaurant type..." style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please describe your restaurant type (optional)</p>';
echo '</div>';



// 聯絡電話
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="restaurant_phone" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Phone Number *</label>';
echo '<input type="tel" id="restaurant_phone" name="restaurant_phone" value="' . esc_attr(get_field('phone', $restaurant_id)) . '" placeholder="e.g.: 02-1234-5678" required style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">';
echo '</div>';

// 聯絡人姓名
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="contact_person" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">User Name *</label>';
$current_user = wp_get_current_user();
echo '<input type="text" id="contact_person" name="contact_person" value="' . esc_attr($current_user->display_name) . '" readonly style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background-color: #f8f9fa; color: #6c757d; cursor: not-allowed;">';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">User name is the name filled during registration and cannot be modified by yourself. If you need to modify, please contact the <a href="https://byobmap.com/contact/" target="_blank" style="color: rgba(139, 38, 53, 0.8); text-decoration: none;">website administrator</a></p>';
echo '</div>';

// 行政區（暫時隱藏）
/*
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="district" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">District *</label>';
echo '<select id="district" name="district" required style="width: 100%; height: 50px; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; line-height: 20px; transition: border-color 0.3s; display: flex; align-items: center;">';
echo '<option value="">Please select district</option>';
echo '<option value="中正區" ' . (get_field('district', $restaurant_id) === '中正區' ? 'selected' : '') . '>中正區</option>';
echo '<option value="大同區" ' . (get_field('district', $restaurant_id) === '大同區' ? 'selected' : '') . '>大同區</option>';
echo '<option value="中山區" ' . (get_field('district', $restaurant_id) === '中山區' ? 'selected' : '') . '>中山區</option>';
echo '<option value="松山區" ' . (get_field('district', $restaurant_id) === '松山區' ? 'selected' : '') . '>松山區</option>';
echo '<option value="大安區" ' . (get_field('district', $restaurant_id) === '大安區' ? 'selected' : '') . '>大安區</option>';
echo '<option value="萬華區" ' . (get_field('district', $restaurant_id) === '萬華區' ? 'selected' : '') . '>萬華區</option>';
echo '<option value="信義區" ' . (get_field('district', $restaurant_id) === '信義區' ? 'selected' : '') . '>信義區</option>';
echo '<option value="士林區" ' . (get_field('district', $restaurant_id) === '士林區' ? 'selected' : '') . '>士林區</option>';
echo '<option value="北投區" ' . (get_field('district', $restaurant_id) === '北投區' ? 'selected' : '') . '>北投區</option>';
echo '<option value="內湖區" ' . (get_field('district', $restaurant_id) === '內湖區' ? 'selected' : '') . '>內湖區</option>';
echo '<option value="南港區" ' . (get_field('district', $restaurant_id) === '南港區' ? 'selected' : '') . '>南港區</option>';
echo '<option value="文山區" ' . (get_field('district', $restaurant_id) === '文山區' ? 'selected' : '') . '>文山區</option>';
echo '</select>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please select the district where the restaurant is located</p>';
echo '</div>';
*/

// 地址
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="restaurant_address" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Address *</label>';
echo '<textarea id="restaurant_address" name="restaurant_address" rows="3" placeholder="Please enter complete address..." required style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; resize: vertical; transition: border-color 0.3s;" oninput="validateAddress(this.value)">' . esc_textarea(get_field('address', $restaurant_id)) . '</textarea>';
echo '<div id="address_validation_result" style="margin-top: 10px;"></div>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please fill in the complete address, including city, so it can be displayed on the front-end and be easily searched by customers</p>';
echo '</div>';

// 營業時間
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="business_hours" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Business Hours</label>';
echo '<textarea id="business_hours" name="business_hours" rows="3" placeholder="e.g.: Monday to Friday 11:00-22:00, Saturday and Sunday 10:00-23:00" style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; resize: vertical; transition: border-color 0.3s;">' . esc_textarea(get_field('business_hours', $restaurant_id)) . '</textarea>';
echo '</div>';

// 是否收開瓶費（Philly 欄位）
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="philly_corkage_fee" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Corkage Fee *</label>';
echo '<div class="radio-group" style="display: flex; gap: 20px; align-items: center; flex-wrap: nowrap;">';
echo '<label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; white-space: nowrap; writing-mode: horizontal-tb; text-orientation: mixed;">';
echo '<input type="radio" name="philly_corkage_fee" value="corkage_fee" ' . (get_field('philly_corkage_fee', $restaurant_id) === 'corkage_fee' ? 'checked' : '') . ' style="margin-right: 8px;">';
echo '<span style="display: inline-block; white-space: nowrap;">Corkage Fee</span>';
echo '</label>';
echo '<label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; white-space: nowrap; writing-mode: horizontal-tb; text-orientation: mixed;">';
echo '<input type="radio" name="philly_corkage_fee" value="free" ' . (get_field('philly_corkage_fee', $restaurant_id) === 'free' ? 'checked' : '') . ' style="margin-right: 8px;">';
echo '<span style="display: inline-block; white-space: nowrap;">Free</span>';
echo '</label>';
echo '<label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; white-space: nowrap; writing-mode: horizontal-tb; text-orientation: mixed;">';
echo '<input type="radio" name="philly_corkage_fee" value="other" ' . (get_field('philly_corkage_fee', $restaurant_id) === 'other' ? 'checked' : '') . ' style="margin-right: 8px;">';
echo '<span style="display: inline-block; white-space: nowrap;">Other</span>';
echo '</label>';
echo '</div>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please select your corkage fee policy</p>';
echo '</div>';

// 開瓶費金額欄位（當選擇「酌收」時顯示）
echo '<div id="corkage_amount_field" class="form-group" style="margin-bottom: 25px; display: none;">';
echo '<label for="corkage_fee_amount" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Corkage Fee Amount *</label>';
echo '<input type="number" id="corkage_fee_amount" name="corkage_fee_amount" value="' . esc_attr(get_field('corkage_fee_amount', $restaurant_id)) . '" placeholder="e.g.: 100" min="0" step="1" style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please enter the corkage fee amount (NTD)</p>';
echo '</div>';

// 開瓶費其他說明欄位（當選擇「其他」時顯示）
echo '<div id="corkage_note_field" class="form-group" style="margin-bottom: 25px; display: none;">';
echo '<label for="corkage_fee_note" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Other Description *</label>';
echo '<input type="text" id="corkage_fee_note" name="corkage_fee_note" value="' . esc_attr(get_field('corkage_fee_note', $restaurant_id)) . '" placeholder="e.g.: Charged per glass, or xxx NTD per table" style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please describe your corkage fee policy</p>';
echo '</div>';

// 酒器設備
$byob_service_level_options = array(
    'full_service' => array(
        'label' => 'Full service (opening, pouring, decanting, chilling)',
        'description' => 'Full service includes opening, pouring, decanting, and chilling.'
    ),
    'basic_service' => array(
        'label' => 'Basic service (opening and pouring)',
        'description' => 'Basic service includes opening and pouring.'
    ),
    'self_service' => array(
        'label' => 'Self-service (equipment provided)',
        'description' => 'Self-service: equipment is provided for guests.'
    ),
    'no_service' => array(
        'label' => 'No service (BYOB only, bring your own equipment)',
        'description' => 'No service: guests should bring their own equipment.'
    ),
);
$byob_service_level_legacy_map = array(
    '有' => 'full_service',
    '無' => 'no_service',
    '其他' => 'self_service',
);
$byob_service_level_value = get_field('byob_service_level', $restaurant_id);
if (empty($byob_service_level_value)) {
    $legacy_value = get_field('open_bottle_service', $restaurant_id);
    if ($legacy_value && isset($byob_service_level_legacy_map[$legacy_value])) {
        $byob_service_level_value = $byob_service_level_legacy_map[$legacy_value];
    }
}
if (!empty($byob_service_level_value) && !isset($byob_service_level_options[$byob_service_level_value])) {
    $byob_service_level_value = '';
}
$byob_service_level_js_data = array();
foreach ($byob_service_level_options as $slug => $data) {
    $byob_service_level_js_data[$slug] = array(
        'label' => $data['label'],
        'description' => $data['description'],
    );
}

echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Wine Equipment</label>';
echo '<div class="checkbox-group" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">';

$equipment_options = array(
    '酒杯' => 'Wine Glasses',
    '開瓶器' => 'Corkscrew',
    '冰桶' => 'Ice Bucket',
    '醒酒器' => 'Decanter',
    '酒塞/瓶塞' => 'Wine Stopper',
    '酒架/酒櫃' => 'Wine Rack',
    '溫度計' => 'Thermometer',
    '濾酒器' => 'Wine Filter',
    '其他' => 'Other',
    '無提供' => 'Not Provided'
);

$current_equipment = get_field('equipment', $restaurant_id);
$current_equipment = is_array($current_equipment) ? $current_equipment : array();

foreach ($equipment_options as $value => $label) {
    $checked = in_array($value, $current_equipment) ? 'checked' : '';
    echo '<label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; padding: 10px; border: 1px solid #ddd; border-radius: 6px; transition: all 0.3s;">';
    echo '<input type="checkbox" name="equipment[]" value="' . $value . '" ' . $checked . ' style="margin-right: 8px;">';
    echo $label;
    echo '</label>';
}

echo '</div>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please select the wine equipment you provide</p>';
echo '</div>';

// 開酒服務
$debug_byob_value = $byob_service_level_value ? $byob_service_level_value : '(empty)';
$legacy_open_bottle_service = get_field('open_bottle_service', $restaurant_id);
if (!$byob_service_level_value && $legacy_open_bottle_service && isset($byob_service_level_legacy_map[$legacy_open_bottle_service])) {
    $debug_byob_value .= ' (derived from legacy value: ' . $legacy_open_bottle_service . ')';
}

echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="byob_service_level" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">BYOB Service Level</label>';
if (defined('WP_DEBUG') && WP_DEBUG) {
    echo '<p style="font-size: 12px; color: #666; margin-bottom: 5px;">🔍 Debug: BYOB service level (slug) = "' . esc_html($debug_byob_value) . '"</p>';
}

echo '<select id="byob_service_level" name="byob_service_level" onchange="toggleOtherNote()" style="width: 100%; height: 50px; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; line-height: 20px; transition: border-color 0.3s; min-width: 200px; text-overflow: clip; white-space: nowrap; display: flex; align-items: center;">';
echo '<option value="">Please select</option>';
foreach ($byob_service_level_options as $slug => $data) {
    $selected = ($byob_service_level_value === $slug) ? 'selected' : '';
    echo '<option value="' . esc_attr($slug) . '" ' . $selected . '>' . esc_html($data['label']) . '</option>';
}
echo '</select>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please select the BYOB service level you provide</p>';
echo '</div>';

$service_message = '';
if ($byob_service_level_value && isset($byob_service_level_options[$byob_service_level_value])) {
    $service_message = $byob_service_level_options[$byob_service_level_value]['description'];
}

echo '<div id="service_status_text" class="form-group" style="margin-bottom: 25px; display: ' . ($service_message ? 'block' : 'none') . ';">';
if ($service_message) {
    echo '<div style="background: #e8f5e8; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; text-align: center;">';
    echo '<p style="margin: 0; color: #155724; font-weight: bold; font-size: 16px;">' . esc_html($service_message) . '</p>';
    echo '</div>';
}
echo '</div>';

// 舊的其他說明欄位暫時保留但隱藏
echo '<div id="other_note_field" class="form-group" style="margin-bottom: 25px; display: none;">';
echo '<label for="open_bottle_service_other_note" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Other Description</label>';
echo '<input type="text" id="open_bottle_service_other_note" name="open_bottle_service_other_note" value="' . esc_attr(get_field('open_bottle_service_other_note', $restaurant_id)) . '" placeholder="Please describe the wine service content you provide..." style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">';
echo '<p style="font-size: 14px; color: 666; margin-top: 5px;">Please describe in detail the wine service content you provide (optional)</p>';
echo '</div>';

// 其他BYOB規定或備註
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="notes" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Other BYOB Policies or Notes</label>';
echo '<textarea id="notes" name="notes" rows="5" placeholder="Other BYOB policies, or your restaurant features, style, service, etc..." style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; resize: vertical; transition: border-color 0.3s;">' . esc_textarea(get_field('notes', $restaurant_id)) . '</textarea>';
echo '</div>';

// 官方網站/社群連結（包含 Yelp Link）
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Yelp Link / Official Website / Social Media Links</label>';
echo '<div style="display: flex; gap: 15px; flex-wrap: wrap;">';
// Yelp Link (第一個欄位)
echo '<div style="flex: 1; min-width: 200px;">';
echo '<label for="yelp_link" style="display: block; margin-bottom: 8px; font-weight: normal; color: #666; font-size: 14px;">Yelp Link</label>';
echo '<input type="url" id="yelp_link" name="yelp_link" value="' . esc_attr(get_field('yelp_link', $restaurant_id)) . '" placeholder="e.g.: https://www.yelp.com/biz/restaurant-name" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.3s;">';
echo '</div>';
echo '<div style="flex: 1; min-width: 200px;">';
echo '<label for="website" style="display: block; margin-bottom: 8px; font-weight: normal; color: #666; font-size: 14px;">Official website or reservation URL</label>';
echo '<input type="url" id="website" name="website" value="' . esc_attr(get_field('website', $restaurant_id)) . '" placeholder="e.g.: https://www.example.com" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.3s;">';
echo '</div>';
echo '<div style="flex: 1; min-width: 200px;">';
echo '<label for="social_links" style="display: block; margin-bottom: 8px; font-weight: normal; color: #666; font-size: 14px;">Social media links</label>';
echo '<input type="url" id="social_links" name="social_links" value="' . esc_attr(get_field('social_links', $restaurant_id)) . '" placeholder="e.g.: Facebook, Instagram, etc." style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.3s;">';
echo '</div>';
echo '</div>';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">Please enter your official website and social media links (optional)</p>';
echo '</div>';

// 聯絡人Email
echo '<div class="form-group" style="margin-bottom: 25px;">';
echo '<label for="contact_email" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Restaurant Email *</label>';
$current_user_email = wp_get_current_user()->user_email;
echo '<input type="email" id="contact_email" name="contact_email" value="' . esc_attr($current_user_email) . '" readonly style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background-color: #f8f9fa; color: #6c757d; cursor: not-allowed;">';
echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">This email is synchronized with the login account</p>';
echo '</div>';

echo '</div>';

// Logo upload section
echo '<div class="form-section" style="margin-bottom: 35px;">';
echo '<h3 style="color: #333; border-bottom: 3px solid rgba(139, 38, 53, 0.8); padding-bottom: 15px; margin-bottom: 25px;">Restaurant LOGO</h3>';

// Display current logo if available
if ($current_logo_url) {
    echo '<div class="current-logo" style="margin-bottom: 25px;">';
    echo '<p style="font-weight: bold; margin-bottom: 15px; color: #333;">Current LOGO:</p>';
    echo '<div class="logo-display-area" style="width: 300px; height: 300px; border: 3px solid #ddd; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; display: flex; align-items: center; justify-content: center;">';
    echo '<img src="' . esc_url($current_logo_url) . '" alt="Current LOGO" class="logo-image" style="max-width: 100%; max-height: 100%; object-fit: contain; transition: all 0.3s;">';
    echo '</div>';
    
    // Additional note below the preview
    echo '<div class="logo-display-info" style="margin-top: 15px; text-align: center;">';
    
    // Delete button
    echo '<div class="logo-actions" style="border-top: 1px solid #e9ecef; padding-top: 15px;">';
    echo '<button type="button" onclick="deleteLogo()" style="background-color: #dc3545; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; font-weight: normal; transition: all 0.3s;">🗑️ Delete LOGO</button>';
    echo '<p style="font-size: 12px; color: #999; margin-top: 8px;">Click to permanently delete current LOGO</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="no-logo" style="margin-bottom: 25px;">';
    echo '<p style="font-weight: bold; margin-bottom: 15px; color: #333;">No LOGO currently set</p>';
    echo '<div class="logo-display-area" style="width: 300px; height: 300px; border: 2px dashed #dee2e6; border-radius: 10px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">';
    echo '<p style="color: #6c757d; margin: 0;">Please upload LOGO or restaurant photo</p>';
    echo '</div>';
    echo '</div>';
}

// Logo upload fieldset
echo '<div class="form-group" style="margin-bottom: 25px;">';
    echo '<label for="restaurant_logo" style="display: block; margin-bottom: 10px; font-weight: bold; color: #333; font-size: 16px;">Upload LOGO or representative restaurant photo (select file then click update restaurant information button)</label>';
echo '<input type="file" id="restaurant_logo" name="restaurant_logo" accept="image/jpeg,image/png,image/webp,image/svg+xml" style="width: 100%; padding: 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; background: white; transition: border-color 0.3s;">';
echo '</div>';

echo '</div>';

// 提交按鈕
echo '<div class="form-submit" style="text-align: center; padding-top: 20px; border-top: 2px solid #e9ecef;">';
echo '<button type="submit" style="background-color: rgba(139, 38, 53, 0.8); color: white; padding: 18px 40px; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; font-weight: bold; transition: all 0.3s; box-shadow: 0 4px 8px rgba(139, 38, 53, 0.3);">💾 Update Restaurant Information</button>';
echo '</div>';

echo '<div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 8px;">';
echo '<p style="font-size: 14px; color: #495057; margin: 0 0 8px 0;"><strong>📋 Upload Guidelines:</strong></p>';
echo '<ul style="font-size: 14px; color: #495057; margin: 0; padding-left: 20px;">';
echo '<li><strong>It is recommended to upload square or near-square image files for the best display effect</strong></li>';
echo '<li>Supported formats: JPG/JPEG, PNG, WebP, SVG</li>';
echo '<li>File size limit: 1MB</li>';
echo '<li>Recommended size: 300x300 pixels or above</li>';
echo '<li>Upload will automatically replace existing LOGO</li>';
echo '</ul>';
echo '</div>';

echo '</form>';
echo '</div>';

// 添加一些 CSS 樣式來改善表單互動
echo '<style>
.form-group input:focus,
.form-group textarea:focus {
    border-color: rgba(139, 38, 53, 0.8) !important;
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 38, 53, 0.1);
}

.form-submit button:hover {
    background-color: rgba(139, 38, 53, 1) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(139, 38, 53, 0.4);
}

.form-submit button:active {
    transform: translateY(0);
}

.checkbox-group label:hover {
    border-color: rgba(139, 38, 53, 0.5);
    background-color: rgba(139, 38, 53, 0.05);
}

.checkbox-group input[type="checkbox"]:checked + span {
    color: rgba(139, 38, 53, 0.8);
    font-weight: bold;
}

/* LOGO 顯示樣式 */
.logo-image {
    width: 100%;
    height: 100%;
    object-fit: contain; /* 預設為保持比例模式 */
}
</style>';

// 添加 JavaScript 來限制餐廳類型最多只能選3個
?>
<script>
const byobServiceInfo = <?php echo wp_json_encode($byob_service_level_js_data); ?>;

function limitCheckboxes(checkbox, maxCount, groupName) {
    const checkboxes = document.querySelectorAll(`input[name="${groupName}[]"]`);
    let checkedCount = 0;
    const otherTypeNoteField = document.getElementById('other_type_note_field');

    checkboxes.forEach((cb) => {
        if (cb.checked) {
            checkedCount += 1;
        }
    });

    if (checkedCount > maxCount) {
        checkbox.checked = false;
        alert(`Restaurant type can only select up to ${maxCount} options`);
        return false;
    }

    const otherCheckbox = document.querySelector(`input[name="${groupName}[]"][value="Other"]`);
    if (otherCheckbox && otherCheckbox.checked) {
        otherTypeNoteField.style.display = 'block';
    } else {
        otherTypeNoteField.style.display = 'none';
        const otherNoteInput = document.getElementById('restaurant_type_other_note');
        if (otherNoteInput) {
            otherNoteInput.value = '';
        }
    }

    return true;
}

function toggleOtherNote() {
    const selectEl = document.getElementById('byob_service_level');
    const serviceStatusText = document.getElementById('service_status_text');
    const otherNoteField = document.getElementById('other_note_field');
    if (!selectEl || !serviceStatusText) {
        return;
    }
    if (otherNoteField) {
        otherNoteField.style.display = 'none';
    }
    const value = selectEl.value || '';
    const info = byobServiceInfo[value];
    if (info && info.description) {
        serviceStatusText.style.display = 'block';
        serviceStatusText.innerHTML = '<div style="background: #e8f5e8; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; text-align: center;"><p style="margin: 0; color: #155724; font-weight: bold; font-size: 16px;">' + info.description + '</p></div>';
    } else {
        serviceStatusText.style.display = 'none';
        serviceStatusText.innerHTML = '';
    }
}

function toggleCorkageFields() {
    const isChargedRadios = document.querySelectorAll('input[name="philly_corkage_fee"]');
    const corkageAmountField = document.getElementById('corkage_amount_field');
    const corkageNoteField = document.getElementById('corkage_note_field');

    if (corkageAmountField) {
        corkageAmountField.style.display = 'none';
    }
    if (corkageNoteField) {
        corkageNoteField.style.display = 'none';
    }

    let selectedValue = '';
    for (let i = 0; i < isChargedRadios.length; i += 1) {
        if (isChargedRadios[i].checked) {
            selectedValue = isChargedRadios[i].value;
            break;
        }
    }

    if (selectedValue === 'corkage_fee' && corkageAmountField) {
        corkageAmountField.style.display = 'block';
        const noteField = document.getElementById('corkage_fee_note');
        if (noteField) {
            noteField.value = '';
        }
    } else if (selectedValue === 'other' && corkageNoteField) {
        corkageNoteField.style.display = 'block';
        const amountField = document.getElementById('corkage_fee_amount');
        if (amountField) {
            amountField.value = '';
        }
    } else {
        const amountField = document.getElementById('corkage_fee_amount');
        const noteField = document.getElementById('corkage_fee_note');
        if (amountField) {
            amountField.value = '';
        }
        if (noteField) {
            noteField.value = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    toggleOtherNote();
    toggleCorkageFields();

    const isChargedRadios = document.querySelectorAll('input[name="philly_corkage_fee"]');
    for (let i = 0; i < isChargedRadios.length; i += 1) {
        isChargedRadios[i].addEventListener('change', toggleCorkageFields);
    }

    const otherCheckbox = document.querySelector('input[name="restaurant_type[]"][value="Other"]');
    const otherTypeNoteField = document.getElementById('other_type_note_field');
    if (otherCheckbox && otherCheckbox.checked && otherTypeNoteField) {
        otherTypeNoteField.style.display = 'block';
        const otherNoteField = document.getElementById('restaurant_type_other_note');
        const otherNoteValue = <?php echo wp_json_encode($other_note); ?>;
        if (otherNoteField && otherNoteValue) {
            otherNoteField.value = otherNoteValue;
        }
    }

    const addressField = document.getElementById('restaurant_address');
    // 暫停地址驗證：初始化時不再自動檢查
    if (addressField) {
        addressField.addEventListener('input', function () {
            const resultDiv = document.getElementById('address_validation_result');
            const textarea = document.getElementById('restaurant_address');
            if (resultDiv) resultDiv.innerHTML = '';
            if (textarea) textarea.style.borderColor = '#ddd';
        });
    }
});

function deleteLogo() {
    if (confirm('Are you sure you want to delete this LOGO? It cannot be recovered after deletion.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete_restaurant_logo';

        const restaurantIdInput = document.createElement('input');
        restaurantIdInput.type = 'hidden';
        restaurantIdInput.name = 'restaurant_id';
        restaurantIdInput.value = '<?php echo esc_js($restaurant_id); ?>';

        form.appendChild(actionInput);
        form.appendChild(restaurantIdInput);
        document.body.appendChild(form);

        form.submit();
    }
}

// 後台地點改為自由輸入，此函式留白避免前端驗證
function validateAddress() {}
</script>
<?php