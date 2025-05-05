var currentPage = 1; // Đưa ra ngoài để dùng chung
var totalPage = 0;
var type = "all";

function Lui() {
  if (currentPage > 1) {
    currentPage--;
    LoadProducts(currentPage);
  }
}

function Tien() {
  if (currentPage < totalPage) {
    currentPage++;
    LoadProducts(currentPage);
  }
}

$(document).ready(function () {
  // Khởi tạo trạng thái ban đầu
  $("#overlay").hide();
  $("#open-form-log-in__dropdown").hide();

  // Load sản phẩm và giỏ hàng khi trang tải
  LoadProducts(currentPage);
  loadCart(false);

  // Sự kiện chọn loại sản phẩm từ header
  $(document).on("click", "#header__menu__sub div", function (event) {
    event.stopPropagation();
    type = $(this).data("tree_type");
    console.log(type);
    LoadProducts(1);
    $(".product-category").css("display", "none");
  });

  // Sự kiện chọn "Tất cả sản phẩm" từ header
  $(document).on("click", "#header__menu__product", function () {
    type = "all";
    console.log(type);
    LoadProducts(1);
  });

  // Sự kiện tăng số lượng trong giỏ hàng
  $(document).on("click", ".increase-qty", function () {
    let productId = $(this).closest(".cart-item").data("id");
    updateCart("increase", productId);
  });

  // Sự kiện giảm số lượng trong giỏ hàng
  $(document).on("click", ".decrease-qty", function () {
    let $quantityElement = $(this).siblings(".quantity-value");
    let currentQuantity = parseInt($quantityElement.text()) || 1;

    if (currentQuantity > 1) {
      let productId = $(this).closest(".cart-item").data("id");
      updateCart("decrease", productId);
    } else {
      alert("Số lượng tối thiểu là 1!");
    }
  });

  // Sự kiện xóa sản phẩm khỏi giỏ hàng
  $(document).on("click", ".remove-cart", function () {
    let productId = $(this).closest(".cart-item").data("id");
    if (confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) {
      updateCart("remove", productId);
    }
  });

  // Lọc sản phẩm từ danh mục
  $(document).on("click", ".category-card", function () {
    type = $(this).data("tree_type");
    console.log("Loại sản phẩm:", type);
    LoadProducts(1);
    $(".product-category").css("display", "none");
  });


$("#open-form-log-in").on("click", function () {
  $.ajax({
      data: {action: "check"},
      type: "POST",
      url: "handle/auth.php",
      dataType: "json",
      success: function (response) {
          if (response.loggedIn) {
              $("#open-form-log-in__dropdown").show();
          } else {
              $("#overlay").show();
          }
      },
      error: function (xhr, status, error) {
          console.error("Lỗi AJAX (auth.php - click):", status, error);
          console.log("Phản hồi từ server:", xhr.responseText);
      },
  });
});

  // Ẩn dropdown khi rê chuột ra ngoài
  $("#open-form-log-in").on("mouseleave", function () {
    $("#open-form-log-in__dropdown").hide();
  });

  // Xử lý nhấp vào "Đăng xuất"
  $("#open-form-log-in__logout").on("click", function (event) {
    event.preventDefault();
    $.ajax({
      data: {action: "logout"},
      type: "POST",
      url: "handle/auth.php",
      dataType: "json",
      success: function () {
        location.reload(true);
      },
      error: function () {
        console.error("Lỗi AJAX!");
      },
    });
  });

  // Xử lý nhấp vào "Thông tin khách hàng"
// Xử lý nhấp vào "Thông tin tài khoản"
$('#open-form-log-in__info').on('click', function (event) {
  event.preventDefault();

  // Kiểm tra trạng thái đăng nhập trước
  $.ajax({
      type: "POST",
      url: "handle/auth.php",
      data: { action: "check" },
      dataType: "json",
      success: function (response) {
          if (response.loggedIn) {
              // Nếu đã đăng nhập, gọi AJAX để lấy thông tin tài khoản
              $.ajax({
                  type: "GET",
                  url: "handle/customer_info.php",
                  dataType: "json",
                  success: function (response) {
                      if (response.status === "success") {

                          let infoHtml = `
                          <div class="account-info-modal">
                              <h2>Thông tin tài khoản</h2>
                              <div><strong>Tên đăng nhập:</strong> ${response.data.username} (Không thể chỉnh sửa)</div>
                              <div>
                                  <strong>Email:</strong>
                                  <input type="email" id="email-input" value="${response.data.email}" placeholder="Nhập email">
                              </div>
                              <div>
                                  <strong>Số điện thoại:</strong>
                                  <input type="text" id="phone-input" value="${response.data.phone}" placeholder="Nhập số điện thoại">
                              </div>
                              <div>
                                  <strong>Tỉnh/Thành phố:</strong>
                                  <select id="province-select">
                                      <option value="">-- Chọn tỉnh/thành phố --</option>
                                      ${response.provinces.map(province => `
                                          <option value="${province.province_id}" ${response.data.province_id === province.province_id ? 'selected' : ''}>
                                              ${province.name}
                                          </option>
                                      `).join('')}
                                  </select>
                              </div>
                              <div>
                                  <strong>Quận/Huyện:</strong>
                                  <select id="district-select">
                                      <option value="">-- Chọn quận/huyện --</option>
                                  </select>
                              </div>
                              <div>
                                  <strong>Địa chỉ chi tiết:</strong>
                                  <input type="text" id="address-detail-input" value="${response.data.address_detail}" placeholder="Nhập địa chỉ chi tiết (tùy chọn)">
                              </div>
                              <button class="save-account">Lưu</button>
                              <button class="close-modal">Đóng</button>
                          </div>
                      `;
                          $('body').append(infoHtml);

                          // Cập nhật danh sách quận/huyện dựa trên tỉnh/thành phố được chọn
                          function updateDistricts(selectedProvinceId) {
                              let districtSelect = $('#district-select');
                              districtSelect.html('<option value="">-- Chọn quận/huyện --</option>');
                              response.districts.forEach(district => {
                                  if (district.province_id === selectedProvinceId) {
                                      districtSelect.append(`
                                          <option value="${district.district_id}" ${response.data.district_id === district.district_id ? 'selected' : ''}>
                                              ${district.name}
                                          </option>
                                      `);
                                  }
                              });
                          }

                          // Gọi hàm cập nhật quận/huyện lần đầu tiên
                          if (response.data.province_id) {
                              updateDistricts(response.data.province_id);
                          }

                          // Xử lý sự kiện thay đổi tỉnh/thành phố
                          $('#province-select').on('change', function () {
                              let selectedProvinceId = $(this).val();
                              updateDistricts(selectedProvinceId);
                          });

                          // Xử lý sự kiện nút "Lưu"
                          $('.save-account').on('click', function () {
                            let newEmail = $('#email-input').val().trim();
                            let newPhone = $('#phone-input').val().trim();
                            let newProvinceId = $('#province-select').val();
                            let newDistrictId = $('#district-select').val();
                            let newAddress = $('#address-detail-input').val().trim();
                        
                            if (!newEmail || !newPhone) {
                                alert("Email và số điện thoại không được để trống!");
                                return;
                            }
                        
                            // Chuyển chuỗi rỗng thành null
                            newProvinceId = newProvinceId === '' ? null : newProvinceId;
                            newDistrictId = newDistrictId === '' ? null : newDistrictId;
                            newAddress = newAddress === '' ? null : newAddress;
                        
                            $.ajax({
                                type: "POST",
                                url: "handle/update_account.php",
                                data: {
                                    email: newEmail,
                                    phone: newPhone,
                                    province_id: newProvinceId,
                                    district_id: newDistrictId,
                                    address_detail: newAddress
                                },
                                dataType: "json",
                                success: function (updateResponse) {
                                    if (updateResponse.status === "success") {
                                        alert(updateResponse.message);
                                        // Cập nhật lại giá trị hiển thị trên modal
                                        $('#email-input').val(newEmail);
                                        $('#phone-input').val(newPhone);
                                        $('#address-detail-input').val(newAddress);
                                        // Đóng modal
                                        $('.account-info-modal').fadeOut(300, function() {
                                            $(this).remove();
                                        });
                                    } else {
                                        alert(updateResponse.message);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.error("Lỗi AJAX (update_account.php):", status, error);
                                    console.log("Phản hồi từ server:", xhr.responseText);
                                    alert("Lỗi khi cập nhật thông tin tài khoản: " + error);
                                }
                            });
                        });

                          // Xử lý sự kiện nút "Đóng" (cũng áp dụng hiệu ứng fadeOut)
                          $('.close-modal').on('click', function () {
                            $('.account-info-modal').fadeOut(300, function() {
                                $(this).remove();
                            });
                          });

                          // Xử lý sự kiện nút "Đóng"
                          $('.close-modal').on('click', function () {
                              $('.account-info-modal').remove();
                          });
                      } else {
                          alert(response.message);
                      }
                  },
                  error: function (xhr, status, error) {
                      console.error("Lỗi AJAX (customer_info.php):", status, error);
                      console.log("Phản hồi từ server:", xhr.responseText);
                      if (xhr.status === 401) {
                          alert("Vui lòng đăng nhập để xem thông tin tài khoản!");
                          $('#overlay').show();
                      } else {
                          alert("Lỗi khi lấy thông tin tài khoản: " + error);
                      }
                  }
              });
          } else {
              $('#overlay').show();
              alert("Vui lòng đăng nhập để xem thông tin tài khoản!");
          }
      },
      error: function () {
          console.error("Lỗi kiểm tra trạng thái đăng nhập!");
          alert("Lỗi kiểm tra trạng thái đăng nhập!");
      }
  });
});

  // Đóng form đăng nhập khi nhấp ra ngoài
  $("#overlay").on("click", function (event) {
    if ($(event.target).closest(".Authentication-Form-Dad").length === 0) {
      $("#overlay").hide();
    }
  });       

  // Sự kiện mở giỏ hàng
  $(document).on("click", ".cart-icon, .cart-icon-sp", function (e) {
    e.preventDefault();

    if ($(this).closest("#productModal").length) {
        let productId = $(this).attr("data-id");
        let productName = $("#modal-title").text();

        if (!productId || productId === "undefined") {
            console.error("productId không hợp lệ trong modal:", productId);
            alert("Không thể thêm sản phẩm: Mã sản phẩm không hợp lệ!");
            return;
        }

        console.log("Thêm sản phẩm từ modal chi tiết:", { productId, productName });

        $.ajax({
            type: "POST",
            url: "handle/auth.php",
            data: { action: "check" },
            dataType: "json",
            success: function (response) {
                if (response.loggedIn) {
                    updateCart(
                        "add",
                        productId,
                        "Đã thêm " + productName + " vào giỏ hàng!"
                    );
                    loadCart(false); // Không hiển thị giỏ hàng
                    closeModal();
                } else {
                    $("#overlay").show();
                    alert("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!");
                }
            },
            error: function (xhr, status, error) {
                console.error(
                    "Lỗi kiểm tra đăng nhập:",
                    status,
                    error,
                    xhr.responseText
                );
                alert("Lỗi kiểm tra trạng thái đăng nhập!");
            },
        });
    } else {
        loadCart(true); 
    }
  });

  // Sự kiện cho nút "Mua tiếp"
  $(document).on("click", ".continue-shopping", function (e) {
    e.preventDefault();
    $("#modal-cart").css("display", "none");
    type = "all";
    LoadProducts(1);
  });

  // Sự kiện cho nút "Thanh toán"
  $(document).on("click", ".checkout", function () {
    $(this).text("Đang xử lý...").prop("disabled", true);

    if (
        $("#cart-items").children().length === 0 ||
        $("#cart-items").find(".empty-cart").length > 0
    ) {
        alert("Giỏ hàng của bạn đang trống! Vui lòng thêm sản phẩm trước khi thanh toán.");
        $(this).text("Thanh toán").prop("disabled", false);
        return;
    }

    $.ajax({
        type: "POST",
        url: "handle/auth.php",
        data: { action: "check" },
        dataType: "json",
        success: function (response) {
            if (response.loggedIn) {
                $("#modal-cart").css("display", "none"); // Đóng modal giỏ hàng
                window.location.href = "index.php?page=checkout"; // Chuyển hướng đến trang thanh toán
            } else {
                $("#overlay").show();
                alert("Vui lòng đăng nhập để thanh toán!");
            }
        },
        error: function (xhr, status, error) {
            console.error("Lỗi AJAX khi kiểm tra đăng nhập:", status, error, xhr.responseText);
            alert("Lỗi kiểm tra trạng thái đăng nhập!");
        },
        complete: function () {
            $(".checkout").text("Thanh toán").prop("disabled", false);
        }
    });
  });

  // Sự kiện cho nút đóng modal
  $(document).on("click", ".close-modal", function () {
    $("#modal-cart").css("display", "none");
  });
  $.ajax({
    data: {action: "check"},
    type: "POST",
    url: "handle/auth.php",
    dataType: "json",
    success: function (response) {
        if (response.username !== undefined) {
            // Kiểm tra xem dòng "Xin chào" đã tồn tại chưa
            if ($(".greeting-message").length === 0) {
                $("#open-form-log-in").after(
                    '<div class="greeting-message" style="font-style: italic; max-width: 300px;">Xin chào, ' +
                    response.username +
                    "!</div>"
                );
            }
        }
    },
});

  // Sự kiện cho nút "Thêm vào giỏ" trong danh sách sản phẩm
  $(document).on("click", ".btn-them", function () {
    let productId = $(this).attr("data-id"); 
    let productName = $(this)
        .closest(".sanpham-card")
        .find(".sanpham-ten")
        .text();

    // Kiểm tra productId
    if (!productId || productId === "undefined") {
        console.error("productId không hợp lệ:", productId);
        alert("Không thể thêm sản phẩm: Mã sản phẩm không hợp lệ!");
        return;
    }

    console.log("Thêm sản phẩm từ danh sách:", { productId, productName });

    $.ajax({
        type: "POST",
        url: "handle/auth.php",
        data: { action: "check" },
        dataType: "json",
        success: function (response) {
            console.log("Trạng thái đăng nhập:", response);
            if (response.loggedIn) {
                updateCart(
                    "add",
                    productId,
                    "Đã thêm " + productName + " vào giỏ hàng!"
                );
                loadCart(false);
            } else {
                $("#overlay").show();
                alert("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!");
            }
        },
        error: function (xhr, status, error) {
            console.error(
                "Lỗi kiểm tra đăng nhập:",
                status,
                error,
                xhr.responseText
            );
            alert("Lỗi kiểm tra trạng thái đăng nhập!");
        },
    });
    $("#payment-modal").on("display", function () {
      initializePaymentModal();
  });

  // Sự kiện thay đổi lựa chọn địa chỉ
  $('input[name="address_option"]').on("change", function () {
      toggleAddressFields();
  });

  // Sự kiện thay đổi tỉnh/thành phố để cập nhật quận/huyện
  $("#city").on("change", function () {
      updateDistricts($(this).val());
  });

});

  // Sự kiện chuyển đổi giữa đăng nhập và đăng ký
  $("#btn-mode").on("click", function (event) {
    event.preventDefault();
    var mode = $(this).data("mode");
    if (mode == "sign-up") {
      build("sign-up");
      $(this).data("mode", "sign-in");
      $("#lbl-mode").html("Nhập");
    } else {
      build("sign-in");
      $(this).data("mode", "sign-up");
      $("#lbl-mode").html("Ký");
    }
  });

  // Xử lý submit form đăng nhập/đăng ký
  $("#Authentication-Form").submit(function (event) {
    event.preventDefault();
    var data = $(this).serialize();
    var mode = $("#btn-mode").data("mode");
    $.ajax({
      type: "POST",
      url: "handle/login.php",
      data: data + "&mode=" + mode,
      dataType: "json",
      success: function (response) {
        if (response.status == "success") {
          $(".error-message").remove();
          alert(response.message);
          $("#Authentication-Form")[0].reset();
          $("#overlay").hide();
          location.reload(true);
        } else if (response.status == "error") {
          $(".error-message").remove();
          $.each(response.errors, function (key, message) {
            $("#" + key).after(
              '<div class="error-message" style="color:red;">' +
                message +
                "</div>"
            );
          });
        }
      },
    });
  });
  const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');

    if (page === 'checkout') {
      initializePaymentModal();
      $("#payment-modal").css("display", "block");
  
      $('input[name="address_option"]').on("change", function () {
          toggleAddressFields();
      });
  
      $("#city").on("change", function () {
          updateDistricts($(this).val());
      });
  
      // Thêm sự kiện cho nút mũi tên trái để đóng modal
      $("#close-payment-modal").on("click", function () {
        $("#payment-modal").css("display", "none");
        // Chuyển hướng về trang sản phẩm khi đóng modal
        window.location.href = "index.php?page=products";
      });
  
    }
    $(document).on("change", 'input[name="payment_method"]', function () {
      if ($(this).val() === "online") {
          $(".online-payment-form").css("display", "block");
      } else {
          $(".online-payment-form").css("display", "none");
      }
  });
});



function LoadProducts(page) {
  if ($("#keyword").val() !== null && $("#keyword").val() !== undefined)
    var keyword = $("#keyword").val().trim();

  if (
    $("#content__input__main__sort_min").val() != null &&
    $("#content__input__main__sort_min").val() !== undefined
  )
    var min = $("#content__input__main__sort_min").val().trim();
  if (
    $("#content__input__main__sort_max").val() != null &&
    $("#content__input__main__sort_max").val() !== undefined
  )
    var max = $("#content__input__main__sort_max").val().trim();

  $("#content__input__main__sort_type").on("change", function () {
    type = $(this).val().trim();
  });

  if (type && type !== "all") {
    $(".product-category").hide();
  } else {
    $(".product-category").show();
  }

  $.ajax({
    type: "POST",
    url: "handle/log.php",
    dataType: "json",
    data: {page: page, keyword: keyword, type: type, min: min, max: max},
    success: function (response) {
      console.log("Danh sách sản phẩm:", response.products);

      if (type && response.header__menu__sub) {
        const selectedType = response.header__menu__sub.find(
          (item) => item.typeid === type
        );
        if (selectedType) {
          $(".product-title").text(selectedType.type.toUpperCase());
        } else {
          $(".product-title").text("TẤT CẢ SẢN PHẨM");
        }
      }

      var tmp = "";

      if (!response.products || response.products.length === 0) {
        tmp = `<div class="empty-cart">
                                <i class="fa-solid fa-face-sad-cry"></i>
                                <p>Không có sản phẩm bạn cần!</p>
                            </div>`;
      } else {
        response.products.forEach((product) => {
          tmp += `
                        <div class="sanpham-card" data-id="${product.id}">
                            <div class="sanpham-img-container">
                                <img src="./${product.image}" alt="${
            product.name
          }" class="sanpham-img" loading="lazy">                                
                                <div class="overlay-xemnhanh" onclick='openModal(${JSON.stringify(
                                  product
                                )})'>Xem chi tiết</div>
                            </div>
                            <h3 class="sanpham-ten">${product.name}</h3>
                            <p class="sanpham-gia">${Number(
                              product.price
                            ).toLocaleString("vi-VN")}₫</p>
                            <button class="btn-them" data-id="${
                              product.id
                            }">Thêm vào giỏ</button>
                        </div>
                    `;
        });
      }
      $("#content__product").html(tmp);

      currentPage = response.page;
      totalPage = response.total;

      if (totalPage && totalPage > 1) {
        $("#page").html("");
        for (let i = 1; i <= totalPage; ++i) {
          $("#page").append(`<span onclick="LoadProducts(${i})">${i}</span>`);
        }
        $(".content__page").css("display", "flex");
      } else {
        $("#page").html("");
        $(".content__page").css("display", "none");

      }

      if (Array.isArray(response.header__menu__sub)) {
        $("#header__menu__sub").html("");
        response.header__menu__sub.forEach((type) => {
          $("#header__menu__sub").append(
            `<div data-tree_type="${type.typeid}">${type.type}</div>`
          );
        });
      }

      let selectedValue = $("#content__input__main__sort_type").val();
      $("#content__input__main__sort_type").html("");
      $("#content__input__main__sort_type").append(
        `<option value="all">All</option>`
      );
      response.header__menu__sub.forEach((type) => {
        $("#content__input__main__sort_type").append(
          `<option value="${type.typeid}">${type.type}</option>`
        );
      });

      if (selectedValue)
        $("#content__input__main__sort_type").val(selectedValue);
      else $("#content__input__main__sort_type").val("all");
    },
    error: function (xhr, status, error) {
      console.error("Lỗi AJAX:", status, error);
    },
  });


  $(document).on("click", "#open-form-log-in__invoices", function (event) {
    event.preventDefault();
    $.ajax({
        type: "POST",
        url: "handle/auth.php",
        data: { action: "check" },
        dataType: "json",
        success: function (response) {
            if (response.loggedIn) {
                $.ajax({
                    type: "GET",
                    url: "handle/get_invoices.php",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            let allInvoices = response.invoices;
                            let invoicesHtml = `
                                <div class="invoices-modal">
                                    <div class="invoices-content">
                                        <h2>DANH SÁCH HÓA ĐƠN</h2>
                                        <div class="search-invoice-container">
                                            <input type="text" id="search-invoice" placeholder="Tìm kiếm hóa đơn (mã, người nhận, số điện thoại...)">
                                        </div>
                                        <table class="invoices-table">
                                            <thead>
                                                <tr>
                                                    <th>Mã hóa đơn</th>
                                                    <th>Mã đơn hàng</th>
                                                    <th>Ngày đặt hàng</th>
                                                    <th>Người nhận</th>
                                                    <th>Số điện thoại</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody id="invoices-table-body">
                            `;
                            if (allInvoices.length === 0) {
                                invoicesHtml += `
                                    <tr>
                                        <td colspan="7" style="text-align: center;">Bạn chưa có hóa đơn nào!</td>
                                    </tr>
                                `;
                            } else {
                                allInvoices.forEach(invoice => {
                                    invoicesHtml += `
                                        <tr>
                                            <td>${invoice.bill_id}</td>
                                            <td>${invoice.order_id}</td>
                                            <td>${new Date(invoice.order_date).toLocaleString('vi-VN')}</td>
                                            <td>${invoice.receiver_name}</td>
                                            <td>${invoice.phone_number}</td>
                                            <td>${Number(invoice.total_price).toLocaleString('vi-VN')}₫</td>
                                            <td>
                                                <button class="view-invoice-detail" data-invoice='${JSON.stringify(invoice)}'>Xem chi tiết</button>
                                            </td>
                                        </tr>
                                    `;
                                });
                            }
                            invoicesHtml += `
                                            </tbody>
                                        </table>
                                        <div class="invoices-actions">
                                            <button class="close-invoices">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $("body").append(invoicesHtml);

                            // Xử lý tìm kiếm cơ bản
                            $("#search-invoice").on("input", function () {
                                let keyword = $(this).val().trim();
                                searchInvoices(keyword, allInvoices, "#invoices-table-body");
                            });

                            // Xử lý đóng modal danh sách hóa đơn
                            $(".close-invoices").on("click", function () {
                                $(".invoices-modal").fadeOut(300, function () {
                                    $(this).remove();
                                });
                            });

                            // Xử lý xem chi tiết hóa đơn
                            $(".view-invoice-detail").on("click", function () {
                                let invoice = $(this).data("invoice");
                                showInvoiceModal(invoice);
                            });
                        } else {
                            alert("Lỗi: " + response.message);
                        }
                    },
                    error: function () {
                        alert("Lỗi khi lấy danh sách hóa đơn!");
                    }
                });
            } else {
                $("#overlay").show();
                alert("Vui lòng đăng nhập để xem hóa đơn!");
            }
        },
        error: function () {
            alert("Lỗi kiểm tra trạng thái đăng nhập!");
        }
    });
});
}

function updateCart(action, productId, successMessage = "") {
  console.log("Gửi yêu cầu updateCart:", {action, productId});
  $.post(
    "handle/cart.php",
    {action: action, productID: productId},
    function (response) {
      console.log("Phản hồi từ cart.php:", response);
      if (response.status === "success") {
        loadCart(false);
        if (successMessage) {
          alert(successMessage);
        }
      } else {
        alert("Lỗi: " + response.message);
      }
    },
    "json"
  ).fail(function (xhr, status, error) {
    console.error(
      "Lỗi AJAX trong updateCart:",
      status,
      error,
      xhr.responseText
    );
    alert(
      "Lỗi kết nối đến server! Chi tiết: " +
        status +
        " - " +
        error +
        " - Phản hồi: " +
        xhr.responseText
    );
  });
}

function loadCart(showModal = false) {
  if ($("#modal-cart").length === 0) {
    console.warn("Loading giohang.php dynamically.");
    $.get("giohang.php", function (data) {
      $("body").append(data);
      renderCart(showModal);
    }).fail(function (xhr, status, error) {
      console.error(
        "Failed to load giohang.php:",
        status,
        error,
        xhr.responseText
      );
      alert("Lỗi: Không thể tải giỏ hàng!");
    });
  } else {
    renderCart(showModal);
  }
}

function renderCart(showModal = false) {
  $.post(
    "handle/cart.php",
    {action: "get"},
    function (response) {
      console.log("Phản hồi từ loadCart:", response);

      let total = 0,
        totalQuantity = 0,
        cartHtml = "";

      if (!response.cart || response.cart.length === 0) {
        cartHtml = `<div class="empty-cart">
                            <i class="fa-solid fa-shopping-cart"></i>
                            <p>Giỏ hàng của bạn đang trống!</p>
                        </div>`;
        if ($("#cart-count").length) {
          $("#cart-count").text("").hide();
        }
      } else {
        response.cart.forEach((item) => {
          if (
            !item.ProductID ||
            !item.ProductName ||
            !item.Price ||
            !item.ProductImage ||
            !item.Quantity
          ) {
            console.error("Dữ liệu sản phẩm không đầy đủ:", item);
            return;
          }

          const price = parseFloat(item.Price) || 0;
          const quantity = parseInt(item.Quantity) || 0;
          total += price * quantity;
          totalQuantity += quantity;

          cartHtml += `<div class="cart-item" data-id="${item.ProductID}">
                    <img src="./${item.ProductImage}" alt="${
            item.ProductName
          }" width="50">
                    <div class="cart-info">
                        <span class="cart-name">${item.ProductName}</span>
                        <span class="cart-price">${price.toLocaleString(
                          "vi-VN"
                        )}đ</span>
                        <div class="cart-quantity">
                            <button class="decrease-qty">−</button>
                            <span class="quantity-value">${quantity}</span>
                            <button class="increase-qty">+</button>
                        </div>
                    </div>
                    <button class="remove-cart"><i class="fa-solid fa-trash"></i></button>
                </div>`;
        });

        if (totalQuantity > 0 && $("#cart-count").length) {
          $("#cart-count").text(totalQuantity).show();
        }
      }

      $("#cart-items").html(cartHtml);
      if ($(".total-price").length) {
        $(".total-price").text(total.toLocaleString("vi-VN") + "đ");
      }

      if (showModal) {
        $("#modal-cart").css("display", "block");
      }
    },
    "json"
  ).fail(function (xhr, status, error) {
    console.error("Lỗi AJAX trong loadCart:", status, error, xhr.responseText);
    alert("Lỗi khi tải giỏ hàng!");
  });
}

function build(mode) {
  $("#Authentication-Form").load("layout/login_layout.php?mode=" + mode);
}

function openModal(product) {
  if (!product.id || product.id === "undefined") {
      console.error("product.id không hợp lệ:", product);
      alert("Không thể mở chi tiết sản phẩm: Mã sản phẩm không hợp lệ!");
      return;
  }

  let modal = document.getElementById("productModal");
  if (!modal) {
      console.warn("Không tìm thấy #productModal trong DOM. Tạo modal động...");
      // Tạo modal động với HTML giống trong contentt.php
      const modalHTML = `
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
      `;
      // Thêm modal vào body
      document.body.insertAdjacentHTML('beforeend', modalHTML);
      modal = document.getElementById("productModal");
      if (!modal) {
          console.error("Không thể tạo #productModal.");
          alert("Lỗi: Không thể hiển thị chi tiết sản phẩm!");
          return;
      }
  }

  updateModalContent(product);
  modal.style.display = "block";
}

function updateModalContent(product) {
  try {
      const modal = document.getElementById("productModal");
      if (!modal) {
          throw new Error("Không tìm thấy #productModal trong DOM.");
      }

      // Cập nhật nội dung modal
      document.getElementById("modal-img").src = "./" + (product.image || './img/default.jpg');
      document.getElementById("modal-title").textContent = product.name || 'Không có tên';
      document.getElementById("modal-code").textContent = "Mã sản phẩm: " + (product.id || 'N/A');
      document.getElementById("modal-quantity").textContent = "Số lượng: " + (product.soluong || 0);
      document.getElementById("modal-price").textContent = Number(product.price || 0).toLocaleString("vi-VN") + "₫";
      document.getElementById("modal-description").textContent = product.mota || "Chưa có mô tả";

      // Đặt lại số lượng mặc định
      const quantityInput = document.getElementById("buy-now-quantity");
      if (quantityInput) {
          quantityInput.value = 1;
      }

      // Cập nhật data-id cho nút "Thêm vào giỏ"
      const cartIcon = document.getElementById("modal-cart-icon");
      if (cartIcon) {
          cartIcon.setAttribute("data-id", product.id);
      } else {
          console.warn("Phần tử modal-cart-icon không tồn tại.");
      }
  } catch (error) {
      console.error("Lỗi trong updateModalContent:", error);
      alert("Lỗi khi hiển thị chi tiết sản phẩm!");
  }
}


function closeModal() {
  const modal = document.getElementById("productModal");
  if (modal) {
      modal.style.display = "none";
  }
}

let searchTimeout;
function liveSearch(keyword, type = 'desktop') {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (keyword.trim() === "") {
            const resultBox = type === 'desktop' ? 
                document.getElementById("searchResult") : 
                document.getElementById("mobile-search-result");
            
            if (resultBox) {
                resultBox.style.display = "none";
            }
            return;
        }

        fetch("handle/search.php", {
            method: "POST",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: "keyword=" + encodeURIComponent(keyword)
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            const resultBox = type === 'desktop' ? 
                document.getElementById("searchResult") : 
                document.getElementById("mobile-search-result");

            if (!resultBox) {
                console.warn(`Không tìm thấy phần tử ${type === 'desktop' ? 'searchResult' : 'mobile-search-result'} trong DOM.`);
                return;
            }

            if (data.products && data.products.length > 0) {
                let html = "";
                data.products.forEach(item => {
                    const safeItem = {
                        id: item.id || '',
                        name: item.name || 'Không có tên',
                        price: item.price || 0,
                        image: item.image || './img/default.jpg',
                        soluong: item.soluong || 0,
                        mota: item.mota || 'Chưa có mô tả'
                    };
                    html += `
                        <div class="search-item" onclick='openModal(${JSON.stringify(safeItem)})' title="Xem chi tiết ${safeItem.name}">
                            <img src="${safeItem.image}" alt="${safeItem.name}" loading="lazy">
                            <div class="info">
                                <div>${safeItem.name}</div>
                                <div class="price">${Number(safeItem.price).toLocaleString("vi-VN")}₫</div>
                            </div>
                        </div>
                    `;
                });
                resultBox.innerHTML = html;
                resultBox.style.display = "block";
            } else {
                resultBox.innerHTML = "<div style='padding: 10px; text-align: center;'>Không tìm thấy sản phẩm</div>";
                resultBox.style.display = "block";
            }
        })
        .catch(error => {
            console.error("Lỗi trong liveSearch:", error);
            const resultBox = type === 'desktop' ? 
                document.getElementById("searchResult") : 
                document.getElementById("mobile-search-result");
            
            if (resultBox) {
                resultBox.innerHTML = "<div style='padding: 10px; color: red;'>Lỗi khi tìm kiếm sản phẩm</div>";
                resultBox.style.display = "block";
            }
        });
    }, 300); 
}

let allDistricts = []; 

function initializePaymentModal() {
  console.log("Khởi tạo modal thanh toán...");
  const urlParams = new URLSearchParams(window.location.search);
  const isBuyNow = urlParams.get('buy_now') === '1';
  const productId = urlParams.get('product_id');

  $.ajax({
    type: "GET",
    url: "handle/customer_info.php",
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        let customer = response.data;
        let isAddressComplete =
          customer.name &&
          customer.phone &&
          customer.province_id &&
          customer.district_id &&
          customer.address_detail;

        if (!isAddressComplete) {
          $("#old_address").prop("disabled", true);
          $(".address-error").show();
          $("#new_address").prop("checked", true);
        } else {
          $("#old_address").prop("disabled", false);
          $(".address-error").hide();
          $("#old_address").prop("checked", true);
        }

        $("#receiver_name").val(customer.name || "");
        $("#phone_number").val(customer.phone || "");
        $("#address_detail").val(customer.address_detail || "");

        let citySelect = $("#city");
        citySelect.html('<option value="">-- Chọn tỉnh/thành phố --</option>');
        response.provinces.forEach(province => {
          citySelect.append(
            `<option value="${province.province_id}" ${customer.province_id === province.province_id ? 'selected' : ''}>
              ${province.name}
            </option>`
          );
        });

        allDistricts = response.districts || [];
        if (customer.province_id) {
          updateDistricts(customer.province_id);
          let districtSelect = $("#district");
          districtSelect.val(customer.district_id || "");
        } else {
          $("#district").html('<option value="">-- Chọn quận/huyện --</option>');
        }

        toggleAddressFields();
      } else {
        alert(response.message);
      }
    },
    error: function () {
      alert("Lỗi khi lấy thông tin khách hàng!");
    }
  });

  if (isBuyNow && productId) {
    loadBuyNowSummary(productId);
  } else {
    loadOrderSummary();
  }
}
function loadBuyNowSummary(productId) {
  $.ajax({
    type: "POST",
    url: "handle/buy_now.php",
    data: { action: "get_product", productId: productId },
    dataType: "json",
    success: function (response) {
      let htmlContent = "";
      if (response.status === "success" && response.product) {
        const quantity = response.quantity || 1;
        htmlContent = `
          <div class="cart-item" data-id="${response.product.masp}" data-price="${response.product.dongiasanpham}">
            <div class="cart-info">
              <span class="cart-name">${response.product.tensp}</span>
              <span class="cart-quantity">Số lượng: ${quantity}</span>
            </div>
          </div>
        `;
        $("#list-order-payment").html(htmlContent);
        $("#payment-cart-price-final").text((response.product.dongiasanpham * quantity).toLocaleString('vi-VN') + "đ");
      } else {
        $("#list-order-payment").html("<p>Sản phẩm không tồn tại.</p>");
        $("#payment-cart-price-final").text("0đ");
      }
    },
    error: function () {
      $("#list-order-payment").html("<p>Lỗi khi tải thông tin sản phẩm.</p>");
    }
  });
}

function updateDistricts(provinceId) {
  console.log("Gọi updateDistricts với provinceId:", provinceId);
  let districtSelect = $("#district");
  districtSelect.html('<option value="">-- Chọn quận/huyện --</option>');

  if (provinceId) {

      let filteredDistricts = allDistricts.filter(district => district.province_id === provinceId);
      console.log("Danh sách quận/huyện lọc được:", filteredDistricts);

      filteredDistricts.forEach(district => {
          districtSelect.append(
              `<option value="${district.district_id}">
                  ${district.name}
              </option>`
          );
      });
  }
}

function toggleAddressFields() {
  const selectedOption = $('input[name="address_option"]:checked').val();
  if (selectedOption === "old") {
      $("#receiver_name").prop("readonly", true);
      $("#phone_number").prop("readonly", true);
      $("#address_detail").prop("readonly", true);
      $("#city").prop("disabled", true);
      $("#district").prop("disabled", true);
  } else {
      $("#receiver_name").prop("readonly", false);
      $("#phone_number").prop("readonly", false);
      $("#address_detail").prop("readonly", false);
      $("#city").prop("disabled", false);
      $("#district").prop("disabled", false);
  }
}

// Load thông tin đơn hàng
function loadOrderSummary() {
  console.log("Loading cart items via AJAX from handle/cart.php.");
  $.ajax({
      type: "POST",
      url: "handle/cart.php",
      data: { action: "get" },
      dataType: "json",
      success: function (response) {
          console.log("Phản hồi từ cart.php:", response);
          let htmlContent = "";

          if (response.status === "success" && response.cart && response.cart.length > 0) {

              htmlContent = response.cart.map(item => `
                  <div class="cart-item" data-id="${item.ProductID}">
                      <div class="cart-info">
                          <span class="cart-name">${item.ProductName}</span>
                          <span class="cart-quantity">Số lượng: ${item.Quantity}</span>
                      </div>
                  </div>
              `).join("");
          } else {
              htmlContent = `
                  <div class="empty-cart" id="empty-cart">
                      <p>Giỏ hàng trống.</p>
                  </div>
              `;
          }

          $("#list-order-payment").html(htmlContent);


          console.log("CSS display của #list-order-payment:", $("#list-order-payment").css("display"));
          console.log("CSS visibility của #list-order-payment:", $("#list-order-payment").css("visibility"));

          // Cập nhật tổng tiền
          if (response.status === "success" && response.cart) {
              let totalPrice = 0;
              response.cart.forEach(item => {
                  totalPrice += item.Price * item.Quantity;
              });
              $("#payment-cart-price-final").text(totalPrice.toLocaleString('vi-VN') + "đ");
          } else {
              $("#payment-cart-price-final").text("0đ");
              console.warn("Không lấy được dữ liệu giỏ hàng từ cart.php:", response);
          }
      },
      error: function (xhr, status, error) {
          console.error("Lỗi khi tải dữ liệu giỏ hàng:", status, error, xhr.responseText);
          $("#list-order-payment").html("<p>Lỗi khi tải thông tin đơn hàng.</p>");
      }
  });
}
// Xử lý đặt hàng
function placeOrder() {
  const receiverName = $("#receiver_name").val();
  const phoneNumber = $("#phone_number").val();
  const addressDetail = $("#address_detail").val();
  const cityId = $("#city").val();
  const districtId = $("#district").val();
  const addressOption = $('input[name="address_option"]:checked').val();
  const paymentMethod = $('input[name="payment_method"]:checked').val();

  if (!receiverName) {
    alert("Vui lòng nhập tên người nhận!");
    $("#receiver_name").focus();
    return;
  }
  if (!phoneNumber) {
    alert("Vui lòng nhập số điện thoại!");
    $("#phone_number").focus();
    return;
  }

  // Kiểm tra định dạng số điện thoại (10 chữ số)
  const phoneRegex = /^\d{10}$/;
  if (!phoneRegex.test(phoneNumber)) {
      alert("Số điện thoại không hợp lệ! Vui lòng nhập 10 chữ số.");
      $("#phone_number").focus();
      return;
  }

  if (!addressDetail) {
      alert("Vui lòng nhập địa chỉ chi tiết!");
      $("#address_detail").focus();
      return;
  }
  if (!cityId) {
    alert("Vui lòng chọn tỉnh/thành phố!");
    $("#city").focus();
    return;
  }

  if (!districtId) {
      alert("Vui lòng chọn quận/huyện!");
      $("#district").focus();
      return;
  }
  let cardNumber, expiryDate, cvv;
    if (paymentMethod === "online") {
        cardNumber = $("#so-the").val().trim();
        expiryDate = $("#ngay-het-han").val().trim();
        cvv = $("#cvv").val().trim();

        if (!cardNumber) {
            alert("Vui lòng nhập số thẻ!");
            $("#so-the").focus();
            return;
        }

        const cardRegex = /^[0-9]{6}$/;
        if (!cardRegex.test(cardNumber)) {
            alert("Số thẻ không hợp lệ! Vui lòng nhập 6 chữ số.");
            $("#so-the").focus();
            return;
        }

        if (!expiryDate) {
            alert("Vui lòng nhập ngày hết hạn!");
            $("#ngay-het-han").focus();
            return;
        }

        if (!cvv) {
            alert("Vui lòng nhập mã CVV!");
            $("#cvv").focus();
            return;
        }

        const cvvRegex = /^[0-9]{3}$/;
        if (!cvvRegex.test(cvv)) {
            alert("Mã CVV không hợp lệ! Vui lòng nhập 3 chữ số.");
            $("#cvv").focus();
            return;
        }
    }

  const data = {
      receiver_name: receiverName,
      phone_number: phoneNumber,
      address_detail: addressDetail,
      city_id: cityId,
      district_id: districtId,
      address_option: addressOption,
      payment_method: paymentMethod
  };

  if (paymentMethod === "online") {
      const cardNumber = $("#so-the").val();
      const expiryDate = $("#ngay-het-han").val();
      const cvv = $("#cvv").val();

      if (!cardNumber || !expiryDate || !cvv) {
          alert("Vui lòng điền đầy đủ thông tin thẻ!");
          return;
      }

      data["so-the"] = cardNumber;
      data["ngay-het-han"] = expiryDate;
      data["cvv"] = cvv;
  }

  $.ajax({
      type: "POST",
      url: "handle/order.php",
      data: data,
      dataType: "json",
      success: function (response) {
          if (response.status === "success") {
              alert("Đặt hàng thành công!");
              $("#payment-modal").css("display", "none");
              showInvoiceModal(response.invoice, true); // Truyền fromCheckout: true
          } else {
              alert("Lỗi khi đặt hàng: " + response.message);
          }
      },
      error: function (xhr, status, error) {
          console.error("Lỗi khi đặt hàng:", status, error);
          alert("Lỗi khi đặt hàng: " + error);
      }
  });
}

function showPreviewOrder() {
  let addressOption = $('input[name="address_option"]:checked').val();
  let receiverName = $("#receiver_name").val().trim();
  let phoneNumber = $("#phone_number").val().trim();
  let addressDetail = $("#address_detail").val().trim();
  let city = $("#city option:selected").text();
  let district = $("#district option:selected").text();

  let previewHtml = `
      <div class="preview-order-modal">
          <h2>Tổng quan đơn hàng</h2>
          <div><strong>Tên người nhận:</strong> ${receiverName}</div>
          <div><strong>Số điện thoại:</strong> ${phoneNumber}</div>
          <div><strong>Địa chỉ:</strong> ${addressDetail}, ${district}, ${city}</div>
          <div><strong>Tổng tiền:</strong> ${$("#payment-cart-price-final").text()}</div>
          <button onclick="$('.preview-order-modal').remove();">Đóng</button>
      </div>
  `;
  $("body").append(previewHtml);
}

$(document).on("click", ".btn-muanhanh", function () {
  let productId = $("#modal-cart-icon").attr("data-id");
  let quantity = parseInt($("#buy-now-quantity").val());
  let stock = parseInt($("#modal-quantity").text().replace("Kho: ", ""));

  if (!productId || productId === "undefined") {
      alert("Không thể mua sản phẩm: Mã sản phẩm không hợp lệ!");
      return;
  }

  if (isNaN(quantity) || quantity < 1) {
      alert("Vui lòng nhập số lượng hợp lệ (tối thiểu 1)!");
      return;
  }

  if (quantity > stock) {
      alert("Số lượng yêu cầu vượt quá tồn kho!");
      return;
  }

  $.ajax({
      type: "POST",
      url: "handle/auth.php",
      data: { action: "check" },
      dataType: "json",
      success: function (response) {
          if (response.loggedIn) {
              $.ajax({
                  type: "POST",
                  url: "handle/buy_now.php",
                  data: { action: "prepare", productId: productId, quantity: quantity },
                  dataType: "json",
                  success: function (buyNowResponse) {
                      if (buyNowResponse.status === "success") {
                          closeModal();
                          window.location.href = "index.php?page=checkout&buy_now=1&product_id=" + productId;
                      } else {
                          alert("Lỗi: " + buyNowResponse.message);
                      }
                  },
                  error: function () {
                      alert("Lỗi khi xử lý mua ngay!");
                  }
              });
          } else {
              $("#overlay").show();
              alert("Vui lòng đăng nhập để mua hàng!");
          }
      },
      error: function () {
          alert("Lỗi kiểm tra trạng thái đăng nhập!");
      }
  });
});

function showPreviewOrder() {
  const receiverName = $("#receiver_name").val();
  const phoneNumber = $("#phone_number").val();
  const addressDetail = $("#address_detail").val();
  const city = $("#city option:selected").text();
  const district = $("#district option:selected").text();
  const totalPrice = $("#payment-cart-price-final").text();

  alert(
      `Đơn hàng của bạn:\n` +
      `Tên người nhận: ${receiverName}\n` +
      `Số điện thoại: ${phoneNumber}\n` +
      `Địa chỉ: ${addressDetail}, ${district}, ${city}\n` +
      `Tổng tiền: ${totalPrice}`
  );
}

function openOrderOverviewModal() {
  // Lấy modal
  const modal = document.getElementById('order-overview-modal');
  const modalBody = modal.querySelector('.modal-body');

  // Xóa nội dung cũ trong modal-body
  modalBody.innerHTML = '';

  // Lấy danh sách sản phẩm từ #list-order-payment
  const orderItems = document.querySelectorAll('#list-order-payment .cart-item');
  if (orderItems.length === 0) {
      modalBody.innerHTML = '<p class="empty-order">Không có sản phẩm nào trong giỏ hàng.</p>';
  } else {
      orderItems.forEach(item => {
          const name = item.querySelector('.cart-name').textContent;
          const quantity = item.querySelector('.cart-quantity').textContent;
          const price = item.getAttribute('data-price') || '0'; 

          const orderItem = `
              <div class="order-item">
                  <div class="item-info">
                      <p class="item-name">${name}</p>
                      <p class="item-quantity">${quantity}</p>
                  </div>
                  <p class="item-price">${price}đ</p>
              </div>
          `;
          modalBody.insertAdjacentHTML('beforeend', orderItem);
      });

      const totalPrice = document.getElementById('payment-cart-price-final').textContent;
      const totalSection = `
          <div class="order-total">
              <p class="text">Tổng cộng</p>
              <p class="price-final">${totalPrice}đ</p>
          </div>
      `;
      modalBody.insertAdjacentHTML('beforeend', totalSection);
  }

  // Hiển thị modal
  modal.classList.add('active');
}

function closeOrderOverviewModal() {
  const modal = document.getElementById('order-overview-modal');
  modal.classList.remove('active');
}

// Đóng modal khi nhấn vào backdrop
document.addEventListener('click', function(e) {
  const modal = document.getElementById('order-overview-modal');
  if (modal && e.target === modal) {
    closeOrderOverviewModal();
  }
});


function showInvoiceModal(invoice, fromCheckout = false) {
  let addressDisplay = `${invoice.address.split(", ")[0]} (Chưa xác định quận/huyện, tỉnh/thành phố)`;

  console.log("showInvoiceModal called with fromCheckout:", fromCheckout);

  $.ajax({
      type: "GET",
      url: "handle/get_location.php",
      data: {
          province_id: invoice.address.split(", ")[2],
          district_id: invoice.address.split(", ")[1]
      },
      dataType: "json",
      success: function (location) {
          if (location.status === "success") {
              addressDisplay = `${invoice.address.split(", ")[0]}, ${location.district_name}, ${location.province_name}`;
          }
          renderInvoiceModal(invoice, addressDisplay, fromCheckout);
      },
      error: function () {
          console.error("Lỗi khi lấy thông tin địa chỉ, sử dụng địa chỉ mặc định.");
          renderInvoiceModal(invoice, addressDisplay, fromCheckout);
      }
  });
}

function renderInvoiceModal(invoice, addressDisplay, fromCheckout) {
  let itemsHtml = invoice.items.map(item => `
      <tr>
          <td>${item.tensp}</td>
          <td>${item.soluong}</td>
          <td>${Number(item.dongiasanpham).toLocaleString('vi-VN')}₫</td>
          <td>${Number(item.thanhtien).toLocaleString('vi-VN')}₫</td>
      </tr>
  `).join('');

  let invoiceHtml = `
      <div class="invoice-modal">
          <div class="invoice-content">
              <h2>HÓA ĐƠN THANH TOÁN</h2>
              <p><strong>Mã hóa đơn:</strong> ${invoice.bill_id}</p>
              <p><strong>Mã đơn hàng:</strong> ${invoice.order_id}</p>
              <p><strong>Ngày đặt hàng:</strong> ${new Date(invoice.order_date).toLocaleDateString('vi-VN')}</p>
              <p><strong>Tên người nhận:</strong> ${invoice.receiver_name}</p>
              <p><strong>Số điện thoại:</strong> ${invoice.phone_number}</p>
              <p><strong>Địa chỉ giao hàng:</strong> ${addressDisplay}</p>
              <p><strong>Phương thức thanh toán:</strong> ${invoice.payment_method}</p>
              <table class="invoice-table">
                  <thead>
                      <tr>
                          <th>Sản phẩm</th>
                          <th>Số lượng</th>
                          <th>Đơn giá</th>
                          <th>Thành tiền</th>
                      </tr>
                  </thead>
                  <tbody>
                      ${itemsHtml}
                  </tbody>
              </table>
              <p><strong>Tổng tiền:</strong> ${Number(invoice.total_price).toLocaleString('vi-VN')}₫</p>
              <div class="invoice-actions">
                  <button class="close-invoice">Đóng</button>
              </div>
          </div>
      </div>
  `;

  $("body").append(invoiceHtml);

  // Gắn sự kiện đóng modal hóa đơn
  $(".close-invoice").on("click", function (event) {
      event.stopPropagation();
      console.log("Close invoice clicked, fromCheckout:", fromCheckout);
      console.log("Current page:", new URLSearchParams(window.location.search).get('page'));
      $(".invoice-modal").fadeOut(300, function() {
          $(this).remove();
          if (fromCheckout) {
              console.log("Redirecting to index.php?page=sanpham");
              window.location.href = "index.php?page=sanpham";
          } else {
              console.log("Staying on current page (hoadon)");
              // Không chuyển hướng, giữ nguyên trang hoadon
          }
      });
  });
}
// Hàm tìm kiếm hóa đơn
function searchInvoices(keyword, invoices, tbodySelector) {
  let filteredInvoices = invoices.filter(invoice => {
      return (
          invoice.bill_id.toLowerCase().includes(keyword.toLowerCase()) ||
          invoice.order_id.toLowerCase().includes(keyword.toLowerCase()) ||
          invoice.receiver_name.toLowerCase().includes(keyword.toLowerCase()) ||
          invoice.phone_number.includes(keyword.toLowerCase()) ||
          new Date(invoice.order_date).toLocaleString('vi-VN').toLowerCase().includes(keyword.toLowerCase())
      );
  });

  // Cập nhật bảng hóa đơn
  let tbodyHtml = "";
  if (filteredInvoices.length === 0) {
      tbodyHtml = `
          <tr>
              <td colspan="7" style="text-align: center;">Không tìm thấy hóa đơn phù hợp!</td>
          </tr>
      `;
  } else {
      filteredInvoices.forEach(invoice => {
          tbodyHtml += `
              <tr>
                  <td>${invoice.bill_id}</td>
                  <td>${invoice.order_id}</td>
                  <td>${new Date(invoice.order_date).toLocaleString('vi-VN')}</td>
                  <td>${invoice.receiver_name}</td>
                  <td>${invoice.phone_number}</td>
                  <td>${Number(invoice.total_price).toLocaleString('vi-VN')}₫</td>
                  <td>
                      <button class="view-invoice-detail" data-invoice='${JSON.stringify(invoice)}'>Xem chi tiết</button>
                  </td>
              </tr>
          `;
      });
  }
  $(tbodySelector).html(tbodyHtml);

  // Gắn lại sự kiện cho các nút "Xem chi tiết"
  $(".view-invoice-detail").on("click", function () {
      const invoice = $(this).data("invoice");
      showInvoiceModal(invoice);
  });
}



$(document).ready(function() {
  // Mở modal giỏ hàng từ menu mobile
  $(document).ready(function() {
    // Mở modal giỏ hàng từ menu mobile
    $("#mobile-menu .mobile-menu-content a[href='index.php?page=giohang']").on("click", function(e) {
        e.preventDefault();
        console.log("Nhấn vào mục GIỎ HÀNG trong menu mobile.");
        if ($("#modal-cart").length) {
            console.log("Modal #modal-cart tồn tại, hiển thị...");
            $("#modal-cart").fadeIn(300).css("display", "block");
            $("#mobile-menu").fadeOut(300);
            renderCart(true);
        } else {
            console.error("Modal #modal-cart không tồn tại, thử tải giohang.php...");
            $.get("giohang.php", function(data) {
                console.log("giohang.php tải thành công, thêm vào DOM.");
                $("body").append(data);
                if ($("#modal-cart").length) {
                    $("#modal-cart").fadeIn(300).css("display", "block");
                    $("#mobile-menu").fadeOut(300);
                    renderCart(true);
                } else {
                    console.error("giohang.php không chứa #modal-cart!");
                    alert("Lỗi: Không thể hiển thị giỏ hàng. Vui lòng kiểm tra console.");
                }
            }).fail(function(xhr, status, error) {
                console.error("Lỗi tải giohang.php:", status, error, xhr.responseText);
                alert("Lỗi: Không thể tải giỏ hàng! Vui lòng kiểm tra console.");
            });
        }
    });

    // Mở menu mobile
    $("#hamburger-menu").on("click", function() {
        $("#mobile-menu").fadeIn(300).css("display", "flex");
    });

    // Đóng menu mobile
    $("#close-menu").on("click", function() {
        $("#mobile-menu").fadeOut(300);
    });

    // Đóng menu mobile khi nhấn liên kết
    $(".mobile-menu-content a").on("click", function() {
        $("#mobile-menu").fadeOut(300);
    });
  });
});


$(document).ready(function() {
  // Tải trước giohang.php để đảm bảo #modal-cart tồn tại
  if ($("#modal-cart").length === 0) {
      console.log("Tải trước giohang.php...");
      $.get("giohang.php", function(data) {
          $("body").append(data);
          console.log("giohang.php đã được tải vào DOM.");
      }).fail(function(xhr, status, error) {
          console.error("Lỗi tải giohang.php:", status, error, xhr.responseText);
          alert("Lỗi: Không thể tải giỏ hàng! Vui lòng kiểm tra console.");
      });
  }

  // Xử lý hamburger menu
  if ($("#hamburger-menu").length && $("#mobile-menu").length && $("#close-menu").length) {
      $("#hamburger-menu").on("click", function() {
          $("#mobile-menu").fadeIn(300).css("display", "flex");
      });

      $("#close-menu").on("click", function() {
          $("#mobile-menu").fadeOut(300);
      });
  } else {
      console.warn("Một hoặc nhiều phần tử hamburger-menu, mobile-menu, close-menu không tồn tại trong DOM.");
  }

  // Mở modal giỏ hàng từ menu mobile
  $("#mobile-menu .mobile-menu-content a[href='index.php?page=giohang']").on("click", function(e) {
    e.preventDefault();
    console.log("Nhấn vào mục GIỎ HÀNG trong menu mobile.");
    if ($("#modal-cart").length) {
        $("#modal-cart").fadeIn(300).css("display", "block");
        $("#mobile-menu").fadeOut(300);
        renderCart(true);
    } else {
        $.get("giohang.php", function(data) {
            $("body").append(data);
            if ($("#modal-cart").length) {
                $("#modal-cart").fadeIn(300).css("display", "block");
                $("#mobile-menu").fadeOut(300);
                renderCart(true);
            }
        });
    }
});

  // Đóng menu mobile khi nhấn vào bất kỳ liên kết nào
  $(".mobile-menu-content a").on("click", function() {
      $("#mobile-menu").fadeOut(300);
  });

});