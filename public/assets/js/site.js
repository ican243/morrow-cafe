/* MORROW COFFEE — 고객용 화면 스크립트 (프레임워크 없이 필요한 것만) */
(function () {
  'use strict';

  var body = document.body;

  // 모바일 메뉴 열기/닫기
  var toggle = document.querySelector('.nav-toggle');
  if (toggle) {
    var setOpen = function (open) {
      body.classList.toggle('nav-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.querySelector('.sr-only').textContent = open ? '메뉴 닫기' : '메뉴 열기';
    };
    toggle.addEventListener('click', function () { setOpen(!body.classList.contains('nav-open')); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && body.classList.contains('nav-open')) { setOpen(false); toggle.focus(); } });
    window.matchMedia('(min-width: 861px)').addEventListener('change', function (m) { if (m.matches) setOpen(false); });
  }

  // HOME: 사진 위 투명 머리말 → 스크롤하면 배경 생김
  var header = document.getElementById('siteHeader');
  if (header && body.classList.contains('is-home')) {
    var onScroll = function () { header.classList.toggle('is-solid', window.scrollY > 40); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // HOME: 메인 사진 넘기기 (자동 넘김 없음 — 읽는 도중 바뀌지 않게)
  var dots = document.querySelectorAll('.hero-dots button');
  dots.forEach(function (dot) {
    dot.addEventListener('click', function () {
      var idx = dot.getAttribute('data-slide');
      document.querySelectorAll('.hero-slide').forEach(function (slide, i) {
        var on = String(i) === idx;
        slide.classList.toggle('is-active', on);
        slide.setAttribute('aria-hidden', on ? 'false' : 'true');
      });
      dots.forEach(function (d) { d.setAttribute('aria-current', d === dot ? 'true' : 'false'); });
    });
  });

  // MENU: 지금 보고 있는 카테고리 탭 표시
  var tabs = document.querySelectorAll('.menu-tabs a');
  if (tabs.length && 'IntersectionObserver' in window) {
    var tabBar = document.querySelector('.menu-tabs');
    var tabList = tabBar.querySelector('ul');
    var byId = {};
    var clicking = null;
    tabs.forEach(function (a) { byId[a.getAttribute('href').slice(1)] = a; });

    // 탭 줄 높이를 CSS 에 알려줘서, 탭을 눌렀을 때 제목이 탭 밑에 가려지지 않게 함
    var setTabsH = function () { document.documentElement.style.setProperty('--tabs-h', tabBar.offsetHeight + 'px'); };
    setTabsH();
    window.addEventListener('resize', setTabsH);

    var activate = function (tab) {
      tabs.forEach(function (a) { a.classList.toggle('is-active', a === tab); });
      // scrollIntoView 는 페이지 전체까지 움직여서 스크롤이 덜컥거림 → 탭 줄만 가로로 이동
      var left = tab.offsetLeft - (tabList.clientWidth - tab.offsetWidth) / 2;
      tabList.scrollTo({ left: Math.max(0, left), behavior: 'smooth' });
    };

    // 탭을 누르면 도착할 때까지 지나가는 카테고리로 탭이 바뀌지 않게 잠시 고정
    tabs.forEach(function (a) {
      a.addEventListener('click', function () {
        activate(a);
        clearTimeout(clicking);
        clicking = setTimeout(function () { clicking = null; }, 1000);
      });
    });

    var io = new IntersectionObserver(function (entries) {
      if (clicking) return;
      entries.forEach(function (en) {
        if (en.isIntersecting && byId[en.target.id]) activate(byId[en.target.id]);
      });
    }, { rootMargin: '-35% 0px -60% 0px' });
    document.querySelectorAll('.menu-cat').forEach(function (sec) { io.observe(sec); });
  }

  // SPACE: 분류 필터
  var filterBtns = document.querySelectorAll('.filter button[data-filter]');
  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var f = btn.getAttribute('data-filter');
      filterBtns.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
      document.querySelectorAll('#gallery li').forEach(function (li) {
        li.hidden = f !== 'all' && li.getAttribute('data-area') !== f;
      });
    });
  });

  // SPACE: 사진 크게 보기
  var lb = document.getElementById('lightbox');
  if (lb && typeof lb.showModal === 'function') {
    // 사진은 처음 열 때 만든다 (빈 이미지 태그를 미리 두지 않음)
    var lbImg = document.createElement('img');
    var lbCap = lb.querySelector('figcaption');
    var current = 0;
    var lastFocus = null;
    var visibleItems = function () {
      return Array.prototype.filter.call(document.querySelectorAll('#gallery li'), function (li) { return !li.hidden; })
        .map(function (li) { return li.querySelector('button'); });
    };
    var show = function (i) {
      var items = visibleItems();
      if (!items.length) return;
      current = (i + items.length) % items.length;
      var b = items[current];
      if (!lbImg.isConnected) lb.querySelector('figure').prepend(lbImg);
      lbImg.src = b.getAttribute('data-full');
      lbImg.alt = b.querySelector('img').alt;
      lbCap.textContent = b.getAttribute('data-caption') || '';
    };
    document.querySelectorAll('#gallery button').forEach(function (b) {
      b.addEventListener('click', function () {
        lastFocus = b;
        show(visibleItems().indexOf(b));
        lb.showModal();
      });
    });
    lb.addEventListener('click', function (e) {
      var act = e.target.closest('[data-lb]');
      if (act) {
        var a = act.getAttribute('data-lb');
        if (a === 'close') lb.close();
        if (a === 'prev') show(current - 1);
        if (a === 'next') show(current + 1);
      } else if (e.target === lb || e.target.tagName === 'FIGURE') {
        lb.close();
      }
    });
    lb.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') show(current - 1);
      if (e.key === 'ArrowRight') show(current + 1);
    });
    // 모바일 좌우 밀기
    var startX = null;
    lb.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', function (e) {
      if (startX === null) return;
      var dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 50) show(current + (dx < 0 ? 1 : -1));
      startX = null;
    });
    lb.addEventListener('close', function () { if (lastFocus) lastFocus.focus(); });
  }

  // LOCATION: 주소 복사
  document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var text = btn.getAttribute('data-copy');
      var done = function () { var t = btn.textContent; btn.textContent = '복사했습니다'; setTimeout(function () { btn.textContent = t; }, 1600); };
      if (navigator.clipboard) navigator.clipboard.writeText(text).then(done);
    });
  });

  // LOCATION: 카카오 지도 (키가 설정된 경우에만 스크립트가 붙는다)
  var mapEl = document.getElementById('map');
  if (mapEl && mapEl.dataset.lat) {
    var tries = 0;
    var waitKakao = function () {
      if (window.kakao && window.kakao.maps) {
        window.kakao.maps.load(function () {
          var pos = new kakao.maps.LatLng(parseFloat(mapEl.dataset.lat), parseFloat(mapEl.dataset.lng));
          var map = new kakao.maps.Map(mapEl, { center: pos, level: 3 });
          new kakao.maps.Marker({ map: map, position: pos, title: mapEl.dataset.name });
          map.setZoomable(false); // 스크롤하다 지도가 확대되는 것 방지
          map.addControl(new kakao.maps.ZoomControl(), kakao.maps.ControlPosition.RIGHT);
        });
      } else if (tries++ < 50) {
        setTimeout(waitKakao, 100);
      }
    };
    waitKakao();
  }
})();
