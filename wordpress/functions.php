<?php
// Add custom Theme Functions here

// BYOB 功能開關設定 - 已移至檔案結尾的 byob_get_feature_settings() 函數

// BYOB Google Form 自動導入 WordPress 功能
// 建立自訂 REST API 端點
add_action('rest_api_init', function () {
    register_rest_route('byob/v1', '/restaurant', array(
        'methods' => 'POST',
        'callback' => 'byob_create_restaurant_post',
        'permission_callback' => 'byob_verify_api_key',
        'args' => array(
            'restaurant_name' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'contact_person' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'email' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_email',
            ),
            'restaurant_type' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'district' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'address' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
            'is_charged' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'corkage_fee_amount' => array(
                'required' => false,
                'sanitize_callback' => 'byob_sanitize_int',
            ),
            'corkage_fee_note' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'equipment' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'equipment_other_note' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'open_bottle_service' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'open_bottle_service_other_note' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'phone' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'website' => array(
                'required' => false,
                'sanitize_callback' => 'esc_url_raw',
            ),
            'social_media' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'notes' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
            'is_owner' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'customer_recommender_name' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'customer_recommender_email' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_email',
            ),
            'source' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
    
    // 新增除錯端點
    register_rest_route('byob/v1', '/debug', array(
        'methods' => 'GET',
        'callback' => 'byob_debug_page',
        'permission_callback' => function() {
            if (current_user_can('administrator')) {
                return true;
            }
            return byob_verify_api_key(new WP_REST_Request());
        },
    ));
    
    // 新增測試端點
    register_rest_route('byob/v1', '/test', array(
        'methods' => 'POST',
        'callback' => 'byob_test_endpoint',
        'permission_callback' => '__return_true',
    ));
    
    // 費城 BYOB 餐廳 API 端點
    register_rest_route('byob/v1', '/philly-restaurant', array(
        'methods' => 'POST',
        'callback' => 'byob_create_philly_restaurant_post',
        'permission_callback' => 'byob_verify_api_key',
        'args' => array(
            'restaurant_name' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'address' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'phone' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'website' => array(
                'required' => false,
                'sanitize_callback' => 'esc_url_raw',
            ),
            'philly_corkage_fee' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'corkage_fee_amount' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'other_corkage_policy' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
            'wine_service_equipment' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'philly_equipment_other_note' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'byob_service_level' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'philly_restaurant_type' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'philly_restaurant_type_other_note' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'philly_dining_experience' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_textarea_field',
            ),
            'philly_reddit_username' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'philly_contact_email' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_email',
            ),
            'show_reddit_username' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'source' => array(
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
});

// 自訂數值清理函數
function byob_sanitize_int($value) {
    return intval($value);
}

// API 金鑰驗證
function byob_verify_api_key($request) {
    $api_key = $request->get_header('X-API-Key');
    $valid_key = get_option('byob_api_key', 'byob-secret-key-2025');
    
    if (!$api_key || $api_key !== $valid_key) {
        return new WP_Error('invalid_api_key', 'Invalid API key', array('status' => 401));
    }
    return true;
}

// =============================================================================
// 🔄 兩種註冊流程的共用函數
// =============================================================================
// 
// 以下函數被兩種註冊流程共同使用：
// 1. Google表單註冊流程（透過邀請碼）
// 2. 餐廳直接加入流程（網站直接註冊）
// 
// 共用原因：避免重複程式碼，確保資料處理邏輯一致
// =============================================================================

/**
 * 建立餐廳文章的核心共用函數
 * 
 * 此函數被兩種註冊流程共同調用：
 * - Google表單流程：建立草稿狀態餐廳文章，等待審核
 * - 直接加入流程：建立發布狀態餐廳文章，立即上架
 * 
 * @param array $restaurant_data 餐廳資料陣列
 * @param string $source 註冊來源 ('google_form', 'direct', 'invitation')
 * @return array|WP_Error 成功返回文章資訊，失敗返回錯誤
 */
function byob_create_restaurant_article($restaurant_data, $source = 'direct') {
    try {
        // 檢查核心必填欄位（只保留 3 個絕對必要的欄位）
        $required_fields = array('restaurant_name', 'address', 'is_charged');
        $missing_fields = array();
        
        foreach ($required_fields as $field) {
            if (empty($restaurant_data[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            return new WP_Error('missing_required_fields', '缺少核心必填欄位: ' . implode(', ', $missing_fields), array('status' => 400));
        }
        
        // 1. 先檢查重複
        $duplicate_check = byob_check_duplicate_restaurant($restaurant_data);
        
        if ($duplicate_check['is_duplicate']) {
            // 重複檢查：使用 'pending' 狀態
            $post_status = 'pending';
            $review_status = 'pending_duplicate_review';
        } else {
            // 一般審核：使用 'draft' 狀態
            $post_status = 'draft';
            $review_status = 'pending_general_review';
        }
        
        // 建立餐廳文章
        $post_data = array(
            'post_title' => sanitize_text_field($restaurant_data['restaurant_name']),
            'post_content' => sanitize_textarea_field($restaurant_data['notes'] ?? ''),
            'post_status' => $post_status,
            'post_type' => 'restaurant',
            'post_author' => $restaurant_data['user_id'] ?? 1,
        );
        
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            throw new Exception('Failed to create post: ' . $post_id->get_error_message());
        }
        
        // 處理餐廳類型 - 增強處理邏輯，支援「其他」選項
        $types = $restaurant_data['restaurant_type'];
        $other_note = $restaurant_data['restaurant_type_other_note'] ?? '';
        
        if (!empty($types)) {
            if (!is_array($types)) {
                // 如果是字串，嘗試用逗號分割，如果沒有逗號就直接轉為陣列
                if (strpos($types, ',') !== false) {
                    $types = array_map('trim', explode(',', $types));
                } else {
                    $types = array(trim($types));
                }
            }
            
            // 記錄除錯資訊
            error_log('BYOB API: 餐廳類型處理 - 原始類型: ' . print_r($types, true));
            error_log('BYOB API: 餐廳類型處理 - 其他說明: "' . $other_note . '"');
            
            // 確保「其他」選項存在（如果沒有「其他」但有說明文字，自動添加）
            if (!empty($other_note) && !in_array('其他', $types)) {
                $types[] = '其他';
                error_log('BYOB API: 自動添加「其他」選項，因為有說明文字: "' . $other_note . '"');
            }
            
            // 清理空值
            $types = array_filter($types, function($type) {
                return !empty(trim($type));
            });
            
            error_log('BYOB API: 餐廳類型處理 - 最終類型: ' . print_r($types, true));
        } else {
            $types = array();
        }
        
        // 處理開酒服務 - 增強處理邏輯，支援「其他」選項
        $bottleService = $restaurant_data['open_bottle_service'] ?? '';
        $bottleServiceOtherNote = $restaurant_data['open_bottle_service_other_note'] ?? '';
        
        if (!empty($bottleServiceOtherNote)) {
            // 如果有其他說明，自動設定為「其他」
            $bottleService = '其他';
            error_log('BYOB API: 開酒服務處理 - 自動設定為「其他」，因為有說明文字: "' . $bottleServiceOtherNote . '"');
        }
        
        error_log('BYOB API: 開酒服務處理 - 最終選項: "' . $bottleService . '"');
        error_log('BYOB API: 開酒服務處理 - 其他說明: "' . $bottleServiceOtherNote . '"');
        
        // 處理設備 - 增強處理邏輯，支援「其他」選項
        $equipment = $restaurant_data['equipment'];
        $equipmentOtherNote = $restaurant_data['equipment_other_note'] ?? '';
        
        if (!empty($equipmentOtherNote)) {
            // 如果有其他說明，確保 equipment 包含「其他」選項
            if (empty($equipment)) {
                $equipment = array('其他');
            } elseif (is_array($equipment)) {
                // 如果沒有「其他」選項，添加它
                if (!in_array('其他', $equipment)) {
                    $equipment[] = '其他';
                }
            } else {
                // 處理字串情況
                if (strpos($equipment, '其他') === false) {
                    $equipment = $equipment . ', 其他';
                }
            }
            error_log('BYOB API: 酒器設備處理 - 因有說明文字而加入「其他」: "' . $equipmentOtherNote . '"');
        }
        
        $equipment_str = is_array($equipment) ? implode(', ', $equipment) : $equipment;
        error_log('BYOB API: 酒器設備處理 - 最終選項: "' . $equipment_str . '"');
        error_log('BYOB API: 酒器設備處理 - 其他說明: "' . $equipmentOtherNote . '"');
        
        if (!empty($equipment) && !is_array($equipment)) {
            $equipment = array_map('trim', explode(',', $equipment));
        }
        
        // 處理社群連結
        $social_media = $restaurant_data['social_media'] ?? '';
        $social_media_primary = '';
        if (!empty($social_media)) {
            $social_links_array = array_map('trim', explode(',', $social_media));
            $social_media_primary = $social_links_array[0];
        }
        
        // 更新 ACF 欄位
        if (function_exists('update_field')) {
            $acf_updates = array(
                'restaurant_name' => sanitize_text_field($restaurant_data['restaurant_name']),
                'contact_person' => sanitize_text_field($restaurant_data['contact_person'] ?? ''),
                'email' => sanitize_email($restaurant_data['email'] ?? ''),
                'district' => sanitize_text_field($restaurant_data['district'] ?? ''),
                'restaurant_type' => $types ?: array(),
                'restaurant_type_other_note' => sanitize_text_field($restaurant_data['restaurant_type_other_note'] ?? ''),
                'address' => sanitize_text_field($restaurant_data['address']),
                'is_charged' => sanitize_text_field($restaurant_data['is_charged']),
                'corkage_fee_amount' => intval($restaurant_data['corkage_fee_amount'] ?? 0),
                'corkage_fee_note' => sanitize_text_field($restaurant_data['corkage_fee_note'] ?? ''),
                'equipment' => $equipment ?: array(),
                'equipment_other_note' => sanitize_text_field($equipmentOtherNote),
                'open_bottle_service' => sanitize_text_field($bottleService),
                'open_bottle_service_other_note' => sanitize_textarea_field($restaurant_data['open_bottle_service_other_note'] ?? ''),
                'phone' => sanitize_text_field($restaurant_data['phone'] ?? ''),
                'website' => esc_url_raw($restaurant_data['website'] ?? ''),
                'social_links' => $social_media_primary,
                'notes' => sanitize_textarea_field($restaurant_data['notes'] ?? ''),
                'last_updated' => current_time('Y-m-d'),
                'source' => $restaurant_data['source'] ?? ($restaurant_data['is_owner'] === '是' ? '店主' : '表單填寫者'),
                'is_owner' => sanitize_text_field($restaurant_data['is_owner'] ?? ''),
                'customer_recommender_name' => sanitize_text_field($restaurant_data['customer_recommender_name'] ?? ''),
                'customer_recommender_email' => sanitize_email($restaurant_data['customer_recommender_email'] ?? ''),
                'review_status' => ($source === 'google_form') ? 'pending' : 'approved',
                'submitted_date' => current_time('mysql'),
                'review_date' => ($source === 'google_form') ? '' : current_time('mysql'),
                'review_notes' => ''
            );
            
            foreach ($acf_updates as $field_name => $field_value) {
                update_field($field_name, $field_value, $post_id);
            }
        }
        
        // 記錄註冊來源
        update_post_meta($post_id, '_byob_registration_source', $source);
        
        // 記錄建立時間
        update_post_meta($post_id, '_byob_created_at', current_time('mysql'));
        
        // 儲存審核狀態
        update_post_meta($post_id, '_byob_review_status', $review_status);
        
        if ($duplicate_check['is_duplicate']) {
            // 儲存重複檢查資訊
            update_post_meta($post_id, '_byob_duplicate_check', $duplicate_check);
        }
        
        return array(
            'success' => true,
            'post_id' => $post_id,
            'post_url' => get_edit_post_link($post_id, ''),
            'post_status' => $post_status,
            'review_status' => $review_status,
            'message' => $duplicate_check['is_duplicate'] ? 
                '發現相似餐廳，已標記為重複檢查' : 
                '餐廳資料已建立，等待審核'
        );
        
    } catch (Exception $e) {
        return new WP_Error('restaurant_creation_failed', $e->getMessage(), array('status' => 500));
    }
}

// =============================================================================
// 🔍 重複檢查功能群組
// =============================================================================

/**
 * 重複檢查主函數（簡化版）
 * 
 * @param array $new_restaurant 新餐廳資料陣列（需包含 restaurant_name 和 address）
 * @param string $project 專案類型（'philly' 或 'taipei' 或空字串代表所有專案）
 * @return array 重複檢查結果
 */
function byob_check_duplicate_restaurant($new_restaurant, $project = '') {
    // 取得現有餐廳資料
    $query_args = [
        'post_type' => 'restaurant',
        'post_status' => ['publish', 'draft', 'pending'],
        'numberposts' => -1,
        'meta_query' => [
            [
                'key' => '_byob_duplicate_status',
                'compare' => 'NOT EXISTS'
            ]
        ]
    ];
    
    // 如果指定專案類型，只檢查該專案的餐廳
    if ($project === 'philly') {
        // 只檢查費城餐廳（透過 source 欄位篩選）
        // 注意：分類是在建立文章後才設定的，所以主要依賴 source 欄位
        // 但如果餐廳還沒有 source 欄位（可能是舊的或測試資料），也納入檢查
        $query_args['meta_query'][] = [
            'relation' => 'OR',
            [
                'key' => 'source',
                'value' => ['philly_community_recommendation', 'philly_owner_verification'],
                'compare' => 'IN'
            ],
            // 如果沒有 source 欄位，可能是舊的費城餐廳，也納入檢查
            [
                'key' => 'source',
                'compare' => 'NOT EXISTS'
            ]
        ];
        
        // 設定 meta_query 的 relation 為 AND（所有條件都要滿足）
        // 但要注意：第一個條件（_byob_duplicate_status）和費城條件是 AND 關係
        // 而費城條件內部是 OR 關係
        $query_args['meta_query']['relation'] = 'AND';
    } elseif ($project === 'taipei') {
        // 只檢查台北餐廳（排除費城餐廳）
        $query_args['meta_query'][] = [
            'relation' => 'OR',
            [
                'key' => 'source',
                'value' => ['philly_community_recommendation', 'philly_owner_verification'],
                'compare' => 'NOT IN'
            ],
            [
                'key' => 'source',
                'compare' => 'NOT EXISTS'
            ]
        ];
        $query_args['meta_query']['relation'] = 'AND';
    }
    
    $existing_restaurants = get_posts($query_args);
    
    // 除錯：記錄查詢結果
    error_log('BYOB 重複檢查查詢：找到 ' . count($existing_restaurants) . ' 個現有餐廳');
    
    foreach ($existing_restaurants as $existing) {
        $existing_data = [
            'name' => get_field('restaurant_name', $existing->ID),
            'address' => get_field('address', $existing->ID)
        ];
        
        // 除錯：記錄比對的餐廳
        error_log('BYOB 重複檢查比對：現有餐廳=' . ($existing_data['name'] ?? '無名稱') . ', 地址=' . ($existing_data['address'] ?? '無地址'));
        
        // 使用簡化的相似度計算
        $similarity = byob_calculate_simple_similarity(
            $new_restaurant['restaurant_name'], 
            $new_restaurant['address'], 
            $existing_data['name'],
            $existing_data['address']
        );
        
        error_log('BYOB 重複檢查相似度：' . $similarity . '%');
        
        if ($similarity >= 80) {
            return [
                'is_duplicate' => true,
                'similar_restaurant_id' => $existing->ID,
                'similar_restaurant_name' => $existing_data['name'],
                'similar_restaurant_address' => $existing_data['address'],
                'similarity_score' => $similarity
            ];
        }
    }
    
    return ['is_duplicate' => false];
}

/**
 * 簡化版相似度計算（通用型）
 * 
 * @param string $name1 新餐廳名稱
 * @param string $addr1 新餐廳地址
 * @param string $name2 現有餐廳名稱
 * @param string $addr2 現有餐廳地址
 * @return float 相似度百分比
 */
function byob_calculate_simple_similarity($name1, $addr1, $name2, $addr2) {
    // 標準化所有字串（移除空格、標點、大小寫、統一地址縮寫）
    $normalize = function($str) {
        if (empty($str)) {
            return '';
        }
        // 轉小寫
        $str = strtolower($str);
        
        // 統一地址縮寫（針對地址字串）
        $address_abbreviations = [
            '/\bstreet\b/' => 'st',
            '/\bavenue\b/' => 'ave',
            '/\bboulevard\b/' => 'blvd',
            '/\broad\b/' => 'rd',
            '/\bdrive\b/' => 'dr',
            '/\blane\b/' => 'ln',
            '/\bphiladelphia\b/' => 'philly',
            '/\bpennsylvania\b/' => 'pa',
        ];
        foreach ($address_abbreviations as $pattern => $replacement) {
            $str = preg_replace($pattern, $replacement, $str);
        }
        
        // 移除所有標點符號、空格、引號、連字符、破折號
        // 包含：空格、引號、括號、逗號、句號、破折號、連字符、冒號、分號、驚嘆號、問號
        $str = preg_replace('/[\s\'"「」『』【】（）()，。、；：！？\-–—,\.;:!?]/', '', $str);
        
        // 移除中文常見詞彙（保留以支援中文餐廳）
        $str = preg_replace('/[店餐廳有限公司股份有限公司]/', '', $str);
        
        return $str;
    };
    
    $name1_norm = $normalize($name1);
    $addr1_norm = $normalize($addr1);
    $name2_norm = $normalize($name2);
    $addr2_norm = $normalize($addr2);
    
    // 處理名稱或地址為空的情況
    $name1_empty = empty($name1_norm);
    $name2_empty = empty($name2_norm);
    $addr1_empty = empty($addr1_norm);
    $addr2_empty = empty($addr2_norm);
    
    // 如果兩個名稱都為空，無法比對
    if ($name1_empty && $name2_empty) {
        return 0;
    }
    
    // 如果名稱完全相同，檢查地址
    if ($name1_norm === $name2_norm && !$name1_empty) {
        // 如果兩個地址都為空，判定為重複（名稱相同且都沒有地址）
        if ($addr1_empty && $addr2_empty) {
            return 90; // 名稱相同且都沒有地址，高相似度
        }
        
        // 如果地址完全相同，100% 相似
        if ($addr1_norm === $addr2_norm && !$addr1_empty) {
            return 100;
        }
        
        // 如果地址都為空，已經在上面處理了
        // 如果一個有地址一個沒有，計算地址相似度
        if (!$addr1_empty && !$addr2_empty) {
            $addr_similarity = byob_calculate_string_similarity($addr1_norm, $addr2_norm);
            if ($addr_similarity >= 70) {
                return 85; // 名稱相同且地址相似，高相似度
            }
        }
    }
    
    // 如果地址完全相同（且都不為空），強制判定為重複（無論名稱是否相似）
    if ($addr1_norm === $addr2_norm && !$addr1_empty && !$addr2_empty) {
        // 使用 levenshtein 距離計算名稱相似度
        $name_similarity = byob_calculate_string_similarity($name1_norm, $name2_norm);
        if ($name_similarity >= 70) {
            return 95; // 地址相同且名稱相似，極高相似度
        } else {
            return 85; // 地址相同但名稱不同，仍然判定為重複
        }
    }
    
    // 計算整體相似度
    $name_similarity = byob_calculate_string_similarity($name1_norm, $name2_norm);
    
    // 如果兩個地址都為空，只依賴名稱相似度
    if ($addr1_empty && $addr2_empty) {
        // 如果名稱相似度 >= 80%，判定為重複
        if ($name_similarity >= 80) {
            return $name_similarity;
        } else {
            return $name_similarity; // 即使不達 80%，也返回名稱相似度
        }
    }
    
    // 如果只有一個地址為空，降低地址權重
    $addr_similarity = byob_calculate_string_similarity($addr1_norm, $addr2_norm);
    if ($addr1_empty || $addr2_empty) {
        // 如果有一個地址為空，主要依賴名稱相似度
        $total_similarity = ($name_similarity * 0.8) + ($addr_similarity * 0.2);
    } else {
        // 兩個地址都有，使用標準加權平均
        $total_similarity = ($name_similarity * 0.4) + ($addr_similarity * 0.6);
    }
    
    return round($total_similarity, 2);
}


/**
 * 提取門牌號碼
 * 
 * @param string $address 完整地址
 * @return int 門牌號碼
 */
function byob_extract_house_number($address) {
    preg_match('/(\d+)號/', $address, $matches);
    return isset($matches[1]) ? intval($matches[1]) : 0;
}

/**
 * 計算字串相似度
 * 
 * @param string $str1 字串1
 * @param string $str2 字串2
 * @return float 相似度百分比
 */
function byob_calculate_string_similarity($str1, $str2) {
    // 使用 levenshtein 距離計算相似度
    $max_len = max(strlen($str1), strlen($str2));
    if ($max_len === 0) return 100;
    
    $distance = levenshtein($str1, $str2);
    $similarity = (1 - ($distance / $max_len)) * 100;
    
    return round($similarity, 2);
}

// =============================================================================
// 📝 註冊流程1：Google表單註冊流程（獨立函數）
// =============================================================================
// 
// 此函數專門處理Google表單的註冊流程：
// - 接收Google表單自動提交的資料
// - 處理REST API請求、參數映射、資料轉換
// - 調用共用函數建立草稿狀態餐廳文章
// - 等待管理員審核後發布
// 
// 註冊流程：Google表單 → 自動建立草稿 → 管理員審核 → 發送邀請碼 → 業者註冊
// =============================================================================

/**
 * 處理Google表單API請求，建立餐廳草稿
 * 
 * 此函數專門處理Google表單註冊流程：
 * - 接收Google表單資料（透過REST API）
 * - 處理參數映射和資料轉換
 * - 調用共用函數建立草稿狀態餐廳文章
 * - 餐廳狀態：草稿（等待審核）
 * 
 * @param WP_REST_Request $request REST API請求物件
 * @return array|WP_Error 成功返回文章資訊，失敗返回錯誤
 */
function byob_create_restaurant_post($request) {
    try {
        // 除錯：記錄接收到的所有參數
        $received_params = $request->get_params();
        error_log('BYOB API: 接收到的參數: ' . print_r($received_params, true));
        
        // 支援多種參數名稱的映射
        $param_mapping = array(
            'restaurant_name' => array('restaurant_name', 'name', 'restaurant_name'),
            'contact_person' => array('contact_person', 'contact', 'contact_name'),
            'email' => array('email', 'contact_email', 'email_address'),
            'restaurant_type' => array('restaurant_type', 'type', 'category'),
            'restaurant_type_other_note' => array('restaurant_type_other_note', 'other_type_note', 'other_note'),
            'district' => array('district', 'area', 'region'),
            'address' => array('address', 'restaurant_address', 'location'),
            'is_charged' => array('is_charged', 'charged', 'corkage_charged'),
            'phone' => array('phone', 'contact_phone', 'phone_number'),
            'corkage_fee_amount' => array('corkage_fee_amount', 'fee_amount', '開瓶費金額'),
            'corkage_fee_note' => array('corkage_fee_note', 'fee_note', '其他：請說明'),
            'equipment' =>	array('equipment', 'equipment_list', 'available_equipment'),
            'equipment_other_note' => array('equipment_other_note', 'equipment_note', 'equipment_other'),
            'open_bottle_service' => array('open_bottle_service', 'bottle_service', 'service_type'),
            'open_bottle_service_other_note' => array('open_bottle_service_other_note', 'service_note', 'other_service'),
            'website' => array('website', 'website_url', 'url'),
            'social_media' => array('social_media', 'social', 'social_links', '餐廳 Instagram 或 Facebook'),
            'notes' => array('notes', 'additional_notes', 'comments'),
            'is_owner' => array('is_owner', 'owner', 'is_restaurant_owner'),
            'customer_recommender_name' => array('customer_recommender_name', 'recommender_name', 'customer_name'),
            'customer_recommender_email' => array('customer_recommender_email', 'recommender_email', 'customer_email'),
            'source' => array('source', 'data_source', 'origin')
        );
        
        // 獲取參數值（支援多種名稱）
        $get_param_value = function($request, $param_names) {
            foreach ($param_names as $name) {
                $value = $request->get_param($name);
                if (!empty($value)) {
                    return $value;
                }
            }
            return '';
        };
        
        // 檢查核心必填參數（只檢查 3 個絕對必要的參數）
        $required_params = array('restaurant_name', 'address', 'is_charged');
        
        $missing_params = array();
        foreach ($required_params as $param) {
            if (empty($get_param_value($request, $param_mapping[$param]))) {
                $missing_params[] = $param;
            }
        }
        
        if (!empty($missing_params)) {
            error_log('BYOB API: 缺少核心必填參數: ' . implode(', ', $missing_params));
            return new WP_Error('missing_required_params', '缺少核心必填參數: ' . implode(', ', $missing_params), array('status' => 400));
        }
        
        // 轉換API請求為標準資料格式
        $restaurant_data = array(
            'restaurant_name' => $get_param_value($request, $param_mapping['restaurant_name']),
            'contact_person' => $get_param_value($request, $param_mapping['contact_person']),
            'email' => $get_param_value($request, $param_mapping['email']),
            'restaurant_type' => $get_param_value($request, $param_mapping['restaurant_type']),
            'restaurant_type_other_note' => $get_param_value($request, $param_mapping['restaurant_type_other_note']),
            'district' => $get_param_value($request, $param_mapping['district']),
            'address' => $get_param_value($request, $param_mapping['address']),
            'is_charged' => $get_param_value($request, $param_mapping['is_charged']),
            'phone' => $get_param_value($request, $param_mapping['phone']),
            'corkage_fee_amount' => $get_param_value($request, $param_mapping['corkage_fee_amount']),
            'corkage_fee_note' => $get_param_value($request, $param_mapping['corkage_fee_note']),
            'equipment' => $get_param_value($request, $param_mapping['equipment']),
            'equipment_other_note' => $get_param_value($request, $param_mapping['equipment_other_note']),
            'open_bottle_service' => $get_param_value($request, $param_mapping['open_bottle_service']),
            'open_bottle_service_other_note' => $get_param_value($request, $param_mapping['open_bottle_service_other_note']),
            'website' => $get_param_value($request, $param_mapping['website']),
            'social_media' => $get_param_value($request, $param_mapping['social_media']),
            'notes' => $get_param_value($request, $param_mapping['notes']),
            'is_owner' => $get_param_value($request, $param_mapping['is_owner']),
            'customer_recommender_name' => $get_param_value($request, $param_mapping['customer_recommender_name']),
            'customer_recommender_email' => $get_param_value($request, $param_mapping['customer_recommender_email']),
            'source' => $get_param_value($request, $param_mapping['source'])
        );
        
        // 記錄餐廳類型相關的除錯資訊
        error_log('BYOB API: 餐廳類型原始值 = "' . $restaurant_data['restaurant_type'] . '"');
        error_log('BYOB API: 餐廳類型其他說明 = "' . $restaurant_data['restaurant_type_other_note'] . '"');
        
        // 處理 ACF 欄位值格式轉換
        $is_charged_raw = $restaurant_data['is_charged'];
        if (strpos($is_charged_raw, '酌收') !== false) {
            $restaurant_data['is_charged'] = 'yes';
        } elseif (strpos($is_charged_raw, '不收') !== false) {
            $restaurant_data['is_charged'] = 'no';
        } elseif (strpos($is_charged_raw, '其他') !== false) {
            $restaurant_data['is_charged'] = 'other';
        }
        
        // 開酒服務選項 - 直接使用原始值，因為 ACF 現在也是中文
        // Google 表單選項：有、無、其他
        // ACF 選項：有、無、其他
        // 不需要轉換，直接使用
        $open_bottle_service_raw = $restaurant_data['open_bottle_service'];
        // 記錄除錯資訊
        error_log('BYOB API: 開酒服務原始值 = "' . $open_bottle_service_raw . '"');
        
        // 調用共用函數建立餐廳文章
        // 注意：這裡調用的是兩種註冊流程的共用函數
        $result = byob_create_restaurant_article($restaurant_data, 'google_form');
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // 記錄 API 呼叫
        byob_log_api_call($result['post_id'], $request->get_params(), 'draft_created');
        
        return $result;
        
    } catch (Exception $e) {
        byob_log_api_call(0, $request->get_params(), 'error: ' . $e->getMessage());
        return new WP_Error('restaurant_creation_failed', $e->getMessage(), array('status' => 500));
    }
}

/**
 * 建立費城 BYOB 餐廳文章（費城專用 API 端點）
 * 
 * 此函數專門處理費城 BYOB 餐廳的社群推薦：
 * - 接收 Reddit 社群推薦的費城餐廳資料
 * - 建立英文文章草稿
 * - 設定費城專用的分類和標籤
 * - 文章狀態：草稿（需要審核）
 * 
 * @param WP_REST_Request $request REST API請求物件
 * @return array|WP_Error 成功返回文章資訊，失敗返回錯誤
 */
function byob_create_philly_restaurant_post($request) {
    try {
        // 除錯：記錄接收到的所有參數
        $received_params = $request->get_params();
        error_log('Philadelphia BYOB API: 接收到的參數: ' . print_r($received_params, true));
        
        // 取得費城專用參數
        $restaurant_name = $request->get_param('restaurant_name');
        $address = $request->get_param('address');
        $phone = $request->get_param('phone');
        // 原本的 website 參數已改為 yelp_link
        // $website = $request->get_param('website');
        $yelp_link = $request->get_param('yelp_link');
        $philly_corkage_fee = $request->get_param('philly_corkage_fee');
        $corkage_fee_amount = $request->get_param('corkage_fee_amount');
        $other_corkage_policy = $request->get_param('other_corkage_policy');
        $wine_service_equipment = $request->get_param('wine_service_equipment');
        $philly_equipment_other_note = $request->get_param('philly_equipment_other_note');
        $byob_service_level = $request->get_param('byob_service_level');
        $philly_restaurant_type = $request->get_param('philly_restaurant_type');
        $philly_restaurant_type_other_note = $request->get_param('philly_restaurant_type_other_note');
        $philly_dining_experience = $request->get_param('philly_dining_experience');
        $philly_reddit_username = $request->get_param('philly_reddit_username');
        $philly_contact_email = $request->get_param('philly_contact_email');
        $show_reddit_username = $request->get_param('show_reddit_username');
        
        // 基本驗證（只有餐廳名稱是必填）
        if (empty($restaurant_name)) {
            return new WP_Error('missing_required_fields', 'Missing required field: restaurant_name', array('status' => 400));
        }
        
        // 取得 source 參數
        $source = $request->get_param('source');
        
        // 準備費城餐廳資料
        $philly_restaurant_data = array(
            'restaurant_name' => $restaurant_name,
            'address' => $address ?: '',
            'phone' => $phone ?: '',
            // 原本的 website 已改為 yelp_link
            // 'website' => $website ?: '',
            'yelp_link' => $yelp_link ?: '',
            'philly_corkage_fee' => $philly_corkage_fee ?: '',
            'corkage_fee_amount' => $corkage_fee_amount ?: '',
            'other_corkage_policy' => $other_corkage_policy ?: '',
            'wine_service_equipment' => $wine_service_equipment ?: '',
            'philly_equipment_other_note' => $philly_equipment_other_note ?: '',
            'byob_service_level' => $byob_service_level ?: '',
            'philly_restaurant_type' => $philly_restaurant_type ?: '',
            'philly_restaurant_type_other_note' => $philly_restaurant_type_other_note ?: '',
            'philly_dining_experience' => $philly_dining_experience ?: '',
            'philly_reddit_username' => $philly_reddit_username ?: '',
            'philly_contact_email' => $philly_contact_email ?: '',
            'show_reddit_username' => $show_reddit_username ?: '',
            'city' => 'Philadelphia',
            'state' => 'PA',
            'country' => 'USA',
            'source' => $source ?: 'philly_community_recommendation', // 預設為社群推薦
            'language' => 'en'
        );
        
        // 建立費城餐廳文章
        $result = byob_create_philly_restaurant_article($philly_restaurant_data);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // 記錄 API 呼叫
        byob_log_api_call($result['post_id'], $request->get_params(), 'philly_draft_created');
        
        return $result;
        
    } catch (Exception $e) {
        byob_log_api_call(0, $request->get_params(), 'philly_error: ' . $e->getMessage());
        return new WP_Error('philly_restaurant_creation_failed', $e->getMessage(), array('status' => 500));
    }
}

/**
 * 建立費城 BYOB 餐廳文章（共用函數）
 * 
 * @param array $restaurant_data 費城餐廳資料陣列
 * @return array|WP_Error 成功返回文章資訊，失敗返回錯誤
 */
function byob_create_philly_restaurant_article($restaurant_data) {
    try {
        // 檢查核心必填欄位（只有餐廳名稱是必填）
        $required_fields = array('restaurant_name');
        $missing_fields = array();
        
        foreach ($required_fields as $field) {
            if (empty($restaurant_data[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            return new WP_Error('missing_required_fields', '缺少核心必填欄位: ' . implode(', ', $missing_fields), array('status' => 400));
        }
        
        // 檢查是否為重複餐廳（只檢查費城餐廳）
        $duplicate_check = byob_check_duplicate_restaurant($restaurant_data, 'philly');
        $is_duplicate = $duplicate_check['is_duplicate'];
        
        // 除錯：記錄重複檢查結果
        error_log('BYOB 重複檢查：餐廳名稱=' . $restaurant_data['restaurant_name'] . ', 是否重複=' . ($is_duplicate ? '是' : '否'));
        if ($is_duplicate) {
            error_log('BYOB 重複檢查：相似餐廳ID=' . ($duplicate_check['similar_restaurant_id'] ?? '未知') . ', 相似度=' . ($duplicate_check['similarity_score'] ?? '未知'));
        }
        
        // 如果發現重複，標題加註「(重複)」（只加一次）
        $restaurant_name = $restaurant_data['restaurant_name'];
        if ($is_duplicate) {
            if (strpos($restaurant_name, '(重複)') === false) {
                $restaurant_name .= ' (重複)';
            }
            $restaurant_data['restaurant_name'] = $restaurant_name;
        }
        
        // 費城專用：直接設為草稿狀態
        $post_status = 'draft';
        $review_status = 'pending_general_review';
        
        // 建立餐廳文章（高度參照現有模式）
        $post_data = array(
            'post_title' => sanitize_text_field($restaurant_data['restaurant_name']),
            'post_content' => sanitize_textarea_field($restaurant_data['philly_dining_experience'] ?? ''),
            'post_status' => $post_status,
            'post_type' => 'restaurant',
            'post_author' => 1, // 設為管理員
        );
        
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            throw new Exception('Failed to create post: ' . $post_id->get_error_message());
        }
        
        // 處理餐廳類型（高度參照現有邏輯）
        $types = $restaurant_data['philly_restaurant_type'] ?? '';
        $other_note = $restaurant_data['philly_restaurant_type_other_note'] ?? '';
        
        if (!empty($types)) {
            if (!is_array($types)) {
                if (strpos($types, ',') !== false) {
                    $types = array_map('trim', explode(',', $types));
                } else {
                    $types = array(trim($types));
                }
            }
            
            // 確保 other 選項存在（如果沒有 other 但有說明文字，自動添加）
            // 同時正規化大小寫："Other" → "other"
            $types = array_map(function($t) { return $t === 'Other' ? 'other' : $t; }, $types);
            if (!empty($other_note) && !in_array('other', $types)) {
                $types[] = 'other';
            }
            
            // 清理空值
            $types = array_filter($types, function($type) {
                return !empty(trim($type));
            });
        } else {
            $types = array();
        }
        
        // 處理酒器設備（高度參照現有邏輯）
        $equipment = $restaurant_data['wine_service_equipment'] ?? '';
        $equipmentOtherNote = $restaurant_data['philly_equipment_other_note'] ?? '';
        
        if (!empty($equipmentOtherNote)) {
            // 如果有其他說明，確保 equipment 包含 other 選項
            if (empty($equipment)) {
                $equipment = array('other');
            } elseif (is_array($equipment)) {
                // 正規化大小寫
                $equipment = array_map(function($e) { return $e === 'Other' ? 'other' : $e; }, $equipment);
                if (!in_array('other', $equipment)) {
                    $equipment[] = 'other';
                }
            } else {
                // 字串情況：若沒有包含 other/Other，則添加
                if (stripos($equipment, 'other') === false) {
                    $equipment = $equipment . ', other';
                }
            }
        }
        
        if (!empty($equipment) && !is_array($equipment)) {
            $equipment = array_map('trim', explode(',', $equipment));
            // 正規化大小寫
            $equipment = array_map(function($e) { return $e === 'Other' ? 'other' : $e; }, $equipment);
        }
        
        // 更新 ACF 欄位（使用費城專用欄位名稱）
        if (function_exists('update_field')) {
            // 正規化開瓶費金額：擷取數字(與小數點)，非數字則為空
            $amount_raw = (string)($restaurant_data['corkage_fee_amount'] ?? '');
            if (preg_match('/\d+(?:\.\d+)?/', $amount_raw, $m)) {
                $corkage_amount_normalized = $m[0];
            } else {
                $corkage_amount_normalized = '';
            }

            // 顯示文字 → ACF 值鍵（安全版，改為就地 if/elseif 避免函式宣告造成衝突）
            $pcf_raw = isset($restaurant_data['philly_corkage_fee']) ? trim($restaurant_data['philly_corkage_fee']) : '';
            $pcf = strtolower($pcf_raw);
            if ($pcf === '' || $pcf === ': -- 請選擇 --') {
                $philly_corkage_fee_value = '';
            } elseif ($pcf === 'free') {
                $philly_corkage_fee_value = 'free';
            } elseif ($pcf === 'corkage fee') {
                $philly_corkage_fee_value = 'corkage_fee';
            } elseif ($pcf === 'other') {
                $philly_corkage_fee_value = 'other';
            } else {
                $philly_corkage_fee_value = '';
            }

            $service_raw = isset($restaurant_data['byob_service_level']) ? trim($restaurant_data['byob_service_level']) : '';
            $svc = strtolower($service_raw);
            if ($svc === '' || $svc === ': -- 請選擇 --') {
                $byob_service_level_value = '';
            } elseif ($svc === 'full service (opening, pouring, decanting, chilling)' || $svc === 'full service') {
                $byob_service_level_value = 'full_service';
            } elseif ($svc === 'basic service (opening and pouring)' || $svc === 'basic service') {
                $byob_service_level_value = 'basic_service';
            } elseif ($svc === 'self-service (equipment provided)' || $svc === 'self service (equipment provided)' || $svc === 'self-service') {
                $byob_service_level_value = 'self_service';
            } elseif ($svc === 'no service (byob only, bring your own equipment)' || $svc === 'no service') {
                $byob_service_level_value = 'no_service';
            } else {
                $byob_service_level_value = '';
            }

            $show_raw = isset($restaurant_data['show_reddit_username']) ? trim($restaurant_data['show_reddit_username']) : '';
            $sr = strtolower($show_raw);
            // 標準化不同撇號與多餘空白
            $sr = str_replace(['’','`','´'], "'", $sr);
            $sr = preg_replace('/\s+/', ' ', $sr);
            if ($sr === '' || strpos($sr, ': --') === 0) {
                $show_reddit_username_value = '';
            } elseif (strpos($sr, 'yes') === 0) {
                $show_reddit_username_value = 'yes';
            } elseif (strpos($sr, 'no') === 0) {
                $show_reddit_username_value = 'no';
            } else {
                $show_reddit_username_value = '';
            }
            
            $acf_updates = array(
                'restaurant_name' => sanitize_text_field($restaurant_data['restaurant_name']),
                'address' => sanitize_text_field($restaurant_data['address'] ?? ''),
                'phone' => sanitize_text_field($restaurant_data['phone'] ?? ''),
                // 原本的 website 已改為 yelp_link
                // 'website' => esc_url_raw($restaurant_data['website'] ?? ''),
                'yelp_link' => esc_url_raw($restaurant_data['yelp_link'] ?? ''),
                'philly_corkage_fee' => $philly_corkage_fee_value,
                'corkage_fee_amount' => $corkage_amount_normalized,
                'other_corkage_policy' => sanitize_textarea_field($restaurant_data['other_corkage_policy'] ?? ''),
                'wine_service_equipment' => $equipment ?: array(),
                'philly_equipment_other_note' => sanitize_text_field($equipmentOtherNote),
                'byob_service_level' => $byob_service_level_value,
                'philly_restaurant_type' => $types ?: array(),
                'philly_restaurant_type_other_note' => sanitize_text_field($other_note),
                'philly_dining_experience' => sanitize_textarea_field($restaurant_data['philly_dining_experience'] ?? ''),
                'philly_reddit_username' => sanitize_text_field($restaurant_data['philly_reddit_username'] ?? ''),
                'philly_contact_email' => sanitize_email($restaurant_data['philly_contact_email'] ?? ''),
                'show_reddit_username' => $show_reddit_username_value,
                'last_updated' => current_time('Y-m-d'),
                'source' => sanitize_text_field($restaurant_data['source'] ?? 'philly_community_recommendation'),
                'review_status' => $review_status,
                'submitted_date' => current_time('mysql'),
                'review_date' => '',
                'review_notes' => '',
                'recommendation_count' => 1  // 新建時預設為 1
            );
            
            foreach ($acf_updates as $field_name => $field_value) {
                update_field($field_name, $field_value, $post_id);
            }

            // 雙軌寫入舊欄位（相容現有前台樣板與舊報表）
            // 1) is_charged 值轉換：Free -> no, Corkage Fee -> yes, Other -> other
            $is_charged_legacy = '';
            $philly_fee_raw = strtolower(trim($restaurant_data['philly_corkage_fee'] ?? ''));
            if ($philly_fee_raw === 'free') {
                $is_charged_legacy = 'no';
            } elseif ($philly_fee_raw === 'corkage fee') {
                $is_charged_legacy = 'yes';
            } elseif ($philly_fee_raw === 'other') {
                $is_charged_legacy = 'other';
            }

            // 2) 舊欄位映射
            // 原本的 website 已改為 yelp_link，但保留 website 欄位以備未來使用
            $legacy_updates = array(
                'restaurant_name' => sanitize_text_field($restaurant_data['restaurant_name']),
                'address' => sanitize_text_field($restaurant_data['address'] ?? ''),
                'phone' => sanitize_text_field($restaurant_data['phone'] ?? ''),
                // 'website' => esc_url_raw($restaurant_data['website'] ?? ''),
                // 不再更新舊欄位，如需可在此處加入 yelp_link -> website 的映射
                'is_charged' => $is_charged_legacy,
                'corkage_fee_amount' => $is_charged_legacy === 'yes' ? $corkage_amount_normalized : '',
                'corkage_fee_note' => sanitize_text_field($restaurant_data['other_corkage_policy'] ?? ''),
                'equipment' => $equipment ?: array(),
                'equipment_other_note' => sanitize_text_field($equipmentOtherNote),
                'open_bottle_service' => $byob_service_level_value,
                'restaurant_type' => $types ?: array(),
                'restaurant_type_other_note' => sanitize_text_field($other_note),
                'notes' => sanitize_textarea_field($restaurant_data['philly_dining_experience'] ?? ''),
            );

            foreach ($legacy_updates as $field_name => $field_value) {
                update_field($field_name, $field_value, $post_id);
            }
        }
        
        // 設定費城專用分類和標籤
        wp_set_post_terms($post_id, array('philly-byob-restaurants'), 'restaurant_category');
        wp_set_post_tags($post_id, array('Philadelphia', 'BYOB', 'Restaurant Guide', 'Community Recommendation'));
        
        return array(
            'success' => true,
            'post_id' => $post_id,
            'post_url' => get_edit_post_link($post_id, ''),
            'post_status' => $post_status,
            'review_status' => $review_status,
            'message' => 'Philadelphia BYOB restaurant draft created successfully'
        );
        
    } catch (Exception $e) {
        return new WP_Error('philly_article_creation_failed', $e->getMessage(), array('status' => 500));
    }
}

/**
 * 生成費城餐廳英文文章內容
 * 
 * @param array $restaurant_data 餐廳資料
 * @return string 文章內容
 */
function byob_generate_philly_article_content($restaurant_data) {
    $content = '';
    
    // 文章開頭
    $content .= '<h2>Restaurant Overview</h2>' . "\n";
    $content .= '<p><strong>Restaurant Name:</strong> ' . esc_html($restaurant_data['restaurant_name']) . '</p>' . "\n";
    
    if (!empty($restaurant_data['address'])) {
        $content .= '<p><strong>Address:</strong> ' . esc_html($restaurant_data['address']) . '</p>' . "\n";
    }
    
    if (!empty($restaurant_data['phone'])) {
        $content .= '<p><strong>Phone:</strong> ' . esc_html($restaurant_data['phone']) . '</p>' . "\n";
    }
    
    // 原本的 Website 顯示已改為 Yelp Link
    // if (!empty($restaurant_data['website'])) {
    //     $content .= '<p><strong>Website:</strong> <a href="' . esc_url($restaurant_data['website']) . '" target="_blank">' . esc_html($restaurant_data['website']) . '</a></p>' . "\n";
    // }
    if (!empty($restaurant_data['yelp_link'])) {
        $content .= '<p><strong>Yelp Link:</strong> <a href="' . esc_url($restaurant_data['yelp_link']) . '" target="_blank">' . esc_html($restaurant_data['yelp_link']) . '</a></p>' . "\n";
    }
    
    if (!empty($restaurant_data['philly_restaurant_type'])) {
        $type_display = $restaurant_data['philly_restaurant_type'];
        if (!empty($restaurant_data['philly_restaurant_type_other_note'])) {
            $type_display = preg_replace('/\bother\b/i', $restaurant_data['philly_restaurant_type_other_note'], $type_display);
        }
        $content .= '<p><strong>Cuisine Type:</strong> ' . esc_html($type_display) . '</p>' . "\n";
    }
    
    $content .= "\n";
    
    // BYOB 政策區塊
    $content .= '<h2>BYOB Policy</h2>' . "\n";
    
    if (!empty($restaurant_data['philly_corkage_fee'])) {
        $content .= '<p><strong>Corkage Policy:</strong> ' . esc_html($restaurant_data['philly_corkage_fee']) . '</p>' . "\n";
    }
    
    if (!empty($restaurant_data['corkage_fee_amount'])) {
        $content .= '<p><strong>Corkage Fee Amount:</strong> ' . esc_html($restaurant_data['corkage_fee_amount']) . '</p>' . "\n";
    }
    
    if (!empty($restaurant_data['other_corkage_policy'])) {
        $content .= '<p><strong>Other Corkage Policy:</strong> ' . esc_html($restaurant_data['other_corkage_policy']) . '</p>' . "\n";
    }
    
    $content .= "\n";
    
    // BYOB 設備和服務區塊
    $content .= '<h2>BYOB Equipment & Service</h2>' . "\n";
    
    if (!empty($restaurant_data['wine_service_equipment'])) {
        $equip_display = $restaurant_data['wine_service_equipment'];
        if (!empty($restaurant_data['philly_equipment_other_note'])) {
            $equip_display = preg_replace('/\bother\b/i', $restaurant_data['philly_equipment_other_note'], $equip_display);
        }
        $content .= '<p><strong>Equipment Provided:</strong> ' . esc_html($equip_display) . '</p>' . "\n";
    }
    
    if (!empty($restaurant_data['philly_equipment_other_note'])) {
        $content .= '<p><strong>Equipment Other Note:</strong> ' . esc_html($restaurant_data['philly_equipment_other_note']) . '</p>' . "\n";
    }
    
    if (!empty($restaurant_data['byob_service_level'])) {
        $content .= '<p><strong>Service Level:</strong> ' . esc_html($restaurant_data['byob_service_level']) . '</p>' . "\n";
    }
    
    $content .= "\n";
    
    // 用餐體驗區塊
    if (!empty($restaurant_data['philly_dining_experience'])) {
        $content .= '<h2>Notes</h2>' . "\n";
        $content .= '<p>' . esc_html($restaurant_data['philly_dining_experience']) . '</p>' . "\n";
        $content .= "\n";
    }
    
    // 貢獻者資訊區塊
    $content .= '<h2>Community Contribution</h2>' . "\n";
    $content .= '<p><em>This restaurant information was contributed by the Philadelphia BYOB community.</em></p>' . "\n";
    
    if (!empty($restaurant_data['philly_reddit_username'])) {
        $content .= '<p><strong>Contributor:</strong> u/' . esc_html($restaurant_data['philly_reddit_username']) . '</p>' . "\n";
    }
    
    $content .= '<p><strong>Last Updated:</strong> ' . current_time('F j, Y') . '</p>' . "\n";
    
    return $content;
}

// =============================================================================
// 🚀 註冊流程2：餐廳直接加入流程（未來開發）
// =============================================================================
// 
// 此函數將專門處理餐廳直接加入的註冊流程：
// - 接收網站表單直接提交的資料
// - 處理用戶帳號建立、權限驗證
// - 調用共用函數建立發布狀態餐廳文章
// - 立即上架，無需審核
// 
// 註冊流程：網站表單 → 建立用戶帳號 → 立即建立發布狀態餐廳 → 自動上架
// 
// 注意：此函數尚未實作，將在後續開發中建立
// =============================================================================

/**
 * 處理餐廳直接加入請求，建立發布狀態餐廳文章
 * 
 * 此函數將專門處理直接加入註冊流程：
 * - 接收網站表單資料
 * - 建立用戶帳號和權限
 * - 調用共用函數建立發布狀態餐廳文章
 * - 餐廳狀態：發布（立即上架）
 * 
 * @param array $form_data 表單提交的資料
 * @return array|WP_Error 成功返回文章資訊，失敗返回錯誤
 */
function byob_create_direct_restaurant($form_data) {
    // TODO: 此函數將在後續開發中實作
    // 目前返回錯誤，表示功能尚未完成
    
    return new WP_Error(
        'function_not_implemented', 
        '餐廳直接加入功能尚未實作，將在後續開發中完成', 
        array('status' => 501)
    );
}

// 記錄 API 呼叫
function byob_log_api_call($post_id, $params, $status) {
    $log_entry = array(
        'timestamp' => current_time('mysql'),
        'post_id' => $post_id,
        'params' => $params,
        'status' => $status
    );
    
    $logs = get_option('byob_api_logs', array());
    $logs[] = $log_entry;
    
    // 只保留最近100筆記錄
    if (count($logs) > 100) {
        $logs = array_slice($logs, -100);
    }
    
    update_option('byob_api_logs', $logs);
}

// 會員系統初始化
function byob_init_membership_systems() {
    $features = byob_get_feature_settings();
    
    // 檢查檔案是否存在再載入 - 使用多個可能的路徑
    // 優先檢查子主題目錄，然後是父主題目錄
    $possible_paths = array(
        get_stylesheet_directory(), // 樣式表目錄（子主題）- 優先
        get_template_directory(), // 當前主題目錄（可能是子主題）
        dirname(__FILE__), // 當前檔案目錄
        ABSPATH . 'wp-content/themes/flatsome-child', // 子主題目錄
        ABSPATH . 'wp-content/themes/flatsome' // 父主題目錄
    );
    
    $restaurant_member_file = null;
    $customer_member_file = null;
    
    // 尋找檔案
    foreach ($possible_paths as $path) {
        $restaurant_path = $path . '/restaurant-member-functions.php';
        $customer_path = $path . '/customer-member-functions.php';
        
        if (!$restaurant_member_file && file_exists($restaurant_path)) {
            $restaurant_member_file = $restaurant_path;
        }
        if (!$customer_member_file && file_exists($customer_path)) {
            $customer_member_file = $customer_path;
        }
    }
    
    // 新增除錯資訊
    error_log('BYOB: 主題目錄: ' . get_template_directory());
    error_log('BYOB: 當前檔案目錄: ' . dirname(__FILE__));
    error_log('BYOB: 餐廳會員檔案路徑: ' . ($restaurant_member_file ?: '未找到'));
    error_log('BYOB: 客人會員檔案路徑: ' . ($customer_member_file ?: '未找到'));
    
    // 載入餐廳業者會員系統（如果啟用）
    if ($features['restaurant_member_system'] && $restaurant_member_file) {
        require_once $restaurant_member_file;
        if (function_exists('byob_init_restaurant_member_system')) {
            byob_init_restaurant_member_system();
        }
        // 立即註冊重寫規則
        if (function_exists('byob_add_rewrite_rules')) {
            byob_add_rewrite_rules();
        }
        if (function_exists('byob_add_query_vars')) {
            add_filter('query_vars', 'byob_add_query_vars');
        }
    } else {
        if (!$features['restaurant_member_system']) {
            error_log('BYOB: 餐廳業者會員系統已停用');
        } else {
            error_log('BYOB: restaurant-member-functions.php 檔案不存在');
        }
    }
    
    // 載入一般客人會員系統（如果啟用）
    if ($features['customer_member_system'] && $customer_member_file) {
        require_once $customer_member_file;
        if (function_exists('byob_init_customer_member_system')) {
            byob_init_customer_member_system();
        }
    } else {
        if (!$features['customer_member_system']) {
            error_log('BYOB: 一般客人會員系統已停用');
        } else {
            error_log('BYOB: customer-member-functions.php 檔案不存在');
        }
    }
}

// 在 WordPress 初始化時載入會員系統
add_action('init', 'byob_init_membership_systems');

// 處理AJAX發送邀請請求
add_action('wp_ajax_byob_send_invitation', 'byob_handle_send_invitation_ajax');

function byob_handle_send_invitation_ajax() {
    // 檢查nonce
    if (!wp_verify_nonce($_POST['nonce'], 'byob_send_invitation')) {
        wp_die('安全驗證失敗');
    }
    
    // 檢查權限
    if (!current_user_can('manage_options')) {
        wp_die('權限不足');
    }
    
    $restaurant_id = intval($_POST['restaurant_id']);
    
    if (!$restaurant_id) {
        wp_send_json_error('無效的餐廳ID');
        return;
    }
    
    // 檢查餐廳是否存在
    $restaurant = get_post($restaurant_id);
    if (!$restaurant || $restaurant->post_type !== 'restaurant') {
        wp_send_json_error('餐廳不存在');
        return;
    }
    
    // 檢查是否已經有會員
    $owner_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    if ($owner_id) {
        wp_send_json_error('該餐廳已有會員註冊');
        return;
    }
    
    // 發送邀請郵件
    $result = byob_send_approval_notification($restaurant_id);
    
    if ($result) {
        wp_send_json_success('邀請郵件已成功發送');
    } else {
        wp_send_json_error('郵件發送失敗');
    }
}

// 設定餐廳列表頁每頁顯示30家餐廳
function byob_custom_restaurant_posts_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('restaurant')) {
            $query->set('posts_per_page', 30);
        }
    }
}
add_action('pre_get_posts', 'byob_custom_restaurant_posts_per_page');

// 確保選單在正確時機註冊
add_action('admin_menu', function() {
    // 使用與初始化相同的邏輯尋找檔案
    // 優先檢查子主題目錄，然後是父主題目錄
    $possible_paths = array(
        get_stylesheet_directory(), // 樣式表目錄（子主題）- 優先
        get_template_directory(), // 當前主題目錄（可能是子主題）
        dirname(__FILE__), // 當前檔案目錄
        ABSPATH . 'wp-content/themes/flatsome-child', // 子主題目錄
        ABSPATH . 'wp-content/themes/flatsome' // 父主題目錄
    );
    
    $restaurant_member_file = null;
    foreach ($possible_paths as $path) {
        $restaurant_path = $path . '/restaurant-member-functions.php';
        if (file_exists($restaurant_path)) {
            $restaurant_member_file = $restaurant_path;
            break;
        }
    }
    
    if ($restaurant_member_file) {
        require_once $restaurant_member_file;
        
        // 註冊審核管理選單 - 已移至主選單註冊區塊，避免重複
        // if (function_exists('byob_add_review_management_menu')) {
        //     byob_add_review_management_menu();
        // }
        
        // 註冊會員管理選單
        if (function_exists('byob_add_member_management_menu')) {
            byob_add_member_management_menu();
        }
        
        // 註冊餐廳業者選單
        if (function_exists('byob_add_restaurant_owner_menu')) {
            byob_add_restaurant_owner_menu();
        }
    }
}, 20);

// 統一權限檢查功能
function byob_check_user_permissions($user_id, $restaurant_id, $permission_type) {
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return false;
    }
    
    switch ($permission_type) {
        case 'edit_restaurant':
            // 檢查是否為餐廳業者且擁有該餐廳
            if (in_array('restaurant_owner', $user->roles)) {
                $owner_restaurant_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
                return $owner_restaurant_id == $user_id;
            }
            break;
            
        case 'view_restaurant_stats':
            // 檢查是否為餐廳業者
            return in_array('restaurant_owner', $user->roles);
            
        default:
            return false;
    }
}

// 新增會員系統相關 REST API 端點
add_action('rest_api_init', function () {
    $features = byob_get_feature_settings();
    
    // 邀請碼系統 API（如果啟用）
    if ($features['invitation_system']) {
        register_rest_route('byob/v1', '/restaurant/(?P<id>\d+)/invitation', array(
            'methods' => 'POST',
            'callback' => 'byob_generate_restaurant_invitation',
            'permission_callback' => function() {
                return current_user_can('administrator');
            },
        ));
    }

    register_rest_route('byob/v1', '/restaurant/(?P<id>\d+)/owner', array(
        'methods' => 'GET',
        'callback' => 'byob_get_restaurant_owner',
        'permission_callback' => '__return_true',
    ));
});

// 生成餐廳邀請
function byob_generate_restaurant_invitation($request) {
    $restaurant_id = $request->get_param('id');
    $restaurant = get_post($restaurant_id);
    
    if (!$restaurant || $restaurant->post_type !== 'restaurant') {
        return new WP_Error('restaurant_not_found', '餐廳不存在', array('status' => 404));
    }
    
    // 生成邀請碼
    $invitation_code = wp_generate_password(12, false);
    $expires = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    // 儲存邀請碼到資料庫
    $invitation_data = array(
        'code' => $invitation_code,
        'restaurant_id' => $restaurant_id,
        'expires' => $expires,
        'used' => false,
        'created' => current_time('mysql')
    );
    
    update_post_meta($restaurant_id, '_byob_invitation_code', $invitation_data);
    
    return array(
        'success' => true,
        'invitation_code' => $invitation_code,
        'restaurant_name' => $restaurant->post_title
    );
}

// 獲取餐廳業者資訊
function byob_get_restaurant_owner($request) {
    $restaurant_id = $request->get_param('id');
    $owner_id = get_post_meta($restaurant_id, '_restaurant_owner_id', true);
    
    if (!$owner_id) {
        return array('has_owner' => false);
    }
    
    $owner = get_user_by('id', $owner_id);
    if (!$owner) {
        return array('has_owner' => false);
    }
    
    return array(
        'has_owner' => true,
        'owner_id' => $owner_id,
        'owner_name' => $owner->display_name,
        'owner_email' => $owner->user_email
    );
}

// 管理員設定頁面
function byob_api_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('權限不足');
    }
    
    if (isset($_POST['submit'])) {
        update_option('byob_api_key', sanitize_text_field($_POST['api_key']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $api_key = get_option('byob_api_key', 'byob-secret-key-2025');
    
    echo '<div class="wrap">';
    echo '<h1>BYOB API Settings</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr><th scope="row">API Key</th><td><input type="text" name="api_key" value="' . esc_attr($api_key) . '" class="regular-text" /></td></tr>';
    echo '</table>';
    echo '<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Settings" /></p>';
    echo '</form>';
    echo '</div>';
}

// 新增管理員選單
add_action('admin_menu', function() {
    add_options_page('BYOB API 設定', 'BYOB API', 'manage_options', 'byob-api-settings', 'byob_api_settings_page');
    
    // 新增功能開關管理頁面
    add_submenu_page(
        'tools.php',
        'BYOB 功能開關',
        'BYOB 功能開關',
        'manage_options',
        'byob-feature-toggle',
        'byob_feature_toggle_page'
    );
    
    // 新增簡化的會員系統狀態檢查選單
    add_submenu_page(
        'tools.php',
        'BYOB 系統狀態',
        'BYOB 系統狀態',
        'manage_options',
        'byob-system-status',
        'byob_system_status_page'
    );
    
    // 新增餐廳審核管理頁面
    add_submenu_page(
        'edit.php?post_type=restaurant',
        '餐廳審核管理',
        '審核管理',
        'manage_options',
        'restaurant-review',
        'byob_restaurant_review_admin_page'
    );
    
    // 新增抽獎管理頁面
    add_submenu_page(
        'edit.php?post_type=restaurant',
        '抽獎管理',
        '抽獎管理',
        'manage_options',
        'lottery-management',
        'byob_lottery_management_page'
    );
    
    // 移除發布管理頁面 - 改為審核通過即立刻發布
    // add_submenu_page(
    //     'edit.php?post_type=restaurant',
    //     '餐廳發布管理',
    //     '發布管理',
    //     'manage_options',
    //     'restaurant-publish',
    //     'byob_publish_management_admin_page'
    // );
    
    // 移除檔案上傳工具選單 - 不再需要
});

// 除錯頁面
function byob_debug_page() {
    if (!current_user_can('administrator')) {
        return new WP_Error('permission_denied', '權限不足', array('status' => 403));
    }
    
    // 檢查會員系統檔案 - 使用與初始化相同的邏輯
    $possible_paths = array(
        get_template_directory(), // 當前主題目錄（可能是子主題）
        get_stylesheet_directory(), // 樣式表目錄（子主題）
        get_template_directory(), // 父主題目錄
        dirname(__FILE__), // 當前檔案目錄
        ABSPATH . 'wp-content/themes/flatsome',
        ABSPATH . 'wp-content/themes/flatsome-child'
    );
    
    $restaurant_member_file = null;
    $customer_member_file = null;
    
    // 尋找檔案
    foreach ($possible_paths as $path) {
        $restaurant_path = $path . '/restaurant-member-functions.php';
        $customer_path = $path . '/customer-member-functions.php';
        
        if (!$restaurant_member_file && file_exists($restaurant_path)) {
            $restaurant_member_file = $restaurant_path;
        }
        if (!$customer_member_file && file_exists($customer_path)) {
            $customer_member_file = $customer_path;
        }
    }
    
    $debug_info = array(
        'wordpress_version' => get_bloginfo('version'),
        'php_version' => PHP_VERSION,
        'acf_loaded' => function_exists('get_field'),
        'restaurant_posts_count' => wp_count_posts('restaurant')->publish,
        'api_key' => get_option('byob_api_key', 'byob-secret-key-2025'),
        'template_directory' => get_template_directory(),
        'stylesheet_directory' => get_stylesheet_directory(),
        'membership_system' => array(
            'restaurant_member_file_exists' => $restaurant_member_file !== null,
            'customer_member_file_exists' => $customer_member_file !== null,
            'restaurant_member_file_path' => $restaurant_member_file,
            'customer_member_file_path' => $customer_member_file,
            'restaurant_owner_role_exists' => get_role('restaurant_owner') !== null,
            'customer_role_exists' => get_role('customer') !== null,
            'restaurant_owner_users_count' => count(get_users(array('role' => 'restaurant_owner'))),
            'customer_users_count' => count(get_users(array('role' => 'customer')))
        )
    );
    
    return $debug_info;
}

// 測試端點
function byob_test_endpoint($request) {
    $received_params = $request->get_params();
    $headers = $request->get_headers();
    
    return array(
        'success' => true,
        'message' => '測試端點正常運作',
        'received_params' => $received_params,
        'headers' => $headers,
        'timestamp' => current_time('mysql'),
        'server_info' => array(
            'php_version' => PHP_VERSION,
            'wordpress_version' => get_bloginfo('version'),
            'rest_api_url' => rest_url('byob/v1/')
        )
    );
}

// 簡化的系統狀態檢查頁面
function byob_system_status_page() {
    if (!current_user_can('manage_options')) {
        wp_die('權限不足');
    }
    
    // 檢查會員系統檔案 - 使用與初始化相同的邏輯
    $possible_paths = array(
        get_template_directory(), // 當前主題目錄（可能是子主題）
        get_stylesheet_directory(), // 樣式表目錄（子主題）
        get_template_directory(), // 父主題目錄
        dirname(__FILE__), // 當前檔案目錄
        ABSPATH . 'wp-content/themes/flatsome',
        ABSPATH . 'wp-content/themes/flatsome-child'
    );
    
    $restaurant_member_file = null;
    $customer_member_file = null;
    
    // 尋找檔案
    foreach ($possible_paths as $path) {
        $restaurant_path = $path . '/restaurant-member-functions.php';
        $customer_path = $path . '/customer-member-functions.php';
        
        if (!$restaurant_member_file && file_exists($restaurant_path)) {
            $restaurant_member_file = $restaurant_path;
        }
        if (!$customer_member_file && file_exists($customer_path)) {
            $customer_member_file = $customer_path;
        }
    }
    
    // 檢查角色
    $restaurant_owner_role = get_role('restaurant_owner');
    $customer_role = get_role('customer');
    
    // 統計使用者
    $restaurant_owners = get_users(array('role' => 'restaurant_owner'));
    $customers = get_users(array('role' => 'customer'));
    
    echo '<div class="wrap">';
    echo '<h1>BYOB System Status Check</h1>';
    
    echo '<h2>📁 File Status</h2>';
    echo '<table class="widefat">';
    echo '<tr><th>File</th><th>Status</th><th>Path</th></tr>';
    echo '<tr><td>Restaurant Owner Member System</td><td>' . ($restaurant_member_file ? '✅ Exists' : '❌ Not Found') . '</td><td>' . ($restaurant_member_file ?: 'Not Found') . '</td></tr>';
    echo '<tr><td>Customer Member System</td><td>' . ($customer_member_file ? '✅ Exists' : '❌ Not Found') . '</td><td>' . ($customer_member_file ?: 'Not Found') . '</td></tr>';
    echo '</table>';
    
    echo '<h2>👥 Role Status</h2>';
    echo '<table class="widefat">';
    echo '<tr><th>Role</th><th>Status</th><th>User Count</th></tr>';
    echo '<tr><td>Restaurant Owner (restaurant_owner)</td><td>' . ($restaurant_owner_role ? '✅ Created' : '❌ Not Created') . '</td><td>' . count($restaurant_owners) . '</td></tr>';
    echo '<tr><td>Customer (customer)</td><td>' . ($customer_role ? '✅ Created' : '❌ Not Created') . '</td><td>' . count($customers) . '</td></tr>';
    echo '</table>';
    
    echo '<h2>🔧 Feature Status</h2>';
    echo '<table class="widefat">';
    echo '<tr><th>Feature</th><th>Setting Status</th><th>Actual Status</th></tr>';
    
    $features = byob_get_feature_settings();
    
    echo '<tr><td>Restaurant Owner Member System</td><td>' . ($features['restaurant_member_system'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>' . (function_exists('byob_init_restaurant_member_system') ? '✅ Loaded' : '❌ Not Loaded') . '</td></tr>';
    echo '<tr><td>Customer Member System</td><td>' . ($features['customer_member_system'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>' . (function_exists('byob_init_customer_member_system') ? '✅ Loaded' : '❌ Not Loaded') . '</td></tr>';
    echo '<tr><td>Invitation System</td><td>' . ($features['invitation_system'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>' . (function_exists('byob_generate_restaurant_invitation') ? '✅ Available' : '❌ Not Available') . '</td></tr>';
    echo '<tr><td>Favorite System</td><td>' . ($features['favorite_system'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>' . (function_exists('byob_toggle_favorite') ? '✅ Available' : '❌ Not Available') . '</td></tr>';
    echo '<tr><td>Review System</td><td>' . ($features['review_system'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>' . (function_exists('byob_add_review') ? '✅ Available' : '❌ Not Available') . '</td></tr>';
    echo '<tr><td>Points System</td><td>' . ($features['points_system'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>' . (function_exists('byob_add_points') ? '✅ Available' : '❌ Not Available') . '</td></tr>';
    echo '<tr><td>REST API Endpoints</td><td>' . ($features['api_endpoints'] ? '✅ Enabled' : '❌ Disabled') . '</td><td>✅ Registered</td></tr>';
    echo '</table>';
    
    echo '<h2>📊 Statistics</h2>';
    echo '<table class="widefat">';
    echo '<tr><th>Item</th><th>Count</th></tr>';
    echo '<tr><td>Total Restaurant Posts</td><td>' . wp_count_posts('restaurant')->publish . '</td></tr>';
    echo '<tr><td>Pending Review Restaurants</td><td>' . wp_count_posts('restaurant')->draft . '</td></tr>';
    echo '<tr><td>Restaurant Owner Members</td><td>' . count($restaurant_owners) . '</td></tr>';
    echo '<tr><td>Customer Members</td><td>' . count($customers) . '</td></tr>';
    echo '</table>';
    
    echo '<h2>📋 Manual Deployment Instructions</h2>';
    echo '<div class="notice notice-info">';
    echo '<p><strong>If file status shows "Not Found", please manually upload the following files to the theme directory:</strong></p>';
    echo '<ul>';
    echo '<li><code>restaurant-member-functions.php</code></li>';
    echo '<li><code>customer-member-functions.php</code></li>';
    echo '</ul>';
    echo '<p><strong>Upload Path:</strong> <code>' . get_template_directory() . '/</code></p>';
    echo '<p><strong>Current Check Paths:</strong></p>';
    echo '<ul>';
    echo '<li>Restaurant Owner File: <code>' . ($restaurant_member_file ?: 'Not Found') . '</code></li>';
    echo '<li>Customer File: <code>' . ($customer_member_file ?: 'Not Found') . '</code></li>';
    echo '</ul>';
    echo '<p><strong>The system checks the following paths:</strong></p>';
    echo '<ul>';
    foreach ($possible_paths as $path) {
        echo '<li><code>' . $path . '/</code></li>';
    }
    echo '</ul>';
    echo '<p><strong>主題目錄資訊：</strong></p>';
    echo '<ul>';
    echo '<li>當前主題目錄：<code>' . get_template_directory() . '</code></li>';
    echo '<li>樣式表目錄（子主題）：<code>' . get_stylesheet_directory() . '</code></li>';
    echo '</ul>';
    echo '</div>';
    
    echo '<h2>🧪 快速連結</h2>';
    echo '<p><a href="' . admin_url('admin.php?page=byob-api-settings') . '" class="button">API 設定</a> ';
    echo '<a href="' . admin_url('edit.php?post_type=restaurant&page=restaurant-review') . '" class="button">審核管理</a> ';
    echo '<a href="' . admin_url('edit.php?post_type=restaurant&page=byob-member-management') . '" class="button">會員管理</a> ';
    echo '<a href="' . admin_url('tools.php?page=byob-feature-toggle') . '" class="button">功能開關</a></p>';
    
    echo '</div>';
}

// 功能開關管理頁面
function byob_feature_toggle_page() {
    if (!current_user_can('manage_options')) {
        wp_die('權限不足');
    }
    
    if (isset($_POST['submit'])) {
        $features = array(
            'restaurant_member_system' => isset($_POST['restaurant_member_system']),
            'customer_member_system' => isset($_POST['customer_member_system']),
            'invitation_system' => isset($_POST['invitation_system']),
            'favorite_system' => isset($_POST['favorite_system']),
            'review_system' => isset($_POST['review_system']),
            'points_system' => isset($_POST['points_system']),
            'api_endpoints' => isset($_POST['api_endpoints'])
        );
        
        update_option('byob_feature_settings', $features);
        echo '<div class="notice notice-success"><p>功能設定已儲存！</p></div>';
    }
    
    $current_features = get_option('byob_feature_settings', byob_get_feature_settings());
    
    echo '<div class="wrap">';
    echo '<h1>BYOB 功能開關管理</h1>';
    echo '<p>在此頁面可以控制 BYOB 系統的各項功能啟用狀態。</p>';
    
    echo '<form method="post">';
    echo '<table class="form-table">';
    
    echo '<tr><th scope="row">餐廳業者會員系統</th><td>';
    echo '<label><input type="checkbox" name="restaurant_member_system" ' . ($current_features['restaurant_member_system'] ? 'checked' : '') . ' /> 啟用餐廳業者會員系統</label>';
    echo '<p class="description">允許餐廳業者註冊、登入和管理餐廳資料</p>';
    echo '</td></tr>';
    
    echo '<tr><th scope="row">一般客人會員系統</th><td>';
    echo '<label><input type="checkbox" name="customer_member_system" ' . ($current_features['customer_member_system'] ? 'checked' : '') . ' /> 啟用一般客人會員系統</label>';
    echo '<p class="description">允許一般客人註冊、登入和使用收藏功能</p>';
    echo '</td></tr>';
    
    echo '<tr><th scope="row">邀請碼系統</th><td>';
    echo '<label><input type="checkbox" name="invitation_system" ' . ($current_features['invitation_system'] ? 'checked' : '') . ' /> 啟用邀請碼系統</label>';
    echo '<p class="description">允許管理員為餐廳生成邀請碼</p>';
    echo '</td></tr>';
    
    echo '<tr><th scope="row">收藏系統</th><td>';
    echo '<label><input type="checkbox" name="favorite_system" ' . ($current_features['favorite_system'] ? 'checked' : '') . ' /> 啟用收藏系統</label>';
    echo '<p class="description">允許客人收藏喜歡的餐廳</p>';
    echo '</td></tr>';
    
    echo '<tr><th scope="row">評論系統</th><td>';
    echo '<label><input type="checkbox" name="review_system" ' . ($current_features['review_system'] ? 'checked' : '') . ' /> 啟用評論系統</label>';
    echo '<p class="description">允許客人對餐廳進行評論和評分</p>';
    echo '</td></tr>';
    
    echo '<tr><th scope="row">積分系統</th><td>';
    echo '<label><input type="checkbox" name="points_system" ' . ($current_features['points_system'] ? 'checked' : '') . ' /> 啟用積分系統</label>';
    echo '<p class="description">允許客人透過各種活動賺取積分</p>';
    echo '</td></tr>';
    
    echo '<tr><th scope="row">REST API 端點</th><td>';
    echo '<label><input type="checkbox" name="api_endpoints" ' . ($current_features['api_endpoints'] ? 'checked' : '') . ' /> 啟用 REST API 端點</label>';
    echo '<p class="description">提供外部系統整合的 API 介面</p>';
    echo '</td></tr>';
    
    echo '</table>';
    echo '<p class="submit"><input type="submit" name="submit" class="button-primary" value="Save Settings" /></p>';
    echo '</form>';
    
    echo '<h2>📋 功能說明</h2>';
    echo '<div class="notice notice-info">';
    echo '<p><strong>注意事項：</strong></p>';
    echo '<ul>';
    echo '<li>修改功能設定後，建議重新載入系統狀態檢查頁面確認變更</li>';
    echo '<li>停用功能後，相關的 API 端點和前端功能將無法使用</li>';
    echo '<li>評論系統和積分系統建議在系統穩定後再啟用</li>';
    echo '</ul>';
    echo '</div>';
    
    echo '</div>';
}

// 更新功能設定函數，支援資料庫儲存
function byob_get_feature_settings() {
    $db_features = get_option('byob_feature_settings');
    if ($db_features) {
        return $db_features;
    }
    
    // 預設設定
    return array(
        'restaurant_member_system' => true,    // 餐廳業者會員系統
        'customer_member_system' => true,      // 一般客人會員系統
        'invitation_system' => true,           // 邀請碼系統
        'favorite_system' => true,             // 收藏系統
        'review_system' => false,              // 評論系統 - 初期關閉
        'points_system' => false,              // 積分系統 - 初期關閉
        'api_endpoints' => true,               // REST API 端點
    );
}

// =============================================================================
// 一鍵註冊邀請系統
// =============================================================================

// 當餐廳文章發布時自動發送邀請（使用審核通過時的email格式）
add_action('transition_post_status', 'byob_auto_send_invitation_on_publish', 10, 3);

function byob_auto_send_invitation_on_publish($new_status, $old_status, $post) {
    // 檢查是否為餐廳文章且從草稿變為發布
    if ($post->post_type !== 'restaurant') {
        return;
    }
    
    if ($new_status !== 'publish') {
        return;
    }
    
    if ($old_status === 'publish') {
        // 如果已經是發布狀態，不重複發送邀請
        return;
    }
    
    // 檢查功能是否啟用
    $features = byob_get_feature_settings();
    if (!$features['invitation_system']) {
        return;
    }
    
    // 檢查資料來源和聯絡資訊
    $source = get_field('source', $post->ID);
    $contact_person = get_field('contact_person', $post->ID);
    $recommender_name = get_field('customer_recommender_name', $post->ID);
    $recommender_email = get_field('customer_recommender_email', $post->ID);
    
    if ($source === 'customer_recommendation') {
        // 顧客推薦的餐廳：發送推薦者通知（如果有 email）
        if (!empty($recommender_email)) {
            // 檢查是否已經發送過推薦者通知
            $recommender_notified = get_post_meta($post->ID, '_byob_recommender_notified', true);
            if ($recommender_notified) {
                return;
            }
            
            error_log('BYOB: 顧客推薦餐廳發布，準備發送推薦者通知 - 文章ID: ' . $post->ID);
            
            $result = byob_send_recommender_notification($post->ID);
            if ($result) {
                update_post_meta($post->ID, '_byob_recommender_notified', current_time('mysql'));
                error_log('BYOB: 推薦者通知發送成功 - 文章ID: ' . $post->ID);
            
            // 記錄抽獎參與者
            byob_record_lottery_participant($post->ID, $recommender_name, $recommender_email);
            } else {
                error_log('BYOB: 推薦者通知發送失敗 - 文章ID: ' . $post->ID);
            }
        } else {
            error_log('BYOB: 顧客推薦但無推薦者email，跳過通知發送 - 文章ID: ' . $post->ID);
        }
        return; // 不執行後面的業者邀請邏輯
    }
    
    // 業者自行提交的餐廳：檢查是否有聯絡人
    if (empty($contact_person)) {
        error_log('BYOB: 業者提交但無聯絡人，跳過邀請發送 - 文章ID: ' . $post->ID);
        return;
    }
    
    // 檢查是否已經發送過邀請
    $invitation_sent = get_post_meta($post->ID, '_byob_invitation_sent', true);
    if ($invitation_sent) {
        return;
    }
    
    error_log('BYOB: 業者提交餐廳發布，準備發送邀請 - 文章ID: ' . $post->ID);
    
    // 使用審核通過時的email格式發送邀請
    $result = byob_send_approval_notification($post->ID);
    
    if ($result) {
        // 標記已發送邀請
        update_post_meta($post->ID, '_byob_invitation_sent', current_time('mysql'));
        
        error_log('BYOB: 邀請發送成功 - 文章ID: ' . $post->ID);
    } else {
        error_log('BYOB: 邀請發送失敗 - 文章ID: ' . $post->ID);
    }
}

// 注意：byob_send_restaurant_invitation 函數已被移除
// 改為使用 byob_send_approval_notification 函數統一發送email

// 創建邀請資料表
function byob_create_invitation_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'byob_invitations';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        token varchar(32) NOT NULL,
        restaurant_id bigint(20) NOT NULL,
        email varchar(100) NOT NULL,
        contact_person varchar(100) NOT NULL,
        expires datetime NOT NULL,
        used tinyint(1) DEFAULT 0,
        used_at datetime NULL,
        user_id bigint(20) NULL,
        created datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY token (token),
        KEY restaurant_id (restaurant_id),
        KEY email (email)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// 注意：byob_send_invitation_email 函數已被移除
// 改為使用 byob_send_approval_notification 函數統一發送email

// =============================================================================
// 註冊流程攔截和自動設定
// =============================================================================

// 載入邀請處理器
$invitation_handler_path = __DIR__ . '/invitation-handler.php';
if (file_exists($invitation_handler_path)) {
    require_once $invitation_handler_path;
} else {
    error_log('BYOB: invitation-handler.php 檔案不存在: ' . $invitation_handler_path);
}

// 確保重寫規則被正確載入
add_action('init', 'byob_maybe_flush_rewrite_rules');

function byob_maybe_flush_rewrite_rules() {
    // 檢查是否需要刷新重寫規則
    $rewrite_rules_version = get_option('byob_rewrite_rules_version', '0');
    $current_version = '1.0'; // 當重寫規則有更新時，增加這個版本號
    
    if ($rewrite_rules_version !== $current_version) {
        flush_rewrite_rules();
        update_option('byob_rewrite_rules_version', $current_version);
        error_log('BYOB: 重寫規則已刷新');
    }
}

// =============================================================================
// 審核通過通知email發送函數
// =============================================================================

/**
 * 發送審核通過通知和邀請郵件
 */
function byob_send_approval_notification($restaurant_id) {
    $restaurant = get_post($restaurant_id);
    $contact_email = get_field('email', $restaurant_id);
    $contact_person = get_field('contact_person', $restaurant_id);
    
    if (!$contact_email) {
        return false;
    }
    
    // 生成邀請碼
    $invitation_code = wp_generate_password(12, false);
    $expires = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    // 儲存邀請碼
    $invitation_data = array(
        'code' => $invitation_code,
        'restaurant_id' => $restaurant_id,
        'expires' => $expires,
        'used' => false,
        'created' => current_time('mysql')
    );
    
    update_post_meta($restaurant_id, '_byob_invitation_code', $invitation_data);
    
    // 建立邀請連結
    $invitation_url = home_url('/register/restaurant?token=' . $invitation_code);
    
    // 郵件內容
    $subject = '🎉 恭喜！您的餐廳「' . $restaurant->post_title . '」已通過審核並上架 - BYOB 台北餐廳地圖';
    
    $message = '
    <div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
        <div style="background-color: #8b2635; color: white; padding: 20px; text-align: center;">
            <h1>BYOB 台北餐廳地圖</h1>
        </div>
        
        <div style="padding: 20px; background-color: #f9f9f9;">
            <h2>親愛的 ' . ($contact_person ?: $restaurant->post_title . ' 負責人') . '，</h2>
            
            <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #155724; margin: 0;">🎉 恭喜！您的餐廳已通過審核並成功上架！</h3>
            </div>
            
            <div style="background-color: white; padding: 15px; margin: 20px 0; border-left: 4px solid #8b2635;">
                <strong>您的餐廳頁面：</strong><br>
                <a href="' . get_permalink($restaurant_id) . '">' . get_permalink($restaurant_id) . '</a>
            </div>
            
            <p>為了讓您能更好地管理餐廳資訊，我們邀請您註冊會員帳號：</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . $invitation_url . '" style="background-color: #8b2635; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    🔗 立即註冊會員
                </a>
            </div>
            
            <h3>會員功能包括：</h3>
            <ul>
                <li>✅ 修改餐廳基本資訊</li>
                <li>✅ 上傳餐廳照片</li>
                <li>✅ 更新 BYOB 政策</li>
            </ul>
            
            <h4 style="color: #666; font-style: italic; margin-top: 20px;">🚧 即將推出的功能（敬請期待）：</h4>
            <ul style="color: #888;">
                <li>🔜 查看瀏覽統計</li>
                <li>🔜 回覆顧客評論</li>
            </ul>
            
            <p><strong>邀請碼：</strong> ' . $invitation_code . '</p>
            <p><small>此邀請碼將於 7 天後過期</small></p>
            
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
 * 發送推薦者成功通知
 */
function byob_send_recommender_notification($restaurant_id) {
    $restaurant = get_post($restaurant_id);
    $recommender_name = get_field('customer_recommender_name', $restaurant_id);
    $recommender_email = get_field('customer_recommender_email', $restaurant_id);
    
    // 驗證推薦者資訊
    if (!$recommender_email || !$restaurant) {
        return false;
    }
    
    // 取得餐廳詳細資訊
    $restaurant_data = byob_get_restaurant_display_data($restaurant_id);
    
    // 生成 Email 內容
    $subject = '🎉 你推薦的餐廳已成功上架 BYOB 平台！';
    
    $message = byob_generate_recommender_notification_html(
        $recommender_name,
        $restaurant_data,
        $restaurant_id
    );
    
    // 發送郵件
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sent = wp_mail($recommender_email, $subject, $message, $headers);
    
    return $sent;
}

/**
 * 取得餐廳顯示資料
 */
function byob_get_restaurant_display_data($restaurant_id) {
    return array(
        'name' => get_the_title($restaurant_id),
        'address' => get_field('address', $restaurant_id),
        'phone' => get_field('phone', $restaurant_id),
        'website' => get_field('website', $restaurant_id),
        'is_charged' => get_field('is_charged', $restaurant_id),
        'corkage_fee_amount' => get_field('corkage_fee_amount', $restaurant_id),
        'corkage_fee_note' => get_field('corkage_fee_note', $restaurant_id),
        'equipment' => get_field('equipment', $restaurant_id),
        'equipment_other_note' => get_field('equipment_other_note', $restaurant_id),
        'permalink' => get_permalink($restaurant_id)
    );
}

/**
 * 生成推薦者通知 HTML 內容
 */
function byob_generate_recommender_notification_html($recommender_name, $restaurant_data, $restaurant_id) {
    // 使用內嵌 HTML 模板（暫時使用簡單版本，之後可以改為讀取外部檔案）
    $html_template = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>推薦成功通知</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f9f9f9;
            }
            .container {
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
            }
            .logo {
                font-size: 24px;
                font-weight: bold;
                color: #8B4513;
                margin-bottom: 10px;
            }
            .title {
                font-size: 20px;
                color: #2c3e50;
                margin-bottom: 20px;
            }
            .restaurant-info {
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                border-left: 4px solid #8B4513;
            }
            .info-item {
                margin: 10px 0;
            }
            .info-label {
                font-weight: bold;
                color: #495057;
            }
            .prize-section {
                background-color: #fff3cd;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
                border: 1px solid #ffeaa7;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                color: #666;
                font-size: 14px;
            }
            .social-links {
                margin: 15px 0;
            }
            .social-links a {
                color: #8B4513;
                text-decoration: none;
                margin: 0 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">🍷 BYOB Map</div>
                <h1 class="title">🎉 推薦成功！</h1>
            </div>

            <p>Hi <strong>[推薦者姓名]</strong> 👋</p>

            <p>太棒了！你推薦的「<strong>[餐廳名稱]</strong>」已經通過審核並成功上架我們的 BYOB 平台了！</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="[餐廳頁面連結]" 
                   style="display: inline-block; background-color: rgba(139, 38, 53, 0.8); color: #f8f9fa; text-decoration: none; padding: 16px 32px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">
                    🔗 立即查看餐廳頁面
                </a>
            </div>

            <p>感謝你的推薦，讓更多愛酒的朋友能找到這個好地方！你的貢獻讓台北變得更開瓶友善 🥂</p>

            <div class="prize-section">
                <h3>🎁 抽獎活動說明</h3>
                <p>你已經獲得本月推薦抽獎資格，獎品包括：</p>
                <ul>
                    <li>🎫 一獎：進口酒商電子禮券</li>
                    <li>🥂 二獎：高級進口紅(白)酒杯</li>
                </ul>
                <p>每月月底我們會抽出幸運得主，記得關注我們的社群更新喔！</p>
            </div>
            
            <div style="background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; margin: 20px 0; border-radius: 6px;">
                <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">📢 額外抽獎機會</h4>
                <p style="color: #0056b3; margin: 0; font-size: 14px; line-height: 1.5;">
                    想要額外1次抽獎機會嗎？<br>
                    1. 點擊連結 <a href="https://reurl.cc/DOVDdO" style="color: #0056b3; text-decoration: underline;">https://reurl.cc/DOVDdO</a> 開啟抽獎活動貼文，然後分享到你的社群媒體<br>
                    2. 分享後回覆此Email並附上你的分享貼文連結<br>
                    3. 我們確認後會為你增加1次抽獎機會！
                </p>
            </div>

            <h3>💡 繼續推薦</h3>
            <p>知道其他可以自帶酒的餐廳嗎？歡迎繼續推薦，每成功推薦1家即增加1次抽獎機會！</p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="https://reurl.cc/qYEmDR" 
                   style="display: inline-block; background-color: rgba(139, 38, 53, 0.8); color: #f8f9fa; text-decoration: none; padding: 16px 32px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">
                    📝 推薦更多餐廳
                </a>
            </div>

            <div class="footer">
                <p><strong>Cheers！<br>— 台北 BYOB 小隊</strong></p>
                
                <div class="social-links">
                    <p>🔍 <a href="https://byobmap.com/">參觀我們的平台</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>';
    
    $replacements = array(
        '[推薦者姓名]' => $recommender_name ?: '朋友',
        '[餐廳名稱]' => $restaurant_data['name'],
        '[餐廳地址]' => $restaurant_data['address'],
        '[開瓶費條件]' => byob_format_corkage_fee($restaurant_data),
        '[酒器設備清單]' => byob_format_equipment($restaurant_data),
        '[電話/網站]' => byob_format_contact_info($restaurant_data),
        '[餐廳頁面連結]' => $restaurant_data['permalink'],
        '[顧客推薦表單連結]' => 'https://forms.gle/NXgsYamUnv7KhznP9'
    );
    
    return str_replace(array_keys($replacements), array_values($replacements), $html_template);
}

// =============================================================================
// 🎁 抽獎活動系統
// =============================================================================

/**
 * 註冊抽獎相關的 Post Type
 */
function byob_register_lottery_post_types() {
    // 抽獎參與者 Post Type
    register_post_type('lottery_participant', [
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=restaurant',
        'supports' => ['title'],
        'labels' => [
            'name' => '抽獎參與者',
            'singular_name' => '參與者',
            'add_new' => '新增參與者',
            'add_new_item' => '新增參與者',
            'edit_item' => '編輯參與者',
            'new_item' => '新增參與者',
            'view_item' => '查看參與者',
            'search_items' => '搜尋參與者',
            'not_found' => '找不到參與者',
            'not_found_in_trash' => '回收桶中找不到參與者'
        ]
    ]);
    
    // 抽獎結果 Post Type
    register_post_type('lottery_result', [
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=restaurant',
        'supports' => ['title'],
        'labels' => [
            'name' => '抽獎結果',
            'singular_name' => '結果',
            'add_new' => '新增結果',
            'add_new_item' => '新增結果',
            'edit_item' => '編輯結果',
            'new_item' => '新增結果',
            'view_item' => '查看結果',
            'search_items' => '搜尋結果',
            'not_found' => '找不到結果',
            'not_found_in_trash' => '回收桶中找不到結果'
        ]
    ]);
}
add_action('init', 'byob_register_lottery_post_types');

// 確保 Post Type 在 ACF 初始化前註冊
add_action('acf/init', function() {
    byob_register_lottery_post_types();
    
    // 新增驗證覆蓋欄位（管理員可編輯）
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_verification_override',
            'title' => 'Verification Override',
            'fields' => array(
                array(
                    'key' => 'field_verification_override',
                    'label' => 'Verification Override',
                    'name' => 'verification_override',
                    'type' => 'select',
                    'instructions' => '管理員可手動覆蓋驗證狀態。留空則使用自動設定的 source 欄位值。',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        '' => '使用預設 source 值',
                        'philly_owner_verification' => 'Verified by Owner',
                        'philly_community_recommendation' => 'Community Recommended',
                    ),
                    'default_value' => '',
                    'allow_null' => 1,
                    'multiple' => 0,
                    'ui' => 1,
                    'ajax' => 0,
                    'return_format' => 'value',
                    'placeholder' => '選擇驗證狀態（選填）',
                ),
                array(
                    'key' => 'field_recommendation_count',
                    'label' => '推薦次數',
                    'name' => 'recommendation_count',
                    'type' => 'number',
                    'instructions' => '此餐廳被推薦的次數（可在後台修改）',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 1,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => 0,
                    'max' => 999,
                    'step' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'restaurant',
                    ),
                ),
            ),
            'menu_order' => 100,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }
});

// =============================================================================
// 驗證徽章系統
// =============================================================================

/**
 * 取得驗證徽章資訊
 * 
 * @param int $post_id 餐廳文章ID
 * @return array 包含 status, label, class, icon, description 的陣列
 */
function byob_get_verification_badge_info($post_id) {
    // 優先檢查覆蓋欄位
    $override = get_field('verification_override', $post_id);
    $source = !empty($override) ? $override : get_field('source', $post_id);
    
    // 預設值（無 source 或未知 source）
    $default = array(
        'status' => 'community',
        'label' => 'Community Recommended',
        'class' => 'badge-community',
        'icon' => '👥',
        'description' => 'Information provided by community members'
    );
    
    if (empty($source)) {
        return $default;
    }
    
    // 根據 source 值判斷驗證狀態
    switch ($source) {
        case 'philly_owner_verification':
            return array(
                'status' => 'verified',
                'label' => 'Verified by Restaurant',
                'class' => 'badge-verified',
                'icon' => '🔒',
                'description' => 'Information verified by restaurant'
            );
            
        case 'philly_community_recommendation':
            return array(
                'status' => 'community',
                'label' => 'Community Recommended',
                'class' => 'badge-community',
                'icon' => '👥',
                'description' => 'Information provided by community members'
            );
            
        default:
            // 其他來源（如 customer_recommendation 等）
            return $default;
    }
}

/**
 * 顯示驗證徽章
 * 
 * @param int|null $post_id 餐廳文章ID（預設為當前文章）
 * @param string $size 徽章大小：'small' 或 'medium'
 * @return string 徽章 HTML
 */
function byob_display_verification_badge($post_id = null, $size = 'small') {
    if ($post_id === null) {
        $post_id = get_the_ID();
    }
    
    if (!$post_id) {
        return '';
    }
    
    $badge_info = byob_get_verification_badge_info($post_id);
    
    $size_class = ($size === 'medium') ? 'badge-medium' : 'badge-small';
    
    $badge_html = sprintf(
        '<span class="verification-badge %s %s" title="%s">%s %s</span>',
        esc_attr($badge_info['class']),
        esc_attr($size_class),
        esc_attr($badge_info['description']),
        esc_html($badge_info['icon']),
        esc_html($badge_info['label'])
    );
    
    return $badge_html;
}

/**
 * 記錄推薦者參與抽獎
 * 
 * @param int $restaurant_id 餐廳ID
 * @param string $recommender_name 推薦者姓名
 * @param string $recommender_email 推薦者Email
 */
function byob_record_lottery_participant($restaurant_id, $recommender_name, $recommender_email) {
    if (empty($recommender_email)) return false;
    
    $current_month = date('Y-m');
    $restaurant_name = get_field('restaurant_name', $restaurant_id);
    
    // 檢查是否已經記錄過（避免重複）
    $existing = get_posts([
        'post_type' => 'lottery_participant',
        'meta_query' => [
            [
                'key' => 'customer_recommender_email',
                'value' => $recommender_email
            ],
            [
                'key' => 'month',
                'value' => $current_month
            ],
            [
                'key' => 'restaurant_id',
                'value' => $restaurant_id
            ]
        ]
    ]);
    
    if (!empty($existing)) return false; // 已經記錄過
    
    // 建立參與者記錄
    $participant_id = wp_insert_post([
        'post_type' => 'lottery_participant',
        'post_title' => $recommender_name . ' - ' . $restaurant_name . ' (' . $current_month . ')',
        'post_status' => 'publish',
        'post_content' => ''
    ]);
    
    if ($participant_id) {
        // 儲存參與者資料
        update_field('customer_recommender_name', $recommender_name, $participant_id);
        update_field('customer_recommender_email', $recommender_email, $participant_id);
        update_field('restaurant_name', $restaurant_name, $participant_id);
        update_field('restaurant_id', $restaurant_id, $participant_id);
        update_field('submission_date', current_time('mysql'), $participant_id);
        update_field('approval_date', current_time('mysql'), $participant_id);
        update_field('month', $current_month, $participant_id);
        update_field('base_chances', 1, $participant_id); // 基本機會
        update_field('social_share_chance', 0, $participant_id); // 社群分享機會
        update_field('total_chances', 1, $participant_id); // 總機會
        update_field('status', 'eligible', $participant_id);
        
        error_log("BYOB: 抽獎參與者已記錄 - 姓名: {$recommender_name}, Email: {$recommender_email}, 餐廳: {$restaurant_name}");
        return $participant_id;
    }
    
    return false;
}

/**
 * 執行抽獎
 * 
 * @param string $month 抽獎月份 (格式: Y-m)
 * @return array 抽獎結果
 */
function byob_execute_lottery($month = null) {
    if (!$month) {
        $month = date('Y-m');
    }
    
    // 取得當月參與者
    $participants = get_posts([
        'post_type' => 'lottery_participant',
        'meta_query' => [
            [
                'key' => 'month',
                'value' => $month
            ],
            [
                'key' => 'status',
                'value' => 'eligible'
            ]
        ],
        'numberposts' => -1
    ]);
    
    if (empty($participants)) {
        return [
            'success' => false,
            'message' => '本月無符合資格的參與者'
        ];
    }
    
    // 計算總抽獎機會
    $total_chances = 0;
    $participant_chances = [];
    
    foreach ($participants as $participant) {
        $base_chances = get_field('base_chances', $participant->ID) ?: 1;
        $social_share_chance = get_field('social_share_chance', $participant->ID) ?: 0;
        $chances = $base_chances + $social_share_chance;
        $total_chances += $chances;
        $participant_chances[] = [
            'id' => $participant->ID,
            'name' => get_field('customer_recommender_name', $participant->ID),
            'email' => get_field('customer_recommender_email', $participant->ID),
            'restaurant' => get_field('restaurant_name', $participant->ID),
            'chances' => $chances
        ];
    }
    
    // 執行抽獎
    $winners = [];
    $prizes = [
        ['name' => '一獎', 'count' => 1, 'description' => '進口酒商電子禮券'],
        ['name' => '二獎', 'count' => 2, 'description' => '高級進口紅白酒杯']
    ];
    
    foreach ($prizes as $prize) {
        for ($i = 0; $i < $prize['count']; $i++) {
            if (empty($participant_chances)) break;
            
            // 生成隨機數
            $random_number = mt_rand(1, $total_chances);
            
            // 找出中獎者
            $current_chance = 0;
            $winner_index = -1;
            
            foreach ($participant_chances as $index => $participant) {
                $current_chance += $participant['chances'];
                if ($random_number <= $current_chance) {
                    $winner_index = $index;
                    break;
                }
            }
            
            if ($winner_index >= 0) {
                $winner = $participant_chances[$winner_index];
                $winner['prize'] = $prize['name'];
                $winner['prize_description'] = $prize['description'];
                $winner['random_number'] = $random_number;
                $winners[] = $winner;
                
                // 從參與者清單中移除中獎者（避免重複中獎）
                unset($participant_chances[$winner_index]);
                $participant_chances = array_values($participant_chances);
                
                // 重新計算總機會數
                $total_chances = 0;
                foreach ($participant_chances as $p) {
                    $total_chances += $p['chances'];
                }
            }
        }
    }
    
    // 記錄抽獎結果
    $lottery_id = wp_insert_post([
        'post_type' => 'lottery_result',
        'post_title' => $month . ' 抽獎結果',
        'post_status' => 'publish',
        'post_content' => json_encode($winners, JSON_UNESCAPED_UNICODE)
    ]);
    
    if ($lottery_id) {
        update_field('month', $month, $lottery_id);
        update_field('total_participants', count($participants), $lottery_id);
        update_field('winners', json_encode($winners, JSON_UNESCAPED_UNICODE), $lottery_id);
        update_field('draw_date', current_time('mysql'), $lottery_id);
    }
    
    // 發送中獎通知
    foreach ($winners as $winner) {
        byob_send_winner_notification($winner);
    }
    
    // 發送未中獎通知
    byob_send_non_winner_notifications($participants, $winners, $month);
    
    return [
        'success' => true,
        'month' => $month,
        'total_participants' => count($participants),
        'winners' => $winners,
        'lottery_id' => $lottery_id
    ];
}

/**
 * 發送中獎通知
 * 
 * @param array $winner 中獎者資料
 */
function byob_send_winner_notification($winner) {
    $admin_email = 'byobmap.tw@gmail.com';
    $subject = '🎉 恭喜中獎！BYOB 推薦抽獎活動 - ' . $winner['prize'];
    
    $message = byob_generate_winner_notification_html($winner);
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: BYOB <' . $admin_email . '>'
    );
    
    $sent = wp_mail($winner['email'], $subject, $message, $headers);
    
    if ($sent) {
        error_log("BYOB: 中獎通知已發送 - 收件人: {$winner['email']}, 獎項: {$winner['prize']}");
    } else {
        error_log("BYOB: 中獎通知發送失敗 - 收件人: {$winner['email']}, 獎項: {$winner['prize']}");
    }
    
    return $sent;
}

/**
 * 生成中獎通知 HTML 內容
 * 
 * @param array $winner 中獎者資料
 * @return string HTML 內容
 */
function byob_generate_winner_notification_html($winner) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>恭喜中獎！</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
        <div style="max-width: 600px; margin: 0 auto; background-color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            
            <!-- 標題區塊 -->
            <div style="background: linear-gradient(135deg, #8b2635 0%, #a0303e 100%); padding: 40px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 300;">🍷 BYOBMAP</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">台灣 BYOB 餐廳地圖</p>
            </div>
            
            <!-- 內容區塊 -->
            <div style="padding: 40px 30px;">
                <h2 style="color: #8b2635; margin: 0 0 20px 0; font-size: 24px;">🎉 恭喜中獎！</h2>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    親愛的 <strong>' . esc_html($winner['name']) . '</strong>，
                </p>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    恭喜你在 BYOB 推薦抽獎活動中獲得 <strong>' . esc_html($winner['prize']) . '</strong>！
                </p>
                
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 20px; margin: 20px 0;">
                    <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 18px;">🏆 獎品詳情</h3>
                    <p style="color: #856404; margin: 0; font-size: 16px; line-height: 1.5;">
                        <strong>獎項：</strong>' . esc_html($winner['prize']) . '<br>
                        <strong>獎品：</strong>' . esc_html($winner['prize_description']) . '<br>
                        <strong>推薦餐廳：</strong>' . esc_html($winner['restaurant']) . '
                    </p>
                </div>
                
                <div style="background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; margin: 20px 0;">
                    <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">📋 領獎說明</h4>
                    <p style="color: #0056b3; margin: 0; font-size: 14px; line-height: 1.5;">
                        請在 7 天內回覆此郵件確認領獎，我們將安排獎品寄送。<br>
                        回覆郵件請註明地址(一獎可免地址)、電話、以及收件人。<br>
						逾時未回覆，獎項將轉贈其他參加者，並恕不另行通知。<br>
						如有疑問，請聯繫：byobmap.tw@gmail.com
                    </p>
                </div>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                    再次感謝你對 BYOB 平台的貢獻！你的推薦讓更多愛酒的朋友能找到優質的 BYOB 餐廳。
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://forms.gle/jAnvmwh2BKyVXq5M8" 
                       style="display: inline-block; background-color: rgba(139, 38, 53, 0.8); color: #f8f9fa; text-decoration: none; padding: 16px 32px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">
                        繼續推薦餐廳
                    </a>
                </div>
                
                <p style="color: #6c757d; font-size: 14px; line-height: 1.5; margin: 30px 0 0 0; text-align: center;">
                    感謝您對 BYOBMAP 的支持！<br>
                    如有任何問題，歡迎隨時聯繫我們。
                </p>
            </div>
            
            <!-- 頁腳 -->
            <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e9ecef;">
                <p style="color: #6c757d; font-size: 12px; margin: 0;">
                    © 2025 BYOBMAP. All rights reserved.<br>
                    Email: byobmap.tw@gmail.com
                </p>
            </div>
            
        </div>
    </body>
    </html>';
}

/**
 * 發送未中獎通知
 * 
 * @param array $participants 所有參與者
 * @param array $winners 中獎者清單
 * @param string $month 抽獎月份
 */
function byob_send_non_winner_notifications($participants, $winners, $month) {
    // 取得中獎者的 Email 清單
    $winner_emails = array_column($winners, 'email');
    
    // 找出未中獎的參與者
    $non_winners = [];
    $sent_emails = []; // 記錄已發送的 Email，避免重複發送
    
    foreach ($participants as $participant) {
        $email = get_field('customer_recommender_email', $participant->ID);
        $name = get_field('customer_recommender_name', $participant->ID);
        
        // 檢查是否有 Email 且未中獎且未發送過
        if (!empty($email) && !in_array($email, $winner_emails) && !in_array($email, $sent_emails)) {
            $non_winners[] = [
                'name' => $name,
                'email' => $email,
                'restaurant' => get_field('restaurant_name', $participant->ID)
            ];
            $sent_emails[] = $email; // 記錄已發送
        }
    }
    
    // 發送未中獎通知
    foreach ($non_winners as $non_winner) {
        byob_send_non_winner_notification($non_winner, $month);
    }
    
    error_log("BYOB: 未中獎通知發送完成 - 共發送 " . count($non_winners) . " 封通知");
}

/**
 * 發送單一未中獎通知
 * 
 * @param array $non_winner 未中獎者資料
 * @param string $month 抽獎月份
 */
function byob_send_non_winner_notification($non_winner, $month) {
    $admin_email = 'byobmap.tw@gmail.com';
    $month_name = date('Y年m月', strtotime($month . '-01'));
    $subject = '🎲 BYOB 推薦抽獎結果通知 - ' . $month_name;
    
    $message = byob_generate_non_winner_notification_html($non_winner, $month_name);
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: BYOB <' . $admin_email . '>'
    );
    
    $sent = wp_mail($non_winner['email'], $subject, $message, $headers);
    
    if ($sent) {
        error_log("BYOB: 未中獎通知已發送 - 收件人: {$non_winner['email']}, 姓名: {$non_winner['name']}");
    } else {
        error_log("BYOB: 未中獎通知發送失敗 - 收件人: {$non_winner['email']}, 姓名: {$non_winner['name']}");
    }
    
    return $sent;
}

/**
 * 生成未中獎通知 HTML 內容
 * 
 * @param array $non_winner 未中獎者資料
 * @param string $month_name 月份名稱
 * @return string HTML 內容
 */
function byob_generate_non_winner_notification_html($non_winner, $month_name) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>抽獎結果通知</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
        <div style="max-width: 600px; margin: 0 auto; background-color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            
            <!-- 標題區塊 -->
            <div style="background: linear-gradient(135deg, #8b2635 0%, #a0303e 100%); padding: 40px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 300;">🍷 BYOBMAP</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">台灣 BYOB 餐廳地圖</p>
            </div>
            
            <!-- 內容區塊 -->
            <div style="padding: 40px 30px;">
                <h2 style="color: #8b2635; margin: 0 0 20px 0; font-size: 24px;">🎲 抽獎結果通知</h2>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    親愛的 <strong>' . esc_html($non_winner['name'] ?: '朋友') . '</strong>，
                </p>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    感謝您參與 ' . $month_name . ' 的 BYOB 推薦抽獎活動！雖然這次沒有中獎，但您的推薦讓更多愛酒的朋友發現了「<strong>' . esc_html($non_winner['restaurant']) . '</strong>」這個好地方。
                </p>
                
                <div style="background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; margin: 20px 0;">
                    <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">🎯 抽獎說明</h4>
                    <p style="color: #0056b3; margin: 0; font-size: 14px; line-height: 1.5;">
                        我們的抽獎系統使用 PHP 的 Mersenne Twister 演算法（mt_rand）生成隨機數，確保每次抽獎的完全隨機性和公平性。
                    </p>
                </div>
                
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 20px; margin: 20px 0;">
                    <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 18px;">🎁 下個月再來！</h3>
                    <p style="color: #856404; margin: 0 0 15px 0; font-size: 16px; line-height: 1.5;">
                        每月的抽獎活動持續進行中，獎品包括：
                    </p>
                    <ul style="color: #856404; margin: 0; padding-left: 20px;">
                        <li>🎫 一獎：進口酒商電子禮券</li>
                        <li>🥂 二獎：高級進口紅白酒杯</li>
                    </ul>
                </div>
                
                <h3 style="color: #8b2635; margin: 30px 0 15px 0; font-size: 18px;">💡 繼續推薦</h3>
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    知道其他可以自帶酒的餐廳嗎？歡迎繼續推薦，增加下次中獎機會：
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://forms.gle/jAnvmwh2BKyVXq5M8" 
                       style="display: inline-block; background-color: rgba(139, 38, 53, 0.8); color: #f8f9fa; text-decoration: none; padding: 16px 32px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">
                        📝 推薦更多餐廳
                    </a>
                </div>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 20px 0 0 0;">
                    感謝您對 BYOB 平台的支持，讓我們一起讓台北變得更開瓶友善！🥂
                </p>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 20px 0 0 0;">
                    Cheers！<br>
                    — 台北 BYOB 小隊
                </p>
            </div>
            
            <!-- 頁腳 -->
            <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e9ecef;">
                <p style="color: #6c757d; font-size: 12px; margin: 0;">
                    © 2025 BYOBMAP. All rights reserved.<br>
                    Email: byobmap.tw@gmail.com
                </p>
            </div>
            
        </div>
    </body>
    </html>';
}

/**
 * 格式化開瓶費資訊
 */
function byob_format_corkage_fee($restaurant_data) {
    $is_charged = $restaurant_data['is_charged'];
    $amount = $restaurant_data['corkage_fee_amount'];
    $note = $restaurant_data['corkage_fee_note'];
    
    switch ($is_charged) {
        case 'free':
            return '免費';
        case 'charged':
            $result = $amount ? "NT$ {$amount}" : '酌收';
            if ($note) {
                $result .= " ({$note})";
            }
            return $result;
        case 'other':
            return $note ?: '其他條件';
        default:
            return '未提供';
    }
}

/**
 * 格式化酒器設備資訊
 */
function byob_format_equipment($restaurant_data) {
    $equipment = $restaurant_data['equipment'];
    $other_note = $restaurant_data['equipment_other_note'];
    
    if (empty($equipment)) {
        return '未提供';
    }
    
    $equipment_list = is_array($equipment) ? $equipment : explode(',', $equipment);
    $formatted = array_map('trim', $equipment_list);
    
    if ($other_note) {
        $formatted[] = "其他：{$other_note}";
    }
    
    return implode('、', $formatted);
}

/**
 * 格式化聯絡資訊
 */
function byob_format_contact_info($restaurant_data) {
    $contact_info = array();
    
    if ($restaurant_data['phone']) {
        $contact_info[] = "電話：{$restaurant_data['phone']}";
    }
    
    if ($restaurant_data['website']) {
        $contact_info[] = "網站：{$restaurant_data['website']}";
    }
    
    return $contact_info ? implode(' | ', $contact_info) : '未提供';
}

// 攔截註冊頁面，處理邀請token
add_action('login_init', 'byob_handle_invitation_registration');

function byob_handle_invitation_registration() {
    // 只在註冊頁面處理
    if (!isset($_GET['action']) || $_GET['action'] !== 'register') {
        return;
    }
    
    // 檢查是否有邀請token
    $invitation_token = isset($_GET['invitation_token']) ? sanitize_text_field($_GET['invitation_token']) : '';
    $restaurant_id = isset($_GET['restaurant_id']) ? intval($_GET['restaurant_id']) : 0;
    
    if (empty($invitation_token) || empty($restaurant_id)) {
        return;
    }
    
    // 驗證邀請token
    $verification = byob_verify_invitation_token($invitation_token);
    
    if (!$verification['valid']) {
        // 如果token無效，顯示錯誤訊息並重導向
        wp_redirect(wp_login_url() . '?byob_error=' . urlencode($verification['error']));
        exit;
    }
    
    // 儲存邀請資訊到session（用於註冊完成後處理）
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['byob_invitation_token'] = $invitation_token;
    $_SESSION['byob_restaurant_id'] = $restaurant_id;
    $_SESSION['byob_invitation_data'] = $verification;
}

// 在註冊頁面顯示歡迎訊息
add_action('login_form_register', 'byob_add_invitation_welcome_message');

function byob_add_invitation_welcome_message() {
    $invitation_token = isset($_GET['invitation_token']) ? sanitize_text_field($_GET['invitation_token']) : '';
    $restaurant_id = isset($_GET['restaurant_id']) ? intval($_GET['restaurant_id']) : 0;
    
    if (empty($invitation_token) || empty($restaurant_id)) {
        return;
    }
    
    // 驗證邀請
    $verification = byob_verify_invitation_token($invitation_token);
    
    if ($verification['valid']) {
        $restaurant_name = $verification['restaurant']->post_title;
        $contact_person = $verification['invitation']->contact_person;
        
        echo '<div style="background: #e8f5e8; border: 1px solid #4caf50; padding: 20px; margin-bottom: 25px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
        echo '<h3 style="margin: 0 0 15px 0; color: #2e7d32; font-size: 20px;">🎉 歡迎加入 BYOBMAP！</h3>';
        echo '<p style="margin: 0; font-size: 16px;">親愛的 <strong>' . esc_html($contact_person) . '</strong>，</p>';
        echo '<p style="margin: 8px 0; font-size: 16px;">您的餐廳「<strong>' . esc_html($restaurant_name) . '</strong>」已成功上架！</p>';
        echo '<p style="margin: 15px 0 0 0; font-size: 14px; color: #666; font-style: italic;">✨ 請填寫以下資訊完成會員註冊，開始享受專業的餐廳管理工具</p>';
        echo '</div>';
    }
}

// 顯示邀請錯誤訊息
add_action('login_form_login', 'byob_show_invitation_error');

function byob_show_invitation_error() {
    if (isset($_GET['byob_error'])) {
        $error_message = sanitize_text_field($_GET['byob_error']);
        echo '<div style="background: #ffe6e6; border: 1px solid #f44336; padding: 15px; margin-bottom: 20px; border-radius: 5px;">';
        echo '<h3 style="margin: 0 0 10px 0; color: #c62828;">⚠️ 邀請連結問題</h3>';
        echo '<p style="margin: 0; color: #d32f2f;">' . esc_html($error_message) . '</p>';
        echo '<p style="margin: 10px 0 0 0; font-size: 14px;">如需協助，請聯繫 BYOBMAP 客服。</p>';
        echo '</div>';
    }
}

// 自訂註冊頁面標題和說明
add_filter('gettext', 'byob_customize_registration_texts', 20, 3);

function byob_customize_registration_texts($translated_text, $text, $domain) {
    // 只在註冊頁面修改文字
    if (!isset($_GET['action']) || $_GET['action'] !== 'register') {
        return $translated_text;
    }
    
    // 檢查是否有邀請 token
    $invitation_token = isset($_GET['invitation_token']) ? sanitize_text_field($_GET['invitation_token']) : '';
    if (empty($invitation_token)) {
        return $translated_text;
    }
    
    // 自訂文字
    switch ($translated_text) {
        case '在這個網站註冊帳號':
            return '🚀 完成會員註冊，開啟餐廳管理新體驗';
        case '註冊確認通知會以電子郵件方式傳送至用於註冊帳號的電子郵件地址。':
            return '✨ 註冊完成後，您將收到確認通知，並可立即開始管理餐廳資料';
        case '註冊':
            return '🎉 立即註冊';
        case '登入':
            return '已有帳號？登入';
        case '忘記密碼?':
            return '忘記密碼？';
    }
    
    return $translated_text;
}

// 註冊完成後自動設定餐廳業者
add_action('user_register', 'byob_auto_setup_restaurant_owner');

function byob_auto_setup_restaurant_owner($user_id) {
    // 啟動session（如果尚未啟動）
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 檢查是否有邀請資訊
    if (!isset($_SESSION['byob_invitation_token']) || !isset($_SESSION['byob_restaurant_id'])) {
        return;
    }
    
    $invitation_token = $_SESSION['byob_invitation_token'];
    $restaurant_id = $_SESSION['byob_restaurant_id'];
    $invitation_data = $_SESSION['byob_invitation_data'];
    
    // 再次驗證邀請（安全起見）
    $verification = byob_verify_invitation_token($invitation_token);
    
    if (!$verification['valid']) {
        return;
    }
    
    // 設定餐廳業者角色和關聯
    $setup_result = byob_setup_restaurant_owner($user_id, $restaurant_id);
    
    if ($setup_result) {
        // 標記邀請為已使用
        byob_mark_invitation_used($invitation_token, $user_id);
        
        // 更新餐廳文章的業者資訊
        update_post_meta($restaurant_id, '_byob_owner_registered', current_time('mysql'));
        
        // 發送歡迎郵件給新註冊的業者
        byob_send_welcome_email($user_id, $restaurant_id);
        
        // 記錄日誌
        error_log("BYOB: 餐廳業者註冊成功 - 用戶ID: {$user_id}, 餐廳ID: {$restaurant_id}");
    }
    
    // 清除session資料
    unset($_SESSION['byob_invitation_token']);
    unset($_SESSION['byob_restaurant_id']);
    unset($_SESSION['byob_invitation_data']);
}

// 餐廳業者正式註冊通知功能
add_action('user_register', 'byob_send_member_registration_notification');

function byob_send_member_registration_notification($user_id) {
    // 延遲執行，確保所有相關資料都已建立
    wp_schedule_single_event(time() + 5, 'byob_delayed_member_notification', array($user_id));
}

// 延遲通知函數
add_action('byob_delayed_member_notification', 'byob_send_delayed_member_notification');

function byob_send_delayed_member_notification($user_id) {
    // 獲取用戶資訊
    $user = get_user_by('id', $user_id);
    if (!$user) {
        return;
    }
    
    // 檢查是否為餐廳業者
    if (!in_array('restaurant_owner', $user->roles)) {
        return;
    }
    
    // 獲取餐廳資訊
    $restaurant_id = get_user_meta($user_id, '_owned_restaurant_id', true);
    $restaurant = null;
    $restaurant_name = '未知餐廳';
    
    // 記錄除錯資訊
    error_log("BYOB: 通知函數執行 - 用戶ID: {$user_id}, 餐廳ID: {$restaurant_id}");
    
    if ($restaurant_id) {
        $restaurant = get_post($restaurant_id);
        if ($restaurant && $restaurant->post_type === 'restaurant') {
            $restaurant_name = $restaurant->post_title;
            error_log("BYOB: 找到餐廳 - ID: {$restaurant_id}, 名稱: {$restaurant_name}");
        } else {
            error_log("BYOB: 餐廳不存在或類型錯誤 - ID: {$restaurant_id}");
        }
    } else {
        error_log("BYOB: 用戶沒有關聯的餐廳ID");
        
        // 嘗試從用戶的display_name中提取餐廳名稱
        if (strpos($user->display_name, ' 負責人') !== false) {
            $restaurant_name = str_replace(' 負責人', '', $user->display_name);
            error_log("BYOB: 從display_name提取餐廳名稱: {$restaurant_name}");
        }
    }
    
    // 判斷註冊方式
    $registration_type = get_user_meta($user_id, '_byob_registration_type', true);
    if (!$registration_type) {
        // 檢查是否有邀請碼使用記錄
        $invitation_data = get_post_meta($restaurant_id, '_byob_invitation_code', true);
        if ($invitation_data && isset($invitation_data['used']) && $invitation_data['used']) {
            $registration_type = '邀請碼註冊';
        } else {
            $registration_type = '直接加入';
        }
    }
    
    // 準備Email內容
    $admin_email = 'byobmap.tw@gmail.com';
    $subject = 'BYOB 新餐廳業者正式註冊 - ' . $restaurant_name;
    
    $message = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
        <div style="background-color: #8b2635; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">🍷 BYOB 新餐廳業者註冊通知</h1>
        </div>
        
        <div style="background-color: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #8b2635; margin-top: 0;">新餐廳業者已正式註冊</h2>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;">
                <h3 style="color: #495057; margin-top: 0;">📋 註冊資訊</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d; width: 30%;">餐廳名稱：</td>
                        <td style="padding: 8px 0; color: #212529;">' . esc_html($restaurant_name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d;">聯絡人：</td>
                        <td style="padding: 8px 0; color: #212529;">' . esc_html($user->display_name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d;">Email：</td>
                        <td style="padding: 8px 0; color: #212529;">' . esc_html($user->user_email) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d;">註冊方式：</td>
                        <td style="padding: 8px 0; color: #212529;">' . esc_html($registration_type) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d;">註冊時間：</td>
                        <td style="padding: 8px 0; color: #212529;">' . date('Y-m-d H:i:s', strtotime($user->user_registered)) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d;">用戶ID：</td>
                        <td style="padding: 8px 0; color: #212529;">' . $user_id . '</td>
                    </tr>';
    
    if ($restaurant_id) {
        $message .= '
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #6c757d;">餐廳ID：</td>
                        <td style="padding: 8px 0; color: #212529;">' . $restaurant_id . '</td>
                    </tr>';
    }
    
    $message .= '
                </table>
            </div>
            
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 6px; border-left: 4px solid #007bff; margin: 20px 0;">
                <h4 style="color: #0056b3; margin-top: 0;">📊 後台管理</h4>
                <p style="margin: 5px 0; color: #0056b3;">
                    <a href="' . admin_url('edit.php?post_type=restaurant&page=byob-member-management') . '" style="color: #007bff; text-decoration: none;">→ 前往會員管理頁面</a>
                </p>
                <p style="margin: 5px 0; color: #0056b3;">
                    <a href="' . admin_url('edit.php?post_type=restaurant') . '" style="color: #007bff; text-decoration: none;">→ 前往餐廳管理頁面</a>
                </p>
            </div>
            
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <h4 style="color: #856404; margin-top: 0;">⚠️ 注意事項</h4>
                <ul style="margin: 5px 0; color: #856404; padding-left: 20px;">
                    <li>請確認餐廳資料完整性</li>
                    <li>檢查聯絡資訊是否正確</li>
                    <li>必要時可主動聯絡新註冊業者</li>
                </ul>
            </div>
            
            <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
            
            <p style="color: #6c757d; font-size: 14px; text-align: center; margin: 0;">
                此郵件由 BYOB 自動化系統發送<br>
                時間：' . current_time('Y-m-d H:i:s') . '
            </p>
        </div>
    </div>';
    
    // 發送Email
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $sent = wp_mail($admin_email, $subject, $message, $headers);
    
    // 記錄日誌
    if ($sent) {
        error_log("BYOB: 餐廳業者註冊通知已發送 - 用戶ID: {$user_id}, 餐廳: {$restaurant_name}, 註冊方式: {$registration_type}");
    } else {
        error_log("BYOB: 餐廳業者註冊通知發送失敗 - 用戶ID: {$user_id}");
    }
    
    return $sent;
}

// 手動觸發通知的函數（供直接加入流程使用）
function byob_trigger_member_notification($user_id) {
    if (function_exists('byob_send_delayed_member_notification')) {
        byob_send_delayed_member_notification($user_id);
    }
}

// 發送歡迎郵件給新註冊的餐廳業者
function byob_send_welcome_email($user_id, $restaurant_id) {
    $user = get_user_by('id', $user_id);
    $restaurant = get_post($restaurant_id);
    
    if (!$user || !$restaurant) {
        return false;
    }
    
    $restaurant_name = $restaurant->post_title;
    $user_name = $user->display_name ?: $user->user_login;
    $login_url = wp_login_url();
    $restaurant_url = get_permalink($restaurant_id);
    
    $subject = "歡迎加入 BYOBMAP！註冊成功通知";
    
    $message = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #8b2635; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .button { display: inline-block; background-color: #8b2635; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .info-box { background-color: #fff; padding: 15px; border-left: 4px solid #8b2635; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 註冊成功！</h1>
            </div>
            
            <div class='content'>
                <h2>親愛的 {$user_name}，您好！</h2>
                
                <p>恭喜您成功註冊為 BYOBMAP 餐廳業者會員！</p>
                
                <div class='info-box'>
                    <h3>📋 您的會員資訊</h3>
                    <p><strong>用戶名稱：</strong>{$user_name}</p>
                    <p><strong>關聯餐廳：</strong>{$restaurant_name}</p>
                    <p><strong>會員類型：</strong>餐廳業者</p>
                </div>
                
                <div class='info-box'>
                    <h3>🔗 重要連結</h3>
                    <p><strong>登入會員系統：</strong><br>
                    <a href='{$login_url}' class='button'>立即登入</a></p>
                    
                    <p><strong>您的餐廳頁面：</strong><br>
                    <a href='{$restaurant_url}'>{$restaurant_url}</a></p>
                </div>
                
                <h3>✨ 會員專屬功能</h3>
                <ul>
                    <li>✓ 更新餐廳資訊和營業時間</li>
                    <li>✓ 上傳餐廳照片和菜單</li>
                    <li>✓ 查看餐廳統計數據</li>
                    <li>✓ 回應客戶評價和問題</li>
                    <li>✓ 參與平台行銷活動</li>
                </ul>
                
                <p>如有任何問題，歡迎隨時與我們聯繫。</p>
                
                <p>
                    再次歡迎您的加入！<br>
                    BYOBMAP 團隊
                </p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: BYOBMAP <noreply@byobmap.com>'
    );
    
    $sent = wp_mail($user->user_email, $subject, $message, $headers);
    
    if ($sent) {
        error_log("BYOB: 歡迎郵件發送成功 - 收件人: {$user->user_email}, 餐廳: {$restaurant_name}");
    } else {
        error_log("BYOB: 歡迎郵件發送失敗 - 收件人: {$user->user_email}, 餐廳: {$restaurant_name}");
    }
    
    return $sent;
}

// 確保session在WordPress中可用
add_action('init', function() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
});

// 手動重發邀請功能（後台使用）
function byob_manual_resend_invitation($restaurant_id) {
    if (!current_user_can('administrator')) {
        return array('success' => false, 'error' => '權限不足');
    }
    
    return byob_resend_invitation($restaurant_id);
}

// =============================================================================
// 診斷工具（僅管理員可用）
// =============================================================================

// 在後台新增診斷頁面
add_action('admin_menu', 'byob_add_diagnostic_menu');

function byob_add_diagnostic_menu() {
    add_submenu_page(
        'tools.php',
        'BYOB 系統診斷',
        'BYOB 診斷',
        'administrator',
        'byob-diagnostic',
        'byob_diagnostic_page'
    );
}

function byob_diagnostic_page() {
    echo '<div class="wrap">';
    echo '<h1>BYOB 系統診斷</h1>';
    
    if (isset($_POST['run_test']) && check_admin_referer('byob_diagnostic')) {
        byob_run_invitation_test();
    }
    
    echo '<form method="post">';
    wp_nonce_field('byob_diagnostic');
    echo '<h2>📋 系統狀態檢查</h2>';
    
    // 檢查基本資訊
    echo '<h3>1. 基本資訊</h3>';
    echo '<table class="widefat">';
    echo '<tr><td>PHP 版本</td><td>' . phpversion() . '</td></tr>';
    echo '<tr><td>WordPress 版本</td><td>' . get_bloginfo('version') . '</td></tr>';
    echo '<tr><td>主題</td><td>' . get_template() . '</td></tr>';
    echo '<tr><td>子主題</td><td>' . get_stylesheet() . '</td></tr>';
    echo '</table>';
    
    // 檢查檔案路徑
    echo '<h3>2. 檔案路徑檢查</h3>';
    $invitation_handler_path = __DIR__ . '/invitation-handler.php';
    echo '<table class="widefat">';
    echo '<tr><td>當前目錄</td><td>' . __DIR__ . '</td></tr>';
    echo '<tr><td>invitation-handler.php 路徑</td><td>' . $invitation_handler_path . '</td></tr>';
    echo '<tr><td>檔案存在</td><td>' . (file_exists($invitation_handler_path) ? '✅ 是' : '❌ 否') . '</td></tr>';
    if (file_exists($invitation_handler_path)) {
        echo '<tr><td>檔案大小</td><td>' . filesize($invitation_handler_path) . ' bytes</td></tr>';
    }
    echo '</table>';
    
    // 檢查函數存在
    echo '<h3>3. 邀請系統函數檢查</h3>';
    $functions_to_check = [
        'byob_verify_invitation_token',
        'byob_mark_invitation_used',
        'byob_setup_restaurant_owner',
        'byob_send_approval_notification',
        'byob_create_invitation_table'
    ];
    
    echo '<table class="widefat">';
    foreach ($functions_to_check as $func) {
        echo '<tr><td>' . $func . '</td><td>' . (function_exists($func) ? '✅ 存在' : '❌ 不存在') . '</td></tr>';
    }
    echo '</table>';
    
    // 檢查資料庫表格
    echo '<h3>4. 資料庫檢查</h3>';
    global $wpdb;
    $table_name = $wpdb->prefix . 'byob_invitations';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    
    echo '<table class="widefat">';
    echo '<tr><td>邀請資料表</td><td>' . ($table_exists ? '✅ 存在' : '❌ 不存在') . '</td></tr>';
    if ($table_exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        echo '<tr><td>邀請記錄數</td><td>' . $count . '</td></tr>';
    }
    echo '</table>';
    
    // 檢查功能設定
    echo '<h3>5. 功能設定檢查</h3>';
    $features = byob_get_feature_settings();
    echo '<table class="widefat">';
    foreach ($features as $key => $value) {
        echo '<tr><td>' . $key . '</td><td>' . ($value ? '✅ 啟用' : '❌ 停用') . '</td></tr>';
    }
    echo '</table>';
    
    echo '<h3>6. 測試邀請功能</h3>';
    echo '<p><strong>注意：</strong>此測試會檢查邀請系統是否正常運作，但不會真的發送郵件。</p>';
    echo '<p class="submit"><input type="submit" name="run_test" class="button-primary" value="執行邀請功能測試" /></p>';
    
    echo '</form>';
    echo '</div>';
}

function byob_run_invitation_test() {
    echo '<div class="notice notice-info"><p><strong>正在執行邀請功能測試...</strong></p></div>';
    
    // 檢查是否有餐廳文章可以測試
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'posts_per_page' => 1,
        'post_status' => 'publish'
    ));
    
    if (empty($restaurants)) {
        echo '<div class="notice notice-error"><p>❌ 沒有找到已發布的餐廳文章，無法測試</p></div>';
        return;
    }
    
    $restaurant = $restaurants[0];
    echo '<div class="notice notice-success"><p>✅ 找到測試餐廳：' . $restaurant->post_title . '</p></div>';
    
    // 檢查餐廳是否有必要欄位
    $contact_person = get_field('contact_person', $restaurant->ID);
    $email = get_field('email', $restaurant->ID);
    
    echo '<h4>餐廳資料檢查：</h4>';
    echo '<ul>';
    echo '<li>聯絡人：' . ($contact_person ? '✅ ' . $contact_person : '❌ 未設定') . '</li>';
    echo '<li>Email：' . ($email ? '✅ ' . $email : '❌ 未設定') . '</li>';
    echo '</ul>';
    
    if (!$email || !is_email($email)) {
        echo '<div class="notice notice-error"><p>❌ 餐廳缺少有效的 Email 地址，無法繼續測試</p></div>';
        return;
    }
    
    // 測試邀請函數
    if (function_exists('byob_send_approval_notification')) {
        echo '<h4>測試邀請生成（不發送郵件）：</h4>';
        
        // 暫時覆蓋郵件函數以避免真的發送
        add_filter('pre_wp_mail', function($atts, $return) {
            echo '<div class="notice notice-info"><p>📧 模擬發送郵件到：' . $atts['to'] . '</p></div>';
            echo '<div class="notice notice-info"><p>📧 郵件主旨：' . $atts['subject'] . '</p></div>';
            return true; // 阻止真的發送郵件
        }, 10, 2);
        
        $result = byob_send_approval_notification($restaurant->ID);
        
        if ($result) {
            echo '<div class="notice notice-success"><p>✅ 邀請生成成功！</p></div>';
            echo '<p>邀請碼已生成並儲存到餐廳的 post meta 中</p>';
        } else {
            echo '<div class="notice notice-error"><p>❌ 邀請生成失敗</p></div>';
        }
        
        // 移除郵件過濾器
        remove_all_filters('pre_wp_mail');
    } else {
        echo '<div class="notice notice-error"><p>❌ byob_send_approval_notification 函數不存在</p></div>';
    }
}

/**
 * 使用 WooCommerce 內容鉤子載入餐廳資料編輯表單
 */
function byob_load_restaurant_profile_content() {
    global $wp_query;
    
    // 檢查是否為餐廳資料編輯頁面
    if (is_account_page() && isset($wp_query->query_vars['restaurant-profile'])) {
        // 移除 WooCommerce 預設的帳戶內容
        remove_action('woocommerce_account_content', 'woocommerce_account_content', 10);
        
        // 載入我們的表單內容
        $template_path = get_stylesheet_directory() . '/woocommerce/myaccount/restaurant-profile.php';
        
        if (file_exists($template_path)) {
            error_log('BYOB: 載入餐廳資料編輯表單: ' . $template_path);
            include $template_path;
        } else {
            error_log('BYOB: 餐廳資料編輯表單檔案不存在: ' . $template_path);
        }
    }
}

// 使用 WooCommerce 內容鉤子
add_action('woocommerce_account_content', 'byob_load_restaurant_profile_content', 5);

// 載入餐廳直接加入功能
// require_once get_template_directory() . '/restaurant-direct-join.php';

/**
 * 餐廳直接加入功能 - Flatsome 主題相容版本
 */

// 防止直接訪問
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 建立簡化餐廳文章
 */
function flatsome_byob_create_simple_restaurant($restaurant_data) {
    // 基本餐廳建立邏輯
    $post_data = array(
        'post_title' => sanitize_text_field($restaurant_data['restaurant_name']),
        'post_content' => '',
        'post_status' => 'publish',
        'post_type' => 'restaurant',
        'post_author' => $restaurant_data['user_id'] ?? 1,
    );
    
    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
        return new WP_Error('restaurant_creation_failed', '餐廳建立失敗');
    }
    
    // 設定餐廳與用戶的關聯
    update_post_meta($post_id, '_restaurant_owner_id', $restaurant_data['user_id']);
    
    // 更新 ACF 欄位
    if (function_exists('update_field')) {
        $acf_updates = array(
            'restaurant_name' => sanitize_text_field($restaurant_data['restaurant_name']),
            'contact_person' => sanitize_text_field($restaurant_data['contact_person']),
            'email' => sanitize_email($restaurant_data['email']),
            'phone' => sanitize_text_field($restaurant_data['phone']),
            'address' => sanitize_text_field($restaurant_data['address']),
            // 缺少的必填欄位設為空值
            'restaurant_type' => array(),
            'district' => '',
            'is_charged' => '',
            'corkage_fee_amount' => 0,
            'corkage_fee_note' => '',
            'equipment' => array(),
            'equipment_other_note' => '',
            'open_bottle_service' => '',
            'open_bottle_service_other_note' => '',
            'website' => '',
            'social_links' => '',
            'notes' => '',
            'last_updated' => current_time('Y-m-d'),
            'source' => '直接註冊',
            'is_owner' => '',
            'review_status' => 'approved',
            'submitted_date' => current_time('mysql'),
            'review_date' => current_time('mysql'),
            'review_notes' => ''
        );
        
        foreach ($acf_updates as $field_name => $field_value) {
            update_field($field_name, $field_value, $post_id);
        }
    }
    
    return array(
        'success' => true,
        'post_id' => $post_id,
        'message' => '餐廳已成功建立！'
    );
}

/**
 * 處理餐廳註冊
 */
function flatsome_byob_handle_direct_restaurant_registration($form_data) {
    // 基本驗證
    if (empty($form_data['restaurant_name']) || empty($form_data['email'])) {
        return new WP_Error('missing_field', '必填欄位不能為空');
    }
    
    // 檢查密碼是否已通過驗證
    if (empty($form_data['password'])) {
        return new WP_Error('missing_password', '密碼不能為空');
    }
    
    // 建立用戶
    $user_data = array(
        'user_login' => $form_data['email'],
        'user_email' => $form_data['email'],
        'user_pass' => $form_data['password'],
        'display_name' => $form_data['contact_person'],
        'role' => 'restaurant_owner'
    );
    
    $user_id = wp_insert_user($user_data);
    if (is_wp_error($user_id)) {
        // 檢查具體的錯誤原因
        $error_code = $user_id->get_error_code();
        $error_message = $user_id->get_error_message();
        
        // 根據錯誤代碼提供更詳細的錯誤訊息
        switch ($error_code) {
            case 'existing_user_login':
                return new WP_Error('user_creation_failed', '用戶建立失敗 (原因：此Email已註冊)');
            case 'existing_user_email':
                return new WP_Error('user_creation_failed', '用戶建立失敗 (原因：此Email已註冊)');
            case 'invalid_email':
                return new WP_Error('user_creation_failed', '用戶建立失敗 (原因：Email格式不正確)');
            case 'invalid_username':
                return new WP_Error('user_creation_failed', '用戶建立失敗 (原因：用戶名稱格式不正確)');
            default:
                return new WP_Error('user_creation_failed', '用戶建立失敗 (原因：' . $error_message . ')');
        }
    }
    
    // 準備餐廳資料（包含所有必要欄位）
    $restaurant_data = array(
        'restaurant_name' => $form_data['restaurant_name'],
        'contact_person' => $form_data['contact_person'],
        'phone' => $form_data['phone'],
        'address' => $form_data['address'],
        'email' => $form_data['email'],
        'user_id' => $user_id
    );
    
    // 建立餐廳
    $result = flatsome_byob_create_simple_restaurant($restaurant_data);
    if (is_wp_error($result)) {
        wp_delete_user($user_id);
        return $result;
    }
    
    // 自動登入用戶
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    // 準備跳轉 URL
    $redirect_url = get_permalink(get_option('woocommerce_myaccount_page_id')) . 'restaurant-profile/';
    
    return array(
        'success' => true,
        'redirect_url' => $redirect_url,
        'message' => '註冊成功！正在跳轉到餐廳資料編輯頁面...',
        'user_id' => $user_id
    );
}

/**
 * 驗證表單資料
 */
function flatsome_byob_validate_direct_registration_form($form_data) {
    // 基本資料清理
    $cleaned_data = array(
        'restaurant_name' => sanitize_text_field($form_data['restaurant_name']),
        'contact_person' => sanitize_text_field($form_data['contact_person']),
        'phone' => sanitize_text_field($form_data['phone']),
        'address' => sanitize_text_field($form_data['address']),
        'email' => sanitize_email($form_data['email']),
        'password' => $form_data['password'],
        'confirm_password' => $form_data['confirm_password'] ?? ''
    );
    
    // 密碼驗證
    if (strlen($cleaned_data['password']) < 8) {
        return new WP_Error('password_too_short', '密碼長度至少需要8個字元');
    }
    
    if ($cleaned_data['password'] !== $cleaned_data['confirm_password']) {
        return new WP_Error('password_mismatch', '密碼與確認密碼不匹配');
    }
    
    // 檢查密碼強度（可選）
    $password = $cleaned_data['password'];
    $strength = 0;
    
    if (strlen($password) >= 8) $strength++;
    if (preg_match('/[a-z]/', $password)) $strength++;
    if (preg_match('/[A-Z]/', $password)) $strength++;
    if (preg_match('/[0-9]/', $password)) $strength++;
    if (preg_match('/[^A-Za-z0-9]/', $password)) $strength++;
    
    if ($strength < 2) {
        return new WP_Error('password_weak', '密碼強度太弱，建議包含大小寫字母、數字和特殊符號');
    }
    
    return $cleaned_data;
}

/**
 * Email 檢查 AJAX 處理
 */
function flatsome_byob_check_email_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'flatsome_byob_check_email')) {
        wp_send_json_error('安全驗證失敗');
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error('Email格式不正確');
    }
    
    $user = get_user_by('email', $email);
    $exists = $user !== false;
    
    wp_send_json_success(array(
        'exists' => $exists,
        'message' => $exists ? '此Email已被註冊' : 'Email可用'
    ));
}

/**
 * AJAX 處理
 */
function flatsome_byob_handle_direct_registration_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'flatsome_byob_direct_registration')) {
        wp_send_json_error('安全驗證失敗');
    }
    
    $cleaned_data = flatsome_byob_validate_direct_registration_form($_POST);
    
    // 檢查驗證是否失敗
    if (is_wp_error($cleaned_data)) {
        wp_send_json_error($cleaned_data->get_error_message());
    }
    
    $result = flatsome_byob_handle_direct_restaurant_registration($cleaned_data);
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }
    
    // 返回包含跳轉資訊的成功回應
    wp_send_json_success($result);
}

// 註冊 AJAX 處理函數
add_action('wp_ajax_flatsome_byob_direct_registration', 'flatsome_byob_handle_direct_registration_ajax');
add_action('wp_ajax_nopriv_flatsome_byob_direct_registration', 'flatsome_byob_handle_direct_registration_ajax');

// 註冊 Email 檢查 AJAX 處理函數
add_action('wp_ajax_flatsome_byob_check_email', 'flatsome_byob_check_email_ajax');
add_action('wp_ajax_nopriv_flatsome_byob_check_email', 'flatsome_byob_check_email_ajax');

/**
 * 餐廳註冊表單短代碼 - Flatsome 相容版本
 */
function flatsome_byob_restaurant_registration_form_shortcode($atts) {
    $nonce = wp_create_nonce('flatsome_byob_direct_registration');
    
    $form_html = '<div style="max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px;">';
    $form_html .= '<form id="flatsome-byob-restaurant-registration" method="post">';
    $form_html .= '<div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px; font-weight: bold;">餐廳名稱 *</label><input type="text" name="restaurant_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"></div>';
    $form_html .= '<div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px; font-weight: bold;">聯絡人姓名 *</label><input type="text" name="contact_person" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"></div>';
    $form_html .= '<div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px; font-weight: bold;">聯絡電話 *</label><input type="tel" name="phone" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"></div>';
    $form_html .= '<div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px; font-weight: bold;">餐廳地址 *</label><textarea name="address" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; min-height: 80px; resize: vertical;"></textarea></div>';
    $form_html .= '<div style="margin-bottom: 15px;"><label style="display: block; margin-bottom: 5px; font-weight: bold;">餐廳Email *</label><input type="email" id="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"><div id="email-status" style="margin-top: 5px; font-size: 12px; color: #666;"></div></div>';
    
    // 密碼欄位區域
    $form_html .= '<div style="margin-bottom: 15px;">';
    $form_html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">密碼 *</label>';
    $form_html .= '<div style="position: relative;">';
    $form_html .= '<input type="password" id="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; padding-right: 50px;">';
    $form_html .= '<button type="button" onclick="togglePassword(\'password\')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px;" title="顯示密碼">👁️</button>';
    $form_html .= '</div>';
    $form_html .= '<div id="password-strength" style="margin-top: 5px; font-size: 12px; color: #666;"></div>';
    $form_html .= '</div>';
    
    // 確認密碼欄位
    $form_html .= '<div style="margin-bottom: 15px;">';
    $form_html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">確認密碼 *</label>';
    $form_html .= '<div style="position: relative;">';
    $form_html .= '<input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; padding-right: 50px;">';
    $form_html .= '<button type="button" onclick="togglePassword(\'confirm_password\')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 16px;" title="顯示密碼">👁️</button>';
    $form_html .= '</div>';
    $form_html .= '<div id="password-match" style="margin-top: 5px; font-size: 12px; color: #666;"></div>';
    $form_html .= '</div>';
    
    // 密碼規則說明
    $form_html .= '<div class="password-rules" style="background-color: white; border-left: 4px solid rgba(139, 38, 53, 0.7); padding: 20px; border-radius: 0 8px 8px 0; box-shadow: 0 1px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">';
    $form_html .= '<h5 style="margin: 0 0 15px 0; color: #495057; font-size: 16px; font-family: \'Microsoft JhengHei\', Arial, sans-serif; font-weight: 600;">📋 密碼設定規則：</h5>';
    $form_html .= '<ul style="margin: 0; padding-left: 25px; color: #6c757d; font-size: 14px; font-family: \'Microsoft JhengHei\', Arial, sans-serif; line-height: 1.8;">';
    $form_html .= '<li>長度：至少8個字元</li>';
    $form_html .= '<li>建議包含：大小寫字母、數字、特殊符號</li>';
    $form_html .= '<li>避免使用：個人資訊、常見密碼</li>';
    $form_html .= '</ul>';
    $form_html .= '</div>';
    
    $form_html .= '<input type="hidden" name="nonce" value="' . $nonce . '">';
    $form_html .= '<input type="hidden" id="email-check-nonce" value="' . wp_create_nonce('flatsome_byob_check_email') . '">';
    $form_html .= '<button type="submit" style="width: 100%; padding: 15px; background: rgba(139, 38, 53, 0.7); color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">註冊餐廳</button>';
    $form_html .= '</form>';
    $form_html .= '<div id="flatsome-byob-registration-message" style="margin-top: 15px;"></div>';
    $form_html .= '<div id="flatsome-byob-countdown" style="margin-top: 15px; text-align: center; display: none;"></div>';
    $form_html .= '<div id="flatsome-byob-redirect-status" style="margin-top: 15px; text-align: center; display: none;"></div>';
    $form_html .= '</div>';
    
    $form_html .= '<script>';
    $form_html .= 'jQuery(document).ready(function($) {';
    
    // 密碼顯示/隱藏功能
    $form_html .= 'window.togglePassword = function(fieldId) {';
    $form_html .= 'const field = document.getElementById(fieldId);';
    $form_html .= 'const button = field.nextElementSibling;';
    $form_html .= 'if (field.type === "password") {';
    $form_html .= 'field.type = "text";';
    $form_html .= 'button.innerHTML = "🙈";';
    $form_html .= 'button.title = "隱藏密碼";';
    $form_html .= '} else {';
    $form_html .= 'field.type = "password";';
    $form_html .= 'button.innerHTML = "👁️";';
    $form_html .= 'button.title = "顯示密碼";';
    $form_html .= '}';
    $form_html .= '};';
    
    // 密碼強度檢查
    $form_html .= 'function checkPasswordStrength(password) {';
    $form_html .= 'let strength = 0;';
    $form_html .= 'let feedback = "";';
    $form_html .= 'if (password.length >= 8) strength += 1;';
    $form_html .= 'if (/[a-z]/.test(password)) strength += 1;';
    $form_html .= 'if (/[A-Z]/.test(password)) strength += 1;';
    $form_html .= 'if (/[0-9]/.test(password)) strength += 1;';
    $form_html .= 'if (/[^A-Za-z0-9]/.test(password)) strength += 1;';
    $form_html .= 'const strengthText = document.getElementById("password-strength");';
    $form_html .= 'switch(strength) {';
    $form_html .= 'case 0:';
    $form_html .= 'case 1:';
    $form_html .= 'feedback = "很弱";';
    $form_html .= 'strengthText.style.color = "#dc3545";';
    $form_html .= 'break;';
    $form_html .= 'case 2:';
    $form_html .= 'feedback = "弱";';
    $form_html .= 'strengthText.style.color = "#fd7e14";';
    $form_html .= 'break;';
    $form_html .= 'case 3:';
    $form_html .= 'feedback = "中等";';
    $form_html .= 'strengthText.style.color = "#ffc107";';
    $form_html .= 'break;';
    $form_html .= 'case 4:';
    $form_html .= 'feedback = "強";';
    $form_html .= 'strengthText.style.color = "#28a745";';
    $form_html .= 'break;';
    $form_html .= 'case 5:';
    $form_html .= 'feedback = "很強";';
    $form_html .= 'strengthText.style.color = "#20c997";';
    $form_html .= 'break;';
    $form_html .= '}';
    $form_html .= 'strengthText.innerHTML = "密碼強度：" + feedback;';
    $form_html .= '}';
    
    // 密碼匹配檢查
    $form_html .= 'function checkPasswordMatch() {';
    $form_html .= 'const password = document.getElementById("password").value;';
    $form_html .= 'const confirmPassword = document.getElementById("confirm_password").value;';
    $form_html .= 'const matchIndicator = document.getElementById("password-match");';
    $form_html .= 'const confirmField = document.getElementById("confirm_password");';
    $form_html .= 'if (confirmPassword === "") {';
    $form_html .= 'matchIndicator.innerHTML = "";';
    $form_html .= 'confirmField.style.borderColor = "#ddd";';
    $form_html .= 'return;';
    $form_html .= '}';
    $form_html .= 'if (password === confirmPassword) {';
    $form_html .= 'matchIndicator.innerHTML = "✅ 密碼匹配！";';
    $form_html .= 'matchIndicator.style.color = "#28a745";';
    $form_html .= 'confirmField.style.borderColor = "#28a745";';
    $form_html .= '} else {';
    $form_html .= 'matchIndicator.innerHTML = "❌ 密碼不匹配";';
    $form_html .= 'matchIndicator.style.color = "#dc3545";';
    $form_html .= 'confirmField.style.borderColor = "#dc3545";';
    $form_html .= '}';
    $form_html .= '}';
    
    // Email 檢查功能
    $form_html .= 'var emailCheckTimer;';
    $form_html .= '$("#email").on("input", function() {';
    $form_html .= 'clearTimeout(emailCheckTimer);';
    $form_html .= 'var email = $(this).val();';
    $form_html .= 'var emailStatus = $("#email-status");';
    $form_html .= 'if (email.length > 0 && email.includes("@")) {';
    $form_html .= 'emailStatus.html("檢查中...");';
    $form_html .= 'emailStatus.css("color", "#666");';
    $form_html .= 'emailCheckTimer = setTimeout(function() {';
    $form_html .= '$.ajax({';
    $form_html .= 'url: "' . admin_url('admin-ajax.php') . '",';
    $form_html .= 'type: "POST",';
    $form_html .= 'data: {';
    $form_html .= 'action: "flatsome_byob_check_email",';
    $form_html .= 'email: email,';
    $form_html .= 'nonce: $("#email-check-nonce").val()';
    $form_html .= '},';
    $form_html .= 'success: function(response) {';
    $form_html .= 'if (response.success) {';
    $form_html .= 'if (response.data.exists) {';
    $form_html .= 'emailStatus.html("❌ " + response.data.message);';
    $form_html .= 'emailStatus.css("color", "#dc3545");';
    $form_html .= '$("#email").css("border-color", "#dc3545");';
    $form_html .= '} else {';
    $form_html .= 'emailStatus.html("✅ " + response.data.message);';
    $form_html .= 'emailStatus.css("color", "#28a745");';
    $form_html .= '$("#email").css("border-color", "#28a745");';
    $form_html .= '}';
    $form_html .= '} else {';
    $form_html .= 'emailStatus.html("❌ " + response.data);';
    $form_html .= 'emailStatus.css("color", "#dc3545");';
    $form_html .= '}';
    $form_html .= '},';
    $form_html .= 'error: function() {';
    $form_html .= 'emailStatus.html("檢查失敗，請稍後再試");';
    $form_html .= 'emailStatus.css("color", "#dc3545");';
    $form_html .= '}';
    $form_html .= '});';
    $form_html .= '}, 500);';
    $form_html .= '} else {';
    $form_html .= 'emailStatus.html("");';
    $form_html .= '$("#email").css("border-color", "#ddd");';
    $form_html .= '}';
    $form_html .= '});';
    
    // 密碼驗證事件監聽器
    $form_html .= '$("#password").on("input", function() {';
    $form_html .= 'checkPasswordStrength(this.value);';
    $form_html .= 'checkPasswordMatch();';
    $form_html .= '});';
    
    $form_html .= '$("#confirm_password").on("input", function() {';
    $form_html .= 'checkPasswordMatch();';
    $form_html .= '});';
    
    // 表單提交處理
    $form_html .= '$("#flatsome-byob-restaurant-registration").on("submit", function(e) {';
    $form_html .= 'e.preventDefault();';
    
    // 前端驗證
    $form_html .= 'const email = $("#email").val();';
    $form_html .= 'const password = $("#password").val();';
    $form_html .= 'const confirmPassword = $("#confirm_password").val();';
    
    // Email 格式檢查
    $form_html .= 'if (!email.includes("@")) {';
    $form_html .= '$("#flatsome-byob-registration-message").html("<div style=background:#f8d7da;color:#721c24;padding:15px;border-radius:4px;text-align:center;>請輸入有效的Email地址</div>");';
    $form_html .= 'return false;';
    $form_html .= '}';
    
    // 密碼驗證
    $form_html .= 'if (password.length < 8) {';
    $form_html .= '$("#flatsome-byob-registration-message").html("<div style=background:#f8d7da;color:#721c24;padding:15px;border-radius:4px;text-align:center;>密碼長度至少需要8個字元</div>");';
    $form_html .= 'return false;';
    $form_html .= '}';
    $form_html .= 'if (password !== confirmPassword) {';
    $form_html .= '$("#flatsome-byob-registration-message").html("<div style=background:#f8d7da;color:#721c24;padding:15px;border-radius:4px;text-align:center;>密碼與確認密碼不匹配</div>");';
    $form_html .= 'return false;';
    $form_html .= '}';
    
    $form_html .= 'var formData = new FormData(this);';
    $form_html .= 'formData.append("action", "flatsome_byob_direct_registration");';
    $form_html .= 'var submitButton = $(this).find("button[type=submit]");';
    $form_html .= 'var originalText = submitButton.text();';
    $form_html .= 'submitButton.text("處理中...").prop("disabled", true);';
    $form_html .= '$.ajax({';
    $form_html .= 'url: "' . admin_url('admin-ajax.php') . '",';
    $form_html .= 'type: "POST",';
    $form_html .= 'data: formData,';
    $form_html .= 'processData: false,';
    $form_html .= 'contentType: false,';
    $form_html .= 'success: function(response) {';
    $form_html .= 'if (response.success) {';
    $form_html .= '$("#flatsome-byob-registration-message").html("<div style=background:#d4edda;color:#155724;padding:15px;border-radius:4px;text-align:center;>" + response.data.message + "</div>");';
    $form_html .= '$("#flatsome-byob-restaurant-registration").hide();';
    $form_html .= 'if (response.data.redirect_url) {';
    $form_html .= 'startCountdownAndRedirect(response.data.redirect_url);';
    $form_html .= '}';
    $form_html .= '} else {';
    $form_html .= '$("#flatsome-byob-registration-message").html("<div style=background:#f8d7da;color:#721c24;padding:15px;border-radius:4px;text-align:center;>" + response.data + "</div>");';
    $form_html .= 'submitButton.text(originalText).prop("disabled", false);';
    $form_html .= '}';
    $form_html .= '},';
    $form_html .= 'error: function() {';
    $form_html .= '$("#flatsome-byob-registration-message").html("<div style=background:#f8d7da;color:#721c24;padding:15px;border-radius:4px;text-align:center;>發生錯誤，請稍後再試</div>");';
    $form_html .= 'submitButton.text(originalText).prop("disabled", false);';
    $form_html .= '}';
    $form_html .= '});';
    $form_html .= '});';
    
    $form_html .= 'function startCountdownAndRedirect(redirectUrl) {';
    $form_html .= 'var countdown = 3;';
    $form_html .= 'var countdownElement = $("#flatsome-byob-countdown");';
    $form_html .= 'var redirectStatusElement = $("#flatsome-byob-redirect-status");';
    $form_html .= 'countdownElement.show();';
    $form_html .= 'redirectStatusElement.show();';
    $form_html .= 'var countdownTimer = setInterval(function() {';
    $form_html .= 'if (countdown > 0) {';
    $form_html .= 'countdownElement.html("<div style=font-size:18px;color:#8b2635;font-weight:bold;>倒數計時：" + countdown + " 秒</div>");';
    $form_html .= 'redirectStatusElement.html("<div style=color:#666;>正在準備跳轉到餐廳資料編輯頁面...</div>");';
    $form_html .= 'countdown--;';
    $form_html .= '} else {';
    $form_html .= 'clearInterval(countdownTimer);';
    $form_html .= 'countdownElement.html("<div style=font-size:18px;color:#8b2635;font-weight:bold;>正在跳轉...</div>");';
    $form_html .= 'redirectStatusElement.html("<div style=color:#666;>跳轉中，請稍候...</div>");';
    $form_html .= 'setTimeout(function() {';
    $form_html .= 'window.location.href = redirectUrl;';
    $form_html .= '}, 1000);';
    $form_html .= '}';
    $form_html .= '}, 1000);';
    $form_html .= '}';
    $form_html .= '});';
    $form_html .= '</script>';
    
    return $form_html;
}

// 註冊短代碼
add_shortcode('flatsome_byob_restaurant_registration_form', 'flatsome_byob_restaurant_registration_form_shortcode');

// =============================================================================
// 🔍 重複檢查後台管理功能
// =============================================================================

/**
 * 餐廳審核管理頁面
 */
function byob_restaurant_review_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('權限不足');
    }
    
    echo '<div class="wrap">';
    echo '<h1>餐廳審核管理</h1>';
    
    // 標籤頁
    echo '<h2 class="nav-tab-wrapper">';
    
    // 計算一般審核數量
    $general_count = count(get_posts([
        'post_type' => 'restaurant',
        'post_status' => 'draft',
        'meta_query' => [
            [
                'key' => '_byob_review_status',
                'value' => 'pending_general_review'
            ]
        ]
    ]));
    
    // 計算重複檢查數量
    $duplicate_count = count(get_posts([
        'post_type' => 'restaurant',
        'post_status' => 'pending',
        'meta_query' => [
            [
                'key' => '_byob_review_status',
                'value' => 'pending_duplicate_review'
            ]
        ]
    ]));
    
    echo '<a href="#general-review" class="nav-tab nav-tab-active">';
    echo '一般審核';
    if ($general_count > 0) {
        echo ' <span class="nav-tab-count" style="background: #d63638; color: white; border-radius: 10px; padding: 2px 6px; font-size: 11px; margin-left: 5px;">' . $general_count . '</span>';
    }
    echo '</a>';
    
    echo '<a href="#duplicate-review" class="nav-tab">';
    echo '重複檢查';
    if ($duplicate_count > 0) {
        echo ' <span class="nav-tab-count" style="background: #d63638; color: white; border-radius: 10px; padding: 2px 6px; font-size: 11px; margin-left: 5px;">' . $duplicate_count . '</span>';
    }
    echo '</a>';
    
    echo '</h2>';
    
    // 一般審核區塊
    echo '<div id="general-review" class="tab-content">';
    echo '<h3>一般審核 (草稿狀態)</h3>';
    
    $general_pending = get_posts([
        'post_type' => 'restaurant',
        'post_status' => 'draft',
        'meta_query' => [
            [
                'key' => '_byob_review_status',
                'value' => 'pending_general_review'
            ]
        ]
    ]);
    
    if (empty($general_pending)) {
        echo '<p>目前沒有待審核的餐廳。</p>';
    } else {
        foreach ($general_pending as $restaurant) {
            echo '<div class="review-item" style="border: 1px solid #ddd; padding: 15px; margin: 10px 0;">';
            echo '<h4>' . get_field('restaurant_name', $restaurant->ID) . '</h4>';
            echo '<p>地址：' . get_field('address', $restaurant->ID) . '</p>';
            echo '<p>提交時間：' . get_post_meta($restaurant->ID, '_byob_created_at', true) . '</p>';
            
            echo '<div class="actions">';
            echo '<button onclick="approveRestaurant(' . $restaurant->ID . ')" class="button button-primary">審核通過</button>';
            echo '<button onclick="rejectRestaurant(' . $restaurant->ID . ')" class="button">審核拒絕</button>';
            echo '</div>';
            echo '</div>';
        }
    }
    
    echo '</div>';
    
    // 重複檢查區塊
    echo '<div id="duplicate-review" class="tab-content" style="display:none;">';
    echo '<h3>重複檢查 (待審核狀態)</h3>';
    
    $duplicate_pending = get_posts([
        'post_type' => 'restaurant',
        'post_status' => 'pending',
        'meta_query' => [
            [
                'key' => '_byob_review_status',
                'value' => 'pending_duplicate_review'
            ]
        ]
    ]);
    
    if (empty($duplicate_pending)) {
        echo '<p>目前沒有待重複檢查的餐廳。</p>';
    } else {
        foreach ($duplicate_pending as $restaurant) {
            $duplicate_info = get_post_meta($restaurant->ID, '_byob_duplicate_check', true);
            $similar_restaurant = get_post($duplicate_info['similar_restaurant_id']);
            
            echo '<div class="duplicate-item" style="border: 1px solid #ff6b6b; padding: 15px; margin: 10px 0; background: #fff5f5;">';
            echo '<h4>⚠️ 待審核餐廳：' . get_field('restaurant_name', $restaurant->ID) . '</h4>';
            echo '<p>地址：' . get_field('address', $restaurant->ID) . '</p>';
            echo '<p>相似度：' . $duplicate_info['similarity_score'] . '%</p>';
            
            echo '<h5>相似餐廳：' . get_field('restaurant_name', $similar_restaurant->ID) . '</h5>';
            echo '<p>地址：' . get_field('address', $similar_restaurant->ID) . '</p>';
            
            echo '<div class="actions">';
            echo '<button onclick="confirmDuplicate(' . $restaurant->ID . ')" class="button">確認重複</button>';
            echo '<button onclick="confirmNotDuplicate(' . $restaurant->ID . ')" class="button button-primary">確認不重複</button>';
            echo '</div>';
            echo '</div>';
        }
    }
    
    echo '</div>';
    echo '</div>';
    
    // 載入 JavaScript 和 jQuery UI
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_style('wp-jquery-ui-dialog');
    ?>
    <script>
    // 標籤頁切換
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
    });
    
    // 審核函數
    function approveRestaurant(restaurantId) {
        if (confirm('確定要審核通過這家餐廳嗎？')) {
            jQuery.post(ajaxurl, {
                action: 'byob_handle_review',
                action_type: 'approve_general',
                restaurant_id: restaurantId
            }, function(response) {
                if (response.success) {
                    alert('審核通過！');
                    location.reload();
                } else {
                    alert('操作失敗：' + response.data);
                }
            });
        }
    }
    
    function rejectRestaurant(restaurantId) {
        // 顯示拒絕原因選擇對話框
        var rejectionReasons = {
            'duplicate_restaurant': '此餐廳與我們資料庫中的現有餐廳重複',
            'incomplete_info': '餐廳資訊不完整，無法提供準確的 BYOB 資訊',
            'invalid_address': '提供的地址格式不正確或無法確認位置',
            'not_byob_restaurant': '經查證，此餐廳不提供 BYOB（自帶酒水）服務',
            'closed_restaurant': '此餐廳已歇業或不存在',
            'inappropriate_content': '推薦內容不符合平台規範',
            'fake_recommendation': '經查證為虛假推薦',
            'custom': '其他原因'
        };
        
        var reasonHtml = '<div style="margin: 20px 0;">';
        reasonHtml += '<h4>請選擇拒絕原因：</h4>';
        
        Object.keys(rejectionReasons).forEach(function(key) {
            reasonHtml += '<label style="display: block; margin: 10px 0; cursor: pointer;">';
            reasonHtml += '<input type="radio" name="rejection_reason" value="' + key + '" style="margin-right: 8px;">';
            reasonHtml += rejectionReasons[key];
            reasonHtml += '</label>';
        });
        
        reasonHtml += '<div id="custom_reason_div" style="margin-top: 15px; display: none;">';
        reasonHtml += '<label>請說明具體原因：</label><br>';
        reasonHtml += '<textarea id="custom_reason" rows="3" cols="50" style="width: 100%; margin-top: 5px;"></textarea>';
        reasonHtml += '</div>';
        
        reasonHtml += '</div>';
        
        // 創建對話框
        var dialog = jQuery('<div>').html(reasonHtml).dialog({
            title: '選擇拒絕原因',
            modal: true,
            width: 500,
            height: 400,
            buttons: {
                '確定拒絕': function() {
                    var selectedReason = jQuery('input[name="rejection_reason"]:checked').val();
                    var customReason = jQuery('#custom_reason').val();
                    
                    if (!selectedReason) {
                        alert('請選擇拒絕原因');
                        return;
                    }
                    
                    if (selectedReason === 'custom' && !customReason.trim()) {
                        alert('請填寫具體的拒絕原因');
                        return;
                    }
                    
                    // 發送拒絕請求
            jQuery.post(ajaxurl, {
                action: 'byob_handle_review',
                action_type: 'reject_general',
                        restaurant_id: restaurantId,
                        rejection_reason: selectedReason,
                        custom_reason: customReason
            }, function(response) {
                if (response.success) {
                            alert('已拒絕並發送通知！');
                    location.reload();
                } else {
                    alert('操作失敗：' + response.data);
                }
            });
                    
                    dialog.dialog('close');
                },
                '取消': function() {
                    dialog.dialog('close');
                }
            }
        });
        
        // 監聽單選按鈕變化
        jQuery('input[name="rejection_reason"]').change(function() {
            if (jQuery(this).val() === 'custom') {
                jQuery('#custom_reason_div').show();
            } else {
                jQuery('#custom_reason_div').hide();
            }
        });
    }
    
    function confirmDuplicate(restaurantId) {
        // 顯示重複餐廳拒絕原因選擇對話框
        var rejectionReasons = {
            'duplicate_restaurant': '此餐廳與我們資料庫中的現有餐廳重複',
            'incomplete_info': '餐廳資訊不完整，無法提供準確的 BYOB 資訊',
            'invalid_address': '提供的地址格式不正確或無法確認位置',
            'not_byob_restaurant': '經查證，此餐廳不提供 BYOB（自帶酒水）服務',
            'closed_restaurant': '此餐廳已歇業或不存在',
            'inappropriate_content': '推薦內容不符合平台規範',
            'fake_recommendation': '經查證為虛假推薦',
            'custom': '其他原因'
        };
        
        var reasonHtml = '<div style="margin: 20px 0;">';
        reasonHtml += '<h4>請選擇拒絕原因：</h4>';
        
        Object.keys(rejectionReasons).forEach(function(key) {
            reasonHtml += '<label style="display: block; margin: 10px 0; cursor: pointer;">';
            reasonHtml += '<input type="radio" name="duplicate_rejection_reason" value="' + key + '" style="margin-right: 8px;">';
            reasonHtml += rejectionReasons[key];
            reasonHtml += '</label>';
        });
        
        reasonHtml += '<div id="duplicate_custom_reason_div" style="margin-top: 15px; display: none;">';
        reasonHtml += '<label>請說明具體原因：</label><br>';
        reasonHtml += '<textarea id="duplicate_custom_reason" rows="3" cols="50" style="width: 100%; margin-top: 5px;"></textarea>';
        reasonHtml += '</div>';
        
        reasonHtml += '</div>';
        
        // 創建對話框
        var dialog = jQuery('<div>').html(reasonHtml).dialog({
            title: '確認重複餐廳 - 選擇拒絕原因',
            modal: true,
            width: 500,
            height: 400,
            buttons: {
                '確認重複並拒絕': function() {
                    var selectedReason = jQuery('input[name="duplicate_rejection_reason"]:checked').val();
                    var customReason = jQuery('#duplicate_custom_reason').val();
                    
                    if (!selectedReason) {
                        alert('請選擇拒絕原因');
                        return;
                    }
                    
                    if (selectedReason === 'custom' && !customReason.trim()) {
                        alert('請填寫具體的拒絕原因');
                        return;
                    }
                    
                    // 發送確認重複請求
            jQuery.post(ajaxurl, {
                action: 'byob_handle_review',
                action_type: 'confirm_duplicate',
                        restaurant_id: restaurantId,
                        rejection_reason: selectedReason,
                        custom_reason: customReason
            }, function(response) {
                if (response.success) {
                            alert('已確認重複並發送通知！');
                    location.reload();
                } else {
                    alert('操作失敗：' + response.data);
                }
            });
                    
                    dialog.dialog('close');
                },
                '取消': function() {
                    dialog.dialog('close');
                }
            }
        });
        
        // 監聽單選按鈕變化
        jQuery('input[name="duplicate_rejection_reason"]').change(function() {
            if (jQuery(this).val() === 'custom') {
                jQuery('#duplicate_custom_reason_div').show();
            } else {
                jQuery('#duplicate_custom_reason_div').hide();
            }
        });
    }
    
    function confirmNotDuplicate(restaurantId) {
        if (confirm('確定這兩家不是重複的餐廳嗎？')) {
            jQuery.post(ajaxurl, {
                action: 'byob_handle_review',
                action_type: 'confirm_not_duplicate',
                restaurant_id: restaurantId
            }, function(response) {
                if (response.success) {
                    alert('已確認不重複，等待發布！');
                    location.reload();
                } else {
                    alert('操作失敗：' + response.data);
                }
            });
        }
    }
    </script>
    <?php
}

/**
 * 抽獎管理頁面
 */
function byob_lottery_management_page() {
    // 處理抽獎執行
    if (isset($_POST['execute_lottery']) && wp_verify_nonce($_POST['lottery_nonce'], 'execute_lottery')) {
        $month = sanitize_text_field($_POST['lottery_month']);
        $result = byob_execute_lottery($month);
        
        if ($result['success']) {
            echo '<div class="notice notice-success"><p>抽獎執行成功！共 ' . $result['total_participants'] . ' 位參與者，' . count($result['winners']) . ' 位中獎者。</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>抽獎執行失敗：' . $result['message'] . '</p></div>';
        }
    }
    
    // 處理增加社群分享機會
    if (isset($_POST['add_social_share']) && wp_verify_nonce($_POST['social_share_nonce'], 'add_social_share')) {
        $participant_id = intval($_POST['participant_id']);
        $current_chance = get_field('social_share_chance', $participant_id) ?: 0;
        $base_chances = get_field('base_chances', $participant_id) ?: 1;
        
        update_field('social_share_chance', 1, $participant_id);
        update_field('total_chances', $base_chances + 1, $participant_id);
        
        echo '<div class="notice notice-success"><p>已為參與者增加社群分享機會！</p></div>';
    }
    
    echo '<div class="wrap">';
    echo '<h1>🎁 抽獎管理</h1>';
    
    // 抽獎執行區塊
    echo '<div class="card" style="max-width: 600px; margin: 20px 0;">';
    echo '<h2>執行抽獎</h2>';
    echo '<form method="post">';
    wp_nonce_field('execute_lottery', 'lottery_nonce');
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row">抽獎月份</th>';
    echo '<td>';
    echo '<select name="lottery_month">';
    
    // 生成最近12個月的選項
    for ($i = 0; $i < 12; $i++) {
        $month = date('Y-m', strtotime("-{$i} months"));
        $month_name = date('Y年m月', strtotime("-{$i} months"));
        echo '<option value="' . $month . '">' . $month_name . '</option>';
    }
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit">';
    echo '<input type="submit" name="execute_lottery" class="button-primary" value="執行抽獎" onclick="return confirm(\'確定要執行抽獎嗎？此操作無法復原。\');">';
    echo '</p>';
    echo '</form>';
    echo '</div>';
    
    // 參與者統計
    $current_month = date('Y-m');
    $participants = get_posts([
        'post_type' => 'lottery_participant',
        'meta_query' => [
            [
                'key' => 'month',
                'value' => $current_month
            ]
        ],
        'numberposts' => -1
    ]);
    
    echo '<div class="card" style="max-width: 800px; margin: 20px 0;">';
    echo '<h2 class="lottery-stats-title">本月參與者統計 (' . date('Y年m月') . ')</h2>';
    echo '<p class="participant-count"><strong>參與人數：</strong>' . count($participants) . ' 人</p>';
    
    if (!empty($participants)) {
        echo '<h3>參與者清單</h3>';
        echo '<table class="wp-list-table widefat fixed striped participant-table">';
        echo '<thead><tr><th>姓名</th><th>Email</th><th>推薦餐廳</th><th>基本機會</th><th>分享機會</th><th>總機會</th><th>操作</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($participants as $participant) {
            $name = get_field('customer_recommender_name', $participant->ID);
            $email = get_field('customer_recommender_email', $participant->ID);
            $restaurant = get_field('restaurant_name', $participant->ID);
            $base_chances = get_field('base_chances', $participant->ID) ?: 1;
            $social_share_chance = get_field('social_share_chance', $participant->ID) ?: 0;
            $total_chances = get_field('total_chances', $participant->ID) ?: 1;
            
            echo '<tr>';
            echo '<td>' . esc_html($name) . '</td>';
            echo '<td>' . esc_html($email) . '</td>';
            echo '<td>' . esc_html($restaurant) . '</td>';
            echo '<td>' . esc_html($base_chances) . '</td>';
            echo '<td>' . esc_html($social_share_chance) . '</td>';
            echo '<td><strong>' . esc_html($total_chances) . '</strong></td>';
            echo '<td>';
            
            if ($social_share_chance == 0) {
                echo '<form method="post" style="display: inline;">';
                wp_nonce_field('add_social_share', 'social_share_nonce');
                echo '<input type="hidden" name="participant_id" value="' . $participant->ID . '">';
                echo '<input type="submit" name="add_social_share" class="button button-small" value="增加分享機會" onclick="return confirm(\'確定要為此參與者增加社群分享機會嗎？\');">';
                echo '</form>';
            } else {
                echo '<span style="color: green;">✓ 已分享</span>';
            }
            
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }
    echo '</div>';
    
    // 歷史抽獎結果
    $lottery_results = get_posts([
        'post_type' => 'lottery_result',
        'numberposts' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    
    if (!empty($lottery_results)) {
        echo '<div class="card" style="max-width: 800px; margin: 20px 0;">';
        echo '<h2>歷史抽獎結果</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>月份</th><th>參與人數</th><th>中獎者</th><th>抽獎日期</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($lottery_results as $result) {
            $month = get_field('month', $result->ID);
            $total_participants = get_field('total_participants', $result->ID);
            $winners_json = get_field('winners', $result->ID);
            $draw_date = get_field('draw_date', $result->ID);
            
            $winners = json_decode($winners_json, true);
            $winner_names = [];
            if (is_array($winners)) {
                foreach ($winners as $winner) {
                    $winner_names[] = $winner['name'] . ' (' . $winner['prize'] . ')';
                }
            }
            
            echo '<tr>';
            echo '<td>' . esc_html($month) . '</td>';
            echo '<td>' . esc_html($total_participants) . ' 人</td>';
            echo '<td>' . esc_html(implode(', ', $winner_names)) . '</td>';
            echo '<td>' . esc_html($draw_date) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        echo '</div>';
    }
    
    // 加入 JavaScript 實現動態月份選擇
    ?>
    <script>
    jQuery(document).ready(function($) {
        // 為月份選擇下拉選單添加事件監聽器
        $('select[name="lottery_month"]').on('change', function() {
            var selectedMonth = $(this).val();
            var monthName = $(this).find('option:selected').text();
            
            // 更新統計標題
            $('.lottery-stats-title').text('本月參與者統計 (' + monthName + ')');
            
            // 發送 AJAX 請求獲取該月份的參與者資料
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_monthly_participants',
                    month: selectedMonth,
                    nonce: '<?php echo wp_create_nonce('lottery_month_ajax'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // 更新統計數字
                        $('.participant-count').text('參與人數: ' + response.data.total + '人');
                        
                        // 更新參與者清單
                        var tbody = $('.participant-table tbody');
                        tbody.empty();
                        
                        if (response.data.participants.length > 0) {
                            $.each(response.data.participants, function(index, participant) {
                                var row = '<tr>' +
                                    '<td>' + participant.name + '</td>' +
                                    '<td>' + participant.email + '</td>' +
                                    '<td>' + participant.restaurant + '</td>' +
                                    '<td>' + participant.base_chances + '</td>' +
                                    '<td>' + participant.social_share_chance + '</td>' +
                                    '<td>' + participant.total_chances + '</td>' +
                                    '<td>' + participant.actions + '</td>' +
                                    '</tr>';
                                tbody.append(row);
                            });
                        } else {
                            tbody.append('<tr><td colspan="7" style="text-align: center;">該月份無參與者</td></tr>');
                        }
                    }
                },
                error: function() {
                    console.log('載入月份資料失敗');
                }
            });
        });
    });
    </script>
    <?php
    
    echo '</div>';
}

/**
 * AJAX 處理函數：獲取月份參與者資料
 */
add_action('wp_ajax_get_monthly_participants', 'byob_get_monthly_participants_ajax');

function byob_get_monthly_participants_ajax() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('權限不足');
        return;
    }
    
    if (!wp_verify_nonce($_POST['nonce'], 'lottery_month_ajax')) {
        wp_send_json_error('安全驗證失敗');
        return;
    }
    
    $month = sanitize_text_field($_POST['month']);
    
    // 取得該月份的參與者
    $participants = get_posts([
        'post_type' => 'lottery_participant',
        'meta_query' => [
            [
                'key' => 'month',
                'value' => $month
            ]
        ],
        'numberposts' => -1
    ]);
    
    $participant_data = [];
    foreach ($participants as $participant) {
        $name = get_field('customer_recommender_name', $participant->ID);
        $email = get_field('customer_recommender_email', $participant->ID);
        $restaurant = get_field('restaurant_name', $participant->ID);
        $base_chances = get_field('base_chances', $participant->ID) ?: 1;
        $social_share_chance = get_field('social_share_chance', $participant->ID) ?: 0;
        $total_chances = get_field('total_chances', $participant->ID) ?: 1;
        
        // 生成操作按鈕
        $actions = '';
        if ($social_share_chance == 0) {
            $actions .= '<form method="post" style="display: inline;">';
            $actions .= wp_nonce_field('add_social_share', 'social_share_nonce', true, false);
            $actions .= '<input type="hidden" name="participant_id" value="' . $participant->ID . '">';
            $actions .= '<input type="submit" name="add_social_share" value="增加分享機會" class="button button-small">';
            $actions .= '</form>';
        } else {
            $actions .= '<span style="color: green;">✓ 已分享</span>';
        }
        
        $participant_data[] = [
            'name' => $name,
            'email' => $email,
            'restaurant' => $restaurant,
            'base_chances' => $base_chances,
            'social_share_chance' => $social_share_chance,
            'total_chances' => $total_chances,
            'actions' => $actions
        ];
    }
    
    wp_send_json_success([
        'total' => count($participants),
        'participants' => $participant_data
    ]);
}

/**
 * 處理審核確認的 AJAX
 */
add_action('wp_ajax_byob_handle_review', 'byob_handle_review_confirmation');

function byob_handle_review_confirmation() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('權限不足');
        return;
    }
    
    $action = $_POST['action_type'];
    $restaurant_id = intval($_POST['restaurant_id']);
    
    if ($action === 'approve_general') {
        // 一般審核通過 - 立即發布
        wp_update_post([
            'ID' => $restaurant_id,
            'post_status' => 'publish'
        ]);
        
        update_post_meta($restaurant_id, '_byob_review_status', 'published');
        update_post_meta($restaurant_id, '_byob_approved_at', current_time('mysql'));
        update_post_meta($restaurant_id, '_byob_published_at', current_time('mysql'));
        
        // 觸發推薦成功通知（如果是顧客推薦）
        $source = get_post_meta($restaurant_id, '_byob_registration_source', true);
        if ($source === 'customer_recommendation') {
            do_action('byob_restaurant_published', $restaurant_id);
        }
        
        wp_send_json_success('審核通過並已發布');
        
    } elseif ($action === 'reject_general') {
        // 一般審核拒絕
        $rejection_reason = sanitize_text_field($_POST['rejection_reason'] ?? '');
        $custom_reason = sanitize_textarea_field($_POST['custom_reason'] ?? '');
        
        wp_trash_post($restaurant_id);
        update_post_meta($restaurant_id, '_byob_review_status', 'rejected');
        update_post_meta($restaurant_id, '_byob_rejection_reason', $rejection_reason);
        update_post_meta($restaurant_id, '_byob_rejection_custom_reason', $custom_reason);
        update_post_meta($restaurant_id, '_byob_rejected_at', current_time('mysql'));
        
        // 發送拒絕通知 Email（如果是顧客推薦）
        $source = get_field('source', $restaurant_id);
        if ($source === 'customer_recommendation') {
            $sent = byob_send_customer_rejection_notification($restaurant_id, $rejection_reason, $custom_reason);
            error_log("BYOB: 一般審核拒絕 - 來源: {$source}, Email發送: " . ($sent ? '成功' : '失敗'));
        } else {
            error_log("BYOB: 一般審核拒絕 - 來源: {$source}, 非顧客推薦，不發送Email");
        }
        
        wp_send_json_success('審核拒絕並已發送通知');
        
    } elseif ($action === 'confirm_not_duplicate') {
        // 重複檢查確認不重複 - 立即發布
        wp_update_post([
            'ID' => $restaurant_id,
            'post_status' => 'publish'
        ]);
        
        delete_post_meta($restaurant_id, '_byob_duplicate_check');
        update_post_meta($restaurant_id, '_byob_review_status', 'published');
        update_post_meta($restaurant_id, '_byob_approved_at', current_time('mysql'));
        update_post_meta($restaurant_id, '_byob_published_at', current_time('mysql'));
        
        // 觸發推薦成功通知（如果是顧客推薦）
        $source = get_post_meta($restaurant_id, '_byob_registration_source', true);
        if ($source === 'customer_recommendation') {
            do_action('byob_restaurant_published', $restaurant_id);
        }
        
        wp_send_json_success('確認不重複並已發布');
        
    } elseif ($action === 'confirm_duplicate') {
        // 重複檢查確認重複 - 接收拒絕原因參數
        $rejection_reason = sanitize_text_field($_POST['rejection_reason'] ?? 'duplicate_restaurant');
        $custom_reason = sanitize_textarea_field($_POST['custom_reason'] ?? '此餐廳與現有餐廳重複');
        
        wp_trash_post($restaurant_id);
        update_post_meta($restaurant_id, '_byob_review_status', 'rejected');
        update_post_meta($restaurant_id, '_byob_rejection_reason', $rejection_reason);
        update_post_meta($restaurant_id, '_byob_rejection_custom_reason', $custom_reason);
        update_post_meta($restaurant_id, '_byob_rejected_at', current_time('mysql'));
        
        // 發送拒絕通知 Email（如果是顧客推薦）
        $source = get_field('source', $restaurant_id);
        if ($source === 'customer_recommendation') {
            $sent = byob_send_customer_rejection_notification($restaurant_id, $rejection_reason, $custom_reason);
            error_log("BYOB: 一般審核拒絕 - 來源: {$source}, Email發送: " . ($sent ? '成功' : '失敗'));
        } else {
            error_log("BYOB: 一般審核拒絕 - 來源: {$source}, 非顧客推薦，不發送Email");
        }
        
        wp_send_json_success('確認重複並已發送通知');
    }
}

/**
 * 發送顧客推薦審核拒絕通知 Email
 * 
 * @param int $restaurant_id 餐廳ID
 * @param string $rejection_reason 拒絕原因代碼
 * @param string $custom_reason 自訂拒絕原因
 */
function byob_send_customer_rejection_notification($restaurant_id, $rejection_reason, $custom_reason = '') {
    $restaurant = get_post($restaurant_id);
    if (!$restaurant) {
        error_log("BYOB: 拒絕通知失敗 - 餐廳不存在，ID: {$restaurant_id}");
        return false;
    }
    
    $recommender_email = get_field('customer_recommender_email', $restaurant_id);
    $recommender_name = get_field('customer_recommender_name', $restaurant_id);
    $restaurant_name = get_field('restaurant_name', $restaurant_id);
    
    error_log("BYOB: 拒絕通知檢查 - 餐廳: {$restaurant_name}, Email: {$recommender_email}, 姓名: {$recommender_name}");
    
    // 除錯：檢查所有相關資料
    byob_debug_recommender_info($restaurant_id);
    
    // 如果沒有 Email 就不發送
    if (empty($recommender_email)) {
        error_log("BYOB: 拒絕通知失敗 - 推薦者Email為空，餐廳ID: {$restaurant_id}");
        return false;
    }
    
    // 取得拒絕原因說明
    $reason_text = byob_get_rejection_reason_text($rejection_reason);
    if ($rejection_reason === 'custom' && !empty($custom_reason)) {
        $reason_text = $custom_reason;
    }
    
    // 準備 Email 內容
    $admin_email = 'byobmap.tw@gmail.com';
    $subject = 'BYOB 餐廳推薦審核結果 - ' . $restaurant_name;
    
    $message = byob_generate_rejection_notification_html($restaurant_name, $recommender_name, $reason_text);
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: BYOB <' . $admin_email . '>'
    );
    
    $sent = wp_mail($recommender_email, $subject, $message, $headers);
    
    // 記錄發送狀態
    if ($sent) {
        error_log("BYOB: 拒絕通知已發送 - 收件人: {$recommender_email}, 餐廳: {$restaurant_name}");
    } else {
        error_log("BYOB: 拒絕通知發送失敗 - 收件人: {$recommender_email}, 餐廳: {$restaurant_name}");
    }
    
    return $sent;
}

/**
 * 測試函數：檢查餐廳的推薦者資訊
 * 
 * @param int $restaurant_id 餐廳ID
 */
function byob_debug_recommender_info($restaurant_id) {
    $restaurant = get_post($restaurant_id);
    $source = get_field('source', $restaurant_id);
    $recommender_email = get_field('customer_recommender_email', $restaurant_id);
    $recommender_name = get_field('customer_recommender_name', $restaurant_id);
    $restaurant_name = get_field('restaurant_name', $restaurant_id);
    
    error_log("BYOB DEBUG - 餐廳ID: {$restaurant_id}");
    error_log("BYOB DEBUG - 餐廳名稱: {$restaurant_name}");
    error_log("BYOB DEBUG - 資料來源: {$source}");
    error_log("BYOB DEBUG - 推薦者Email: {$recommender_email}");
    error_log("BYOB DEBUG - 推薦者姓名: {$recommender_name}");
    
    return [
        'restaurant_id' => $restaurant_id,
        'restaurant_name' => $restaurant_name,
        'source' => $source,
        'recommender_email' => $recommender_email,
        'recommender_name' => $recommender_name
    ];
}

/**
 * 取得拒絕原因說明文字
 * 
 * @param string $reason_code 拒絕原因代碼
 * @return string 拒絕原因說明
 */
function byob_get_rejection_reason_text($reason_code) {
    $reasons = array(
        'duplicate_restaurant' => '此餐廳與我們資料庫中的現有餐廳重複',
        'incomplete_info' => '餐廳資訊不完整，無法提供準確的 BYOB 資訊',
        'invalid_address' => '提供的地址格式不正確或無法確認位置',
        'not_byob_restaurant' => '經查證，此餐廳不提供 BYOB（自帶酒水）服務',
        'closed_restaurant' => '此餐廳已歇業或不存在',
        'inappropriate_content' => '推薦內容不符合平台規範',
        'fake_recommendation' => '經查證為虛假推薦',
        'custom' => '其他原因'
    );
    
    return isset($reasons[$reason_code]) ? $reasons[$reason_code] : '未知原因';
}

/**
 * 生成拒絕通知 Email HTML 內容
 * 
 * @param string $restaurant_name 餐廳名稱
 * @param string $recommender_name 推薦者姓名
 * @param string $reason_text 拒絕原因
 * @return string HTML 內容
 */
function byob_generate_rejection_notification_html($restaurant_name, $recommender_name, $reason_text) {
    $display_name = !empty($recommender_name) ? $recommender_name : '親愛的推薦者';
    
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BYOB 餐廳推薦審核結果</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
        <div style="max-width: 600px; margin: 0 auto; background-color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            
            <!-- 標題區塊 -->
            <div style="background: linear-gradient(135deg, #8b2635 0%, #a0303e 100%); padding: 40px 30px; text-align: center; border-radius: 8px 8px 0 0;">
                <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 300;">🍷 BYOBMAP</h1>
                <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">台灣 BYOB 餐廳地圖</p>
            </div>
            
            <!-- 內容區塊 -->
            <div style="padding: 40px 30px;">
                <h2 style="color: #8b2635; margin: 0 0 20px 0; font-size: 24px;">餐廳推薦審核結果</h2>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    親愛的 <strong>' . esc_html($display_name) . '</strong>，
                </p>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                    感謝您推薦餐廳「<strong>' . esc_html($restaurant_name) . '</strong>」給我們！
                </p>
                
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 20px; margin: 20px 0;">
                    <h3 style="color: #856404; margin: 0 0 10px 0; font-size: 18px;">📋 審核結果</h3>
                    <p style="color: #856404; margin: 0; font-size: 16px; line-height: 1.5;">
                        很遺憾地通知您，經過我們的審核，此餐廳推薦未能通過。<br>
                        <strong>原因：</strong>' . esc_html($reason_text) . '
                    </p>
                </div>
                
                <p style="color: #495057; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                    請不要灰心！我們非常重視每一位用戶的推薦，您的參與對我們來說非常重要。
                </p>
                
                <div style="background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 20px; margin: 20px 0;">
                    <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">💡 建議</h4>
                    <p style="color: #0056b3; margin: 0; font-size: 14px; line-height: 1.5;">
                        如果您有其他優質的 BYOB 餐廳推薦，歡迎繼續使用我們的推薦表單分享給大家！
                    </p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://forms.gle/jAnvmwh2BKyVXq5M8" 
                       style="display: inline-block; background-color: rgba(139, 38, 53, 0.8); color: #f8f9fa; text-decoration: none; padding: 16px 32px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">
                        繼續推薦餐廳
                    </a>
                </div>
                
                <p style="color: #6c757d; font-size: 14px; line-height: 1.5; margin: 30px 0 0 0; text-align: center;">
                    感謝您對 BYOBMAP 的支持！<br>
                    如有任何問題，歡迎隨時聯繫我們。
                </p>
            </div>
            
            <!-- 頁腳 -->
            <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; border-top: 1px solid #e9ecef;">
                <p style="color: #6c757d; font-size: 12px; margin: 0;">
                    © 2025 BYOBMAP. All rights reserved.<br>
                    Email: byobmap.tw@gmail.com
                </p>
            </div>
            
        </div>
    </body>
    </html>';
}

// =============================================================================
// 合併餐廳資料函數（已移除 - 改為直接拒絕重複餐廳）
// =============================================================================
// 
// 原本的合併功能已移除，因為：
// 1. 合併邏輯複雜，容易出錯
// 2. 可能覆蓋正確的資料
// 3. 維護成本高
// 
// 現在改為直接拒絕重複餐廳，更簡單、安全、可靠
// =============================================================================

// =============================================================================
// 📢 餐廳發布管理功能（已移除 - 改為審核通過即立刻發布）
// =============================================================================
// 
// 原本的發布管理頁面已移除，因為現在改為審核通過即立刻發布
// 與 WordPress 文章管理頁面的發布機制保持一致
// 
// 發布流程：
// 1. 管理員在審核管理頁面點擊「審核通過」
// 2. 系統立即將餐廳狀態改為「已發布」
// 3. 觸發 transition_post_status hook
// 4. 自動發送推薦成功通知（如果是顧客推薦）
// =============================================================================