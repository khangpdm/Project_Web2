
<div class="content">
    <div class="content__input">
        <h1>CÂY TRỒNG<br> DÀNH CHO BẠN     
        </h1>
        <div class="content__input-img">
            <img src="img/cay.png" alt="">
        </div>
        <p class="content__slogan">Khám phá thế giới cây xanh, làm đẹp không gian sống!</p>

        <div class="content__input__main">
            <input id="keyword" type="text" placeholder="Tìm kiếm">
            <input id="content__input__main__sort_min" type="text" placeholder="min">
            <input id="content__input__main__sort_max" type="text" placeholder="max">
            <select id="content__input__main__sort_type">
            </select>
            <i class="fa-solid fa-magnifying-glass" onclick="LoadProducts(1)"></i>
        </div>        
    </div>

    <div class="product-category">
        <h2 class="category-title">DANH MỤC</h2>

        <div class="product-category-grid">
            <div class="category-card" data-tree_type="TYP001">
                <img src="img/caydecham.jpg" alt="Cây dễ chăm">
                <div class="category-label">CÂY DỄ CHĂM</div>
            </div>

            <div class="category-card" data-tree_type="TYP002">
                <img src="img/cayvanphong.jpg" alt="Cây văn phòng">
                <div class="category-label">CÂY VĂN PHÒNG</div>
            </div>

            <div class="category-card" data-tree_type="TYP003">
                <img src="img/cayphongthuy.jpg" alt="Cây phong thủy">
                <div class="category-label">CÂY PHONG THUỶ</div>
            </div>

            <div class="category-card" data-tree_type="TYP004">
                <img src="img/caydeban.jpg" alt="Cây để bàn">
                <div class="category-label">CÂY ĐỂ BÀN</div>
            </div>

            <div class="category-card" data-tree_type="TYP005">
                <img src="img/caytrongnuoc.jpg" alt="Cây trồng nước">
                <div class="category-label">CÂY TRỒNG NƯỚC</div>
            </div>

            <div class="category-card" data-tree_type="TYP006">
                <img src="img/caycaocap.jpg" alt="Cây cao cấp">
                <div class="category-label">CÂY CAO CẤP</div>
            </div>

            <div class="category-card" data-tree_type="TYP007">
                <img src="img/chaudatnung.jpg" alt="Chậu đất nung">
                <div class="category-label">CHẬU ĐẤT NUNG</div>
            </div>

            <div class="category-card" data-tree_type="TYP008">
                <img src="img/chauximang.jpg" alt="Chậu xi măng">
                <div class="category-label">CHẬU XI MĂNG</div>
            </div>
        </div>
    </div>

    <div class="product">
        <h2 class="product-title">
            <!-- Tên loại sản phẩm -->
        </h2>
        <div id="content__product">
                <!-- Sản phẩm sẽ được tải vào đây -->
            
        </div>
        
    </div>

    <div class="content__page">
        <div class="prev" onclick="Lui()">
            < </div>
                <div id="page"> </div>
                <div class="next" onclick="Tien()"> > </div>
        </div>
    </div>
    
</div>


<!-- Modal chi tiết sản phẩm -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <div class="modal-layout">
            <div class="modal-left">
                <img id="modal-img" src="" alt="Ảnh sản phẩm">
            </div>
            <div class="modal-right">
                <h1 id="modal-title" class="sanpham-ten">Tên sản phẩm</h1>
                <p id="modal-code" class="product-code">Mã sản phẩm</p>
                <p id="modal-quantity" class="product-quantity">Số lượng: 0</p>
                <p id="modal-price" class="sanpham-gia">0.000₫</p>
                <p id="modal-description">Mô tả sản phẩm...</p>
                <!-- Thêm trường nhập số lượng -->
                <div class="quantity-input">
                    <label for="buy-now-quantity">Số lượng:</label>
                    <input type="number" id="buy-now-quantity" min="1" value="1">
                </div>
                <div class="button-group">
                    <div class="cart-icon-sp" id="modal-cart-icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <button class="btn-muanhanh">Mua ngay</button>
                </div>
            </div>
        </div>
    </div>
</div>