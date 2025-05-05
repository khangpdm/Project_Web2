$(document).ready(function () {
    // Khởi tạo trạng thái ban đầu
    $("#overlay").hide();
    $("#open-form-log-in__dropdown").hide();

    $("#open-form-log-in").on("click", function () {
        $.ajax({
            data: { action: "check" },
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
            data: { action: "logout" },
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

    // Đóng form đăng nhập khi nhấp ra ngoài
    $("#overlay").on("click", function (event) {
        if ($(event.target).closest(".Authentication-Form-Dad").length === 0) {
            $("#overlay").hide();
        }
    });

    $.ajax({
        data: { action: "check" },
        type: "POST",
        url: "handle/auth.php",
        dataType: "json",
        success: function (response) {
            if (response.staffname !== undefined) {
                $(".left-panel").eq(0).append(
                    '<div class="greeting-message" style="font-style: italic; max-width: 300px;">Xin chào, ' +
                    response.staffname +
                    "!</div>"
                );
            }
        },
    });

    // Xử lý submit form đăng nhập
    $("#Authentication-Form").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "handle/login.php",
            data: data,
            dataType: "json",
            success: function (response) {
                console.log("Login response:", response);
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
            error: function (xhr, status, error) {
                console.error("Login AJAX error:", status, error);
                console.log("Response text:", xhr.responseText);
            }
        });
    });

    function checkPowerGroup() {
        setTimeout(function () {
            $.ajax({
                type: "POST",
                url: "handle/auth.php",
                data: { action: "check_powergroup" },
                dataType: "json",
                success: function (response) {
                    // Hàm lấy thông tin page từ URL
                    function getPageFromURL() {
                        const UrlParams = new URLSearchParams(window.location.search);
                        return UrlParams.get("page")?.toUpperCase();
                    }

                    let currentModule = getPageFromURL();

                    // BUG
                    // Map page names to permission ids if needed
                    // const pagePermissionMap = {
                    //     "STAFF": "NHANVIEN"
                    // };

                    let permissionKey = currentModule;

                    // 1. Ẩn toàn bộ menu trước
                    $(".ajax-link").each(function () {
                        const page = $(this).attr("href").split("page=")[1]?.toUpperCase();

                        if (page === "HOME") {
                            $(this).show();
                            return; // bỏ qua xử lý tiếp theo
                        }

                        // Always show menu for admin powergroupid = 1
                        if (response['powergroupid'] === 1) {
                            $(this).show();
                            return;
                        }

                        if (response[page] && response[page].includes("xem")) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });

                    if (response[permissionKey]
                        || response['powergroupid'] === 1
                    ) {
                        // Hiển thị module nếu có quyền or if admin
                        $("." + currentModule).show();

                        // Ẩn tất cả các nút hành động trước khi hiển thị chúng dựa trên quyền của module hiện tại
                        $(".permission-sua, .permission-xoa, .permission-them").hide();
                        console.log(permissionKey);
                        console.log(response[permissionKey]);
                        // Lặp qua quyền của module hiện tại để hiển thị các nút hành động
                        response[permissionKey].forEach(function (action) {
                            // Kiểm tra quyền cho các hành động như "sua", "xoa", "them"
                            if (action === "sua") {
                                $(".permission-sua").show();
                            } else if (action === "xoa") {
                                $(".permission-xoa").show();
                            } else if (action === "them") {
                                $(".permission-them").show();
                            }
                        });
                    } else {
                        // Nếu module hiện tại không có quyền nào, ẩn tất cả các nút hành động
                        $(".permission-sua, .permission-xoa, .permission-them").hide();
                    }
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 401) {
                        // Xử lý khi chưa đăng nhập
                        $("#overlay").show();

                        $("#content-wrapper").css({
                            "margin": "0",
                            "padding": "0",
                            "width": "100vw",
                            "height": "80vh",
                            "display": "flex",
                            "align-items": "center",
                            "justify-content": "center",
                            "flex-direction": "column",
                            "font-family": "'Segoe UI', 'Roboto', sans-serif",
                            "text-align": "center"
                        }).html(`
                            <img src="uploads/22-03-2025/Error 401.jpg" 
                                 alt="Lỗi 401 - Chưa đăng nhập" 
                                 style="max-width: 90%; width: 600px; height: auto; margin-bottom: 20px;" />
                            <p style="font-size: 24px; color: #333;">
                                401
                            </p>
                            <p style="font-size: 24px; color: #333;">
                                You don't have permission to access the resource.
                            </p>
                        `);

                    } else {
                        console.error("Lỗi khác:", xhr);
                    }
                },
            });
        }, 50);
    }

    checkPowerGroup();

    $(".ajax-link").on("click", function () {
        checkPowerGroup();
    });

    window.checkPowerGroup = checkPowerGroup;
});