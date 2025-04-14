# PHP Bulletin Board

This is a lightweight but structured PHP bulletin board system built with:  
가볍지만 구조적으로 설계된 PHP 기반의 게시판 시스템입니다.

- 🧱 Custom MVC architecture (without frameworks)  
  프레임워크 없이 직접 설계한 MVC 아키텍처 구조
- 📦 Composer autoloading (PSR-4)  
  Composer 기반 PSR-4 오토로딩 적용
- 🔐 Prepared for DB integration and user authentication  
  DB 연동 및 사용자 인증 기능을 위한 구조 설계
- 🎯 Ready to expand with posts, comments, login, admin features  
  게시글, 댓글, 로그인, 관리자 기능 확장을 고려한 설계

---

## Getting Started  
## 시작하기

To run the project locally:  
이 프로젝트를 로컬에서 실행하려면 다음 명령어를 사용하세요:

```bash
composer install        # 의존성 설치
php -S localhost:8080 -t public   # 내장 서버 실행 (public 디렉토리를 루트로 설정)
