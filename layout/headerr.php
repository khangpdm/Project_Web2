<div class="header">
    <div class="header__name"><a href="index.php?page=trangchu">Green Haven</a></div>

    <div class="header__menu">
        <div><a href="index.php?page=trangchu">TRANG CHỦ</a></div>
        <div id="header__menu__product">
            <a href="index.php?page=sanpham">SẢN PHẨM</a>
            <div id="header__menu__sub"></div>
        </div>
        <div><a href="index.php?page=chinhsach">CHÍNH SÁCH</a></div>
    </div>

    <div class="header__icons">

        <div class="live-search-wrapper">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Tìm sản phẩm..." onkeyup="liveSearch(this.value, 'desktop')">
                <i class="fa-solid fa-magnifying-glass"></i>
                <div class="search-result" id="searchResult"></div>
            </div>

        </div>

        <a href="index.php?page=giohang" class="cart-icon">
            <i class="fa-solid fa-cart-shopping"></i>
            <span id="cart-count">0</span>
        </a>

        <div id="open-form-log-in">
            <div style="font-style: italic; max-width: 300px;" id="user-greeting"></div>
            <span class="user-icon"><i class="fa-solid fa-user"></i></span>
            <div id="open-form-log-in__dropdown" style="display: none;">
            <a href="#" id="open-form-log-in__info">Thông tin tài khoản</a>
            <a href="#" id="open-form-log-in__invoices">Hóa đơn</a>
            <a href="#" id="open-form-log-in__logout">Đăng xuất</a>
            </div>
        </div>

        <i class="fa-solid fa-bars" id="hamburger-menu"></i>

    </div>
    
</div>

<!-- Menu Mobile Full Màn Hình -->
<div id="mobile-menu" style="display: none;">
    <div class="mobile-menu-content">
        <div class="mobile-menu-search">
            <input type="text" id="mobile-search-input" placeholder="Tìm kiếm sản phẩm" onkeyup="liveSearch(this.value, 'mobile')">
            <i class="fa-solid fa-magnifying-glass"></i>
            <!-- Mobile search result -->
            <div id="mobile-search-result" class="search-result" style="display: none;"></div>

        </div>

        <a href="index.php?page=trangchu">TRANG CHỦ</a>
        <a href="index.php?page=sanpham">SẢN PHẨM</a>
        <a href="index.php?page=chinhsach">CHÍNH SÁCH</a>
        <a href="index.php?page=giohang" >GIỎ HÀNG</a>
    </div>

    <!-- Nút Đóng -->
    <div id="close-menu">
        <i class="fa-solid fa-xmark"></i>
    </div>
</div>


<script>
window.onload = function() {
  var hamburger = document.getElementById("hamburger-menu");
  var mobileMenu = document.getElementById("mobile-menu");
  var closeMenu = document.getElementById("close-menu");

  if (hamburger && mobileMenu && closeMenu) {
      hamburger.addEventListener("click", function() {
          mobileMenu.style.display = "flex";
      });

      closeMenu.addEventListener("click", function() {
          mobileMenu.style.display = "none";
      });
  }
};
</script>

