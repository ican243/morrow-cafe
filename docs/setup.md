# MORROW COFFEE 세팅 기록

> 비밀번호·키 값은 이 문서에 적지 않습니다. 서버 안 `.env` 파일에만 있습니다.

- 주소: https://kir1.cafe24.com/cafe (관리자: `/cafe/admin`)
- 성격: 포트폴리오·숨고 외주 샘플용 **가상 카페**
- 스택: PHP 8.3 · CodeIgniter 4.7 · MySQL 8 · Nginx (하위 경로 방식)

## 1. 폴더 구조

```
app/
  Config/Cafe.php            알레르기 항목, 매장정보 칸, 사진 크기 등 사이트 전용 설정
  Config/Routes.php          주소 목록 (고객용 / 관리자용 분리)
  Controllers/               고객용 (SiteController 를 상속 → 머리말·바닥글 정보 자동)
  Controllers/Admin/         관리자용 (AdminController 를 상속, adminAuth 필터로 로그인 확인)
  Models/                    테이블당 1개. SortableModel = ▲▼ 순서 바꾸기 공통
  Libraries/ImageService.php 사진 검사 → 회전 보정 → WebP 3가지 크기로 저장
  Helpers/cafe_helper.php    화면 공통 함수 (사진 태그, 가격, 길찾기 주소, 아이콘)
  Views/layouts/             site.php(고객) / admin.php(관리자) 틀
  Views/partials/            머리말, 바닥글, 영업시간 표 등 반복 부품
  Database/Migrations/       테이블 구조
  Database/Seeds/            InitialSeeder(운영) / DemoSeeder(데모) / images(데모 사진 원본)
  Commands/CreateAdmin.php   관리자 계정 만들기 명령
public/assets/               css, js, cropper(사진 자르기 라이브러리)
public/uploads/              올라간 사진 (git 제외)
docs/                        이 문서, 이미지 출처
```

## 2. 서버 세팅 (2026-10-24)

| 항목 | 내용 |
|---|---|
| DB | `cafe_db` (utf8mb4), 계정 `cafe@localhost` — 서버 안에서만 접속 가능 |
| `.env` | DB 접속 정보, `app.baseURL`, 카카오 키, 쿠키 경로(`/cafe/`) |
| Nginx | `/etc/nginx/snippets/cafe-subpath.conf` 를 lambda 443 server 블록에서 include (notedot 와 같은 방식). 수정 전 원본: `/root/lambda.nginx.bak-2026-10-24` |
| PHP-FPM | 전용 풀 `/etc/php/8.3/fpm/pool.d/cafe.conf` — 업로드 10MB, `open_basedir=/var/www/cafe/` (다른 프로젝트 폴더 못 읽음) |
| 업로드 용량 | 브라우저 검사 10MB → nginx 12MB → PHP 10MB/12MB 순서로 맞춤 |
| 권한 | `chown -R www-data:www-data /var/www/cafe`, `.env` 640 |
| 모드 | `CI_ENVIRONMENT = production` (오류 내용이 화면에 안 보임, 로그는 `writable/logs/`) |

## 3. 자주 쓰는 명령

```bash
cd /var/www/cafe
sudo -u www-data php spark migrate                 # 테이블 만들기/변경 반영
sudo -u www-data php spark db:seed InitialSeeder   # 운영용 기본값 (여러 번 실행해도 안전)
sudo -u www-data php spark db:seed DemoSeeder      # 데모 데이터 (메뉴가 하나라도 있으면 실행 안 됨)
sudo -u www-data php spark cafe:admin 이메일 이름    # 관리자 계정 + 임시 비밀번호 발급
sudo -u www-data php spark cache:clear             # 매장정보 캐시 비우기
sudo nginx -t && sudo systemctl reload nginx
```

> 꼭 `sudo -u www-data` 로 실행: root 로 시드를 돌리면 사진 파일 주인이 root 가 되어 관리자 화면에서 삭제가 안 됨.

## 4. 카카오 지도 켜기

1. https://developers.kakao.com → 내 애플리케이션 → 앱 추가
2. 앱 설정 > 플랫폼 > Web > 사이트 도메인에 `https://kir1.cafe24.com` 등록
3. 앱 키 중 **JavaScript 키** 복사
4. 서버 `.env` 의 `kakao.jsKey = ` 뒤에 붙여넣기 (따옴표 없이)
5. 관리자 > 주소·연락처에서 위도·경도 확인 → LOCATION 페이지에 지도 표시

키가 없으면 지도 자리에 주소 카드가 나오고, 네이버·카카오 길찾기 버튼은 그대로 동작합니다.

## 5. 백업

`/var/www/backups/` 에 시각을 붙여 3개씩 저장 (권한 600, 서버 관리자만 읽기)

| 파일 | 내용 |
|---|---|
| `cafe_code_날짜.tar.gz` | 코드 전체 + `.env` + 올린 사진 + `.git` (vendor·캐시·세션·로그 제외) |
| `cafe_db_날짜.sql.gz` | DB 전체 |
| `cafe_server_날짜.tar.gz` | nginx 설정, PHP-FPM 풀, ssh config |

되돌리기: `zcat cafe_db_날짜.sql.gz | sudo mysql cafe_db` / `tar -xzf cafe_code_날짜.tar.gz -C /var/www`

## 6. 보안 정리

- 모든 POST 폼 CSRF 토큰 확인, 관리자 로그인 5분 5회 제한, 로그인 시 세션 ID 재발급
- 비밀번호는 `password_hash` 로만 저장. 초기 비밀번호는 명령 실행 시 한 번만 출력
- 사진: 확장자가 아니라 실제 내용으로 형식 검사 → GD 로 새로 그려 저장 (EXIF 위치정보 제거, 위장 파일 차단). `uploads/` 안 PHP 실행 차단
- 관리자 입력 글은 HTML 을 허용하지 않고 줄바꿈만 살림 (스크립트 삽입 차단). 링크는 http(s) 만 허용
- 사용 중인 사진·메뉴가 있는 카테고리는 삭제 불가 (DB 외래키 + 화면 안내 이중으로)
- `/cafe/` 전체 `noindex` (가상 매장이 검색에 노출되지 않게)

## 7. 실제 고객에게 납품할 때

1. 새 DB·계정 만들고 `.env` 새로 작성 (`app.baseURL`, `cookie.path` 를 고객 도메인에 맞게)
2. `migrate` → `InitialSeeder` 만 실행 (**DemoSeeder 실행 금지**)
3. `cafe:admin 사장님이메일 이름` → 임시 비밀번호 전달 → 첫 로그인 후 변경 안내
4. nginx 의 `X-Robots-Tag noindex` 와 `layouts/site.php` 의 `robots noindex`, 바닥글 "가상 매장" 문구 제거
5. 카카오 키를 고객 도메인으로 새로 발급
