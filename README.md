## 카페 단체 주문 시스템

이 프로그램은 개인적인 목적으로 만들어진 시스템 입니다.

단체 주문 시 카페 메뉴를 전달하고 각 개인의 주문을 받아 취합하는 번거로움을 줄이기 위해 개발했습니다.

관리자가 주문을 오픈하면 허용된 사용자만 주문을 입력할 수 있습니다.

주문이 마감되면 메뉴명을 기준으로 취합되어 출력됩니다.

## 개요
- 개인 프로젝트
- 2021-02-10~2021-03-16
- <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=PHP&logoColor=white"> <img src="https://img.shields.io/badge/codeigniter-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white"> <img src="https://img.shields.io/badge/javascript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=white"> <img src="https://img.shields.io/badge/jquery-0769AD?style=for-the-badge&logo=jquery&logoColor=white"> <img src="https://img.shields.io/badge/node.js-5FA04E?style=for-the-badge&logo=nodedotjs&logoColor=white"> <img src="https://img.shields.io/badge/soket.io-010101?style=for-the-badge&logo=socketdotio&logoColor=white">

## 설명
### 구성도
<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/0698050d-1406-4d5b-ae6b-3f207c51c945" width="50%">

### ERD
<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/bbe8d567-c100-4865-a429-3a589a5e86c7" width="50%">

### 로그인 화면
세션 기반의 로그인 기능이며, 사원 API 목록에 존재하는 이름으로만 로그인 할 수 있습니다. 사원이 아닌 경우 로그인 할 수 없습니다.

<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/a31544fb-cbec-4a36-b298-6055c493da70" width="50%">

### 관리자 화면
카페, 팀, 마감 시간을 설정하여 주문서를 열 수 있습니다. 해당 팀이 아닌 사원은 주문서에 진입 할 수 없습니다.

주문서 생성 시점에 사원 목록과 카페 메뉴를 24시간 기준으로 새로 업데이트 합니다. 

<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/1c14856b-8f8a-492a-8165-85ac30cc93f5" width="50%">

### 주문서 화면
카페 홈페이지로부터 크롤링한 데이터를 메뉴로 출력합니다. 주문 완료 시 실시간 주문 현황 목록에 broadcasting 됩니다.

<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/5a79730a-5d98-4e17-b3db-d51d9b1fd89d" width="50%">

#### 실시간 주문 현황을 볼 수 있습니다.
node.js와 soket.io를 사용하여 구현하였습니다. 

<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/004a27ed-e2f0-4627-af2e-6c3c0df341fb" width="50%">

### 취합된 메뉴 목록을 출력합니다.
<img src="https://github.com/daye9005kim/cafe_ordering/assets/78843974/c78e166b-562b-489a-9471-0bd2b2b139f3" width="50%">

