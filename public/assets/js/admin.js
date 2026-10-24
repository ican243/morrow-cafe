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
