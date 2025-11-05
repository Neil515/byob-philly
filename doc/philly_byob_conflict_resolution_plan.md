# 🔄 費城 BYOB 資料衝突處理機制（簡化版 MVP）

## 📋 問題

不同網友推薦同一家餐廳，但提供的資訊不同（例如：開瓶費 $15 vs $20）

## 🎯 簡單解決方案（MVP）

### 核心邏輯：**3 步驟處理**

#### 步驟 1：檢測重複餐廳
- 使用現有的 `byob_check_duplicate_restaurant()` 函數
- 如果發現重複，**不建立新文章**，而是更新現有文章

#### 步驟 2：比較資訊差異
- 只比較關鍵欄位：開瓶費、設備、服務等級
- 如果**完全相同**或**新資訊補充空白**→ 自動合併
- 如果**有衝突**→ 標記為「待審核」，通知管理員

#### 步驟 3：人工審核
- 後台顯示衝突餐廳列表
- 管理員選擇採用哪個版本，或手動編輯
- 決定後更新餐廳資料

---

## 🔧 實作細節

### 1. 修改現有重複檢查邏輯

**現況：**
- 發現重複時，標記為 `pending_duplicate_review`，等待管理員決定是否重複

**修改後：**
- 發現重複時，檢查資訊是否衝突
- 無衝突 → 自動合併，更新現有餐廳
- 有衝突 → 標記為 `pending_conflict_review`，等待管理員審核

### 2. 衝突檢測（只檢測 3 個關鍵欄位）

```php
function byob_check_data_conflict($existing_id, $new_data) {
    $conflicts = [];
    
    // 只檢測 3 個關鍵欄位
    $key_fields = [
        'philly_corkage_fee',
        'corkage_fee_amount', 
        'byob_service_level'
    ];
    
    foreach ($key_fields as $field) {
        $existing_value = get_field($field, $existing_id);
        $new_value = $new_data[$field] ?? '';
        
        // 如果現有值為空，新值補充 → 無衝突
        if (empty($existing_value) && !empty($new_value)) {
            continue; // 無衝突，可以合併
        }
        
        // 如果兩個值都不同且都不為空 → 有衝突
        if (!empty($existing_value) && !empty($new_value) && $existing_value !== $new_value) {
            $conflicts[] = $field;
        }
    }
    
    return [
        'has_conflict' => !empty($conflicts),
        'conflicts' => $conflicts
    ];
}
```

### 3. 處理流程

```php
// 在 byob_create_philly_restaurant_article() 中
function byob_create_philly_restaurant_article($restaurant_data) {
    // 1. 檢查重複
    $duplicate_check = byob_check_duplicate_restaurant($restaurant_data);
    
    if ($duplicate_check['is_duplicate']) {
        $existing_id = $duplicate_check['similar_restaurant_id'];
        
        // 2. 檢查衝突
        $conflict_check = byob_check_data_conflict($existing_id, $restaurant_data);
        
        if ($conflict_check['has_conflict']) {
            // 有衝突：保留現有資料，記錄新推薦供參考，簡單通知管理員
            // 不需要複雜的後台審核介面，因為衝突情況不多
            update_post_meta($existing_id, '_byob_last_conflict_data', $restaurant_data);
            update_post_meta($existing_id, '_byob_last_conflict_date', current_time('mysql'));
            
            // 簡單 email 通知管理員（可選）
            wp_mail(
                get_option('admin_email'),
                'BYOB 餐廳資訊衝突：' . $restaurant_data['restaurant_name'],
                '餐廳「' . $restaurant_data['restaurant_name'] . '」有新推薦，但資訊與現有資料不同。' . "\n\n" .
                '請到 WordPress 後台查看：' . admin_url('post.php?post=' . $existing_id . '&action=edit')
            );
            
            return [
                'success' => true,
                'message' => '此餐廳已有資料，且資訊與新推薦不同。已通知管理員。',
                'post_id' => $existing_id,
                'status' => 'conflict_noted'
            ];
        } else {
            // 無衝突：自動合併
            byob_merge_restaurant_data($existing_id, $restaurant_data);
            
            // 【重要】即使自動合併，也要通知管理員有新推薦
            $recommendation_count = get_post_meta($existing_id, '_byob_recommendation_count', true) ?: 0;
            wp_mail(
                get_option('admin_email'),
                'BYOB 餐廳資訊已自動合併：' . $restaurant_data['restaurant_name'],
                '餐廳「' . $restaurant_data['restaurant_name'] . '」有新推薦，已自動合併到現有資料。' . "\n\n" .
                '推薦者：' . ($restaurant_data['philly_reddit_username'] ?? '未提供') . "\n" .
                '總推薦次數：' . ($recommendation_count + 1) . " 次\n\n" .
                '請到 WordPress 後台查看：' . admin_url('post.php?post=' . $existing_id . '&action=edit')
            );
            
            return [
                'success' => true,
                'message' => '資訊已自動合併到現有餐廳',
                'post_id' => $existing_id,
                'status' => 'merged'
            ];
        }
    }
    
    // 非重複餐廳：正常建立新文章
    // ... 原有邏輯
}
```

### 4. 簡單的合併邏輯（包含記錄推薦歷史）

```php
function byob_merge_restaurant_data($existing_id, $new_data) {
    // 只合併非空欄位（補充空白資訊）
    $fields_to_merge = [
        'philly_corkage_fee',
        'corkage_fee_amount',
        'other_corkage_policy',
        'wine_service_equipment',
        'byob_service_level',
        'philly_restaurant_type',
        'yelp_link',
        'philly_dining_experience'
    ];
    
    foreach ($fields_to_merge as $field) {
        $existing_value = get_field($field, $existing_id);
        $new_value = $new_data[$field] ?? '';
        
        // 如果現有值為空，新值不為空 → 更新
        if (empty($existing_value) && !empty($new_value)) {
            update_field($field, $new_value, $existing_id);
        }
    }
    
    // 【重要】記錄合併歷史（讓管理員知道有多次推薦）
    $merge_history = get_post_meta($existing_id, '_byob_merge_history', true) ?: [];
    $merge_history[] = [
        'date' => current_time('mysql'),
        'source' => $new_data['source'] ?? 'philly_community_recommendation',
        'reddit_username' => $new_data['philly_reddit_username'] ?? '',
        'contact_email' => $new_data['philly_contact_email'] ?? '',
        'merged_fields' => array_keys(array_filter($new_data, function($v) { return !empty($v); }))
    ];
    update_post_meta($existing_id, '_byob_merge_history', $merge_history);
    
    // 更新推薦次數統計
    $recommendation_count = count($merge_history);
    update_post_meta($existing_id, '_byob_recommendation_count', $recommendation_count);
}

---

## 🖥️ 後台管理（超簡單版）

### 不需要建立專門的審核介面！

**處理方式：**
- 如果有衝突 → 保留現有資料不變
- 記錄新推薦資料在餐廳的 post meta 中（`_byob_last_conflict_data`）
- 發送 email 通知管理員（可選）
- 管理員收到 email 後，可以：
  - 直接到 WordPress 後台編輯該餐廳
  - 查看記錄的新推薦資料（在「自訂欄位」區塊）
  - 手動決定是否更新

**優點：**
- 不需要建立新的後台介面
- 不需要複雜的 AJAX 處理
- 因為衝突情況不多，手動處理即可
- 減少開發時間和維護成本

**可選：簡單的提示標記**

如果想在後台顯示提示，可以在餐廳編輯頁面加入簡單的提示：

```php
// 在餐廳編輯頁面頂部顯示提示（可選）
add_action('admin_notices', function() {
    global $post;
    if ($post && $post->post_type === 'restaurant') {
        $conflict_data = get_post_meta($post->ID, '_byob_last_conflict_data', true);
        $conflict_date = get_post_meta($post->ID, '_byob_last_conflict_date', true);
        
        if ($conflict_data) {
            echo '<div class="notice notice-warning">';
            echo '<p><strong>⚠️ 資訊衝突提醒：</strong>此餐廳在 ' . $conflict_date . ' 有新推薦，但資訊與現有資料不同。請查看「自訂欄位」區塊中的 <code>_byob_last_conflict_data</code> 欄位。</p>';
            echo '</div>';
        }
        
        // 顯示推薦次數（讓管理員知道有多次推薦）
        $recommendation_count = get_post_meta($post->ID, '_byob_recommendation_count', true);
        if ($recommendation_count && $recommendation_count > 1) {
            echo '<div class="notice notice-info">';
            echo '<p><strong>ℹ️ 推薦統計：</strong>此餐廳已被 <strong>' . $recommendation_count . ' 位網友</strong>推薦。推薦歷史記錄在「自訂欄位」區塊中的 <code>_byob_merge_history</code> 欄位。</p>';
            echo '</div>';
        }
    }
});
```

---

## 📊 資料優先順序（簡化版）

**只保留 2 個等級：**

1. **餐廳老闆驗證** → 直接採用，覆蓋所有其他版本
2. **社群推薦** → 如果有衝突，人工審核

**不需要複雜的多數決邏輯**，因為：
- 初期可能不會有 3+ 位網友推薦同一家餐廳
- 簡單的人工審核更可靠
- 避免過度複雜的邏輯

---

## ✅ 實施步驟

### 階段 1：基礎功能（1-2 天）
1. ✅ 修改 `byob_create_philly_restaurant_article()` 函數
2. ✅ 新增 `byob_check_data_conflict()` 函數
3. ✅ 新增 `byob_merge_restaurant_data()` 函數
4. ✅ 測試自動合併功能

### 階段 2：後台提示（可選，30 分鐘）
1. ⏳ 在餐廳編輯頁面顯示衝突提示（可選）
2. ⏳ 測試 email 通知功能（可選）

**注意：因為衝突情況不多，階段 2 是可選的，不實作也可以**

### 階段 3：優化（可選）
1. ⏳ 發送衝突通知 email 給管理員
2. ⏳ 記錄合併歷史（可選）

---

## 💡 優點

### 相比複雜版本：
- ✅ **簡單**：只檢測 3 個關鍵欄位
- ✅ **快速實施**：2-3 天即可完成
- ✅ **易於維護**：邏輯簡單，容易理解
- ✅ **足夠使用**：解決核心問題

### 未來擴展：
- 如果之後需要更複雜的功能（版本歷史、多數決等），可以在這個基礎上擴展
- 不需要一開始就建立複雜系統

---

## 🎯 總結

**MVP 版本核心邏輯：**
1. 發現重複餐廳 → 檢查資訊是否衝突
2. 無衝突 → 自動合併（補充空白欄位）
3. 有衝突 → 保留現有資料，記錄新推薦供參考，簡單通知管理員

**處理衝突的方式：**
- 不需要建立複雜的審核介面
- 直接保留現有資料，記錄新推薦
- 管理員收到 email 後（可選），可以手動編輯餐廳
- 因為衝突情況不多，這樣處理就足夠了

**不需要：**
- ❌ 複雜的版本管理系統
- ❌ 多數決邏輯
- ❌ 版本歷史記錄
- ❌ 複雜的可信度等級

**需要：**
- ✅ 簡單的衝突檢測
- ✅ 自動合併邏輯
- ✅ 後台審核介面

---

*最後更新：2025年11月5日*
*版本：v2.0（簡化版 MVP）*
