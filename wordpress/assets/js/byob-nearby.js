/**
 * BYOB Nearby Restaurants Module
 * Handles Google Maps rendering, geolocation, nearest list, and sorting logic.
 */
(function (window, document) {
  'use strict';

  const data = window.BYOB_RESTAURANT_ARCHIVE || {};
  const restaurants = Array.isArray(data.restaurants) ? data.restaurants.slice() : [];
  const settings = data.settings || {};
  const maxNearby = Number(settings.maxNearby || 5);
  const fallbackCenter = settings.fallbackCenter || { lat: 39.9526, lng: -75.1652 };
  const messages = settings.messages || {};
  const markerIconUrl = settings.markerIconUrl || '';
  const hasHoverPointer =
    window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  const mapElement = document.getElementById('byob-restaurant-map');
  const statusElement = document.getElementById('byob-map-status');
  const nearbyWrapper = document.getElementById('byob-nearby-wrapper');
  const nearbyListElement = document.getElementById('byob-nearby-list');
  const retryButton = document.getElementById('byob-retry-location');
  const cardsContainer = document.querySelector('.restaurant-archive-list');
  const detailPanel = document.getElementById('byob-map-detail');
  const detailContent = detailPanel ? detailPanel.querySelector('.byob-map-detail__content') : null;
  const detailCloseButton = detailPanel ? detailPanel.querySelector('.byob-map-detail__close') : null;
  const detailOverlay = document.getElementById('byob-map-overlay');
  const detailDefaultContent = detailContent ? detailContent.innerHTML : '';

  const markerIndex = new Map();
  let mapInstance = null;
  let infoWindow = null;
  let activeMarker = null;
  let userMarker = null;
  let isLocating = false;
  let lockedMarkerId = null;
  let infoWindowCloseTimeout = null;

  /**
   * Utility helpers
   */
  function escapeHtml(value) {
    if (value === null || value === undefined) {
      return '';
    }
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function updateStatus(message) {
    if (statusElement) {
      statusElement.textContent = message || '';
    }
  }

  function kilometersToMiles(km) {
    return km * 0.621371;
  }

  function formatDistance(miles) {
    if (Number.isNaN(miles)) {
      return '';
    }
    if (miles < 0.1) {
      return (miles * 5280).toFixed(0) + ' ft';
    }
    return miles.toFixed(miles < 10 ? 1 : 0) + ' mi';
  }

  function formatMultiline(value) {
    if (!value) {
      return '';
    }
    return escapeHtml(String(value)).replace(/\n/g, '<br>');
  }

  function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // earth radius in km
    const toRad = Math.PI / 180;
    const dLat = (lat2 - lat1) * toRad;
    const dLon = (lon2 - lon1) * toRad;
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * toRad) * Math.cos(lat2 * toRad) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // distance in km
  }

  /**
   * Sorting logic for the main restaurant list.
   */
  function sortRestaurantCards() {
    if (!cardsContainer) {
      return;
    }

    const cards = Array.from(cardsContainer.querySelectorAll('.restaurant-card'));
    if (!cards.length) {
      return;
    }

    cards.sort((a, b) => {
      // 1. 驗證狀態：verified (2) > community (1) > none (0)
      const aVerification = Number(a.dataset.verification || 0);
      const bVerification = Number(b.dataset.verification || 0);
      if (aVerification !== bVerification) {
        return bVerification - aVerification;
      }

      // 2. 資料完整度：分數高的優先
      const aCompleteness = Number(a.dataset.completeness || 0);
      const bCompleteness = Number(b.dataset.completeness || 0);
      if (aCompleteness !== bCompleteness) {
        return bCompleteness - aCompleteness;
      }

      // 3. 是否有餐廳照片：有照片 (1) > 無照片 (0)
      const aPhoto = Number(a.dataset.hasPhoto || 0);
      const bPhoto = Number(b.dataset.hasPhoto || 0);
      if (aPhoto !== bPhoto) {
        return bPhoto - aPhoto;
      }

      // 4. 距離：近的優先（只有在距離不是預設值 999999 時才比較）
      const aDistance = parseFloat(a.dataset.distance || '999999');
      const bDistance = parseFloat(b.dataset.distance || '999999');
      // 如果兩個距離都不是預設值，或只有一個是預設值，才比較距離
      if (aDistance !== 999999 || bDistance !== 999999) {
        if (aDistance !== bDistance) {
          return aDistance - bDistance;
        }
      }

      // 5. 收藏數：收藏多的優先（目前都是 0，未來功能）
      const aFavorite = Number(a.dataset.favorite || 0);
      const bFavorite = Number(b.dataset.favorite || 0);
      if (aFavorite !== bFavorite) {
        return bFavorite - aFavorite;
      }

      // 6. 最後依名稱字母順序
      const aTitleElement = a.querySelector('h2 a');
      const bTitleElement = b.querySelector('h2 a');
      const aTitle = aTitleElement ? aTitleElement.textContent.trim().toLowerCase() : '';
      const bTitle = bTitleElement ? bTitleElement.textContent.trim().toLowerCase() : '';
      return aTitle.localeCompare(bTitle);
    });

    const fragment = document.createDocumentFragment();
    cards.forEach((card) => fragment.appendChild(card));
    cardsContainer.appendChild(fragment);
  }

  sortRestaurantCards();

  /**
   * Map initialization
   */
  if (!mapElement || !window.google || !google.maps) {
    console.warn('[BYOB] Google Maps is not available. Skipping map and nearby functionality.');
    updateStatus(messages.unsupported || 'Google Maps failed to load. Showing the full restaurant list.');
    return;
  }

  mapInstance = new google.maps.Map(mapElement, {
    center: fallbackCenter,
    zoom: 13,
    streetViewControl: false,
    mapTypeControl: false,
    fullscreenControl: false,
  });

  infoWindow = new google.maps.InfoWindow();
  const bounds = new google.maps.LatLngBounds();

  // 使用自定義 SVG 圖標（如果提供），否則使用默認 SVG 路徑
  let defaultMarkerIcon;
  let highlightMarkerIcon;

  // 調試：確認圖標 URL
  if (markerIconUrl) {
    console.log('[BYOB] 使用自定義圖標:', markerIconUrl);
  } else {
    console.warn('[BYOB] 未找到圖標 URL，使用默認圖標');
  }

  if (markerIconUrl) {
    // 使用圖片 URL 方式（SVG 或 PNG）
    // SVG 尺寸是 512x512，我們縮放到合適的大小
    // 根據 SVG 結構，圖標底部約在 y=480 左右（不是 y=512）
    // 錨點設置在底部中心
    // 圖標尺寸：40x40 像素（可調整：24, 32, 40, 48, 64）
    const iconSize = new google.maps.Size(32, 32);
    const iconAnchor = new google.maps.Point(12, 23); // 底部中心，根據尺寸調整
    const iconOrigin = new google.maps.Point(0, 0);

    defaultMarkerIcon = {
      url: markerIconUrl,
      scaledSize: iconSize,
      size: iconSize,
      anchor: iconAnchor,
      origin: iconOrigin,
    };

    // 高亮圖標（稍微放大）
    const highlightSize = new google.maps.Size(40, 40);
    const highlightAnchor = new google.maps.Point(16, 30);
    highlightMarkerIcon = {
      url: markerIconUrl,
      scaledSize: highlightSize,
      size: highlightSize,
      anchor: highlightAnchor,
      origin: iconOrigin,
    };
  } else {
    // 回退到默認 SVG 路徑圖標
    defaultMarkerIcon = {
      path: 'M12 2C8 2 5 5 5 9c0 3.58 2.69 6.55 6.2 6.95l-1.7 5.1h6.99l-1.7-5.1C18.31 15.55 21 12.58 21 9c0-4-3-7-7-7z',
      fillColor: '#8b2635',
      fillOpacity: 0.92,
      strokeColor: '#541622',
      strokeOpacity: 0.9,
      strokeWeight: 1.2,
      scale: 1.05,
      anchor: new google.maps.Point(12, 20),
    };

    highlightMarkerIcon = Object.assign({}, defaultMarkerIcon, {
      fillColor: '#d47988',
      strokeColor: '#812030',
      scale: 1.2,
    });
  }

  const restaurantWithCoords = restaurants.filter(
    (restaurant) =>
      restaurant &&
      typeof restaurant.latitude === 'number' &&
      !Number.isNaN(restaurant.latitude) &&
      typeof restaurant.longitude === 'number' &&
      !Number.isNaN(restaurant.longitude)
  );

  restaurantWithCoords.forEach((restaurant) => {
    const position = { lat: restaurant.latitude, lng: restaurant.longitude };
    const marker = new google.maps.Marker({
      position,
      map: mapInstance,
      title: restaurant.title,
      icon: defaultMarkerIcon,
    });

    marker.__restaurantId = restaurant.id;
    markerIndex.set(String(restaurant.id), marker);
    bounds.extend(position);

    marker.addListener('mouseover', () => {
      if (!hasHoverPointer || lockedMarkerId) {
        return;
      }
      highlightRestaurant(restaurant.id, true);
      openInfoWindow(marker, restaurant);
    });

    marker.addListener('mouseout', () => {
      if (!hasHoverPointer) {
        return;
      }
      scheduleInfoWindowClose();
      clearHighlights(false);
    });

    marker.addListener('click', () => {
      highlightRestaurant(restaurant.id, false);
      openDetailPanel(restaurant);
    });
  });

  if (!bounds.isEmpty()) {
    mapInstance.fitBounds(bounds);
    const listener = google.maps.event.addListenerOnce(mapInstance, 'idle', () => {
      if (mapInstance.getZoom() > 15) {
        mapInstance.setZoom(15);
      }
      google.maps.event.removeListener(listener);
    });
  } else {
    mapInstance.setCenter(fallbackCenter);
  }

  if (mapInstance) {
    mapInstance.addListener('click', () => {
      closeDetailPanel();
    });
  }

  if (detailCloseButton) {
    detailCloseButton.addEventListener('click', () => {
      closeDetailPanel();
    });
  }

  if (detailOverlay) {
    detailOverlay.addEventListener('click', () => {
      closeDetailPanel();
    });
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeDetailPanel();
    }
  });

  /**
   * Highlight helpers
   */
  function highlightRestaurant(restaurantId, fromHover) {
    if (lockedMarkerId && fromHover) {
      return;
    }
    const marker = markerIndex.get(String(restaurantId));
    if (marker) {
      if (activeMarker && activeMarker !== marker) {
        activeMarker.setIcon(defaultMarkerIcon);
      }
      marker.setIcon(highlightMarkerIcon);
      activeMarker = marker;
      if (!fromHover) {
        lockedMarkerId = restaurantId;
        mapInstance.panTo(marker.getPosition());
      }
    }

    Array.from(document.querySelectorAll('.restaurant-card--highlight')).forEach((card) => {
      card.classList.remove('restaurant-card--highlight');
    });
    const card = document.getElementById('restaurant-card-' + restaurantId);
    if (card) {
      card.classList.add('restaurant-card--highlight');
    }

    if (nearbyListElement) {
      Array.from(nearbyListElement.querySelectorAll('.byob-nearby-item')).forEach((item) => {
        if (item.dataset.restaurantId === String(restaurantId)) {
          item.classList.add('is-active');
        } else {
          item.classList.remove('is-active');
        }
      });
    }
  }

  function clearHighlights(force = false) {
    if (!force && lockedMarkerId) {
      return;
    }
    if (activeMarker) {
      activeMarker.setIcon(defaultMarkerIcon);
      activeMarker = null;
    }
    if (infoWindow) {
      infoWindow.close();
    }
    Array.from(document.querySelectorAll('.restaurant-card--highlight')).forEach((card) => {
      card.classList.remove('restaurant-card--highlight');
    });
    if (nearbyListElement) {
      Array.from(nearbyListElement.querySelectorAll('.byob-nearby-item')).forEach((item) => {
        item.classList.remove('is-active');
      });
    }
  }

  function openInfoWindow(marker, restaurant) {
    if (!infoWindow) {
      return;
    }
    if (infoWindowCloseTimeout) {
      clearTimeout(infoWindowCloseTimeout);
      infoWindowCloseTimeout = null;
    }
    const distanceText = restaurant.distanceMiles
      ? `<div class="nearby-meta">${escapeHtml(formatDistance(restaurant.distanceMiles))}</div>`
      : '';

    const typeLabel = Array.isArray(restaurant.typeLabels) && restaurant.typeLabels.length
      ? `<div class="nearby-meta">${escapeHtml(restaurant.typeLabels.join(' / '))}</div>`
      : '';

    const content = `
      <div class="byob-infowindow">
        <strong>${escapeHtml(restaurant.title)}</strong>
        ${distanceText}
        ${typeLabel}
        <div class="byob-infowindow__hint">${escapeHtml('Click to open details')}</div>
      </div>
    `;

    infoWindow.setContent(content);
    infoWindow.open(mapInstance, marker);
  }

  function scheduleInfoWindowClose() {
    if (!infoWindow) {
      return;
    }
    if (infoWindowCloseTimeout) {
      clearTimeout(infoWindowCloseTimeout);
    }
    infoWindowCloseTimeout = window.setTimeout(() => {
      infoWindow.close();
      infoWindowCloseTimeout = null;
    }, 450);
  }

  function renderDetailContent(restaurant) {
    if (!detailContent) {
      return;
    }
    const typeLine =
      Array.isArray(restaurant.typeLabels) && restaurant.typeLabels.length
        ? escapeHtml(restaurant.typeLabels.join(' / '))
        : '';
    const distanceLine =
      typeof restaurant.distanceMiles === 'number'
        ? `${escapeHtml(formatDistance(restaurant.distanceMiles))} away`
        : '';
    const address = restaurant.address
      ? restaurant.mapLink
        ? `<a href="${escapeHtml(restaurant.mapLink)}" target="_blank" rel="noopener">${escapeHtml(
            restaurant.address
          )}</a>`
        : escapeHtml(restaurant.address)
      : '<span class="byob-detail__muted">N/A</span>';
    const phone = restaurant.phone
      ? `<a href="${restaurant.formattedPhone ? escapeHtml(restaurant.formattedPhone) : `tel:${escapeHtml(restaurant.phone)}`}">${escapeHtml(
          restaurant.phone
        )}</a>`
      : '<span class="byob-detail__muted">N/A</span>';
    const corkage = restaurant.corkageFee
      ? escapeHtml(restaurant.corkageFee)
      : '<span class="byob-detail__muted">No corkage info yet</span>';
    const corkageDetails = restaurant.corkageDetails
      ? `<div class="byob-detail__subtext">${formatMultiline(restaurant.corkageDetails)}</div>`
      : '';
    const equipment = restaurant.equipment
      ? formatMultiline(restaurant.equipment)
      : '<span class="byob-detail__muted">Not specified</span>';
    const notes = restaurant.notes
      ? formatMultiline(restaurant.notes)
      : '<span class="byob-detail__muted">—</span>';

    detailContent.innerHTML = `
      <div class="byob-detail__header">
        <div>
          ${typeLine ? `<div class="byob-detail__eyebrow">${typeLine}</div>` : ''}
          <h3>${escapeHtml(restaurant.title || '')}</h3>
          ${distanceLine ? `<div class="byob-detail__meta">${distanceLine}</div>` : ''}
        </div>
        <a class="byob-detail__cta" href="${escapeHtml(restaurant.permalink || '#')}" target="_blank" rel="noopener">
          View Details >>
        </a>
      </div>
      <div class="byob-detail__section">
        <div class="byob-detail__row"><strong>Address:</strong> ${address}</div>
        <div class="byob-detail__row"><strong>Phone:</strong> ${phone}</div>
        <div class="byob-detail__row"><strong>Corkage Fee:</strong> ${corkage}</div>
        ${corkageDetails ? `<div class="byob-detail__row">${corkageDetails}</div>` : ''}
        <div class="byob-detail__row">
          <strong>Wine Equipment:</strong> ${equipment}
        </div>
        <div class="byob-detail__row">
          <strong>Notes:</strong> ${notes}
        </div>
      </div>
    `;
  }

  function toggleDetailOverlay(active) {
    if (!detailOverlay) {
      return;
    }
    if (active) {
      detailOverlay.hidden = false;
      detailOverlay.classList.add('is-active');
    } else {
      detailOverlay.classList.remove('is-active');
      detailOverlay.hidden = true;
    }
  }

  function openDetailPanel(restaurant) {
    if (!detailPanel || !detailContent) {
      return;
    }
    renderDetailContent(restaurant);
    detailPanel.classList.add('is-active');
    detailPanel.setAttribute('aria-expanded', 'true');
    toggleDetailOverlay(true);
  }

  function closeDetailPanel() {
    if (!detailPanel || !detailPanel.classList.contains('is-active')) {
      return;
    }
    detailPanel.classList.remove('is-active');
    detailPanel.setAttribute('aria-expanded', 'false');
    toggleDetailOverlay(false);
    lockedMarkerId = null;
    if (detailContent) {
      detailContent.innerHTML = detailDefaultContent;
    }
    clearHighlights(true);
  }

  /**
   * Nearby list rendering
   */
  function renderNearbyList() {
    if (!nearbyWrapper || !nearbyListElement) {
      return;
    }

    const withDistance = restaurantWithCoords
      .filter((restaurant) => typeof restaurant.distanceMiles === 'number')
      .sort((a, b) => a.distanceMiles - b.distanceMiles)
      .slice(0, maxNearby);

    if (!withDistance.length) {
      nearbyWrapper.hidden = true;
      return;
    }

    nearbyWrapper.hidden = false;
    nearbyListElement.innerHTML = '';

    const fragment = document.createDocumentFragment();

    withDistance.forEach((restaurant) => {
      const listItem = document.createElement('li');
      listItem.className = 'byob-nearby-item';
      listItem.dataset.restaurantId = String(restaurant.id);

      const infoWrapper = document.createElement('div');
      infoWrapper.className = 'nearby-info';

      const nameEl = document.createElement('div');
      nameEl.className = 'nearby-name';
      nameEl.textContent = restaurant.title || '';

      const metaPieces = [];
      if (typeof restaurant.distanceMiles === 'number') {
        metaPieces.push(formatDistance(restaurant.distanceMiles));
      }
      if (Array.isArray(restaurant.typeLabels) && restaurant.typeLabels.length) {
        metaPieces.push(restaurant.typeLabels.join(' / '));
      }

      const metaEl = document.createElement('div');
      metaEl.className = 'nearby-meta';
      metaEl.textContent = metaPieces.join(' · ');

      infoWrapper.appendChild(nameEl);
      if (metaPieces.length) {
        infoWrapper.appendChild(metaEl);
      }

      const linkEl = document.createElement('a');
      linkEl.className = 'nearby-link';
      linkEl.href = restaurant.permalink || '#';
      linkEl.textContent = 'View Details >>';
      linkEl.target = '_blank';
      linkEl.rel = 'noopener';

      linkEl.addEventListener('click', (event) => {
        event.stopPropagation();
      });

      listItem.appendChild(infoWrapper);
      listItem.appendChild(linkEl);

      if (hasHoverPointer) {
        listItem.addEventListener('mouseenter', () => {
          if (lockedMarkerId) {
            return;
          }
          highlightRestaurant(restaurant.id, true);
        });
        listItem.addEventListener('mouseleave', () => {
          if (lockedMarkerId) {
            return;
          }
          clearHighlights(false);
        });
      }

      listItem.addEventListener('click', (event) => {
        event.preventDefault();
        highlightRestaurant(restaurant.id, false);
        openDetailPanel(restaurant);
      });

      fragment.appendChild(listItem);
    });

    nearbyListElement.appendChild(fragment);
  }

  /**
   * Distance + geolocation handling
   */
  function applyDistances(userPosition) {
    restaurantWithCoords.forEach((restaurant) => {
      const distanceKm = haversineDistance(
        userPosition.lat,
        userPosition.lng,
        restaurant.latitude,
        restaurant.longitude
      );
      restaurant.distanceKm = distanceKm;
      restaurant.distanceMiles = kilometersToMiles(distanceKm);

      const card = document.getElementById('restaurant-card-' + restaurant.id);
      if (card) {
        card.dataset.distance = restaurant.distanceMiles.toFixed(4);
      }
    });
  }

  function requestGeolocation() {
    if (isLocating) {
      return;
    }
    isLocating = true;
  updateStatus(messages.locating || '');

    if (!navigator.geolocation) {
      handleGeolocationFailure('unsupported');
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        isLocating = false;
        const userLatLng = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        if (!userMarker) {
          userMarker = new google.maps.Marker({
            position: userLatLng,
            map: mapInstance,
            icon: {
              path: google.maps.SymbolPath.CIRCLE,
              scale: 7,
              fillColor: '#1a73e8',
              fillOpacity: 1,
              strokeColor: '#ffffff',
              strokeWeight: 2,
            },
            title: 'Your Location',
          });
        } else {
          userMarker.setPosition(userLatLng);
        }

        mapInstance.panTo(userLatLng);
        mapInstance.setZoom(13);

        applyDistances(userLatLng);
        renderNearbyList();
        sortRestaurantCards();
        updateStatus('');
      },
      (error) => {
        console.warn('[BYOB] Geolocation failed', error);
        handleGeolocationFailure('permissionDenied');
      },
      {
        enableHighAccuracy: false,
        timeout: 10000,
        maximumAge: 300000,
      }
    );
  }

  function handleGeolocationFailure(reason) {
    isLocating = false;

    if (nearbyWrapper) {
      nearbyWrapper.hidden = true;
    }

    switch (reason) {
      case 'permissionDenied':
        updateStatus(messages.permissionDenied || 'Location access denied. Showing the full restaurant list.');
        break;
      case 'unsupported':
      default:
        updateStatus(messages.unsupported || 'Your browser does not support geolocation. Showing the full restaurant list.');
        break;
    }

    restaurantWithCoords.forEach((restaurant) => {
      const card = document.getElementById('restaurant-card-' + restaurant.id);
      if (card) {
        card.dataset.distance = '999999';
      }
    });
    sortRestaurantCards();
  }

  if (retryButton) {
    retryButton.addEventListener('click', () => {
      closeDetailPanel();
      clearHighlights(true);
      requestGeolocation();
    });
  }

  // Initial geolocation attempt
  requestGeolocation();

  // Ensure cards are sorted even if geolocation never succeeds
  sortRestaurantCards();

})(window, document);

