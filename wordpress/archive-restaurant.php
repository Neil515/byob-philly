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
  text-align: center;
  margin: 40px 0;
  padding: 20px 0;
}

.restaurant-pagination .page-numbers {
  display: inline-block;
  padding: 8px 12px;
  margin: 0 4px;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  text-decoration: none;
  color: #495057;
  transition: all 0.3s ease;
}

.restaurant-pagination .page-numbers:hover {
  background: #8b2635;
  color: white;
  border-color: #8b2635;
}

.restaurant-pagination .page-numbers.current {
  background: #8b2635;
  color: white;
  border-color: #8b2635;
}

.restaurant-pagination .page-numbers.prev,
.restaurant-pagination .page-numbers.next {
  font-weight: bold;
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

/* 餐廳列表頁面標題和內容右邊距調整 */
.restaurant-card h2,
.restaurant-card .field {
  padding-right: 140px !important;
}

.restaurant-title-line {
  padding-right: 140px !important;
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
</style>
<div class="page-header">
  <h1 class="page-title">所有餐廳列表</h1>
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
      console.log('篩選條件已儲存:', filters);
    } catch (error) {
      console.error('儲存篩選條件失敗:', error);
    }
  }
  
  // 從 sessionStorage 恢復篩選條件
  function restoreFilters() {
    try {
      const savedFilters = sessionStorage.getItem(FILTER_STORAGE_KEY);
      if (savedFilters) {
        const filters = JSON.parse(savedFilters);
        console.log('恢復篩選條件:', filters);
        return filters;
      }
    } catch (error) {
      console.error('恢復篩選條件失敗:', error);
    }
    return null;
  }
  
  // 清除篩選條件
  function clearFilters() {
    try {
      sessionStorage.removeItem(FILTER_STORAGE_KEY);
      console.log('篩選條件已清除');
    } catch (error) {
      console.error('清除篩選條件失敗:', error);
    }
  }
  
  // 頁面載入時嘗試恢復篩選條件
  const savedFilters = restoreFilters();
  if (savedFilters) {
    console.log('發現儲存的篩選條件，等待外掛整合...');
    // 這裡未來會與您購買的篩選外掛整合
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
  
  // 將函數暴露到全域，供未來的外掛整合使用
  window.RestaurantFilterMemory = {
    saveFilters: saveFilters,
    restoreFilters: restoreFilters,
    clearFilters: clearFilters
  };
});
</script>

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
      
      <h2 class="restaurant-title-line">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <?php
          $types = get_field('restaurant_type');
          if ($types):
            // 處理餐廳類型，將「其他」替換為「其他: [說明文字]」
            $type_output = '';
            if (is_array($types)) {
              $processed_types = array();
              foreach ($types as $type) {
                if ($type === '其他') {
                  // 獲取其他類型說明
                  $other_note = get_field('restaurant_type_other_note');
                  if (!empty($other_note)) {
                    $processed_types[] = '其他: ' . $other_note;
                  } else {
                    $processed_types[] = $type;
                  }
                } else {
                  $processed_types[] = $type;
                }
              }
              $type_output = implode(' / ', $processed_types);
            } else {
              // 如果是字串，檢查是否包含「其他」
              if (strpos($types, '其他') !== false) {
                $other_note = get_field('restaurant_type_other_note');
                if (!empty($other_note)) {
                  $type_output = str_replace('其他', '其他: ' . $other_note, $types);
                } else {
                  $type_output = $types;
                }
              } else {
                $type_output = $types;
              }
            }
            echo '<span class="restaurant-type">（' . esc_html($type_output) . '）</span>';
          endif;
        ?>
      </h2>

      <div class="acf-fields">
        <div class="info-group basic-info">

		<?php
		  $address = get_field('address');
		  $map_link = get_field('map_link');

		  // fallback：若沒填 map_link，就用地址產生 Google Maps 搜尋網址
		  if (!$map_link && $address) {
			$map_link = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
		  }
		?>

		<?php if($address): ?>
		  <div class="field">
			<strong>地址：</strong>
			<?php if($map_link): ?>
			  <a href="<?php echo esc_url($map_link); ?>" target="_blank" rel="noopener">
				<?php echo esc_html($address); ?> 📍
			  </a>
			<?php else: ?>
			  <?php echo esc_html($address); ?>
			<?php endif; ?>
		  </div>
		<?php else: ?>
		  <div class="field"><strong>地址：</strong></div>
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
              <strong>餐廳聯絡電話：</strong>
              <a href="tel:<?php echo esc_attr($tel_link); ?>"><?php echo esc_html($phone); ?> 📞</a>
            </div>
          <?php else: ?>
            <div class="field"><strong>餐廳聯絡電話：</strong></div>
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
            <div class="field"><strong>是否收開瓶費：</strong><?php echo esc_html($charged_output); ?> 🥂</div>
          <?php else: ?>
            <div class="field"><strong>是否收開瓶費：</strong></div>
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
              $fee_output = '酌收（金額未設定）';
            } elseif ($charged_value === '其他' || $charged_value === 'other') {
              $fee_output = '其他（說明未設定）';
            } elseif ($charged_value === 'no') {
              $fee_output = '不收開瓶費';
            } else {
              $fee_output = $charged_value;
            }
          ?>
            <div class="field"><strong>開瓶費說明：</strong><?php echo esc_html($fee_output); ?> 🪙</div>
          <?php else: ?>
            <div class="field"><strong>開瓶費說明：</strong></div>
          <?php endif; ?>
          <?php if(get_field('equipment')): ?>
            <div class="field"><strong>提供酒器設備：</strong><?php the_field('equipment'); ?></div>
          <?php else: ?>
            <div class="field"><strong>提供酒器設備：</strong></div>
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
          <?php /*
          <?php if(get_field('social_links')): ?>
            <div class="field"><strong>官方網站/社群連結：</strong><a href="<?php the_field('social_links'); ?>" target="_blank" rel="noopener"><?php the_field('social_links'); ?></a></div>
          <?php else: ?>
            <div class="field"><strong>官方網站/社群連結：</strong>暫無資料</div>
          <?php endif; ?>
          */ ?>
        </div>

        <div class="info-group other-info">
          <?php if(get_field('notes')): ?>
            <div class="field"><strong>備註說明：</strong><?php the_field('notes'); ?> 📝</div>
          <?php else: ?>
            <div class="field"><strong>備註說明：</strong></div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- 更多詳情按鈕 -->
      <div class="more-details-btn">
        <a href="<?php the_permalink(); ?>" class="details-link">
          更多詳情 >>
        </a>
      </div>
    </div>
  <?php endwhile; else: ?>
    <p>目前沒有餐廳資料。</p>
  <?php endif; ?>
</div>

<!-- 分頁導航 -->
<?php
// WordPress 預設分頁導航
the_posts_pagination(array(
    'prev_text' => '← 上一頁',
    'next_text' => '下一頁 →',
    'mid_size' => 2,
    'before_page_number' => '<span class="meta-nav screen-reader-text">第 </span>',
    'after_page_number' => '<span class="meta-nav screen-reader-text"> 頁</span>',
));
?>

<?php get_footer(); ?>
