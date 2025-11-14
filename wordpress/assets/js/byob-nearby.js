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
  const hasHoverPointer =
    window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  const mapElement = document.getElementById('byob-restaurant-map');
  const statusElement = document.getElementById('byob-map-status');
  const nearbyWrapper = document.getElementById('byob-nearby-wrapper');
  const nearbyListElement = document.getElementById('byob-nearby-list');
  const retryButton = document.getElementById('byob-retry-location');
  const cardsContainer = document.querySelector('.restaurant-archive-list');

  const markerIndex = new Map();
  let mapInstance = null;
  let infoWindow = null;
  let activeMarker = null;
  let userMarker = null;
  let isLocating = false;

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
      const aVerification = Number(a.dataset.verification || 0);
      const bVerification = Number(b.dataset.verification || 0);
      if (aVerification !== bVerification) {
        return bVerification - aVerification;
      }

      const aCompleteness = Number(a.dataset.completeness || 0);
      const bCompleteness = Number(b.dataset.completeness || 0);
      if (aCompleteness !== bCompleteness) {
        return bCompleteness - aCompleteness;
      }

      const aPhoto = Number(a.dataset.hasPhoto || 0);
      const bPhoto = Number(b.dataset.hasPhoto || 0);
      if (aPhoto !== bPhoto) {
        return bPhoto - aPhoto;
      }

      const aDistance = parseFloat(a.dataset.distance || '999999');
      const bDistance = parseFloat(b.dataset.distance || '999999');
      if (aDistance !== bDistance) {
        return aDistance - bDistance;
      }

      const aFavorite = Number(a.dataset.favorite || 0);
      const bFavorite = Number(b.dataset.favorite || 0);
      if (aFavorite !== bFavorite) {
        return bFavorite - aFavorite;
      }

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

  const defaultMarkerIcon = {
    path: 'M12 2C8 2 5 5 5 9c0 3.58 2.69 6.55 6.2 6.95l-1.7 5.1h6.99l-1.7-5.1C18.31 15.55 21 12.58 21 9c0-4-3-7-7-7z',
    fillColor: '#8b2635',
    fillOpacity: 0.92,
    strokeColor: '#541622',
    strokeOpacity: 0.9,
    strokeWeight: 1.2,
    scale: 1.05,
    anchor: new google.maps.Point(12, 20),
  };

  const highlightMarkerIcon = Object.assign({}, defaultMarkerIcon, {
    fillColor: '#d47988',
    strokeColor: '#812030',
    scale: 1.2,
  });

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
      if (!hasHoverPointer) {
        return;
      }
      highlightRestaurant(restaurant.id, true);
      openInfoWindow(marker, restaurant);
    });

    marker.addListener('mouseout', () => {
      if (!hasHoverPointer) {
        return;
      }
      clearHighlights();
    });

    marker.addListener('click', () => {
      highlightRestaurant(restaurant.id, false);
      openInfoWindow(marker, restaurant);
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

  /**
   * Highlight helpers
   */
  function highlightRestaurant(restaurantId, fromHover) {
    const marker = markerIndex.get(String(restaurantId));
    if (marker) {
      if (activeMarker && activeMarker !== marker) {
        activeMarker.setIcon(defaultMarkerIcon);
      }
      marker.setIcon(highlightMarkerIcon);
      activeMarker = marker;
      if (!fromHover) {
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

  function clearHighlights() {
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
        <div style="margin-top:8px;"><a href="${escapeHtml(restaurant.permalink)}" target="_blank" rel="noopener">${escapeHtml('View Details >>')}</a></div>
      </div>
    `;

    infoWindow.setContent(content);
    infoWindow.open(mapInstance, marker);
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
          highlightRestaurant(restaurant.id, true);
        });
        listItem.addEventListener('mouseleave', () => {
          clearHighlights();
        });
      }

      listItem.addEventListener('click', (event) => {
        event.preventDefault();
        highlightRestaurant(restaurant.id, false);
        openInfoWindow(markerIndex.get(String(restaurant.id)), restaurant);
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
    updateStatus(messages.locating || 'Locating nearby restaurants...');

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
        updateStatus('Sorted by distance. Showing nearby restaurants.');
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
      clearHighlights();
      requestGeolocation();
    });
  }

  // Initial geolocation attempt
  requestGeolocation();

  // Ensure cards are sorted even if geolocation never succeeds
  sortRestaurantCards();

})(window, document);

