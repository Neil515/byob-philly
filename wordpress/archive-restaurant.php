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
  .restaurant-title-line,
  .verification-badge-container,
  .acf-fields,
  .info-group,
  .more-details-btn {
    padding-right: 0 !important;
    min-width: 0 !important;
  }

  .restaurant-title-line {
    justify-content: center;
    text-align: center;
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
</style>
<div class="page-header">
  <h1 class="page-title">All BYOB Restaurants</h1>
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
    <div class="restaurant-card">
      <!-- 加入圖片顯示 -->
      <?php 
      // 獲取兩個 LOGO 的上傳時間，選擇最新的
      $admin_logo = get_field('restaurant_photo', get_the_ID());
      $user_logo_id = get_post_meta(get_the_ID(), '_restaurant_logo', true);
      
      $logo_id = null;
      
      if ($admin_logo && is_array($admin_logo)) {
        $admin_logo_id = $admin_logo['ID'];
        $admin_time = get_post_modified_time('U', false, $admin_logo_id);
        
        if ($user_logo_id) {
          $user_time = get_post_modified_time('U', false, $user_logo_id);
          // 選擇最新的
          $logo_id = ($admin_time > $user_time) ? $admin_logo_id : $user_logo_id;
        } else {
          $logo_id = $admin_logo_id;
        }
      } else {
        $logo_id = $user_logo_id;
      }
      
      if ($logo_id): 
        // 強制讀取原始圖片，避免使用任何預處理的尺寸
        $logo_url = wp_get_attachment_url($logo_id);
        if ($logo_url): ?>
          <div class="restaurant-photo">
            <img data-src="<?php echo esc_url($logo_url); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?> LOGO"
                 class="restaurant-image loading-placeholder"
                 style="object-fit: contain; max-width: 100%; height: auto;">
          </div>
        <?php endif;
      endif; ?>
      
      <!-- 驗證徽章顯示（在餐廳名稱上方） -->
      <div class="verification-badge-container">
        <?php echo byob_display_verification_badge(get_the_ID(), 'small'); ?>
      </div>
      
      <h2 class="restaurant-title-line">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <?php
          $type_labels = byob_get_restaurant_type_labels(get_the_ID());
          if (!empty($type_labels)) {
            echo '<span class="restaurant-type">(' . esc_html(implode(' / ', $type_labels)) . ')</span>';
          }
        ?>
      </h2>

      <div class="acf-fields">
        <div class="info-group basic-info">

		<?php
		  $address = get_field('address');
		  $map_link = get_field('map_link');

			  // Fallback: if no map_link, generate Google Maps search URL from address
		  if (!$map_link && $address) {
				// Clean address: remove floor info for Google Maps search
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
				<?php echo esc_html($address); ?> 📍
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
            // Check if contains hyphen, add if not present
            $tel_link = $phone;
            if (strpos($phone, '-') === false) {
              // Automatically add hyphen based on length
              $clean_phone = preg_replace('/[^0-9]/', '', $phone);
              if (strlen($clean_phone) == 8) {
                // Landline format: 02-12345678
                $tel_link = substr($clean_phone, 0, 2) . '-' . substr($clean_phone, 2);
              } elseif (strlen($clean_phone) == 10 && substr($clean_phone, 0, 2) == '09') {
                // Mobile format: 0932-123456
                $tel_link = substr($clean_phone, 0, 4) . '-' . substr($clean_phone, 4);
              }
            }
          ?>
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
          // 先獲取 is_charged 值
          $is_charged = get_field('is_charged');
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
          // Display corresponding corkage fee info based on is_charged option
          // Note: Do not re-fetch $is_charged, use the value already obtained above
          $corkage_fee_amount = get_field('corkage_fee_amount');
          $corkage_fee_note = get_field('corkage_fee_note');
          
          // Debug: display corkage fee related field values
          if (current_user_can('administrator')) {
            echo '<!-- DEBUG: is_charged = ' . var_export($is_charged, true) . ' -->';
            echo '<!-- DEBUG: corkage_fee_amount = ' . var_export($corkage_fee_amount, true) . ' -->';
            echo '<!-- DEBUG: corkage_fee_note = ' . var_export($corkage_fee_note, true) . ' -->';
          }
          
          if ($is_charged): 
            // Handle array case
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
              // Replace 'Other' with actual description text (similar to restaurant type display)
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
              // Handle string case (precaution)
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

          <?php /*
          <?php if(get_field('open_bottle_service')): ?>
            <div class="field"><strong>是否提供開酒服務？：</strong><?php the_field('open_bottle_service'); ?></div>
          <?php else: ?>
            <div class="field"><strong>是否提供開酒服務？：</strong>暫無資料</div>
          <?php endif; ?>
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
          $notes = get_field('notes');
          if($notes): 
            // Limit notes to first 100 characters in list view
            $truncated_notes = mb_strlen($notes) > 100 ? mb_substr($notes, 0, 100) . '...' : $notes;
          ?>
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
if ($restaurant_pagination_html) {
  echo '<div class="restaurant-pagination restaurant-pagination--bottom">' . $restaurant_pagination_html . '</div>';
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
  const overflowElements = [];

  document.querySelectorAll('body *').forEach(el => {
    const rect = el.getBoundingClientRect();
    if (rect.right > viewportWidth + 0.5) {
      el.style.outline = '2px solid #ff4d4f';
      el.style.outlineOffset = '2px';
      overflowElements.push({
        selector: el.tagName.toLowerCase() + (el.className ? '.' + el.className.toString().trim().replace(/\s+/g, '.') : ''),
        right: Math.round(rect.right),
        viewportWidth: Math.round(viewportWidth)
      });
    }
  });

  if (overflowElements.length) {
    const panel = document.createElement('div');
    panel.style.position = 'fixed';
    panel.style.bottom = '16px';
    panel.style.left = '16px';
    panel.style.right = '16px';
    panel.style.maxHeight = '40vh';
    panel.style.overflowY = 'auto';
    panel.style.background = 'rgba(255, 77, 79, 0.9)';
    panel.style.color = '#fff';
    panel.style.fontSize = '14px';
    panel.style.lineHeight = '1.4';
    panel.style.zIndex = '99999';
    panel.style.padding = '12px 14px';
    panel.style.borderRadius = '8px';
    panel.style.boxShadow = '0 6px 24px rgba(0,0,0,0.25)';

    const title = document.createElement('strong');
    title.textContent = 'Overflow elements detected:';
    panel.appendChild(title);

    const list = document.createElement('ol');
    list.style.margin = '8px 0 0';
    list.style.paddingLeft = '20px';

    overflowElements.forEach(item => {
      const li = document.createElement('li');
      li.textContent = `${item.selector} ⇒ right: ${item.right}px (viewport: ${item.viewportWidth}px)`;
      list.appendChild(li);
    });

    panel.appendChild(list);
    document.body.appendChild(panel);
  } else {
    console.info('%cNo overflow elements detected.', 'color: #52c41a; font-weight: bold;');
  }
});
</script>

<?php get_footer(); ?>
