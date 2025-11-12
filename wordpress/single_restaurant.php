<?php get_header(); ?>

<style>
/* 單一餐廳頁面標題樣式 - 覆蓋通用h1樣式 */
.single-restaurant .restaurant-card h1 {
  padding-right: 200px !important;
  max-width: none !important;
  margin: 0 !important;
  padding-left: 0 !important;
  text-align: left !important;
  margin-bottom: 30px !important;
  font-size: 3.0rem !important;
  font-weight: 600 !important;
  letter-spacing: 2px !important;
  font-family: sans-serif !important;
  color: #333 !important;
}

/* 手機版響應式 */
@media (max-width: 768px) {
  .single-restaurant .restaurant-card h1 {
    padding-right: 100px !important;
    font-size: 2.5rem !important;
  }
}

/* 單一餐廳頁面行距優化 */
.single-restaurant .field {
  line-height: 1.6 !important;
  margin-bottom: 10px !important;
}

.single-restaurant .info-group {
  margin-bottom: 20px !important;
}

.single-restaurant .acf-fields {
  line-height: 1.5 !important;
}

/* 單一餐廳頁面標題行距 */
.single-restaurant .restaurant-card h1 {
  line-height: 1.3 !important;
  margin-bottom: 25px !important;
}

/* 底部操作按鈕間距 */
.single-page-actions {
  margin-top: 30px !important;
  padding-top: 20px !important;
  border-top: 2px solid #eee !important;
}

/* 驗證徽章樣式 */
.verification-badge-container {
  margin-bottom: 12px;
}

.verification-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  vertical-align: middle;
  white-space: nowrap;
  line-height: 1.4;
}

.verification-badge.badge-medium {
  font-size: 14px;
  padding: 6px 12px;
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

</style>

<div class="restaurant-card" style="max-width: 800px; margin: auto; padding: 2em; font-family: sans-serif; position: relative;">
  <!-- 加入圖片顯示 -->
  <?php 
  // 依序檢查 ACF restaurant_logo、自訂欄位 _restaurant_logo、舊的 restaurant_photo
  $acf_logo      = get_field('restaurant_logo', get_the_ID());
  $user_logo_id  = get_post_meta(get_the_ID(), '_restaurant_logo', true);
  $admin_logo    = get_field('restaurant_photo', get_the_ID());
  
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
  
  if ($logo_id): 
    // 強制讀取原始圖片，避免使用任何預處理的尺寸
    $logo_url = wp_get_attachment_url($logo_id);
    if ($logo_url): ?>
      <div class="restaurant-photo" style="position: absolute; top: 20px; right: 20px; width: 120px; height: 120px; overflow: hidden; border-radius: 4px; z-index: 2;">
        <img src="<?php echo esc_url($logo_url); ?>" 
             alt="<?php echo esc_attr(get_the_title()); ?> LOGO"
             class="restaurant-image"
             style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
      </div>
    <?php endif;
  endif; ?>
  
  <!-- 驗證徽章顯示（在餐廳名稱上方） -->
  <div class="verification-badge-container">
    <?php echo byob_display_verification_badge(get_the_ID(), 'medium'); ?>
  </div>
  
  <h1><?php the_title(); ?></h1>

  <div class="acf-fields">
    <!-- 基本資料 -->
    <div class="info-group basic-info">
      <?php
        $address = get_field('address');
        $map_link = get_field('map_link');

        if (!$map_link && $address) {
          // 清理地址：移除樓層資訊用於Google Maps搜尋
          $clean_address = preg_replace('/(\d+樓|\d+[Ff]|\d+樓層|地下\d+樓|[Bb]\d+)/u', '', $address);
          $clean_address = trim($clean_address);
          
          $map_link = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($clean_address);
        }
      ?>

      <?php if($address): ?>
        <div class="field">
          <strong>Address:</strong>
          <?php if($map_link): ?>
            <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener">
              <?php echo esc_html($address); ?> &#128205;
            </a>
          <?php else: ?>
            <?php echo esc_html($address); ?>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="field"><strong>Address:</strong> </div>
      <?php endif; ?>

      <?php 
      $phone = get_field('phone');
      if ($phone): 
        // 檢查是否包含連字號，如果沒有則加入
        $tel_link = $phone;
        if (strpos($phone, '-') === false) {
          // 根據長度自動加入連字號
          $clean_phone = preg_replace('/[^0-9]/', '', $phone);
          if (strlen($clean_phone) == 8) {
            // 市話格式：02-12345678
            $tel_link = substr($clean_phone, 0, 2) . '-' . substr($clean_phone, 2);
          } elseif (strlen($clean_phone) == 10 && substr($clean_phone, 0, 2) == '09') {
            // 手機格式：0932-123456
            $tel_link = substr($clean_phone, 0, 4) . '-' . substr($clean_phone, 4);
          }
        }
      ?>
        <div class="field">
          <strong>Phone:</strong>
          <a href="tel:<?php echo esc_attr($tel_link); ?>"><?php echo esc_html($phone); ?> &#128222;</a>
        </div>
      <?php else: ?>
        <div class="field"><strong>Phone:</strong> </div>
      <?php endif; ?>

      <?php 
      $type_labels = byob_get_restaurant_type_labels(get_the_ID());
      if (!empty($type_labels)): ?>
        <div class="field"><strong>Cuisine Type:</strong> <?php echo esc_html(implode(' / ', $type_labels)); ?></div>
      <?php else: ?>
        <div class="field"><strong>Cuisine Type:</strong> </div>
      <?php endif; ?>
    </div>

    <!-- 酒水相關 -->
    <div class="info-group wine-info">
      <?php 
      $is_charged = get_field('is_charged');
      // 除錯：顯示原始值和類型
      if (current_user_can('administrator')) {
        echo '<!-- DEBUG: is_charged = ' . var_export($is_charged, true) . ' -->';
      }
      
      if ($is_charged): 
        if (is_array($is_charged)) {
          $charged_output = implode(' / ', $is_charged);
        } else {
          $charged_output = $is_charged;
        }
      ?>
        <div class="field"><strong>Corkage Fee:</strong> <?php echo esc_html($charged_output); ?> 🥂</div>
      <?php else: ?>
        <div class="field"><strong>Corkage Fee:</strong> </div>
      <?php endif; ?>

      <?php 
      // 根據 is_charged 選項顯示對應的開瓶費資訊
      // 注意：這裡不重新獲取 $is_charged，使用上面已經獲取的值
      $corkage_fee_amount = get_field('corkage_fee_amount');
      $corkage_fee_note = get_field('corkage_fee_note');
      
      // 除錯：顯示開瓶費相關欄位的值
      if (current_user_can('administrator')) {
        echo '<!-- DEBUG: is_charged = ' . var_export($is_charged, true) . ' -->';
        echo '<!-- DEBUG: corkage_fee_amount = ' . var_export($corkage_fee_amount, true) . ' -->';
        echo '<!-- DEBUG: corkage_fee_note = ' . var_export($corkage_fee_note, true) . ' -->';
      }
      
      if ($is_charged): 
        // 處理陣列情況
        $charged_value = is_array($is_charged) ? $is_charged[0] : $is_charged;
        
        if (($charged_value === '酌收' || $charged_value === 'yes') && $corkage_fee_amount) {
          $fee_output = $corkage_fee_amount;
        } elseif (($charged_value === '其他' || $charged_value === 'other') && $corkage_fee_note) {
          $fee_output = $corkage_fee_note;
        } elseif ($charged_value === '酌收' || $charged_value === 'yes') {
          $fee_output = 'Charged (amount not set)';
        } elseif ($charged_value === '其他' || $charged_value === 'other') {
          $fee_output = 'Other (description not set)';
        } elseif ($charged_value === 'no') {
          $fee_output = 'No corkage fee';
        } else {
          $fee_output = $charged_value;
        }
      ?>
        <div class="field"><strong>Corkage Details:</strong> <?php echo esc_html($fee_output); ?> 🪙</div>
      <?php else: ?>
        <div class="field"><strong>Corkage Details:</strong> </div>
      <?php endif; ?>

      <?php 
      $equipment = get_field('equipment');
      $equipment_other_note = get_field('equipment_other_note');
      
      if ($equipment): 
        if (is_array($equipment)) {
          // 將「其他」替換為實際說明文字（類似餐廳類型的顯示方式）
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
          // 處理字串情況（防備）
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
      ?>
        <div class="field"><strong>Wine Equipment:</strong> <?php echo esc_html($equipment_output); ?></div>
      <?php else: ?>
        <div class="field"><strong>Wine Equipment:</strong> </div>
      <?php endif; ?>

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
          <?php endif; ?>
        </div>
    </div>

	<!-- 連結資訊（原本的 Website/Social Links 已註解，改為顯示 Yelp） -->
	<div class="info-group link-info">
		<?php 
		  // 原本的 Website/Social Links 顯示邏輯已註解，改為只顯示 Yelp
		  /*
		  $website = get_field('website');
		  $social_links = get_field('social_links');
		  $links = [];

		  if ($website) {
			$links[] = '<a href="'.esc_url($website).'" target="_blank" rel="noopener">Website</a>';
		  }
		  if ($social_links) {
			$links[] = '<a href="'.esc_url($social_links).'" target="_blank" rel="noopener">Social Media</a>';
		  }
		  */

		  // 新增 Yelp 連結顯示
		  $yelp_link = get_field('yelp_link');
		?>

		<?php if ($yelp_link): ?>
		  <div class="field">
			<strong>Yelp:</strong>
			<a href="<?php echo esc_url($yelp_link); ?>" target="_blank" rel="noopener"><?php echo esc_html($yelp_link); ?></a>
		  </div>
		<?php else: ?>
		  <div class="field"><strong>Yelp:</strong> </div>
		<?php endif; ?>

		<?php 
		  // 原本的 Website/Social Links 顯示（已註解）
		  /*
		  if (!empty($links)) {
			echo '<div class="field">';
			echo '<strong>Website/Social Links:</strong>';
			echo implode(' | ', $links);
			echo '</div>';
		  } else {
			echo '<div class="field"><strong>Website/Social Links:</strong></div>';
		  }
		  */
		?>
	</div>


    <!-- 其他資訊 -->
    <div class="info-group other-info">
      <?php if(get_field('notes')): ?>
        <div class="field"><strong>Notes:</strong> <?php the_field('notes'); ?> 📝</div>
      <?php else: ?>
        <div class="field"><strong>Notes:</strong> </div>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- 餐廳照片區塊 -->
  <?php
  // 獲取餐廳照片
  $photo_1 = get_field('restaurant_photo_1', get_the_ID());
  $photo_2 = get_field('restaurant_photo_2', get_the_ID());
  $photo_3 = get_field('restaurant_photo_3', get_the_ID());
  
  $photos = array();
  
  // 檢查照片是否有效並添加到陣列
  if ($photo_1 && !empty($photo_1['photo'])) {
    $photos[] = $photo_1;
  }
  if ($photo_2 && !empty($photo_2['photo'])) {
    $photos[] = $photo_2;
  }
  if ($photo_3 && !empty($photo_3['photo'])) {
    $photos[] = $photo_3;
  }
  
  // 如果有照片才顯示區塊
  if (!empty($photos)): ?>
    <div class="restaurant-photos-section">
      <h3 style="color: #333; margin: 0 0 20px 0;">
        🏠 Restaurant Photos
      </h3>
      
      <div class="restaurant-photos-grid">
        <?php foreach ($photos as $index => $photo): 
          $photo_id = null;
          $photo_url = '';
          $photo_description = '';
          
          // 正確獲取照片說明
          if (isset($photo['description']) && !empty($photo['description'])) {
            $photo_description = $photo['description'];
          }
          
          // 獲取照片ID
          if (isset($photo['photo'])) {
            if (is_numeric($photo['photo'])) {
              $photo_id = intval($photo['photo']);
            } elseif (is_array($photo['photo']) && isset($photo['photo']['ID'])) {
              $photo_id = intval($photo['photo']['ID']);
            }
          }
          
          // 獲取照片URL - 使用縮圖尺寸
          if ($photo_id) {
            $photo_url = wp_get_attachment_image_url($photo_id, 'thumbnail');
            if (!$photo_url) {
              $photo_url = wp_get_attachment_image_url($photo_id, 'medium');
            }
            if (!$photo_url) {
              $photo_url = wp_get_attachment_url($photo_id);
            }
          }
          
          if ($photo_url): ?>
            <div class="restaurant-photo-item" data-photo-index="<?php echo $index; ?>">
              <img src="<?php echo esc_url($photo_url); ?>" 
                   alt="<?php echo esc_attr($photo_description ?: 'Restaurant Photo'); ?>"
                   class="restaurant-photo-image"
                   loading="lazy"
                   title="<?php echo esc_attr($photo_description ?: 'Click to enlarge'); ?>"
                   data-description="<?php echo esc_attr($photo_description); ?>">
            </div>
          <?php endif;
        endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  
  <!-- 底部操作按鈕 -->
  <div class="single-page-actions">
    <div class="back-to-list">
             <?php
       // 簡化返回邏輯：總是返回餐廳列表頁
       $archive_url = get_post_type_archive_link('restaurant');
       ?>
       
       <a href="<?php echo esc_url($archive_url); ?>" class="back-link" id="back-to-list-link">
         << Back to Restaurant List
       </a>
    </div>
    
    <div class="contact-restaurant">
      <?php 
      $phone = get_field('phone');
      if ($phone): 
        // 檢查是否包含連字號，如果沒有則加入
        $tel_link = $phone;
        if (strpos($phone, '-') === false) {
          // 根據長度自動加入連字號
          $clean_phone = preg_replace('/[^0-9]/', '', $phone);
          if (strlen($clean_phone) == 8) {
            // 市話格式：02-12345678
            $tel_link = substr($clean_phone, 0, 2) . '-' . substr($clean_phone, 2);
          } elseif (strlen($clean_phone) == 10 && substr($clean_phone, 0, 2) == '09') {
            // 手機格式：0932-123456
            $tel_link = substr($clean_phone, 0, 4) . '-' . substr($clean_phone, 4);
          }
        }
      ?>
        <a href="tel:<?php echo esc_attr($tel_link); ?>" class="contact-link">
         Call Restaurant 📞
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>


<!-- 照片放大覆蓋層 -->
<div id="photo-overlay" class="photo-overlay">
  <div class="photo-overlay-content">
    <img id="overlay-image" src="" alt="Restaurant Photo">
    <div id="overlay-description" class="overlay-description"></div>
    <button class="close-overlay" aria-label="Close">×</button>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const photoItems = document.querySelectorAll('.restaurant-photo-item');
  const photoOverlay = document.getElementById('photo-overlay');
  const overlayImage = document.getElementById('overlay-image');
  const overlayDescription = document.getElementById('overlay-description');
  const closeOverlay = document.querySelector('.close-overlay');
  
  // 點擊照片開啟放大視窗
  photoItems.forEach(function(item) {
    item.addEventListener('click', function() {
      const photo = item.querySelector('.restaurant-photo-image');
      
      if (photo) {
        // 獲取原始大圖URL（替換縮圖URL）
        let originalUrl = photo.src;
        if (originalUrl.includes('-150x150') || originalUrl.includes('-300x300')) {
          // 如果是縮圖，嘗試獲取原始圖
          originalUrl = originalUrl.replace(/-150x150|-\d+x\d+/g, '');
        }
        
        overlayImage.src = originalUrl;
        overlayImage.alt = photo.alt;
        
        // 從data-description獲取說明文字
        const description = photo.getAttribute('data-description');
        if (description && description.trim()) {
          overlayDescription.textContent = description;
          overlayDescription.style.display = 'block';
        } else {
          overlayDescription.style.display = 'none';
        }
        
        photoOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // 防止背景滾動
      }
    });
  });
  
  // 關閉放大視窗
  function closePhotoOverlay() {
    photoOverlay.style.display = 'none';
    document.body.style.overflow = ''; // 恢復背景滾動
  }
  
  // 點擊關閉按鈕
  closeOverlay.addEventListener('click', closePhotoOverlay);
  
  // 點擊背景關閉
  photoOverlay.addEventListener('click', function(e) {
    if (e.target === photoOverlay) {
      closePhotoOverlay();
    }
  });
  
  // ESC鍵關閉
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && photoOverlay.style.display === 'flex') {
      closePhotoOverlay();
    }
  });
  
     // 觸控手勢支援（手機友善）
   let startY = 0;
   let currentY = 0;
   
   photoOverlay.addEventListener('touchstart', function(e) {
     startY = e.touches[0].clientY;
   });
   
   photoOverlay.addEventListener('touchmove', function(e) {
     currentY = e.touches[0].clientY;
   });
   
   photoOverlay.addEventListener('touchend', function() {
     const diff = startY - currentY;
     if (Math.abs(diff) > 100) { // 滑動超過100px
       closePhotoOverlay();
     }
   });
   
   // 篩選條件記憶功能
   const backToListLink = document.getElementById('back-to-list-link');
   if (backToListLink) {
     backToListLink.addEventListener('click', function(e) {
       // 檢查是否有儲存的篩選條件
       const savedFilters = sessionStorage.getItem('restaurant_filters');
       if (savedFilters) {
         try {
           const filters = JSON.parse(savedFilters);
           // 將篩選條件附加到URL參數
           const url = new URL(this.href);
           
           // 添加篩選參數
           Object.keys(filters).forEach(key => {
             if (filters[key] && filters[key] !== '') {
               url.searchParams.set(key, filters[key]);
             }
           });
           
           // 更新連結的href
           this.href = url.toString();
         } catch (error) {
           console.log('Filter parsing failed, using default return');
         }
       }
     });
   }
 });
</script>

<?php get_footer(); ?>