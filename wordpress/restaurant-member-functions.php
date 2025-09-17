<?php
/**
 * 餐廳業者會員系統功能
 * 
 * 主要功能：
 * 1. 邀請碼系統
 * 2. 餐廳業者註冊流程
 * 3. 餐廳管理權限
 * 4. 會員管理介面
 */

// 防止直接訪問
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 初始化餐廳會員系統
 */
function byob_init_restaurant_member_system() {
    error_log('=== BYOB 系統初始化開始 ===');
    error_log('時間: ' . date('Y-m-d H:i:s'));
    error_log('函數: byob_init_restaurant_member_system() 開始執行');
    
    // 檢查當前使用者
    $user_id = get_current_user_id();
    error_log('當前使用者ID: ' . $user_id);
    
    if ($user_id) {
        $user = get_user_by('id', $user_id);
        $roles = $user ? $user->roles : array();
        error_log('使用者角色: ' . implode(', ', $roles));
        
        if (in_array('restaurant_owner', $roles)) {
            error_log('使用者是餐廳業者，開始註冊端點');
            byob_register_restaurant_endpoints();
        } else {
            error_log('使用者不是餐廳業者，跳過端點註冊');
        }
    } else {
        error_log('沒有登入使用者');
    }
    
    // 註冊自定義使用者角色
    byob_register_restaurant_owner_role();
    
    // 註冊 REST API 端點
    add_action('rest_api_init', 'byob_register_member_api_endpoints');
    
    // 處理邀請碼驗證
    add_action('init', 'byob_handle_invitation_verification');
    
    // 新增前端會員介面
    add_action('wp_enqueue_scripts', 'byob_enqueue_member_scripts');
    
    // 新增邀請碼註冊頁面
    // 注意：重寫規則和查詢變數已在 functions.php 中處理
    add_action('template_redirect', 'byob_handle_restaurant_registration_page');
    
    // 註冊限制存取功能
    add_action('init', 'byob_restrict_restaurant_owner_access');
    
    // 註冊存取控制
    add_action('admin_init', 'byob_restrict_admin_access');
    
    // 自定義 WooCommerce 會員選單
    add_action('init', 'byob_customize_woocommerce_menu');
    add_filter('woocommerce_account_menu_items', 'byob_customize_account_menu_items', 999);
    add_action('woocommerce_account_restaurant-profile_endpoint', 'byob_restaurant_profile_content');
    add_action('woocommerce_account_restaurant-photos_endpoint', 'byob_restaurant_photos_content');
    add_action('woocommerce_account_restaurant-menu_endpoint', 'byob_restaurant_menu_content');
    
    // 覆蓋預設控制台內容
    add_action('woocommerce_account_dashboard', 'byob_override_dashboard_content', 999);
    
    // 註冊自定義端點
    add_action('init', 'byob_register_restaurant_endpoints');
    
    error_log('=== BYOB 系統初始化結束 ===');
}

/**
 * 註冊餐廳業者角色
 */
function byob_register_restaurant_owner_role() {
    // 檢查角色是否已存在
    if (!get_role('restaurant_owner')) {
        add_role('restaurant_owner', '餐廳業者', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'edit_restaurant' => true, // 自定義權限
            'edit_own_restaurant' => true, // 編輯自己的餐廳
            'upload_files' => true, // 上傳檔案
            'read_restaurant_stats' => true, // 查看餐廳統計
        ));
    }
}

/**
 * 註冊會員相關 REST API 端點
 */
function byob_register_member_api_endpoints() {
    // 驗證邀請碼
    register_rest_route('byob/v1', '/verify-invitation', array(
        'methods' => 'POST',
        'callback' => 'byob_verify_invitation_code',
        'permission_callback' => '__return_true',
    ));
    
    // 餐廳業者註冊
    register_rest_route('byob/v1', '/register-restaurant-owner', array(
        'methods' => 'POST',
        'callback' => 'byob_register_restaurant_owner',
        'permission_callback' => '__return_true',
    ));
    
    // 獲取餐廳管理資料
    register_rest_route('byob/v1', '/restaurant-management/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'byob_get_restaurant_management_data',
        'permission_callback' => 'byob_check_restaurant_owner_permission',
    ));
    
    // 更新餐廳資料
    register_rest_route('byob/v1', '/restaurant-management/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'byob_update_restaurant_data',
        'permission_callback' => 'byob_check_restaurant_owner_permission',
    ));
}

/**
 * 生成邀請碼
 */
function byob_generate_invitation_code($restaurant_id) {
    $code = wp_generate_password(12, false);
    $expires = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    // 儲存邀請碼到資料庫
    $invitation_data = array(
        'code' => $code,
        'restaurant_id' => $restaurant_id,
        'expires' => $expires,
        'used' => false,
        'created' => current_time('mysql')
    );
    
    update_post_meta($restaurant_id, '_byob_invitation_code', $invitation_data);
    
    return $code;
}

/**
 * 驗證邀請碼（直接調用版本）
 */
function byob_verify_invitation_code_direct($code) {
    // 除錯：記錄收到的邀請碼
    error_log('BYOB: byob_verify_invitation_code_direct 收到邀請碼: ' . $code);
    
    if (empty($code)) {
        error_log('BYOB: 邀請碼為空');
        return new WP_Error('invalid_code', '邀請碼不能為空', array('status' => 400));
    }
    
    // 查詢邀請碼
    global $wpdb;
    $meta_key = '_byob_invitation_code';
    
    // 先獲取所有餐廳的邀請碼資料
    $query = $wpdb->prepare(
        "SELECT post_id, meta_value FROM {$wpdb->postmeta} 
         WHERE meta_key = %s",
        $meta_key
    );
    
    $results = $wpdb->get_results($query);
    
    if (!$results) {
        error_log('BYOB: 沒有找到任何邀請碼資料');
        return new WP_Error('invalid_code', '邀請碼無效', array('status' => 404));
    }
    
    error_log('BYOB: 找到 ' . count($results) . ' 個邀請碼記錄');
    
    // 遍歷所有結果，找到匹配的邀請碼
    foreach ($results as $result) {
        $invitation_data = maybe_unserialize($result->meta_value);
        error_log('BYOB: 檢查邀請碼記錄: ' . print_r($invitation_data, true));
        
        // 檢查邀請碼是否匹配
        if (isset($invitation_data['code']) && $invitation_data['code'] === $code) {
            error_log('BYOB: 找到匹配的邀請碼');
            
            // 檢查是否已使用
            if (isset($invitation_data['used']) && $invitation_data['used']) {
                error_log('BYOB: 邀請碼已使用');
                return new WP_Error('code_used', '邀請碼已使用', array('status' => 400));
            }
            
            // 檢查是否過期
            if (isset($invitation_data['expires']) && strtotime($invitation_data['expires']) < time()) {
                error_log('BYOB: 邀請碼已過期');
                return new WP_Error('code_expired', '邀請碼已過期', array('status' => 400));
            }
            
            // 獲取餐廳資訊
            $restaurant = get_post($result->post_id);
            if (!$restaurant || $restaurant->post_type !== 'restaurant') {
                error_log('BYOB: 餐廳不存在或類型錯誤');
                return new WP_Error('restaurant_not_found', '餐廳不存在', array('status' => 404));
            }
            
            error_log('BYOB: 邀請碼驗證成功，餐廳: ' . $restaurant->post_title);
            return array(
                'success' => true,
                'restaurant_id' => $result->post_id,
                'restaurant_name' => $restaurant->post_title,
                'invitation_code' => $code
            );
        }
    }
    
    // 如果沒有找到匹配的邀請碼
    error_log('BYOB: 沒有找到匹配的邀請碼');
    return new WP_Error('invalid_code', '邀請碼無效', array('status' => 404));
}

/**
 * 驗證邀請碼（REST API版本）
 */
function byob_verify_invitation_code($request) {
    $code = sanitize_text_field($request->get_param('code'));
    
    // 除錯：記錄收到的邀請碼
    error_log('BYOB: byob_verify_invitation_code 收到邀請碼: ' . $code);
    
    if (empty($code)) {
        error_log('BYOB: 邀請碼為空');
        return new WP_Error('invalid_code', '邀請碼不能為空', array('status' => 400));
    }
    
    // 查詢邀請碼
    global $wpdb;
    $meta_key = '_byob_invitation_code';
    
    // 先獲取所有餐廳的邀請碼資料
    $query = $wpdb->prepare(
        "SELECT post_id, meta_value FROM {$wpdb->postmeta} 
         WHERE meta_key = %s",
        $meta_key
    );
    
    $results = $wpdb->get_results($query);
    
    if (!$results) {
        return new WP_Error('invalid_code', '邀請碼無效', array('status' => 404));
    }
    
    // 遍歷所有結果，找到匹配的邀請碼
    foreach ($results as $result) {
        $invitation_data = maybe_unserialize($result->meta_value);
        
        // 檢查邀請碼是否匹配
        if (isset($invitation_data['code']) && $invitation_data['code'] === $code) {
            // 檢查是否已使用
            if (isset($invitation_data['used']) && $invitation_data['used']) {
                return new WP_Error('code_used', '邀請碼已使用', array('status' => 400));
            }
            
            // 檢查是否過期
            if (isset($invitation_data['expires']) && strtotime($invitation_data['expires']) < time()) {
                return new WP_Error('code_expired', '邀請碼已過期', array('status' => 400));
            }
            
            // 獲取餐廳資訊
            $restaurant = get_post($result->post_id);
            if (!$restaurant || $restaurant->post_type !== 'restaurant') {
                return new WP_Error('restaurant_not_found', '餐廳不存在', array('status' => 404));
            }
            
            return array(
                'success' => true,
                'restaurant_id' => $result->post_id,
                'restaurant_name' => $restaurant->post_title,
                'invitation_code' => $code
            );
        }
    }
    
    // 如果沒有找到匹配的邀請碼
    return new WP_Error('invalid_code', '邀請碼無效', array('status' => 404));
}

/**
 * 餐廳業者註冊
 */
function byob_register_restaurant_owner($request) {
    $invitation_code = sanitize_text_field($request->get_param('invitation_code'));
    $email = sanitize_email($request->get_param('email'));
    $password = $request->get_param('password');
    $restaurant_name = sanitize_text_field($request->get_param('restaurant_name'));
    
    // 驗證邀請碼
    $verification = byob_verify_invitation_code_direct($invitation_code);
    if (is_wp_error($verification)) {
        return $verification;
    }
    
    // 檢查 email 是否已存在
    $existing_user = get_user_by('email', $email);
    if ($existing_user) {
        return new WP_Error('email_exists', '此 email 已被註冊', array('status' => 400));
    }
    
    // 檢查 email 長度（作為使用者名稱）
    if (strlen($email) < 3 || strlen($email) > 50) {
        return new WP_Error('invalid_email_length', 'Email 長度必須在 3-50 字元之間', array('status' => 400));
    }
    
    // 建立使用者
    $user_data = array(
        'user_login' => $email,
        'user_email' => $email,
        'user_pass' => $password,
        'role' => 'restaurant_owner',
        'display_name' => $restaurant_name . ' 負責人'
    );
    
    $user_id = wp_insert_user($user_data);
    
    if (is_wp_error($user_id)) {
        return $user_id;
    }
    
    // 關聯餐廳與使用者
    update_post_meta($verification['restaurant_id'], '_restaurant_owner_id', $user_id);
    update_user_meta($user_id, '_owned_restaurant_id', $verification['restaurant_id']);
    
    // 標記邀請碼為已使用
    $invitation_data = get_post_meta($verification['restaurant_id'], '_byob_invitation_code', true);
    $invitation_data['used'] = true;
    $invitation_data['used_by'] = $user_id;
    $invitation_data['used_at'] = current_time('mysql');
    update_post_meta($verification['restaurant_id'], '_byob_invitation_code', $invitation_data);
    
    // 自動登入
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    return array(
        'success' => true,
        'user_id' => $user_id,
        'restaurant_id' => $verification['restaurant_id'],
        'message' => '註冊成功！'
    );
}

/**
 * 檢查餐廳業者權限
 */
function byob_check_restaurant_owner_permission($request) {
    // 檢查使用者是否已登入
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    
    // 檢查使用者是否為餐廳業者角色
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return false;
    }
    
    // 檢查使用者是否擁有該餐廳
    $restaurant_id = $request->get_param('id');
    $owner_restaurant_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    
    // 如果餐廳沒有擁有者，拒絕存取
    if (!$owner_restaurant_id) {
        return false;
    }
    
    return $owner_restaurant_id == $user_id;
}

/**
 * 獲取使用者擁有的餐廳
 */
function byob_get_user_restaurants($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    if (!$user_id) {
        return array();
    }
    
    // 查詢該使用者擁有的所有餐廳
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_restaurant_owner_id',
                'value' => $user_id,
                'compare' => '='
            )
        )
    ));
    
    return $restaurants;
}

/**
 * 檢查使用者是否可以存取餐廳
 */
function byob_can_user_access_restaurant($user_id, $restaurant_id) {
    // 檢查使用者是否為餐廳業者
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return false;
    }
    
    // 檢查使用者是否擁有該餐廳
    $owner_restaurant_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    
    return $owner_restaurant_id == $user_id;
}

/**
 * 獲取餐廳管理資料
 */
function byob_get_restaurant_management_data($request) {
    $restaurant_id = $request->get_param('id');
    $restaurant = get_post($restaurant_id);
    
    if (!$restaurant || $restaurant->post_type !== 'restaurant') {
        return new WP_Error('restaurant_not_found', '餐廳不存在', array('status' => 404));
    }
    
    // 獲取 ACF 欄位資料
    $acf_fields = array();
    if (function_exists('get_fields')) {
        $acf_fields = get_fields($restaurant_id);
    }
    
    // 獲取統計資料
    $stats = array(
        'views' => get_post_meta($restaurant_id, '_view_count', true) ?: 0,
        'favorites' => get_post_meta($restaurant_id, '_favorite_count', true) ?: 0,
        'last_updated' => get_post_meta($restaurant_id, 'last_updated', true) ?: ''
    );
    
    return array(
        'restaurant_id' => $restaurant_id,
        'restaurant_name' => $restaurant->post_title,
        'acf_fields' => $acf_fields,
        'stats' => $stats,
        'edit_url' => get_edit_post_link($restaurant_id, 'raw')
    );
}

/**
 * 更新餐廳資料
 */
function byob_update_restaurant_data($request) {
    $restaurant_id = $request->get_param('id');
    $restaurant = get_post($restaurant_id);
    
    if (!$restaurant || $restaurant->post_type !== 'restaurant') {
        return new WP_Error('restaurant_not_found', '餐廳不存在', array('status' => 404));
    }
    
    // 更新基本資料
    $post_data = array(
        'ID' => $restaurant_id,
        'post_title' => sanitize_text_field($request->get_param('restaurant_name')),
        'post_content' => sanitize_textarea_field($request->get_param('notes'))
    );
    
    $updated_post = wp_update_post($post_data);
    
    if (is_wp_error($updated_post)) {
        return $updated_post;
    }
    
    // 更新 ACF 欄位
    if (function_exists('update_field')) {
        $acf_fields = array(
            'contact_person' => sanitize_text_field($request->get_param('contact_person')),
            'address' => sanitize_textarea_field($request->get_param('address')),
            'phone' => sanitize_text_field($request->get_param('phone')),
            'website' => esc_url_raw($request->get_param('website')),
            'is_charged' => sanitize_text_field($request->get_param('is_charged')),
            'corkage_fee' => sanitize_text_field($request->get_param('corkage_fee')),
            'equipment' => $request->get_param('equipment'),
            'open_bottle_service' => sanitize_text_field($request->get_param('open_bottle_service')),
            'open_bottle_service_other_note' => sanitize_text_field($request->get_param('open_bottle_service_other_note')),
            'social_media' => sanitize_text_field($request->get_param('social_media')),
            'notes' => sanitize_textarea_field($request->get_param('notes')),
            'last_updated' => current_time('Y-m-d')
        );
        
        foreach ($acf_fields as $field_name => $field_value) {
            update_field($field_name, $field_value, $restaurant_id);
        }
    }
    
    // 更新最後修改時間
    update_post_meta($restaurant_id, 'last_updated', current_time('Y-m-d'));
    
    return array(
        'success' => true,
        'message' => '餐廳資料更新成功！',
        'restaurant_id' => $restaurant_id
    );
}

// 注意：byob_send_member_invitation_email 函數已被移除
// 改為使用 functions.php 中的 byob_send_approval_notification 函數統一發送email

/**
 * 處理邀請碼驗證
 */
function byob_handle_invitation_verification() {
    if (isset($_GET['token']) && isset($_GET['page']) && $_GET['page'] === 'register') {
        // 這裡可以加入邀請碼驗證邏輯
        // 並重定向到註冊頁面
    }
}

/**
 * 載入會員相關腳本
 */
function byob_enqueue_member_scripts() {
    if (is_page('register') || is_page('member-dashboard')) {
        wp_enqueue_script('byob-member', get_template_directory_uri() . '/js/member.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('byob-member', get_template_directory_uri() . '/css/member.css', array(), '1.0.0');
    }
}

/**
 * 限制餐廳業者後台存取
 */
function byob_restrict_admin_access() {
    // 只在後台執行
    if (!is_admin()) {
        return;
    }
    
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return;
    }
    
    // 獲取當前頁面
    $current_screen = get_current_screen();
    
    // 如果是餐廳列表頁面，限制只能看到自己的餐廳
    if ($current_screen && $current_screen->post_type === 'restaurant') {
        // 已經在 byob_restrict_restaurant_owner_access() 中處理
        return;
    }
    
    // 如果是其他頁面，檢查是否有權限
    $allowed_pages = array(
        'profile',
        'profile.php',
        'user-edit.php'
    );
    
    $current_page = $_GET['page'] ?? '';
    $current_action = $_GET['action'] ?? '';
    
    // 允許存取個人資料頁面
    if (in_array($current_page, $allowed_pages) || $current_action === 'edit') {
        return;
    }
    
    // 如果是餐廳業者，重定向到自己的儀表板
    if (!in_array($current_page, $allowed_pages)) {
        wp_redirect(admin_url('admin.php?page=restaurant-owner-dashboard'));
        exit;
    }
}

/**
 * 限制餐廳業者只能看到自己的餐廳
 */
function byob_restrict_restaurant_owner_access() {
    // 只在後台執行
    if (!is_admin()) {
        return;
    }
    
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return;
    }
    
    // 如果是餐廳業者，限制只能看到自己的餐廳
    add_action('pre_get_posts', function($query) use ($user_id) {
        if ($query->get('post_type') === 'restaurant' && is_admin()) {
            $query->set('meta_query', array(
                array(
                    'key' => '_restaurant_owner_id',
                    'value' => $user_id,
                    'compare' => '='
                )
            ));
        }
    });
}

/**
 * 審核餐廳資料
 */
function byob_review_restaurant($restaurant_id, $status, $review_notes = '') {
    $restaurant = get_post($restaurant_id);
    if (!$restaurant || $restaurant->post_type !== 'restaurant') {
        return new WP_Error('restaurant_not_found', '餐廳不存在');
    }
    
    $contact_email = get_field('email', $restaurant_id);
    $contact_person = get_field('contact_person', $restaurant_id);
    
    if ($status === 'approved') {
        // 審核通過
        $post_data = array(
            'ID' => $restaurant_id,
            'post_status' => 'publish'
        );
        
        $result = wp_update_post($post_data);
        if (is_wp_error($result)) {
            return $result;
        }
        
        // 更新審核狀態
        update_field('review_status', 'approved', $restaurant_id);
        update_field('review_date', current_time('mysql'), $restaurant_id);
        update_field('review_notes', $review_notes, $restaurant_id);
        
        // 注意：不再在此處發送email，改由文章發布時統一發送
        // 記錄審核日誌
        byob_log_review_action($restaurant_id, 'approved', $review_notes);
        
        return array(
            'success' => true,
            'message' => '餐廳已審核通過並發布',
            'invitation_code' => $invitation_data['code'] ?? null
        );
        
    } elseif ($status === 'rejected') {
        // 審核未通過
        update_field('review_status', 'rejected', $restaurant_id);
        update_field('review_date', current_time('mysql'), $restaurant_id);
        update_field('review_notes', $review_notes, $restaurant_id);
        
        // 發送審核未通過通知
        byob_send_rejection_notification($restaurant_id, $review_notes);
        
        return array(
            'success' => true,
            'message' => '餐廳審核未通過，已通知業者'
        );
    }
    
    return new WP_Error('invalid_status', '無效的審核狀態');
}

/**
 * 記錄審核操作
 */
function byob_log_review_action($restaurant_id, $action, $notes = '') {
    $log_entry = array(
        'timestamp' => current_time('mysql'),
        'restaurant_id' => $restaurant_id,
        'action' => $action,
        'notes' => $notes,
        'reviewer_id' => get_current_user_id(),
        'reviewer_name' => wp_get_current_user()->display_name
    );
    
    $logs = get_option('byob_review_logs', array());
    $logs[] = $log_entry;
    
    // 只保留最近100筆記錄
    if (count($logs) > 100) {
        $logs = array_slice($logs, -100);
    }
    
    update_option('byob_review_logs', $logs);
}

/**
 * 新增重寫規則
 */
function byob_add_rewrite_rules() {
    add_rewrite_rule(
        'register/restaurant/?$',
        'index.php?byob_restaurant_registration=1',
        'top'
    );
}

/**
 * 註冊自訂查詢變數
 */
function byob_add_query_vars($vars) {
    $vars[] = 'byob_restaurant_registration';
    return $vars;
}

/**
 * 處理餐廳註冊頁面
 */
function byob_handle_restaurant_registration_page() {
    if (get_query_var('byob_restaurant_registration')) {
        byob_display_restaurant_registration_page();
        exit;
    }
}

/**
 * 顯示餐廳註冊頁面
 */
function byob_display_restaurant_registration_page() {
    $token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
    $error_message = '';
    $success_message = '';
    $restaurant_info = null;
    
    // 如果有邀請碼，先驗證
    if ($token) {
        // 除錯：記錄邀請碼
        error_log('BYOB: 收到邀請碼: ' . $token);
        
        // 直接調用函數，不使用REST API包裝
        $verification = byob_verify_invitation_code_direct($token);
        if (!is_wp_error($verification)) {
            $restaurant_info = $verification;
            error_log('BYOB: 邀請碼驗證成功，餐廳: ' . $verification['restaurant_name']);
        } else {
            $error_message = $verification->get_error_message();
            error_log('BYOB: 邀請碼驗證失敗: ' . $error_message);
        }
    } else {
        error_log('BYOB: 沒有收到邀請碼');
    }
    
    // 處理註冊表單提交
    if ($_POST && isset($_POST['byob_restaurant_register'])) {
        // 直接處理表單資料，不使用 WP_REST_Request
        $invitation_code = sanitize_text_field($_POST['invitation_code']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $restaurant_name = sanitize_text_field($_POST['restaurant_name']);
        
        // 驗證邀請碼
        $verification = byob_verify_invitation_code_direct($invitation_code);
        if (is_wp_error($verification)) {
            $error_message = $verification->get_error_message();
        } else {
            // 檢查 email 是否已存在
            $existing_user = get_user_by('email', $email);
            if ($existing_user) {
                $error_message = '此 email 已被註冊';
            } else {
                // 檢查 email 長度（作為使用者名稱）
                if (strlen($email) < 3 || strlen($email) > 50) {
                    $error_message = 'Email 長度必須在 3-50 字元之間';
                } else {
                    // 建立使用者
                    $user_data = array(
                        'user_login' => $email,
                        'user_email' => $email,
                        'user_pass' => $password,
                        'role' => 'restaurant_owner',
                        'display_name' => $restaurant_name . ' 負責人'
                    );
                    
                    $user_id = wp_insert_user($user_data);
                    
                    if (is_wp_error($user_id)) {
                        $error_message = $user_id->get_error_message();
                    } else {
                        // 關聯餐廳與使用者
                        update_post_meta($verification['restaurant_id'], '_restaurant_owner_id', $user_id);
                        update_user_meta($user_id, '_owned_restaurant_id', $verification['restaurant_id']);
                        
                        // 標記邀請碼為已使用
                        $invitation_data = get_post_meta($verification['restaurant_id'], '_byob_invitation_code', true);
                        $invitation_data['used'] = true;
                        $invitation_data['used_by'] = $user_id;
                        $invitation_data['used_at'] = current_time('mysql');
                        update_post_meta($verification['restaurant_id'], '_byob_invitation_code', $invitation_data);
                        
                        // 自動登入
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id);
                        
                        $success_message = '註冊成功！您現在可以登入管理您的餐廳了。';
                    }
                }
            }
        }
    }
    
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>餐廳業者註冊 - <?php bloginfo('name'); ?></title>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <div class="byob-registration-page" style="max-width: 700px; margin: 50px auto; padding: 50px; background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 40px;">
                <h1 style="color: #8b2635; font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 32px; font-weight: 700; margin-bottom: 20px; text-align: center;">BYOB 餐廳業者註冊</h1>
                <p style="font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 18px; color: #666; text-align: center; line-height: 1.6;">歡迎加入 BYOB 台北餐廳地圖！</p>
            </div>
            
            <?php if ($error_message): ?>
                <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <?php echo esc_html($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <?php echo esc_html($success_message); ?>
                    <br><br>
                    <div style="text-align: center;">
                        <a href="https://byobmap.com/my-account/" style="background-color: rgba(139, 38, 53, 0.7); color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">前往登入</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($restaurant_info && !$success_message): ?>
                <div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; padding: 25px; margin-bottom: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 20px; font-weight: 600; color: #2c3e50; margin: 0 0 20px 0; text-align: center;">餐廳資訊</h3>
                    <p style="font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 16px; margin: 10px 0; color: #34495e;"><strong>餐廳名稱：</strong><?php echo esc_html($restaurant_info['restaurant_name']); ?></p>
                    <p style="font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 16px; margin: 10px 0; color: #34495e;"><strong>邀請碼：</strong><?php echo esc_html($restaurant_info['invitation_code']); ?></p>
                </div>
                
                <form method="post" style="margin-top: 20px;">
                    <input type="hidden" name="invitation_code" value="<?php echo esc_attr($token); ?>">
                    <input type="hidden" name="restaurant_name" value="<?php echo esc_attr($restaurant_info['restaurant_name']); ?>">
                    
                    <div style="margin-bottom: 15px;">
                        <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email 地址 *</label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <!-- 密碼設定區塊 -->
                    <div class="password-section" style="background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h4 style="margin: 0 0 20px 0; color: #495057; font-size: 18px; border-bottom: 2px solid #8b2635; padding-bottom: 12px; font-family: 'Microsoft JhengHei', Arial, sans-serif; font-weight: 600;">
                            🔐 密碼設定
                        </h4>
                        
                        <!-- 密碼欄位 -->
                        <div class="password-field" style="margin-bottom: 25px;">
                            <label for="password" style="display: block; margin-bottom: 10px; font-weight: 600; color: #333; font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 16px;">
                                密碼 * <span class="password-strength" id="password-strength"></span>
                            </label>
                            <div class="password-input-wrapper" style="position: relative;">
                                <input type="password" id="password" name="password" required 
                                       style="width: 100%; padding: 12px 45px 12px 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; transition: border-color 0.3s; box-sizing: border-box;">
                                <button type="button" class="toggle-password" onclick="togglePassword('password')" 
                                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    👁️
                                </button>
                            </div>
                            <div class="password-strength-bar" style="margin-top: 8px; height: 4px; background-color: #eee; border-radius: 2px; overflow: hidden;">
                                <div class="strength-fill" id="strength-fill" style="height: 100%; width: 0%; transition: width 0.3s, background-color 0.3s;"></div>
                            </div>
                        </div>
                        
                        <!-- 確認密碼欄位 -->
                        <div class="confirm-password-field" style="margin-bottom: 25px;">
                            <label for="confirm_password" style="display: block; margin-bottom: 10px; font-weight: 600; color: #333; font-family: 'Microsoft JhengHei', Arial, sans-serif; font-size: 16px;">
                                確認密碼 * <span class="match-indicator" id="match-indicator"></span>
                            </label>
                            <div class="password-input-wrapper" style="position: relative;">
                                <input type="password" id="confirm_password" name="confirm_password" required 
                                       style="width: 100%; padding: 12px 45px 12px 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; transition: border-color 0.3s; box-sizing: border-box;">
                                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')" 
                                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 18px; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    👁️
                                </button>
                            </div>
                            <div class="match-message" id="match-message" style="margin-top: 5px; font-size: 14px;"></div>
                        </div>
                        
                        <!-- 密碼規則 -->
                        <div class="password-rules" style="background-color: white; border-left: 4px solid #8b2635; padding: 20px; border-radius: 0 8px 8px 0; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
                            <h5 style="margin: 0 0 15px 0; color: #495057; font-size: 16px; font-family: 'Microsoft JhengHei', Arial, sans-serif; font-weight: 600;">📋 密碼設定規則：</h5>
                            <ul style="margin: 0; padding-left: 25px; color: #6c757d; font-size: 14px; font-family: 'Microsoft JhengHei', Arial, sans-serif; line-height: 1.8;">
                                <li>長度：至少8個字元</li>
                                <li>建議包含：大小寫字母、數字、特殊符號</li>
                                <li>避免使用：個人資訊、常見密碼</li>
                            </ul>
                        </div>
                    </div>
                    
                    <button type="submit" name="byob_restaurant_register" style="width: 100%; background-color: #8b2635; color: white; padding: 18px; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; font-family: 'Microsoft JhengHei', Arial, sans-serif; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(139, 38, 53, 0.3);">
                        完成註冊
                    </button>
                </form>
            <?php elseif (!$token): ?>
                <div style="text-align: center; padding: 40px 20px;">
                    <h3>請使用有效的邀請碼</h3>
                    <p>您需要有效的邀請碼才能註冊餐廳業者帳號。</p>
                    <p>如果您有邀請碼，請將邀請碼加入網址後方：</p>
                    <code style="background-color: #f5f5f5; padding: 10px; display: block; margin: 20px 0; border-radius: 5px;">
                        <?php echo home_url('/register/restaurant?token=您的邀請碼'); ?>
                    </code>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- JavaScript 功能 -->
        <script>
        // 密碼顯示/隱藏功能
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                button.innerHTML = '🙈';
                button.title = '隱藏密碼';
            } else {
                field.type = 'password';
                button.innerHTML = '👁️';
                button.title = '顯示密碼';
            }
        }
        
        // 密碼強度檢查
        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength += 1;
            if (/[a-z]/.test(password)) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            const strengthBar = document.getElementById('strength-fill');
            const strengthText = document.getElementById('password-strength');
            
            switch(strength) {
                case 0:
                case 1:
                    feedback = '很弱';
                    strengthBar.style.backgroundColor = '#dc3545';
                    strengthBar.style.width = '20%';
                    break;
                case 2:
                    feedback = '弱';
                    strengthBar.style.backgroundColor = '#fd7e14';
                    strengthBar.style.width = '40%';
                    break;
                case 3:
                    feedback = '中等';
                    strengthBar.style.backgroundColor = '#ffc107';
                    strengthBar.style.width = '60%';
                    break;
                case 4:
                    feedback = '強';
                    strengthBar.style.backgroundColor = '#28a745';
                    strengthBar.style.width = '80%';
                    break;
                case 5:
                    feedback = '很強';
                    strengthBar.style.backgroundColor = '#20c997';
                    strengthBar.style.width = '100%';
                    break;
            }
            
            strengthText.innerHTML = ' (' + feedback + ')';
            strengthText.style.color = strengthBar.style.backgroundColor;
        }
        
        // 密碼匹配檢查
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchIndicator = document.getElementById('match-indicator');
            const matchMessage = document.getElementById('match-message');
            const confirmField = document.getElementById('confirm_password');
            
            if (confirmPassword === '') {
                matchIndicator.innerHTML = '';
                matchMessage.innerHTML = '';
                confirmField.style.borderColor = '#ddd';
                return;
            }
            
            if (password === confirmPassword) {
                matchIndicator.innerHTML = ' ✅';
                matchIndicator.style.color = '#28a745';
                matchMessage.innerHTML = '密碼匹配！';
                matchMessage.style.color = '#28a745';
                confirmField.style.borderColor = '#28a745';
            } else {
                matchIndicator.innerHTML = ' ❌';
                matchIndicator.style.color = '#dc3545';
                matchMessage.innerHTML = '密碼不匹配';
                matchMessage.style.color = '#dc3545';
                confirmField.style.borderColor = '#dc3545';
            }
        }
        
        // 頁面載入完成後綁定事件
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            if (passwordField) {
                passwordField.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    if (confirmPasswordField.value) {
                        checkPasswordMatch();
                    }
                });
            }
            
            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', checkPasswordMatch);
            }
        });
        </script>
        
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}

// 注意：byob_send_approval_notification 函數已移動到 functions.php 中
// 此處不再重複定義

/**
 * 發送審核未通過通知
 */
function byob_send_rejection_notification($restaurant_id, $review_notes = '') {
    $restaurant = get_post($restaurant_id);
    $contact_email = get_field('email', $restaurant_id);
    $contact_person = get_field('contact_person', $restaurant_id);
    
    if (!$contact_email) {
        return false;
    }
    
    // 郵件內容
    $subject = '關於您的餐廳申請 - BYOB 台北餐廳地圖';
    
    $message = '
    <div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
        <div style="background-color: #8b2635; color: white; padding: 20px; text-align: center;">
            <h1>BYOB 台北餐廳地圖</h1>
        </div>
        
        <div style="padding: 20px; background-color: #f9f9f9;">
            <h2>親愛的 ' . ($contact_person ?: $restaurant->post_title . ' 負責人') . '，</h2>
            
            <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #721c24; margin: 0;">很抱歉，您的餐廳申請未能通過審核</h3>
            </div>
            
            <p>感謝您對 BYOB 台北餐廳地圖的支持。經過我們的審核，很抱歉您的餐廳申請目前無法通過。</p>
            
            ' . ($review_notes ? '<div style="background-color: white; padding: 15px; margin: 20px 0; border-left: 4px solid #dc3545;">
                <strong>審核意見：</strong><br>
                ' . nl2br(esc_html($review_notes)) . '
            </div>' : '') . '
            
            <p>如果您認為這是一個誤會，或者您已經解決了相關問題，歡迎重新提交申請。</p>
            
            <p>如有任何問題，請隨時聯絡我們。</p>
            
            <p>BYOB 台北餐廳地圖團隊</p>
        </div>
    </div>
    ';
    
    // 發送郵件
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sent = wp_mail($contact_email, $subject, $message, $headers);
    
    return $sent;
}

/**
 * 新增審核管理選單
 */
function byob_add_review_management_menu() {
    add_submenu_page(
        'edit.php?post_type=restaurant',
        '審核管理',
        '審核管理',
        'manage_options',
        'byob-review-management',
        'byob_review_management_page'
    );
}

/**
 * 審核管理頁面
 */
function byob_review_management_page() {
    if (isset($_POST['action']) && isset($_POST['restaurant_id'])) {
        $restaurant_id = intval($_POST['restaurant_id']);
        $action = $_POST['action'];
        $review_notes = sanitize_textarea_field($_POST['review_notes'] ?? '');
        
        if ($action === 'approve') {
            $result = byob_review_restaurant($restaurant_id, 'approved', $review_notes);
        } elseif ($action === 'reject') {
            $result = byob_review_restaurant($restaurant_id, 'rejected', $review_notes);
        }
        
        if (is_wp_error($result)) {
            echo '<div class="notice notice-error"><p>操作失敗：' . $result->get_error_message() . '</p></div>';
        } else {
            echo '<div class="notice notice-success"><p>' . $result['message'] . '</p></div>';
        }
    }
    
    ?>
    <div class="wrap">
        <h1>BYOB 餐廳審核管理</h1>
        
        <h2>待審核餐廳</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>餐廳名稱</th>
                    <th>聯絡人</th>
                    <th>Email</th>
                    <th>提交日期</th>
                    <th>狀態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $pending_restaurants = get_posts(array(
                    'post_type' => 'restaurant',
                    'numberposts' => -1,
                    'post_status' => 'draft',
                    'meta_query' => array(
                        array(
                            'key' => 'review_status',
                            'value' => 'pending',
                            'compare' => '='
                        )
                    )
                ));
                
                if (empty($pending_restaurants)) {
                    echo '<tr><td colspan="6">目前沒有待審核的餐廳</td></tr>';
                } else {
                    foreach ($pending_restaurants as $restaurant) {
                        $contact_person = get_field('contact_person', $restaurant->ID);
                        $email = get_field('email', $restaurant->ID);
                        $submitted_date = get_field('submitted_date', $restaurant->ID);
                        
                        echo '<tr>';
                        echo '<td><a href="' . get_edit_post_link($restaurant->ID) . '">' . $restaurant->post_title . '</a></td>';
                        echo '<td>' . ($contact_person ?: '未填寫') . '</td>';
                        echo '<td>' . ($email ?: '未填寫') . '</td>';
                        echo '<td>' . ($submitted_date ? date('Y-m-d H:i', strtotime($submitted_date)) : '未知') . '</td>';
                        echo '<td><span style="color: orange;">待審核</span></td>';
                        echo '<td>';
                        echo '<form method="post" style="display: inline;">';
                        echo '<input type="hidden" name="restaurant_id" value="' . $restaurant->ID . '">';
                        echo '<input type="hidden" name="action" value="approve">';
                        echo '<textarea name="review_notes" placeholder="審核意見（可選）" style="width: 200px; height: 60px;"></textarea><br>';
                        echo '<button type="submit" class="button button-primary" onclick="return confirm(\'確定要通過審核嗎？\')">通過審核</button> ';
                        echo '</form>';
                        
                        echo '<form method="post" style="display: inline;">';
                        echo '<input type="hidden" name="restaurant_id" value="' . $restaurant->ID . '">';
                        echo '<input type="hidden" name="action" value="reject">';
                        echo '<textarea name="review_notes" placeholder="拒絕原因（必填）" style="width: 200px; height: 60px;" required></textarea><br>';
                        echo '<button type="submit" class="button button-secondary" onclick="return confirm(\'確定要拒絕審核嗎？\')">拒絕審核</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * 會員管理頁面
 */
function byob_member_management_page() {
    ?>
    <div class="wrap">
        <h1>BYOB 會員管理</h1>
        
        <h2>餐廳業者會員</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>餐廳名稱</th>
                    <th>聯絡人</th>
                    <th>Email</th>
                    <th>會員狀態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $restaurants = get_posts(array(
                    'post_type' => 'restaurant',
                    'numberposts' => -1,
                    'post_status' => 'publish'
                ));
                
                foreach ($restaurants as $restaurant) {
                    $owner_id = get_post_meta($restaurant->ID, '_restaurant_owner_id', true);
                    $contact_person = get_field('contact_person', $restaurant->ID);
                    $email = get_field('email', $restaurant->ID);
                    
                    if ($owner_id) {
                        $owner = get_userdata($owner_id);
                        $member_status = '已註冊';
                        $action = '<a href="' . admin_url('user-edit.php?user_id=' . $owner_id) . '">查看會員資料</a>';
                    } else {
                        $member_status = '未註冊';
                        $action = '<button onclick="sendInvitation(' . $restaurant->ID . ')">發送邀請</button>';
                    }
                    
                    echo '<tr>';
                    echo '<td>' . $restaurant->post_title . '</td>';
                    echo '<td>' . ($contact_person ?: '未填寫') . '</td>';
                    echo '<td>' . ($email ?: '未填寫') . '</td>';
                    echo '<td>' . $member_status . '</td>';
                    echo '<td>' . $action . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <script>
    function sendInvitation(restaurantId) {
        if (confirm('確定要發送會員邀請郵件嗎？')) {
            // 顯示載入狀態
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = '發送中...';
            button.disabled = true;
            
            // 發送AJAX請求
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'byob_send_invitation',
                    'restaurant_id': restaurantId,
                    'nonce': '<?php echo wp_create_nonce('byob_send_invitation'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('邀請郵件已成功發送！');
                    // 可以選擇重新載入頁面或更新按鈕狀態
                    location.reload();
                } else {
                    alert('發送失敗：' + (data.message || '未知錯誤'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('發送失敗，請稍後再試');
            })
            .finally(() => {
                button.textContent = originalText;
                button.disabled = false;
            });
        }
    }
    </script>
    <?php
}

/**
 * 新增管理員選單
 */
function byob_add_member_management_menu() {
    add_submenu_page(
        'edit.php?post_type=restaurant',
        '會員管理',
        '會員管理',
        'manage_options',
        'byob-member-management',
        'byob_member_management_page'
    );
}

/**
 * 餐廳業者儀表板頁面
 */
function byob_restaurant_owner_dashboard() {
    // 檢查使用者是否已登入且為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_redirect(wp_login_url(get_permalink()));
        exit;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        wp_die('權限不足，只有餐廳業者才能存取此頁面。');
    }
    
    // 獲取使用者擁有的餐廳
    $user_restaurants = byob_get_user_restaurants($user_id);
    
    if (empty($user_restaurants)) {
        echo '<div class="wrap">';
        echo '<h1>餐廳業者儀表板</h1>';
        echo '<p>您目前沒有關聯的餐廳。請聯絡管理員。</p>';
        echo '</div>';
        return;
    }
    
    echo '<div class="wrap">';
    echo '<h1>餐廳業者儀表板</h1>';
    echo '<p>歡迎，' . esc_html($user->display_name) . '！</p>';
    
    echo '<h2>您的餐廳</h2>';
    echo '<div class="restaurant-list">';
    
    foreach ($user_restaurants as $restaurant) {
        echo '<div class="restaurant-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;">';
        echo '<h3>' . esc_html($restaurant->post_title) . '</h3>';
        echo '<p><strong>地址：</strong>' . esc_html(get_field('address', $restaurant->ID)) . '</p>';
        echo '<p><strong>電話：</strong>' . esc_html(get_field('phone', $restaurant->ID)) . '</p>';
        echo '<p><strong>狀態：</strong>已上架</p>';
        echo '<div class="restaurant-actions">';
        echo '<a href="' . admin_url('post.php?post=' . $restaurant->ID . '&action=edit') . '" class="button">編輯餐廳資料</a> ';
        echo '<a href="' . get_permalink($restaurant->ID) . '" class="button" target="_blank">查看餐廳頁面</a> ';
        echo '<a href="' . get_permalink($restaurant->ID) . '" class="button" target="_blank" style="background-color: rgba(139, 38, 53, 0.8); color: white;">👁️ 預覽餐廳</a>';
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
}



/**
 * 餐廳資料編輯頁面內容
 */
function byob_restaurant_profile_content() {
    // 檢查使用者是否為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        echo '<p>請先登入。</p>';
        return;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        echo '<p>權限不足，只有餐廳業者才能存取此頁面。</p>';
        return;
    }
    
    // 獲取使用者擁有的餐廳
    $user_restaurants = byob_get_user_restaurants($user_id);
    if (empty($user_restaurants)) {
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px;">';
        echo '<h3>⚠️ 注意</h3>';
        echo '<p>您目前沒有關聯的餐廳。請聯絡管理員。</p>';
        echo '</div>';
        return;
    }
    
    $restaurant = $user_restaurants[0]; // 取第一個餐廳
    $restaurant_id = $restaurant->ID;
    
    // 預覽餐廳按鈕
    echo '<div style="text-align: right; margin-bottom: 20px;">';
    echo '<a href="' . get_permalink($restaurant_id) . '" class="button" target="_blank" style="background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 10px 20px; font-size: 14px; display: inline-block; text-decoration: none; color: white; border: none;">👁️ 預覽餐廳</a>';
    echo '</div>';
    
    // 獲取當前餐廳資料
    $current_logo_id = get_post_meta($restaurant_id, '_restaurant_logo', true);
    $current_logo_url = $current_logo_id ? wp_get_attachment_image_url($current_logo_id, 'thumbnail') : '';
    
    // 處理表單提交
    if ($_POST['action'] === 'update_restaurant_profile') {
        byob_handle_restaurant_profile_submit($restaurant_id);
    }
    
    echo '<div class="restaurant-profile" style="max-width: 800px;">';
    echo '<h2>餐廳資料編輯</h2>';
    echo '<p>編輯您的餐廳基本資料和 LOGO。</p>';
    
    // 預覽餐廳按鈕
    echo '<div style="text-align: right; margin-bottom: 20px;">';
    echo '<a href="' . get_permalink($restaurant_id) . '" class="button" target="_blank" style="background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 10px 20px; font-size: 14px; display: inline-block; text-decoration: none; color: white; border: none;">👁️ 預覽餐廳</a>';
    echo '</div>';
    
    // 顯示成功/失敗訊息
    if (isset($_GET['message'])) {
        $message = sanitize_text_field($_GET['message']);
        if ($message === 'success') {
            echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
            echo '✅ 餐廳資料更新成功！';
            echo '</div>';
        } elseif ($message === 'error') {
            echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
            echo '❌ 更新失敗，請檢查輸入資料。';
            echo '</div>';
        }
    }
    
    echo '<form method="post" enctype="multipart/form-data" style="background: #f9f9f9; padding: 25px; border-radius: 8px;">';
    echo '<input type="hidden" name="action" value="update_restaurant_profile">';
    echo '<input type="hidden" name="restaurant_id" value="' . esc_attr($restaurant_id) . '">';
    
    // 餐廳基本資料
    echo '<div style="margin-bottom: 25px;">';
    echo '<h3 style="color: #333; border-bottom: 2px solid #8b2635; padding-bottom: 10px;">基本資料</h3>';
    
    // 餐廳名稱
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="restaurant_name" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">餐廳名稱 *</label>';
    echo '<input type="text" id="restaurant_name" name="restaurant_name" value="' . esc_attr($restaurant->post_title) . '" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">';
    echo '</div>';
    
    // 餐廳描述
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="restaurant_description" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">餐廳描述</label>';
    echo '<textarea id="restaurant_description" name="restaurant_description" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">' . esc_textarea($restaurant->post_content) . '</textarea>';
    echo '</div>';
    
    // 聯絡電話
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="restaurant_phone" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">聯絡電話</label>';
    echo '<input type="tel" id="restaurant_phone" name="restaurant_phone" value="' . esc_attr(get_field('phone', $restaurant_id)) . '" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">';
    echo '</div>';
    
    // 地址
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="restaurant_address" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">地址</label>';
    echo '<textarea id="restaurant_address" name="restaurant_address" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">' . esc_textarea(get_field('address', $restaurant_id)) . '</textarea>';
    echo '</div>';
    
    // 營業時間
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="business_hours" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">營業時間</label>';
    echo '<textarea id="business_hours" name="business_hours" rows="3" placeholder="例：週一至週五 11:00-22:00，週六日 10:00-23:00" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">' . esc_textarea(get_field('business_hours', $restaurant_id)) . '</textarea>';
    echo '</div>';
    
    echo '</div>';
    
    // LOGO 上傳
    echo '<div style="margin-bottom: 25px;">';
    echo '<h3 style="color: #333; border-bottom: 2px solid #8b2635; padding-bottom: 10px;">餐廳 LOGO</h3>';
    
    // 顯示當前 LOGO
    if ($current_logo_url) {
        echo '<div style="margin-bottom: 20px;">';
        echo '<p style="font-weight: bold; margin-bottom: 10px;">當前 LOGO：</p>';
        echo '<img src="' . esc_url($current_logo_url) . '" alt="當前 LOGO" style="max-width: 150px; max-height: 150px; border: 2px solid #ddd; border-radius: 5px;">';
        echo '</div>';
    }
    
    // LOGO 上傳欄位
    echo '<div style="margin-bottom: 20px;">';
    echo '<label for="restaurant_logo" style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">上傳新 LOGO</label>';
    echo '<input type="file" id="restaurant_logo" name="restaurant_logo" accept="image/jpeg,image/png,image/gif" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px;">';
    echo '<p style="font-size: 14px; color: #666; margin-top: 5px;">支援格式：JPG、PNG、GIF，檔案大小限制 2MB</p>';
    echo '</div>';
    
    echo '</div>';
    
    // 提交按鈕
    echo '<div style="text-align: center;">';
    echo '<button type="submit" style="background-color: rgba(139, 38, 53, 0.8); color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">更新餐廳資料</button>';
    echo '</div>';
    
    echo '</form>';
    echo '</div>';
}

/**
 * 照片管理頁面內容
 */
function byob_restaurant_photos_content() {
    // 檢查使用者是否為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        echo '<p>請先登入。</p>';
        return;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        echo '<p>權限不足，只有餐廳業者才能存取此頁面。</p>';
        return;
    }
    
    // 獲取使用者擁有的餐廳
    $user_restaurants = byob_get_user_restaurants($user_id);
    if (empty($user_restaurants)) {
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px;">';
        echo '<h3>⚠️ 注意</h3>';
        echo '<p>您目前沒有關聯的餐廳。請聯絡管理員。</p>';
        echo '</div>';
        return;
    }
    
    $restaurant = $user_restaurants[0]; // 取第一個餐廳
    $restaurant_id = $restaurant->ID;
    
    // 處理照片上傳
    if ($_POST['action'] === 'upload_photos') {
        if (isset($_FILES['photos']) && $_FILES['photos']['error'][0] === UPLOAD_ERR_OK) {
            // 檢查檔案類型
            $file_type = wp_check_filetype($_FILES['photos']['name'][0]);
            
            if (in_array($file_type['type'], array('image/jpeg', 'image/png', 'image/webp'))) {
                // 準備檔案上傳參數
                $file = array(
                    'name' => $_FILES['photos']['name'][0],
                    'type' => $_FILES['photos']['type'][0],
                    'tmp_name' => $_FILES['photos']['tmp_name'][0],
                    'error' => $_FILES['photos']['error'][0],
                    'size' => $_FILES['photos']['size'][0]
                );
                
                // 上傳檔案
                $upload = wp_handle_upload($file, array('test_form' => false));
                
                if (isset($upload['error'])) {
                    echo '<div class="error" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">檔案上傳失敗：' . esc_html($upload['error']) . '</div>';
                } else {
                    // 建立附件
                    $attachment = array(
                        'post_mime_type' => $upload['type'],
                        'post_title' => sanitize_file_name($file['name']),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    
                    $attachment_id = wp_insert_attachment($attachment, $upload['file'], $restaurant_id);
                    
                    if (is_wp_error($attachment_id)) {
                        echo '<div class="error" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">建立附件失敗：' . esc_html($attachment_id->get_error_message()) . '</div>';
                    } else {
                        // 新增到餐廳照片欄位
                        $new_photo = array(
                            'photo' => $attachment_id,
                            'description' => sanitize_text_field($_POST['photo_description'] ?? '')
                        );
                        
                        // 找到第一個空的群組欄位
                        $photo_1 = get_field('restaurant_photo_1', $restaurant_id);
                        $photo_2 = get_field('restaurant_photo_2', $restaurant_id);
                        $photo_3 = get_field('restaurant_photo_3', $restaurant_id);
                        
                        if (!$photo_1 || empty($photo_1['photo'])) {
                            update_field('restaurant_photo_1', $new_photo, $restaurant_id);
                        } elseif (!$photo_2 || empty($photo_2['photo'])) {
                            update_field('restaurant_photo_2', $new_photo, $restaurant_id);
                        } elseif (!$photo_3 || empty($photo_3['photo'])) {
                            update_field('restaurant_photo_3', $new_photo, $restaurant_id);
                        }
                        
                        echo '<div class="success" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">照片上傳成功！</div>';
                    }
                }
            } else {
                echo '<div class="error" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">檔案類型不支援：' . esc_html($file_type['type']) . '</div>';
            }
        } else {
            echo '<div class="error" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">檔案上傳失敗或未選擇檔案</div>';
        }
    }
    
    // 處理照片刪除
    if ($_POST['action'] === 'delete_photo') {
        $photo_id = intval($_POST['photo_id']);
        $result = byob_delete_restaurant_photo($restaurant_id, $photo_id);
        if (is_wp_error($result)) {
            echo '<div class="error" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">' . $result->get_error_message() . '</div>';
        } else {
            echo '<div class="success" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">照片刪除成功！</div>';
        }
    }
    
    // 處理照片說明更新
    if ($_POST['action'] === 'update_photo_description') {
        $photo_id = intval($_POST['photo_id']);
        $new_description = sanitize_text_field($_POST['photo_description'] ?? '');
        
        $result = byob_update_photo_description($restaurant_id, $photo_id, $new_description);
        if (is_wp_error($result)) {
            echo '<div class="error" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">' . $result->get_error_message() . '</div>';
        } else {
            echo '<div class="success" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0;">照片說明更新成功！</div>';
        }
    }
    
    // 獲取現有照片（讀取三個群組欄位）
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
    
    echo '<div class="restaurant-photos-management" style="max-width: 800px;">';
    echo '<h2>餐廳照片管理</h2>';
    
    // 預覽餐廳按鈕
    echo '<div style="text-align: center; margin: 20px 0; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px;">';
    echo '<a href="' . get_permalink($restaurant_id) . '" target="_blank" style="background-color: #8b2635; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">👁️ 預覽餐廳</a>';
    echo '<p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">點擊按鈕在新分頁中預覽您的餐廳前台頁面</p>';
    echo '</div>';
    
    // 照片上傳區域（永久顯示）
    echo '<div class="photo-upload-section" style="background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 8px;">';
    echo '<h3>上傳照片</h3>';
    
    // 上傳須知（永久顯示）
    echo '<div class="upload-instructions" style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">';
    echo '<p><strong>上傳須知：</strong></p>';
    echo '<ul style="margin: 10px 0; padding-left: 20px;">';
    echo '<li>最多可上傳 ' . $max_photos . ' 張照片</li>';
    echo '<li>建議上傳餐廳環境、用餐區域等代表性照片（建議尺寸：1200x800 像素）</li>';
    echo '<li>可加註照片說明</li>';
    echo '<li>支援 JPG、PNG、WebP 格式，單張檔案大小不超過 2MB</li>';
    echo '<li>照片排序：最晚上傳的照片會顯示在最前面</li>';
    echo '</ul>';
    echo '</div>';
    
    if ($can_upload) {
        // 可以上傳時顯示表單
        echo '<form method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="action" value="upload_photos">';
        
        echo '<div class="photo-upload-fields">';
        echo '<div class="photo-field" style="margin: 15px 0;">';
        echo '<label style="display: block; margin-bottom: 5px; font-weight: bold;">選擇照片：</label>';
        echo '<input type="file" name="photos[]" accept="image/*" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        echo '</div>';
        echo '<div class="photo-field" style="margin: 15px 0;">';
        echo '<label style="display: block; margin-bottom: 5px; font-weight: bold;">照片說明（選填）：</label>';
        echo '<input type="text" name="photo_description" placeholder="例如：餐廳用餐區域" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        echo '</div>';
        echo '</div>';
        
        echo '<button type="submit" class="upload-button" style="background: rgba(139, 38, 53, 0.7); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">上傳照片</button>';
        echo '</form>';
    } else {
        // 已達上限時顯示狀態訊息
        echo '<div class="upload-limit-reached" style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; color: #0066cc; margin: 10px 0; display: flex; align-items: center;">';
        echo '<p style="margin: 0;"><strong>目前狀態：</strong>您已達到照片數量上限（' . $max_photos . '張）。如需上傳新照片，請先刪除現有照片。</p>';
        echo '</div>';
    }
    echo '</div>';
    
    // 現有照片管理
    echo '<div class="existing-photos-section" style="background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 8px;">';
    echo '<h3>現有照片（' . $photo_count . '/' . $max_photos . '）</h3>';
    
    // 自動清理無效照片
    byob_cleanup_invalid_photos($restaurant_id);
    
    if (empty($existing_photos)) {
        echo '<p>目前還沒有上傳任何照片。</p>';
    } else {
        echo '<div class="photos-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 300px)); gap: 20px; margin-top: 20px; justify-content: start;">';
        foreach ($existing_photos as $index => $photo) {
            // 統一處理圖片資料結構，使用新的輔助函數
            $attachment_id = byob_get_photo_id($photo);
            $image_data = null;
            
            if ($attachment_id) {
                // 檢查照片是否有效
                if (byob_is_photo_valid($attachment_id)) {
                    $image_data = array(
                        'ID' => $attachment_id,
                        'sizes' => array(),
                        'url' => wp_get_attachment_url($attachment_id)
                    );
                } else {
                    // 照片無效，標記為需要清理
                    $attachment_id = null;
                }
            }
            
            // 獲取圖片 URL
            $image_url = '';
            if (isset($image_data['sizes']['thumbnail']) && !empty($image_data['sizes']['thumbnail'])) {
                $image_url = $image_data['sizes']['thumbnail'];
            } elseif (isset($image_data['sizes']['medium']) && !empty($image_data['sizes']['medium'])) {
                $image_url = $image_data['sizes']['medium'];
            } elseif (isset($image_data['url']) && !empty($image_data['url'])) {
                $image_url = $image_data['url'];
            } else {
                // 如果都沒有，嘗試從附件 ID 獲取
                $image_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                if (!$image_url) {
                    $image_url = wp_get_attachment_image_url($attachment_id, 'medium');
                }
                if (!$image_url) {
                    $image_url = wp_get_attachment_url($attachment_id);
                }
            }
            
            // 只顯示有效的照片
            if ($attachment_id && $image_data) {
                echo '<div class="photo-item" style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                echo '<div class="photo-preview">';
                if ($image_url) {
                    echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($photo['description'] ?: '餐廳照片') . '" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px;">';
                } else {
                    echo '<div style="width: 100%; height: 150px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px; color: #666;">圖片載入失敗</div>';
                }
                echo '</div>';
                echo '<div class="photo-info" style="margin-top: 10px;">';
                echo '<form method="post" class="photo-description-form">';
                echo '<input type="hidden" name="action" value="update_photo_description">';
                echo '<input type="hidden" name="photo_id" value="' . $attachment_id . '">';
                echo '<div class="description-field" style="margin: 10px 0;">';
                echo '<label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 12px;">照片說明：</label>';
                echo '<input type="text" name="photo_description" value="' . esc_attr($photo['description'] ?: '') . '" placeholder="例如：餐廳用餐區域" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">';
                echo '</div>';
                echo '<div class="photo-actions" style="display: flex; gap: 10px;">';
                echo '<button type="submit" class="save-button" style="background: #46b450; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">儲存照片說明</button>';
                echo '</div>';
                echo '</form>';
                echo '<form method="post" class="delete-photo-form" style="margin-top: 10px;">';
                echo '<input type="hidden" name="action" value="delete_photo">';
                echo '<input type="hidden" name="photo_id" value="' . $attachment_id . '">';
                echo '<button type="submit" class="delete-button" onclick="return confirm(\'確定要刪除這張照片嗎？\')" style="background: #dc3232; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">刪除照片</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}

/**
 * 菜單管理頁面內容
 */
function byob_restaurant_menu_content() {
    echo '<div class="restaurant-menu">';
    echo '<h2>菜單管理</h2>';
    echo '<p>此功能正在開發中...</p>';
    echo '</div>';
}

/**
 * 新增餐廳業者儀表板選單
 */
function byob_add_restaurant_owner_menu() {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return;
    }
    
    // 新增餐廳業者儀表板選單
    add_menu_page(
        '餐廳管理',
        '餐廳管理',
        'read',
        'restaurant-owner-dashboard',
        'byob_restaurant_owner_dashboard',
        'dashicons-store',
        30
    );
}

/**
 * 自定義 WooCommerce 會員選單
 */
function byob_customize_woocommerce_menu() {
    // 註冊自定義端點
    add_rewrite_endpoint('restaurant-profile', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('restaurant-photos', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('restaurant-menu', EP_ROOT | EP_PAGES);
}

/**
 * 自定義帳戶選單項目
 */
function byob_customize_account_menu_items($menu_items) {
    // 檢查使用者是否為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        return $menu_items;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return $menu_items;
    }
    
    // 完全重新定義選單項目，強制覆蓋所有其他外掛的修改
    $new_menu_items = array();
    
    // 只保留我們需要的選單項目，使用固定的順序
    $new_menu_items['dashboard'] = '控制台';
    $new_menu_items['restaurant-profile'] = '餐廳資料編輯';
    $new_menu_items['restaurant-photos'] = '照片管理';
    $new_menu_items['restaurant-menu'] = '菜單管理';
    // 不添加 customer-logout，讓 WooCommerce 自動處理
    
    return $new_menu_items;
}

/**
 * 覆蓋預設 WooCommerce 帳戶儀表板內容
 */
function byob_override_dashboard_content() {
    // 檢查使用者是否已登入且為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        return;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return;
    }
    
    // 清除 WooCommerce 預設的控制台內容
    ob_clean();
    
    // 獲取使用者擁有的餐廳
    $user_restaurants = byob_get_user_restaurants($user_id);
    
    echo '<div class="restaurant-dashboard-main">';
    echo '<h2>餐廳業者控制台</h2>';
    echo '<p>歡迎，' . esc_html($user->display_name) . '！</p>';
    
    if (!empty($user_restaurants)) {
        $restaurant = $user_restaurants[0]; // 取第一個餐廳
        echo '<div class="restaurant-overview-main" style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0;">';
        echo '<h3>餐廳概覽</h3>';
        echo '<p><strong>餐廳名稱：</strong>' . esc_html($restaurant->post_title) . '</p>';
        echo '<p><strong>狀態：</strong>已上架</p>';
        echo '<p><strong>最後更新：</strong>' . get_the_modified_date('Y-m-d H:i', $restaurant->ID) . '</p>';
        echo '</div>';
        
        echo '<div class="quick-actions-main" style="margin: 20px 0;">';
        echo '<h3>快速操作</h3>';
        echo '<a href="' . wc_get_account_endpoint_url('restaurant-profile') . '" class="button" style="margin-right: 10px; background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 12px 20px; font-size: 16px; display: inline-block; text-decoration: none; color: white;">編輯餐廳資料</a> ';
        echo '<a href="' . wc_get_account_endpoint_url('restaurant-photos') . '" class="button" style="margin-right: 10px; background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 12px 20px; font-size: 16px; display: inline-block; text-decoration: none; color: white;">管理照片</a> ';
        echo '<a href="' . wc_get_account_endpoint_url('restaurant-menu') . '" class="button" style="margin-right: 10px; background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 12px 20px; font-size: 16px; display: inline-block; text-decoration: none; color: white;">管理菜單</a> ';
        echo '<a href="' . get_permalink($restaurant->ID) . '" class="button" target="_blank" style="background-color: rgba(139, 38, 53, 0.8); border-radius: 5px; padding: 12px 20px; font-size: 16px; display: inline-block; text-decoration: none; color: white;">預覽餐廳</a>';
        echo '</div>';
        
        echo '<div class="restaurant-stats-main" style="background: #f9f9f9; padding: 20px; border-radius: 5px;">';
        echo '<h3>統計資訊</h3>';
        echo '<p><strong>餐廳頁面訪問次數：</strong>統計中...</p>';
        echo '<p><strong>評論數量：</strong>0</p>';
        echo '<p><strong>照片數量：</strong>0</p>';
        echo '</div>';
    } else {
        echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px; margin: 20px 0;">';
        echo '<h3>⚠️ 注意</h3>';
        echo '<p>您目前沒有關聯的餐廳。這可能是因為：</p>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li>餐廳資料尚未建立</li>';
        echo '<li>餐廳與您的帳號尚未關聯</li>';
        echo '<li>餐廳狀態不是「已上架」</li>';
        echo '</ul>';
        echo '<p>請聯絡管理員協助處理。</p>';
        echo '</div>';
    }
    echo '</div>';
}

/**
 * 處理餐廳資料提交
 */
function byob_handle_restaurant_profile_submit($restaurant_id) {
    // 檢查權限
    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    // 驗證餐廳所有權
    $owner_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    if ($owner_id != $user_id) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    // 驗證必填欄位
    if (empty($_POST['restaurant_name'])) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    // 驗證新增的必填欄位
    if (empty($_POST['district'])) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    if (empty($_POST['contact_email'])) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    // 地址驗證
    if (!empty($_POST['restaurant_address'])) {
        $address = $_POST['restaurant_address'];
        $address_errors = array();
        
        // 縣市驗證：必須包含「市」、「縣」等關鍵字
        if (!preg_match('/(市|縣)/', $address)) {
            $address_errors[] = '缺少縣市資訊（如：台北市、新北市、桃園市等）';
        }
        
        // 行政區驗證：必須包含「區」等關鍵字
        if (!preg_match('/區/', $address)) {
            $address_errors[] = '缺少行政區資訊（如：信義區、大安區等）';
        }
        
        // 路街驗證：必須包含「路」、「街」、「道」等關鍵字
        if (!preg_match('/(路|街|道)/', $address)) {
            $address_errors[] = '缺少路街資訊（如：信義路、忠孝東路等）';
        }
        
        // 門牌驗證：必須包含數字
        if (!preg_match('/\d/', $address)) {
            $address_errors[] = '缺少門牌號碼';
        }
        
        // 如果有地址錯誤，重導向並顯示錯誤訊息
        if (!empty($address_errors)) {
            $error_message = 'address_validation_error';
            wp_redirect(add_query_arg(array('message' => $error_message, 'address_errors' => urlencode(implode('|', $address_errors))), wc_get_account_endpoint_url('restaurant-profile')));
            exit;
        }
    }
    
    // 更新餐廳基本資料
    $post_data = array(
        'ID' => $restaurant_id,
        'post_title' => sanitize_text_field($_POST['restaurant_name'])
    );
    
    $updated_post = wp_update_post($post_data);
    
    if (is_wp_error($updated_post)) {
        wp_redirect(add_query_arg('message', 'error', wc_get_account_endpoint_url('restaurant-profile')));
        exit;
    }
    
    // 更新 ACF 欄位
    if (function_exists('update_field')) {
        // 基本資料欄位
        update_field('phone', sanitize_text_field($_POST['restaurant_phone']), $restaurant_id);
        
        // 聯絡人姓名使用當前用戶的顯示名稱
        $current_user = wp_get_current_user();
        update_field('contact_person', sanitize_text_field($current_user->display_name), $restaurant_id);
        
        update_field('district', sanitize_text_field($_POST['district']), $restaurant_id);
        update_field('address', sanitize_textarea_field($_POST['restaurant_address']), $restaurant_id);
        update_field('business_hours', sanitize_textarea_field($_POST['business_hours']), $restaurant_id);
        
        // 新增欄位：餐廳類型（核取方塊陣列）
        if (isset($_POST['restaurant_type']) && is_array($_POST['restaurant_type'])) {
            $restaurant_types = array_map('sanitize_text_field', $_POST['restaurant_type']);
            update_field('restaurant_type', $restaurant_types, $restaurant_id);
        }
        
        // 新增欄位：其他類型說明（單行文字）
        if (isset($_POST['restaurant_type_other_note'])) {
            update_field('restaurant_type_other_note', sanitize_text_field($_POST['restaurant_type_other_note']), $restaurant_id);
        }
        
        // 新增欄位：是否收開瓶費（選項按鈕）
        if (isset($_POST['is_charged'])) {
            update_field('is_charged', sanitize_text_field($_POST['is_charged']), $restaurant_id);
        }
        
        // 新增欄位：開瓶費金額（數值）
        if (isset($_POST['corkage_fee_amount'])) {
            update_field('corkage_fee_amount', intval($_POST['corkage_fee_amount']), $restaurant_id);
        }
        
        // 新增欄位：開瓶費其他說明（單行文字）
        if (isset($_POST['corkage_fee_note'])) {
            update_field('corkage_fee_note', sanitize_text_field($_POST['corkage_fee_note']), $restaurant_id);
        }
        
        // 新增欄位：酒器設備（核取方塊陣列）
        if (isset($_POST['equipment']) && is_array($_POST['equipment'])) {
            $equipment = array_map('sanitize_text_field', $_POST['equipment']);
            update_field('equipment', $equipment, $restaurant_id);
        }
        
        // 新增欄位：開酒服務（選取）
        if (isset($_POST['open_bottle_service'])) {
            update_field('open_bottle_service', sanitize_text_field($_POST['open_bottle_service']), $restaurant_id);
        }
        
        // 新增欄位：開酒服務其他說明（單行文字）
        if (isset($_POST['open_bottle_service_other_note'])) {
            update_field('open_bottle_service_other_note', sanitize_text_field($_POST['open_bottle_service_other_note']), $restaurant_id);
        }
        
        // 新增欄位：官方網站（URL）
        if (isset($_POST['website'])) {
            update_field('website', esc_url_raw($_POST['website']), $restaurant_id);
        }
        
        // 新增欄位：社群連結（URL）
        if (isset($_POST['social_links'])) {
            update_field('social_links', esc_url_raw($_POST['social_links']), $restaurant_id);
        }
        
        // 新增欄位：備註說明（多行文字）
        if (isset($_POST['notes'])) {
            update_field('notes', sanitize_textarea_field($_POST['notes']), $restaurant_id);
        }
        
        // 新增欄位：聯絡人Email
        if (isset($_POST['contact_email'])) {
            update_field('email', sanitize_email($_POST['contact_email']), $restaurant_id);
        }
    }
    
    // 處理 LOGO 上傳
    if (!empty($_FILES['restaurant_logo']['name'])) {
        $logo_result = byob_handle_logo_upload($restaurant_id);
        if (is_wp_error($logo_result)) {
            // LOGO 上傳失敗，但基本資料已更新
            wp_redirect(add_query_arg('message', 'partial_success', wc_get_account_endpoint_url('restaurant-profile')));
            exit;
        }
    }
    
    // 重導向到成功頁面
    wp_redirect(add_query_arg('message', 'success', wc_get_account_endpoint_url('restaurant-profile')));
    exit;
}

/**
 * 處理 LOGO 上傳
 */
function byob_handle_logo_upload($restaurant_id) {
    // 檢查是否有檔案上傳
    if (!isset($_FILES['restaurant_logo']) || $_FILES['restaurant_logo']['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', '檔案上傳失敗');
    }
    
    $file = $_FILES['restaurant_logo'];
    
    // 驗證檔案類型
    $allowed_types = array('image/jpeg', 'image/png', 'image/webp', 'image/bmp', 'image/tiff');
    if (!in_array($file['type'], $allowed_types)) {
        return new WP_Error('invalid_type', '只支援 JPG、PNG 及其他常見圖片格式');
    }
    
    // 驗證檔案大小（1MB）
    if ($file['size'] > 1 * 1024 * 1024) {
        return new WP_Error('file_too_large', '檔案大小不能超過 1MB');
    }
    
    // 準備上傳參數
    $upload_overrides = array(
        'test_form' => false,
        'mimes' => array(
            'jpg|jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'tiff|tif' => 'image/tiff'
        )
    );
    
    // 處理檔案上傳
    $uploaded_file = wp_handle_upload($file, $upload_overrides);
    
    if (isset($uploaded_file['error'])) {
        return new WP_Error('upload_failed', $uploaded_file['error']);
    }
    
    // 準備附件資料
    $attachment = array(
        'post_mime_type' => $uploaded_file['type'],
        'post_title' => sanitize_file_name($file['name']),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    // 插入附件到媒體庫
    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file'], $restaurant_id);
    
    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }
    
    // 生成縮圖 - 禁用自動裁切，保持原始比例
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // 自定義圖片尺寸，避免裁切
    add_filter('wp_image_editors', function($editors) {
        return array('WP_Image_Editor_GD', 'WP_Image_Editor_Imagick');
    });
    
    // 生成附件元數據，但不生成預設的 thumbnail 尺寸
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
    
    // 移除預設的 thumbnail 尺寸，避免裁切
    if (isset($attachment_data['sizes']['thumbnail'])) {
        unset($attachment_data['sizes']['thumbnail']);
    }
    
    wp_update_attachment_metadata($attachment_id, $attachment_data);
    
    // 更新餐廳的 LOGO meta
    update_post_meta($restaurant_id, '_restaurant_logo', $attachment_id);
    
    return $attachment_id;
}

/**
 * 註冊餐廳相關端點
 */
function byob_register_restaurant_endpoints() {
    // 添加診斷日誌
    error_log('=== BYOB 診斷開始 ===');
    error_log('時間: ' . date('Y-m-d H:i:s'));
    error_log('函數: byob_register_restaurant_endpoints() 開始執行');
    
    // 註冊自定義端點
    add_rewrite_endpoint('restaurant-profile', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('restaurant-photos', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('restaurant-menu', EP_ROOT | EP_PAGES);
    
    error_log('端點註冊完成，準備刷新重寫規則');
    
    // 強制刷新重寫規則
    flush_rewrite_rules();
    
    error_log('重寫規則已刷新');
    error_log('=== BYOB 診斷結束 ===');
}

/**
 * 檢查重寫規則
 */
function byob_check_rewrite_rules() {
    error_log('=== BYOB 重寫規則檢查 ===');
    
    // 獲取所有重寫規則
    $rules = get_option('rewrite_rules');
    error_log('總重寫規則數量: ' . count($rules));
    
    // 檢查我們的端點是否存在
    $our_endpoints = array(
        'restaurant-profile' => false,
        'restaurant-photos' => false,
        'restaurant-menu' => false
    );
    
    foreach ($rules as $rule => $rewrite) {
        foreach ($our_endpoints as $endpoint => $found) {
            if (strpos($rule, $endpoint) !== false) {
                $our_endpoints[$endpoint] = true;
                error_log('找到端點: ' . $endpoint . ' -> ' . $rule);
            }
        }
    }
    
    // 報告結果
    foreach ($our_endpoints as $endpoint => $found) {
        $status = $found ? '✅ 已註冊' : '❌ 未註冊';
        error_log($endpoint . ': ' . $status);
    }
    
    error_log('=== BYOB 重寫規則檢查結束 ===');
}

/**
 * 處理餐廳照片上傳
 */
function byob_handle_photo_upload($restaurant_id, $files) {
    // 檢查用戶是否為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('permission_denied', '請先登入');
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return new WP_Error('permission_denied', '只有餐廳業者才能上傳照片');
    }
    
    // 檢查用戶是否擁有該餐廳
    $owner_restaurant_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    if ($owner_restaurant_id != $user_id) {
        return new WP_Error('permission_denied', '您沒有權限上傳照片到此餐廳');
    }
    
    // 檢查照片數量限制
    $photo_1 = get_field('restaurant_photo_1', $restaurant_id);
    $photo_2 = get_field('restaurant_photo_2', $restaurant_id);
    $photo_3 = get_field('restaurant_photo_3', $restaurant_id);
    
    $existing_count = 0;
    if ($photo_1 && !empty($photo_1['photo'])) $existing_count++;
    if ($photo_2 && !empty($photo_2['photo'])) $existing_count++;
    if ($photo_3 && !empty($photo_3['photo'])) $existing_count++;
    
    if ($existing_count >= 3) {
        return new WP_Error('limit_reached', '已達到照片數量上限（3張）');
    }
    
    // 處理上傳的檔案
    if (!isset($files['name'][0]) || empty($files['name'][0])) {
        return new WP_Error('no_file', '請選擇要上傳的照片');
    }
    
    // 檢查檔案類型
    $file_type = wp_check_filetype($files['name'][0]);
    if (!in_array($file_type['type'], array('image/jpeg', 'image/png', 'image/webp'))) {
        return new WP_Error('invalid_type', '只支援 JPG、PNG 和 WebP 格式的圖片');
    }
    
    // 檢查檔案大小（2MB限制）
    if ($files['size'][0] > 2 * 1024 * 1024) {
        return new WP_Error('file_too_large', '檔案大小不能超過 2MB');
    }
    
    // 準備檔案上傳參數
    $file = array(
        'name' => $files['name'][0],
        'type' => $files['type'][0],
        'tmp_name' => $files['tmp_name'][0],
        'error' => $files['error'][0],
        'size' => $files['size'][0]
    );
    
    // 上傳檔案
    $upload = wp_handle_upload($file, array('test_form' => false));
    
    if (isset($upload['error'])) {
        return new WP_Error('upload_failed', '檔案上傳失敗：' . $upload['error']);
    }
    
    // 建立附件
    $attachment = array(
        'post_mime_type' => $upload['type'],
        'post_title' => sanitize_file_name($files['name'][0]),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    $attachment_id = wp_insert_attachment($attachment, $upload['file'], $restaurant_id);
    
    if (is_wp_error($attachment_id)) {
        return new WP_Error('attachment_failed', '建立附件失敗');
    }
    
    // 更新附件元數據
    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $upload['file']));
    
    // 新增到餐廳照片欄位（使用群組欄位）
    $new_photo = array(
        'photo' => $attachment_id,
        'description' => sanitize_text_field($_POST['photo_description'] ?? '')
    );
    
    // 找到第一個空的群組欄位
    if (!$photo_1 || empty($photo_1['photo'])) {
        update_field('restaurant_photo_1', $new_photo, $restaurant_id);
    } elseif (!$photo_2 || empty($photo_2['photo'])) {
        update_field('restaurant_photo_2', $new_photo, $restaurant_id);
    } elseif (!$photo_3 || empty($photo_3['photo'])) {
        update_field('restaurant_photo_3', $new_photo, $restaurant_id);
    }
    
    return array(
        'success' => true,
        'message' => '照片上傳成功',
        'attachment_id' => $attachment_id
    );
}

/**
 * 刪除餐廳照片
 */
function byob_delete_restaurant_photo($restaurant_id, $photo_id) {
    // 檢查用戶是否為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('permission_denied', '請先登入');
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return new WP_Error('permission_denied', '只有餐廳業者才能刪除照片');
    }
    
    // 檢查用戶是否擁有該餐廳
    $owner_restaurant_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    if ($owner_restaurant_id != $user_id) {
        return new WP_Error('permission_denied', '您沒有權限刪除此照片');
    }
    
    // 檢查三個群組欄位，找到要刪除的照片
    $photo_1 = get_field('restaurant_photo_1', $restaurant_id);
    $photo_2 = get_field('restaurant_photo_2', $restaurant_id);
    $photo_3 = get_field('restaurant_photo_3', $restaurant_id);
    
    $found = false;
    $field_to_clear = '';
    
    // 檢查第一個群組
    if ($photo_1 && byob_get_photo_id($photo_1) == $photo_id) {
        $field_to_clear = 'restaurant_photo_1';
        $found = true;
    }
    // 檢查第二個群組
    elseif ($photo_2 && byob_get_photo_id($photo_2) == $photo_id) {
        $field_to_clear = 'restaurant_photo_2';
        $found = true;
    }
    // 檢查第三個群組
    elseif ($photo_3 && byob_get_photo_id($photo_3) == $photo_id) {
        $field_to_clear = 'restaurant_photo_3';
        $found = true;
    }
    
    if (!$found) {
        return new WP_Error('photo_not_found', '沒有找到指定的照片');
    }
    
    // 清空對應的群組欄位
    update_field($field_to_clear, array(), $restaurant_id);
    
    // 刪除附件（可選，如果您希望保留附件）
    // wp_delete_attachment($photo_id, true);
    
    return array(
        'success' => true,
        'message' => '照片刪除成功'
    );
}

/**
 * 更新照片說明
 */
function byob_update_photo_description($restaurant_id, $photo_id, $new_description) {
    // 檢查用戶是否為餐廳業者
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('permission_denied', '請先登入');
    }
    
    $user = get_user_by('id', $user_id);
    if (!in_array('restaurant_owner', $user->roles)) {
        return new WP_Error('permission_denied', '只有餐廳業者才能更新照片說明');
    }
    
    // 檢查用戶是否擁有該餐廳
    $owner_restaurant_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    if ($owner_restaurant_id != $user_id) {
        return new WP_Error('permission_denied', '您沒有權限更新此照片的說明');
    }
    
    // 檢查三個群組欄位，找到要更新說明的照片
    $photo_1 = get_field('restaurant_photo_1', $restaurant_id);
    $photo_2 = get_field('restaurant_photo_2', $restaurant_id);
    $photo_3 = get_field('restaurant_photo_3', $restaurant_id);
    
    $found = false;
    $field_to_update = '';
    
    // 檢查第一個群組
    if ($photo_1 && byob_get_photo_id($photo_1) == $photo_id) {
        $field_to_update = 'restaurant_photo_1';
        $found = true;
    }
    // 檢查第二個群組
    elseif ($photo_2 && byob_get_photo_id($photo_2) == $photo_id) {
        $field_to_update = 'restaurant_photo_2';
        $found = true;
    }
    // 檢查第三個群組
    elseif ($photo_3 && byob_get_photo_id($photo_3) == $photo_id) {
        $field_to_update = 'restaurant_photo_3';
        $found = true;
    }
    
    if (!$found) {
        return new WP_Error('photo_not_found', '沒有找到指定的照片');
    }
    
         // 更新說明，保持原有的照片資料結構
     $current_photo = get_field($field_to_update, $restaurant_id);
     if ($current_photo && is_array($current_photo)) {
         $current_photo['description'] = $new_description;
         update_field($field_to_update, $current_photo, $restaurant_id);
     }
    
    return array(
        'success' => true,
        'message' => '照片說明更新成功'
    );
}

/**
 * 獲取照片 ID，支援舊格式和新格式
 * 舊格式：photo['photo'] = 123
 * 新格式：photo['photo'] = {'ID': 123, 'description': '...'}
 */
function byob_get_photo_id($photo_data) {
    if (empty($photo_data) || !is_array($photo_data)) {
        return null;
    }
    
    if (isset($photo_data['photo'])) {
        if (is_numeric($photo_data['photo'])) {
            // 舊格式：photo['photo'] = 123
            return intval($photo_data['photo']);
        } elseif (is_array($photo_data['photo']) && isset($photo_data['photo']['ID'])) {
            // 新格式：photo['photo'] = {'ID': 123, ...}
            return intval($photo_data['photo']['ID']);
        }
    }
    
    return null;
}

/**
 * 檢查照片是否存在且有效
 */
function byob_is_photo_valid($photo_id) {
    if (!$photo_id) {
        return false;
    }
    
    $attachment = get_post($photo_id);
    if (!$attachment || $attachment->post_type !== 'attachment') {
        return false;
    }
    
    // 檢查檔案是否存在
    $file_path = get_attached_file($photo_id);
    if (!$file_path || !file_exists($file_path)) {
        return false;
    }
    
    return true;
}

/**
 * 自動清理無效的照片資料
 */
function byob_cleanup_invalid_photos($restaurant_id) {
    $photo_1 = get_field('restaurant_photo_1', $restaurant_id);
    $photo_2 = get_field('restaurant_photo_2', $restaurant_id);
    $photo_3 = get_field('restaurant_photo_3', $restaurant_id);
    
    $cleaned = false;
    
    // 檢查並清理第一個群組
    if ($photo_1 && !byob_is_photo_valid(byob_get_photo_id($photo_1))) {
        update_field('restaurant_photo_1', array(), $restaurant_id);
        $cleaned = true;
    }
    
    // 檢查並清理第二個群組
    if ($photo_2 && !byob_is_photo_valid(byob_get_photo_id($photo_2))) {
        update_field('restaurant_photo_2', array(), $restaurant_id);
        $cleaned = true;
    }
    
    // 檢查並清理第三個群組
    if ($photo_3 && !byob_is_photo_valid(byob_get_photo_id($photo_3))) {
        update_field('restaurant_photo_3', array(), $restaurant_id);
        $cleaned = true;
    }
    
    // 如果有清理，顯示訊息
    if ($cleaned) {
        echo '<div class="notice" style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; color: #856404; margin: 10px 0;">';
        echo '<p><strong>系統通知：</strong>已自動清理無效的照片資料。</p>';
        echo '</div>';
    }
}

// 初始化系統
add_action('init', 'byob_init_restaurant_member_system');

// 在 wp_loaded 鉤子中檢查重寫規則（確保所有鉤子都已註冊）
add_action('wp_loaded', 'byob_check_rewrite_rules');

// ========================================
// 簡單的餐廳資料完整性檢查
// ========================================

/**
 * 檢查餐廳是否有完整的基本資料
 * @param int $restaurant_id 餐廳ID
 * @return bool 是否完整
 */
function byob_is_restaurant_complete($restaurant_id) {
    // 檢查 6 個必填欄位
    $post = get_post($restaurant_id);
    $restaurant_name = $post ? $post->post_title : '';
    $phone = function_exists('get_field') ? get_field('phone', $restaurant_id) : '';
    $address = function_exists('get_field') ? get_field('address', $restaurant_id) : '';
    $district = function_exists('get_field') ? get_field('district', $restaurant_id) : '';
    $restaurant_type = function_exists('get_field') ? get_field('restaurant_type', $restaurant_id) : '';
    $corkage_fee = function_exists('get_field') ? get_field('is_charged', $restaurant_id) : '';
    
    // 如果所有欄位都有值，返回 true
    return !empty(trim($restaurant_name)) && 
           !empty(trim($phone)) && 
           !empty(trim($address)) && 
           !empty(trim($district)) &&
           !empty($restaurant_type) &&
           !empty($corkage_fee);
}

/**
 * 前台過濾不完整的餐廳
 */
function byob_filter_incomplete_restaurants($query) {
    // 只在前台頁面過濾，不影響後台
    if (!is_admin() && $query->is_main_query() && 
        (is_post_type_archive('restaurant') || is_singular('restaurant'))) {
        
        // 使用 posts_results 過濾，更可靠
        add_filter('posts_results', 'byob_filter_complete_restaurants');
    }
}
add_action('pre_get_posts', 'byob_filter_incomplete_restaurants');

/**
 * 過濾完整的餐廳
 */
function byob_filter_complete_restaurants($posts) {
    if (empty($posts)) {
        return $posts;
    }
    
    $complete_posts = array();
    
    foreach ($posts as $post) {
        if ($post->post_type === 'restaurant') {
            // 檢查餐廳是否完整
            if (byob_is_restaurant_complete($post->ID)) {
                $complete_posts[] = $post;
            }
        } else {
            // 非餐廳文章直接保留
            $complete_posts[] = $post;
        }
    }
    
    return $complete_posts;
} 