<?php get_header(); ?>
<style>
.page-header {
  margin: 2rem 0;
  padding: 1rem 0;
  text-align: left;
  border-bottom: 2px solid #D87F8D;
}

.page-title {
  margin: 0;
  padding: 0;
  font-size: 2.5rem;
  font-weight: bold;
  color: #333;
}

/* 讓標題與餐廳卡片使用相同的容器邏輯 */
.page-header {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 1rem 2rem;
}

/* 分頁導航樣式 */
.restaurant-pagination {
  margin: 40px 0;
  display: flex;
  justify-content: center;
  padding: 0;
}

.restaurant-pagination nav.navigation.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 12px;
}

.restaurant-pagination .page-numbers {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 48px;
  min-height: 44px;
  padding: 0 18px;
  background: #ffffff;
  border: 1px solid #cfd4da;
  border-radius: 999px;
  text-decoration: none;
  color: #495057;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.3px;
  transition: all 0.25s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.restaurant-pagination .page-numbers:hover {
  background: #8b2635;
  color: #ffffff;
  border-color: #8b2635;
  box-shadow: 0 4px 10px rgba(139, 38, 53, 0.2);
}

.restaurant-pagination .page-numbers.current {
  background: #8b2635;
  color: #ffffff;
  border-color: #8b2635;
  cursor: default;
}

.restaurant-pagination .page-numbers.prev,
.restaurant-pagination .page-numbers.next {
  font-weight: 600;
  padding: 0 22px;
}

.restaurant-pagination .page-numbers.dots {
  background: transparent;
  border: none;
  box-shadow: none;
  pointer-events: none;
  color: #6c757d;
}

.restaurant-pagination--top {
  margin-top: 0;
  margin-bottom: 30px;
}

.restaurant-pagination--bottom {
  margin-top: 50px;
  margin-bottom: 60px;
}

/* 懶載入動畫 */
.restaurant-card {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.6s ease;
}

.restaurant-card.loaded {
  opacity: 1;
  transform: translateY(0);
}

/* 驗證徽章容器不受右邊距限制 */
.verification-badge-container {
  padding-right: 0 !important;
  max-width: 100%;
  width: 100%;
}

/* 圖片懶載入樣式 */
.restaurant-image {
  opacity: 0;
  transition: opacity 0.3s ease;
}

.restaurant-image.loaded {
  opacity: 1;
}

/* 餐廳照片容器樣式 */
.restaurant-photo {
  width: 100%;
  max-width: 300px;
  height: 200px;
  overflow: hidden;
  border-radius: 8px;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
}

/* 載入中狀態 */
.loading-placeholder {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  background-repeat: no-repeat;
  background-position: center;
}

@keyframes loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* 餐廳卡片行距優化 */
.restaurant-card .field {
  line-height: 1.6 !important;
  margin-bottom: 8px !important;
}

.restaurant-card .info-group {
  margin-bottom: 15px !important;
}

.restaurant-card .acf-fields {
  line-height: 1.5 !important;
}

/* 餐廳標題行距 */
.restaurant-card h2 {
  line-height: 1.4 !important;
  margin-bottom: 15px !important;
}

/* 行動版排版修正，避免卡片寬度溢出 */
@media (max-width: 768px) {
  .restaurant-card {
    width: 100% !important;
    box-sizing: border-box !important;
    min-width: 0 !important;
    --card-media-size: 96px !important;
    --card-media-gap: 0px !important;
  }

  .restaurant-card::before {
    display: none !important;
  }

  .restaurant-photo {
    position: static !important;
    width: 96px !important;
    height: 96px !important;
    margin: 0 auto 16px !important;
  }

  .restaurant-card h2,
  .restaurant-card .field,
  .verification-badge-container,
  .acf-fields,
  .info-group,
  .more-details-btn {
    padding-right: 0 !important;
    min-width: 0 !important;
  }

  .restaurant-title-line {
    justify-content: flex-start;
    text-align: left;
    padding-right: 0 !important;
    width: 100%;
  }

  .restaurant-card h2 {
    text-align: left !important;
    width: 100%;
  }

  .restaurant-type {
    margin-left: 6px;
  }

  .verification-badge-container {
    display: flex !important;
    justify-content: center !important;
  }
}

/* 更多詳情按鈕間距 */
.more-details-btn {
  margin-top: 20px !important;
  padding-top: 15px !important;
  border-top: 1px solid #eee !important;
}

/* 驗證徽章樣式 */
.verification-badge-container {
  margin-bottom: 8px;
  padding-right: 0 !important;
  max-width: 100%;
  width: 100%;
  display: block;
}

.verification-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  vertical-align: middle;
  white-space: nowrap;
  line-height: 1.4;
  width: auto;
  max-width: 100%;
  box-sizing: border-box;
}

.verification-badge.badge-small {
  font-size: 12px;
  padding: 4px 10px;
}

.verification-badge.badge-verified {
  background-color: #e3f2fd;
  color: #424242;
  border: 1px solid #90caf9;
}

.verification-badge.badge-community {
  background-color: #fff3e0;
  color: #424242;
  border: 1px solid #ffcc80;
}

.verification-badge:hover {
  opacity: 0.9;
  cursor: help;
}

/* 最近餐廳地圖區塊 */
.byob-map-section {
  max-width: 1200px;
  margin: 0 auto 40px;
  padding: 0 20px;
}

.byob-map-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
}

.byob-map-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0;
  color: #333333;
}

.byob-map-retry {
  border: 1px solid #8b2635;
  background: #8b2635;
  color: #ffffff;
  padding: 10px 20px;
  border-radius: 999px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

.byob-map-retry:hover,
.byob-map-retry:focus {
  background: #a33745;
  border-color: #a33745;
  outline: none;
}

.byob-map {
  width: 100%;
  height: 420px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.byob-map-status {
  margin: 16px 0 0;
  font-size: 0.95rem;
  color: #555555;
}

.byob-nearby-wrapper {
  margin-top: 24px;
  padding: 20px;
  border: 1px solid #f0d9dd;
  border-radius: 12px;
  background: #fff5f6;
  box-shadow: inset 0 0 0 1px rgba(139, 38, 53, 0.05);
}

.byob-nearby-title {
  margin: 0 0 16px;
  font-size: 1.35rem;
  font-weight: 700;
  color: #8b2635;
}

.byob-nearby-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: grid;
  gap: 12px;
}

.byob-nearby-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  border-radius: 10px;
  background: #ffffff;
  border: 1px solid transparent;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.byob-nearby-item:hover,
.byob-nearby-item.is-active {
  border-color: #8b2635;
  box-shadow: 0 10px 18px rgba(139, 38, 53, 0.12);
  transform: translateY(-2px);
}

.byob-nearby-item .nearby-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.byob-nearby-item .nearby-name {
  font-size: 1.05rem;
  font-weight: 600;
  color: #2f2f2f;
  margin: 0;
}

.byob-nearby-item .nearby-meta {
  font-size: 0.9rem;
  color: #666666;
}

.restaurant-card--highlight {
  border-color: #8b2635 !important;
  box-shadow: 0 10px 20px rgba(139, 38, 53, 0.18) !important;
  transform: translateY(-3px);
}

@media (max-width: 768px) {
  .byob-map {
    height: 320px;
  }

  .byob-nearby-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .byob-map-header {
    align-items: flex-start;
  }
}
</style>
<div class="page-header page-header--nearby">
  <h1 class="page-title">BYOB Near You</h1>
</div>

<?php
$byob_restaurant_js_data = array();

$byob_google_args = array('libraries' => 'places');
$byob_google_api_key = apply_filters('byob_google_maps_api_key', '');

if (!empty($byob_google_api_key)) {
  $byob_google_args['key'] = $byob_google_api_key;
}

$byob_google_map_url = add_query_arg($byob_google_args, 'https://maps.googleapis.com/maps/api/js');

wp_enqueue_script(
  'byob-google-maps',
  esc_url_raw($byob_google_map_url),
  array(),
  null,
  true
);

$byob_nearby_script_path = get_stylesheet_directory() . '/assets/js/byob-nearby.js';
$byob_nearby_script_version = file_exists($byob_nearby_script_path) ? filemtime($byob_nearby_script_path) : null;

wp_enqueue_script(
  'byob-nearby-restaurants',
  get_stylesheet_directory_uri() . '/assets/js/byob-nearby.js',
  array('byob-google-maps'),
  $byob_nearby_script_version,
  true
);
?>

<div class="byob-map-section" id="byob-map-section">
  <div class="byob-map-header">
    <h2 class="byob-map-title"><?php esc_html_e('Find BYOB Near You', 'byob'); ?></h2>
    <button type="button" id="byob-retry-location" class="byob-map-retry">
      <?php esc_html_e('Locate Again', 'byob'); ?>
    </button>
  </div>
  <div id="byob-restaurant-map" class="byob-map" role="region" aria-label="<?php esc_attr_e('Map of BYOB restaurants', 'byob'); ?>"></div>
  <p id="byob-map-status" class="byob-map-status">
    <?php esc_html_e('Loading map. Please allow location access to see nearby restaurants.', 'byob'); ?>
  </p>
  <div id="byob-nearby-wrapper" class="byob-nearby-wrapper" hidden>
    <h3 class="byob-nearby-title"><?php esc_html_e('Closest 5 Restaurants', 'byob'); ?></h3>
    <ul id="byob-nearby-list" class="byob-nearby-list" aria-live="polite"></ul>
  </div>
</div>

<!-- 篩選條件記憶功能 + 懶載入優化 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 篩選條件記憶功能
  const FILTER_STORAGE_KEY = 'restaurant_filters';
  
  // 儲存篩選條件到 sessionStorage
  function saveFilters(filters) {
    try {
      sessionStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(filters));
      console.log('Filters saved:', filters);
    } catch (error) {
      console.error('Failed to save filters:', error);
    }
  }
  
  // 從 sessionStorage 恢復篩選條件
  function restoreFilters() {
    try {
      const savedFilters = sessionStorage.getItem(FILTER_STORAGE_KEY);
      if (savedFilters) {
        const filters = JSON.parse(savedFilters);
        console.log('Restored filters:', filters);
        return filters;
      }
    } catch (error) {
      console.error('Failed to restore filters:', error);
    }
    return null;
  }
  
  // 清除篩選條件
  function clearFilters() {
    try {
      sessionStorage.removeItem(FILTER_STORAGE_KEY);
      console.log('Filters cleared');
    } catch (error) {
      console.error('Failed to clear filters:', error);
    }
  }
  
  // 頁面載入時嘗試恢復篩選條件
  const savedFilters = restoreFilters();
  if (savedFilters) {
    console.log('Found saved filters, waiting for plugin integration...');
            // Future integration with filter plugin
  }
  
  // 懶載入功能
  function initLazyLoading() {
    const restaurantCards = document.querySelectorAll('.restaurant-card');
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target;
          const img = card.querySelector('.restaurant-image');
          
          // 卡片動畫
          setTimeout(() => {
            card.classList.add('loaded');
          }, 100);
          
          // 圖片懶載入
          if (img && img.dataset.src) {
            img.src = img.dataset.src;
            img.onload = () => {
              img.classList.add('loaded');
            };
            img.removeAttribute('data-src');
          }
          
          observer.unobserve(card);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '50px'
    });
    
    restaurantCards.forEach(card => {
      imageObserver.observe(card);
    });
  }
  
  // 初始化懶載入
  initLazyLoading();
  
  // Expose functions globally for future plugin integration
  window.RestaurantFilterMemory = {
    saveFilters: saveFilters,
    restoreFilters: restoreFilters,
    clearFilters: clearFilters
  };
});
</script>

<div class="page-header page-header--all">
  <h1 class="page-title">All BYOB Restaurants</h1>
</div>

<?php
$restaurant_pagination_args = array(
  'prev_text' => '← Previous',
  'next_text' => 'Next →',
  'mid_size'  => 2,
  'before_page_number' => '<span class="screen-reader-text">Page </span>',
);
$restaurant_pagination_html = get_the_posts_pagination($restaurant_pagination_args);

if ($restaurant_pagination_html) {
  echo '<div class="restaurant-pagination restaurant-pagination--top">' . $restaurant_pagination_html . '</div>';
}
?>

<div class="restaurant-archive-list">
  
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php
      $post_id = get_the_ID();
      $post_title = get_the_title();
      $permalink = get_permalink();

      $latitude = get_field('latitude', $post_id);
      $longitude = get_field('longitude', $post_id);

      $address = get_field('address', $post_id);
      $map_link = get_field('map_link', $post_id);

      if (!$map_link && $address) {
        $clean_address = preg_replace('/(\d+樓|\d+[Ff]|\d+樓層|地下\d+樓|[Bb]\d+)/u', '', $address);
        $clean_address = trim($clean_address);
        $map_link = $clean_address ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($clean_address) : '';
      }

      $phone = get_field('phone', $post_id);
      $tel_link = '';
      if (!empty($phone)) {
        $tel_link = $phone;
        if (strpos($phone, '-') === false) {
          $clean_phone = preg_replace('/[^0-9]/', '', $phone);
          if (strlen($clean_phone) == 8) {
            $tel_link = substr($clean_phone, 0, 2) . '-' . substr($clean_phone, 2);
          } elseif (strlen($clean_phone) == 10 && substr($clean_phone, 0, 2) == '09') {
            $tel_link = substr($clean_phone, 0, 4) . '-' . substr($clean_phone, 4);
          }
        }
      }

      $type_labels = byob_get_restaurant_type_labels($post_id);

      $is_charged = get_field('is_charged', $post_id);
      $corkage_fee_amount = get_field('corkage_fee_amount', $post_id);
      $corkage_fee_note = get_field('corkage_fee_note', $post_id);

      $charged_output = '';
      if ($is_charged) {
        if (is_array($is_charged)) {
          $charged_output = implode(' / ', $is_charged);
        } else {
          $charged_output = $is_charged;
        }
      }

      $fee_output = '';
      if ($is_charged) {
        $charged_value = is_array($is_charged) ? reset($is_charged) : $is_charged;

        if (($charged_value === '酌收' || $charged_value === 'yes') && $corkage_fee_amount) {
          $fee_output = $corkage_fee_amount;
        } elseif (($charged_value === '其他' || $charged_value === 'other') && $corkage_fee_note) {
          $fee_output = $corkage_fee_note;
        } elseif ($charged_value === '酌收' || $charged_value === 'yes') {
          $fee_output = __('Charged (amount not set)', 'byob');
        } elseif ($charged_value === '其他' || $charged_value === 'other') {
          $fee_output = __('Other (description not set)', 'byob');
        } elseif ($charged_value === 'no') {
          $fee_output = __('No corkage fee', 'byob');
        } else {
          $fee_output = $charged_value;
        }
      }

      $equipment = get_field('equipment', $post_id);
      $equipment_other_note = get_field('equipment_other_note', $post_id);
      $equipment_output = '';

      if ($equipment) {
        if (is_array($equipment)) {
          $equipment_display = array();
          foreach ($equipment as $item) {
            if ($item === '其他' || strtolower($item) === 'other') {
              if (!empty($equipment_other_note)) {
                $equipment_display[] = 'Other: ' . $equipment_other_note;
              } else {
                $equipment_display[] = $item;
              }
            } else {
              $equipment_display[] = $item;
            }
          }
          $equipment_output = implode(' | ', $equipment_display);
        } else {
          if (strpos($equipment, '其他') !== false || stripos($equipment, 'other') !== false) {
            if (!empty($equipment_other_note)) {
              $equipment_output = preg_replace('/\bother\b/i', 'Other: ' . $equipment_other_note, $equipment);
            } else {
              $equipment_output = $equipment;
            }
          } else {
            $equipment_output = $equipment;
          }
        }
      }

      $notes = get_field('notes', $post_id);
      $truncated_notes = '';
      if ($notes) {
        $truncated_notes = mb_strlen($notes) > 100 ? mb_substr($notes, 0, 100) . '...' : $notes;
      }

      $verification_info = byob_get_verification_badge_info($post_id);
      $verification_score = 0;
      if ($verification_info['status'] === 'verified') {
        $verification_score = 2;
      } elseif ($verification_info['status'] === 'community') {
        $verification_score = 1;
      }

      $gallery_fields = array('restaurant_photo_1', 'restaurant_photo_2', 'restaurant_photo_3');
      $has_gallery_photo = false;
      foreach ($gallery_fields as $gallery_field) {
        $photo_value = get_field($gallery_field, $post_id);
        if (is_array($photo_value) && !empty($photo_value['ID'])) {
          $has_gallery_photo = true;
          break;
        } elseif (!empty($photo_value)) {
          $has_gallery_photo = true;
          break;
        }
      }

      // 依序檢查 ACF restaurant_logo、自訂欄位 _restaurant_logo、舊的 restaurant_photo
      $acf_logo      = get_field('restaurant_logo', $post_id);
      $user_logo_id  = get_post_meta($post_id, '_restaurant_logo', true);
      $admin_logo    = get_field('restaurant_photo', $post_id);

      $logo_id = null;

      if ($acf_logo) {
        if (is_array($acf_logo) && isset($acf_logo['ID'])) {
          $logo_id = $acf_logo['ID'];
        } elseif (is_numeric($acf_logo)) {
          $logo_id = intval($acf_logo);
        }
      }

      if (!$logo_id && $user_logo_id) {
        $logo_id = $user_logo_id;
      }

      if (!$logo_id && $admin_logo && is_array($admin_logo) && isset($admin_logo['ID'])) {
        $logo_id = $admin_logo['ID'];
      }

      $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';

      $completeness_sources = array(
        $address,
        $phone,
        $is_charged,
        $corkage_fee_amount,
        $corkage_fee_note,
        $equipment,
        $notes,
        $map_link,
        $type_labels,
        $latitude,
        $longitude,
      );

      $completeness_score = 0;
      foreach ($completeness_sources as $source_value) {
        if (is_array($source_value)) {
          if (!empty(array_filter($source_value))) {
            $completeness_score++;
          }
        } else {
          if (!empty($source_value) || $source_value === 0 || $source_value === '0') {
            $completeness_score++;
          }
        }
      }

      $byob_restaurant_js_data[] = array(
        'id' => $post_id,
        'title' => $post_title,
        'permalink' => $permalink,
        'latitude' => ($latitude !== null && $latitude !== '') ? (float) $latitude : null,
        'longitude' => ($longitude !== null && $longitude !== '') ? (float) $longitude : null,
        'address' => $address,
        'mapLink' => $map_link,
        'phone' => $phone,
        'formattedPhone' => $tel_link,
        'verificationStatus' => $verification_info['status'],
        'verificationLabel' => $verification_info['label'],
        'verificationIcon' => $verification_info['icon'],
        'verificationDescription' => $verification_info['description'],
        'hasPhoto' => $has_gallery_photo,
        'completenessScore' => $completeness_score,
        'favoriteCount' => 0,
        'corkageFee' => $charged_output,
        'corkageDetails' => $fee_output,
        'equipment' => $equipment_output,
        'notes' => $notes,
        'typeLabels' => array_values((array) $type_labels),
        'logoUrl' => $logo_url,
      );
    ?>
    <div
      class="restaurant-card"
      id="restaurant-card-<?php echo esc_attr($post_id); ?>"
      data-restaurant-id="<?php echo esc_attr($post_id); ?>"
      data-verification="<?php echo esc_attr($verification_score); ?>"
      data-completeness="<?php echo esc_attr($completeness_score); ?>"
      data-has-photo="<?php echo $has_gallery_photo ? '1' : '0'; ?>"
      data-distance="999999"
      data-favorite="0"
    >
      <!-- 加入圖片顯示 -->
      <?php if ($logo_url): ?>
        <div class="restaurant-photo">
          <img data-src="<?php echo esc_url($logo_url); ?>" 
               alt="<?php echo esc_attr($post_title); ?> LOGO"
               class="restaurant-image loading-placeholder"
               style="object-fit: contain; max-width: 100%; height: auto;">
        </div>
      <?php endif; ?>
      
      <!-- 驗證徽章顯示（在餐廳名稱上方） -->
      <div class="verification-badge-container">
        <?php echo byob_display_verification_badge($post_id, 'small'); ?>
      </div>
      
      <h2 class="restaurant-title-line">
        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($post_title); ?></a>
        <?php if (!empty($type_labels)) : ?>
          <span class="restaurant-type">(<?php echo esc_html(implode(' / ', $type_labels)); ?>)</span>
        <?php endif; ?>
      </h2>

      <div class="acf-fields">
        <div class="info-group basic-info">

		<?php if($address): ?>
			  <div class="field">
				<strong>Address:</strong>
          <?php if($map_link): ?>
            <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener">
              <?php echo esc_html($address); ?> 📍
            </a>
          <?php else: ?>
            <?php echo esc_html($address); ?>
          <?php endif; ?>
		  </div>
		<?php else: ?>
			  <div class="field"><strong>Address:</strong> </div>
		<?php endif; ?>


          <?php if (!empty($phone)): ?>
            <div class="field">
              <strong>Phone:</strong>
              <a href="tel:<?php echo esc_attr($tel_link); ?>"><?php echo esc_html($phone); ?> 📞</a>
            </div>
          <?php else: ?>
            <div class="field"><strong>Phone:</strong> </div>
          <?php endif; ?>

          <?php /*
          <?php if(get_field('restaurant_type')): ?>
            <div class="field"><strong>餐廳類型：</strong><?php the_field('restaurant_type'); ?></div>
          <?php else: ?>
            <div class="field"><strong>餐廳類型：</strong>暫無資料</div>
          <?php endif; ?>
          */ ?>
        </div>

        <div class="info-group wine-info">
          <?php 
          if ($is_charged): ?>
            <div class="field"><strong>Corkage Fee:</strong> <?php echo esc_html($charged_output); ?> 🥂</div>
          <?php else: ?>
            <div class="field"><strong>Corkage Fee:</strong> </div>
          <?php endif; ?>
          <?php 
          if (current_user_can('administrator')) {
            echo '<!-- DEBUG: is_charged = ' . var_export($is_charged, true) . ' -->';
            echo '<!-- DEBUG: corkage_fee_amount = ' . var_export($corkage_fee_amount, true) . ' -->';
            echo '<!-- DEBUG: corkage_fee_note = ' . var_export($corkage_fee_note, true) . ' -->';
          }
          
          if ($is_charged): ?>
            <div class="field"><strong>Corkage Details:</strong> <?php echo esc_html($fee_output); ?> 🪙</div>
          <?php else: ?>
            <div class="field"><strong>Corkage Details:</strong> </div>
          <?php endif; ?>
          <?php 
          if ($equipment_output): ?>
            <div class="field"><strong>Wine Equipment:</strong> <?php echo esc_html($equipment_output); ?></div>
          <?php else: ?>
            <div class="field"><strong>Wine Equipment:</strong> </div>
          <?php endif; ?>

          <?php /*
          <?php
            $byob_service_level = get_field('byob_service_level');
            $legacy_open_bottle_service = get_field('open_bottle_service');
            $open_bottle_service_other_note = get_field('open_bottle_service_other_note');

            $service_map = array(
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

            $legacy_map = array(
              '有' => 'full_service',
              '無' => 'no_service',
              '其他' => 'self_service',
              'yes' => 'full_service',
              'no' => 'no_service',
              'other' => 'self_service',
            );

            $service_slug = '';
            if (!empty($byob_service_level) && isset($service_map[$byob_service_level])) {
              $service_slug = $byob_service_level;
            } elseif (!empty($legacy_open_bottle_service) && isset($legacy_map[$legacy_open_bottle_service])) {
              $service_slug = $legacy_map[$legacy_open_bottle_service];
            }

            $service_label = '';
            $service_description = '';

            if ($service_slug && isset($service_map[$service_slug])) {
              $service_label = $service_map[$service_slug]['label'];
              $service_description = $service_map[$service_slug]['description'];
            } elseif (!empty($open_bottle_service_other_note)) {
              $service_label = $open_bottle_service_other_note;
            }
          ?>
            <div class="field">
              <strong>BYOB Service:</strong>
              <?php if ($service_label): ?>
                <?php echo esc_html($service_label); ?>
              <?php else: ?>
                暫無資料
              <?php endif; ?>
            </div>
          */ ?>
        </div>

        <div class="info-group link-info">
          <?php 
            // 原本的 Website/Social Links 顯示（已註解）
            /*
            if (get_field('social_links')) {
              echo '<div class="field"><strong>官方網站/社群連結：</strong><a href="' . esc_url(get_field('social_links')) . '" target="_blank" rel="noopener">' . esc_html(get_field('social_links')) . '</a></div>';
            } else {
              echo '<div class="field"><strong>官方網站/社群連結：</strong>暫無資料</div>';
            }
            */
            
            // Yelp 連結顯示（暫時註解，未來如需顯示可啟用）
            /*
            $yelp_link = get_field('yelp_link');
            if ($yelp_link) {
              echo '<div class="field">';
              echo '<strong>Yelp:</strong>';
              echo '<a href="' . esc_url($yelp_link) . '" target="_blank" rel="noopener">' . esc_html($yelp_link) . '</a>';
              echo '</div>';
            } else {
              echo '<div class="field"><strong>Yelp:</strong></div>';
            }
            */
          ?>
        </div>

        <div class="info-group other-info">
          <?php 
          if($notes): ?>
            <div class="field"><strong>Notes:</strong> <?php echo esc_html($truncated_notes); ?> 📝</div>
          <?php else: ?>
            <div class="field"><strong>Notes:</strong> </div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- 更多詳情按鈕 -->
      <div class="more-details-btn">
        <a href="<?php the_permalink(); ?>" class="details-link">
          More Details >>
        </a>
      </div>
    </div>
  <?php endwhile; else: ?>
    <p>No restaurants found.</p>
  <?php endif; ?>
</div>

<?php
wp_localize_script(
  'byob-nearby-restaurants',
  'BYOB_RESTAURANT_ARCHIVE',
  array(
    'restaurants' => array_values($byob_restaurant_js_data),
    'settings' => array(
      'maxNearby' => 5,
      'fallbackCenter' => array(
        'lat' => 39.9526,
        'lng' => -75.1652,
      ),
      'messages' => array(
        'locating' => __('Locating nearby restaurants...', 'byob'),
        'permissionDenied' => __('Location access denied. Showing the full restaurant list.', 'byob'),
        'unsupported' => __('Your browser does not support geolocation. Showing the full restaurant list.', 'byob'),
        'nearestListHeading' => __('Closest 5 Restaurants', 'byob'),
      ),
    ),
  )
);
?>

<?php
if ($restaurant_pagination_html) {
  echo '<div class="restaurant-pagination restaurant-pagination--bottom">' . $restaurant_pagination_html . '</div>';
}
?>

<?php get_footer(); ?>
