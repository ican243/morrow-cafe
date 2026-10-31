/* MORROW COFFEE 관리자 화면 스크립트 */
(function () {
  'use strict';

  // 모바일 메뉴
  var opener = document.getElementById('sideOpen');
  if (opener) {
    opener.addEventListener('click', function () { document.body.classList.toggle('menu-open'); });
    document.addEventListener('click', function (e) {
      if (document.body.classList.contains('menu-open') && !e.target.closest('.side') && e.target !== opener) {
        document.body.classList.remove('menu-open');
      }
    });
  }

  // 삭제 등 되돌릴 수 없는 작업 전 확인
  document.addEventListener('submit', function (e) {
    var msg = e.target.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) e.preventDefault();
  });

  // 영업시간: 휴무 체크하면 시간 칸 흐리게
  document.querySelectorAll('.hours-edit input[data-closed]').forEach(function (cb) {
    var sync = function () { cb.closest('tr').classList.toggle('is-closed', cb.checked); };
    cb.addEventListener('change', sync);
    sync();
  });

  // ---------- 사진 선택 → 검사 → 크롭 → 미리보기 ----------
  var MAX_MB = 10;
  var TYPES = ['image/jpeg', 'image/png', 'image/webp'];
  var dialog = document.getElementById('cropDialog');
  var cropper = null;
  var active = null; // { input, preview, ratio }

  function setPreview(preview, url) {
    preview.innerHTML = '';
    var img = document.createElement('img');
    img.src = url;
    img.alt = '';
    preview.appendChild(img);
    // 아직 저장 전이라는 표시 (갤러리 타일에서 사용)
    preview.closest('.img-field').classList.add('is-changed');
  }

  function replaceFile(input, file) {
    try {
      var dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;
      return true;
    } catch (err) {
      return false; // 아주 오래된 브라우저: 원본 그대로 올라가고 서버가 크기만 줄인다
    }
  }

  function closeCrop() {
    if (cropper) { cropper.destroy(); cropper = null; }
    if (dialog.open) dialog.close();
  }

  document.querySelectorAll('[data-image-input]').forEach(function (input) {
    var wrap = input.closest('.img-field');
    var preview = wrap.querySelector('.img-preview');
    var msg = wrap.querySelector('[data-image-msg]');

    input.addEventListener('change', function () {
      msg.textContent = '';
      var file = input.files[0];
      if (!file) return;

      if (TYPES.indexOf(file.type) === -1) {
        msg.textContent = 'JPG, PNG, WEBP 사진만 올릴 수 있습니다.';
        input.value = '';
        return;
      }
      if (file.size > MAX_MB * 1024 * 1024) {
        msg.textContent = '사진 용량이 ' + (file.size / 1048576).toFixed(1) + 'MB 입니다. ' + MAX_MB + 'MB 이하로 올려 주세요.';
        input.value = '';
        return;
      }

      var url = URL.createObjectURL(file);
      if (!dialog || typeof window.Cropper === 'undefined' || typeof dialog.showModal !== 'function') {
        setPreview(preview, url);
        return;
      }

      active = { input: input, preview: preview, ratio: parseFloat(input.getAttribute('data-ratio')) || NaN, url: url, name: file.name };
      var stageImg = dialog.querySelector('.crop-stage img');
      stageImg.src = url;
      dialog.showModal();
      cropper = new Cropper(stageImg, {
        aspectRatio: active.ratio,
        viewMode: 1,
        autoCropArea: 1,
        background: false,
        checkOrientation: true
      });
    });
  });

  if (dialog) {
    dialog.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-crop]');
      if (!btn || !active) return;
      var act = btn.getAttribute('data-crop');

      if (act === 'rotate') { cropper.rotate(90); return; }

      if (act === 'cancel') {
        active.input.value = '';
        closeCrop();
        return;
      }

      if (act === 'original') {
        setPreview(active.preview, active.url);
        closeCrop();
        return;
      }

      if (act === 'apply') {
        var canvas = cropper.getCroppedCanvas({ maxWidth: 2400, maxHeight: 2400, fillColor: '#ffffff', imageSmoothingQuality: 'high' });
        var target = active;
        btn.disabled = true;
        canvas.toBlob(function (blob) {
          btn.disabled = false;
          var name = target.name.replace(/\.[^.]+$/, '') + '.jpg';
          var file = new File([blob], name, { type: 'image/jpeg' });
          if (replaceFile(target.input, file)) {
            setPreview(target.preview, URL.createObjectURL(blob));
          } else {
            setPreview(target.preview, target.url);
          }
          closeCrop();
        }, 'image/jpeg', 0.92);
      }
    });
    dialog.addEventListener('cancel', function () { if (active) active.input.value = ''; closeCrop(); });
  }
})();

// 순서 바꾸기: ⋮⋮ 손잡이를 끌어서 놓으면 바로 저장, 실패하면 원래 순서로 되돌림
(function () {
  if (typeof window.Sortable === 'undefined') return;
  var token = document.querySelector('meta[name="X-CSRF-TOKEN"]');

  function toast(text, ok) {
    var el = document.createElement('div');
    el.className = 'toast' + (ok ? '' : ' toast--err');
    el.setAttribute('role', ok ? 'status' : 'alert');
    el.textContent = text;
    document.body.appendChild(el);
    setTimeout(function () { el.remove(); }, ok ? 2000 : 4000);
  }

  document.querySelectorAll('[data-sortable]').forEach(function (list) {
    var items = function () { return Array.prototype.filter.call(list.children, function (el) { return el.hasAttribute('data-id'); }); };
    var ids = function () { return items().map(function (el) { return el.getAttribute('data-id'); }); };
    var before = [];

    function restore() {
      before.forEach(function (id) {
        var el = list.querySelector(':scope > [data-id="' + id + '"]');
        if (el) list.appendChild(el);
      });
    }

    Sortable.create(list, {
      handle: '.drag-handle',
      draggable: '[data-id]',
      animation: 150,
      ghostClass: 'is-ghost',
      chosenClass: 'is-chosen',
      onStart: function () { before = ids(); },
      onEnd: function () {
        var now = ids();
        if (now.join() === before.join()) return;
        list.classList.add('is-saving');

        fetch(list.getAttribute('data-sortable'), {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
          },
          body: JSON.stringify({ ids: now })
        })
          .then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (data) {
              if (!res.ok || !data.ok) throw new Error(data.message || '');
              return data;
            });
          })
          .then(function (data) { toast(data.message, true); })
          .catch(function (err) {
            restore();
            toast(err.message || '순서를 저장하지 못했습니다. 새로고침 후 다시 시도해 주세요.', false);
          })
          .then(function () { list.classList.remove('is-saving'); });
      }
    });
  });
})();

// 매장 정보: 주소 검색 → 위도·경도 자동 입력 → 지도에서 확인 (핀을 끌어 입구 위치로 조정)
(function () {
  var box = document.querySelector('[data-map-check]');
  if (!box) return;
  var key = box.getAttribute('data-kakao-key');
  var addr = document.getElementById('address');
  var detail = document.getElementById('address_detail');
  var lat = document.getElementById('map_lat');
  var lng = document.getElementById('map_lng');
  var mapEl = box.querySelector('[data-map-box]');
  var msg = box.querySelector('[data-map-msg]');
  var map = null, marker = null, sdk = null;

  function say(text, kind) {
    msg.textContent = text;
    msg.className = 'muted' + (kind ? ' is-' + kind : '');
  }

  function loadScript(src) {
    return new Promise(function (resolve, reject) {
      var s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }

  function kakaoReady() {
    if (!key) return Promise.reject();
    if (!sdk) {
      sdk = loadScript('https://dapi.kakao.com/v2/maps/sdk.js?appkey=' + encodeURIComponent(key) + '&libraries=services&autoload=false')
        .then(function () { return new Promise(function (r) { kakao.maps.load(r); }); });
    }
    return sdk;
  }

  function show(y, x) {
    kakaoReady().then(function () {
      var pos = new kakao.maps.LatLng(y, x);
      mapEl.hidden = false;
      if (!map) {
        map = new kakao.maps.Map(mapEl, { center: pos, level: 3 });
        marker = new kakao.maps.Marker({ position: pos, draggable: true, map: map });
        kakao.maps.event.addListener(marker, 'dragend', function () {
          var p = marker.getPosition();
          lat.value = p.getLat().toFixed(7);
          lng.value = p.getLng().toFixed(7);
          say('핀 위치로 바꿨습니다. 저장을 눌러야 홈페이지에 반영됩니다.', 'ok');
        });
      } else {
        map.relayout();
        map.setCenter(pos);
        marker.setPosition(pos);
      }
    }).catch(function () {});
  }

  function locate(address) {
    if (!address) return;
    kakaoReady().then(function () {
      new kakao.maps.services.Geocoder().addressSearch(address, function (res, status) {
        if (status !== kakao.maps.services.Status.OK || !res.length) {
          say('이 주소로 지도 위치를 찾지 못했습니다. \'주소 검색\' 버튼으로 주소를 골라 주세요.', 'err');
          return;
        }
        lat.value = parseFloat(res[0].y).toFixed(7);
        lng.value = parseFloat(res[0].x).toFixed(7);
        show(res[0].y, res[0].x);
        say('지도 위치를 새 주소로 맞췄습니다. 핀이 입구와 조금 다르면 끌어서 옮기고, 저장을 눌러 주세요.', 'ok');
      });
    }).catch(function () {
      say('지도를 불러오지 못해 위치를 자동으로 맞추지 못했습니다. 잠시 후 다시 시도해 주세요.', 'err');
    });
  }

  // 처음 열었을 때: 저장된 위치를 지도로 보여 준다
  if (key && lat.value && lng.value && !isNaN(lat.value) && !isNaN(lng.value)) {
    show(lat.value, lng.value);
    say('지금 홈페이지에 표시되는 매장 위치입니다. 핀이 입구와 다르면 끌어서 옮기고 저장하세요.');
  }

  // 주소를 직접 고쳐 쓴 경우에도 위치를 다시 찾는다
  addr.addEventListener('change', function () { locate(addr.value.trim()); });

  // 주소 검색 (우편번호 찾기 화면을 페이지 위에 띄움)
  var btn = document.querySelector('[data-address-search]');
  var postcode = null;
  btn.addEventListener('click', function () {
    postcode = postcode || loadScript('https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js');
    postcode.then(function () {
      var layer = document.createElement('div');
      layer.className = 'postcode-layer';
      layer.innerHTML = '<div role="dialog" aria-label="주소 검색"><header>주소 검색<button class="btn btn--line btn--sm" type="button">닫기</button></header><div data-postcode-box></div></div>';
      document.body.appendChild(layer);
      var close = function () { layer.remove(); };
      layer.querySelector('header button').addEventListener('click', close);
      layer.addEventListener('click', function (e) { if (e.target === layer) close(); });
      new daum.Postcode({
        width: '100%',
        height: '100%',
        oncomplete: function (d) {
          var a = d.roadAddress || d.jibunAddress || d.address;
          if (d.buildingName && d.apartment === 'Y') a += ' (' + d.buildingName + ')';
          addr.value = a;
          close();
          locate(d.roadAddress || d.jibunAddress || d.address);
          if (detail) detail.focus();
        }
      }).embed(layer.querySelector('[data-postcode-box]'));
    }).catch(function () {
      postcode = null;
      say('주소 검색을 불러오지 못했습니다. 인터넷 연결을 확인한 뒤 다시 눌러 주세요.', 'err');
    });
  });
})();

// 배너: 버튼 누르면 갈 곳 — '직접 입력'일 때만 주소 칸, '버튼 없음'이면 버튼 글자 칸 숨김
(function () {
  var sel = document.querySelector('[data-link-target]');
  if (!sel) return;
  var custom = document.querySelector('[data-link-custom]');
  var label = document.querySelector('[data-link-label]');
  sel.addEventListener('change', function () {
    custom.hidden = sel.value !== 'custom';
    label.hidden = sel.value === '';
    if (!custom.hidden) custom.querySelector('input').focus();
  });
})();

// 공지·이벤트 글: 분류가 '이벤트'일 때만 기간 칸 보이기
(function () {
  var type = document.querySelector('[data-post-type]');
  if (!type) return;
  type.addEventListener('change', function () {
    document.querySelectorAll('[data-event-only]').forEach(function (el) { el.hidden = type.value !== 'event'; });
  });
})();

// 영업시간: 한 요일 시간을 휴무가 아닌 다른 요일에 똑같이 넣기
(function () {
  var msg = document.querySelector('[data-hours-msg]');
  document.querySelectorAll('[data-copy-hours]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var from = btn.closest('tr');
      var src = from.querySelectorAll('input[type=time]');
      var days = [];
      from.parentNode.querySelectorAll('tr').forEach(function (tr) {
        if (tr === from || tr.querySelector('[data-closed]').checked) return;
        tr.querySelectorAll('input[type=time]').forEach(function (input, i) { input.value = src[i].value; });
        days.push(tr.getAttribute('data-day'));
      });
      msg.textContent = days.length
        ? from.getAttribute('data-day') + '요일 시간을 ' + days.join('·') + '요일에 넣었습니다. \'영업시간 저장\'을 눌러야 반영됩니다.'
        : '휴무가 아닌 다른 요일이 없습니다.';
      msg.classList.add('is-ok');
    });
  });
})();
