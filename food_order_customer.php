<style>
.order_food_heading {
    background-color: #118EEA;
    color: white;
    text-align: center;
}

.product {
    display: flex;
    flex-wrap: wrap;
}

.product_item {
    flex-basis: calc(50% - 20px);
    /* Thêm 20px để trừ khoảng cách giữa các sản phẩm */
    padding: 10px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
}


.product_img {
    flex-basis: 33.33%;
    justify-content: center;
}

.product_img img {
    max-width: 70%;
    /* Hình ảnh hình vuông nên max 1 trong 2 là dc*/
    height: auto;
    /* Đảm bảo tỷ lệ hình ảnh được giữ nguyên */
}


.product_info {
    flex-basis: 50%;
    flex-basis: 66.67%;

}

.product_info h3 {
    margin-top: 0;
    color: #1A2C50;
}

.product_info p {
    margin-bottom: 5px;
}

.product_info .quantity {
    display: flex;
    align-items: center;
}

.product_info .quantity .btn {
    background-color: #118EEA;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}

.product_info .quantity .value {
    border: 1px solid #ccc;
    padding: 5px;
    width: 20px;
    text-align: center;
    margin: 0 10px;
}

.next-button {
    background-color: #1A2C50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 80px;
    margin-bottom: 20px;
    float: right;
}

ol {
    list-style-type: disc;
}
</style>

<?php
require_once 'db_module.php';

// Tạo kết nối
$link = NULL;
taoKetNoi($link);

// Truy vấn để lấy dữ liệu từ bảng combo_food
$query = "SELECT * FROM combo_food WHERE is_deleted = '0'";
$result = chayTruyVanTraVeDL($link, $query);

// Khởi tạo một mảng để lưu trữ dữ liệu sản phẩm
$products = array();

// Kiểm tra xem có kết quả không
if (mysqli_num_rows($result) > 0) {
    // Lặp qua mỗi hàng kết quả và lưu trữ vào mảng
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
} else {
    echo "Không có sản phẩm nào.";
}

// Giải phóng bộ nhớ
giaiPhongBoNho($link, $result);

// Hàm hiển thị thông tin sản phẩm
function hienThiSanPham($product)
{
    echo '<div class="product_item">';
    echo '<div class="product_img">';
    echo '<img src="' . $product['img_url'] . '" alt="Product Image">';
    echo '</div>';
    echo '<div class="product_info">';
    echo '<h3 class="combo-name">' . $product['combo_name'] . '</h3>';
    echo $product['description'];
    // Định dạng số tiền
    $formatted_price = number_format($product['price'], 0, ',', '.'); // Tham số 2 là thể hiện stp ví dụ 1 thì 2.000,0; ts 3 là dấu stp, 4 là dấu phần ngàn
    echo '<p>Giá: <b class="price">' . $formatted_price . ' VNĐ</b></p>'; // In đậm số tiền và chèn dấu chấm mỗi phần ngàn
    echo '<div class="quantity">';
    echo '<button class="btn minus">-</button>';
    echo '<div class="value">0</div>';
    echo '<button class="btn plus">+</button>';
    echo '</div>';
    echo '<input type="hidden" name="combo_id[]" value="' . $product['combo_id'] . '">';
    echo '</div>';
    echo '</div>';
}
?>


<link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">

<div class="order_food_container">
    <?php
    include "header.php";
    ?>
    <div class="order_food_heading">
        <h2>Bắp nước</h2>
    </div>

    <div class="product">
        <?php
        // Lặp qua mỗi sản phẩm trong mảng để hiển thị
        foreach ($products as $product) {
            hienThiSanPham($product);
        }
        ?>
    </div>


    <button class="next-button" name="next">Tiếp theo</button>

    <?php
    include "footer.php";
    ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.product .product_item .quantity').forEach(function(item) {
        var valueElement = item.querySelector('.value');
        var minusButton = item.querySelector('.minus');
        var plusButton = item.querySelector('.plus');
        var value = 0;

        function updateValue() {
            valueElement.textContent = value;
        }

        minusButton.addEventListener('click', function() {
            if (value > 0) {
                value--;
                updateValue();
            }
        });

        plusButton.addEventListener('click', function() {
            if (value < 5) { // Giới hạn số lượng tối đa là 5
                value++;
                updateValue();
            }
        });
    });

    // Bắt sự kiện submit của form
    document.querySelector('.next-button').addEventListener('click', function(event) {
        // Lặp qua mỗi sản phẩm để lấy combo_id và quantity và cập nhật vào input ẩn
        console.log('window href')

        var selectedQuantities = [];
        document.querySelectorAll('.product_item').forEach(function(productItem) {
            var comboId = productItem.querySelector('input[name="combo_id[]"]').value;
            var quantity = parseInt(productItem.querySelector('.quantity .value').textContent);
            var price = parseInt(productItem.querySelector('.price').textContent.replace(
                /[,.]/g, ''));
            const name = productItem.querySelector('.combo-name').textContent;
            selectedQuantities.push({
                comboId: comboId,
                quantity: quantity,
                price: price,
                name: name
            });
        });
        localStorage.setItem('selectedFood', JSON.stringify(selectedQuantities));

        // Cập nhật giá trị vào input ẩn
        window.location.href = 'confirm-payment.php'

    });
});
</script>