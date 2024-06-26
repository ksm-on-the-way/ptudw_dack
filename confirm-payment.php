<?php
require_once './db_module.php';
session_start();
// Kết nối đến cơ sở dữ liệu
$link = null;
taoKetNoi($link);
if (isset($_POST['action']) && $_POST['action'] === 'saveReservation') {
    $show_id = $_POST['show_id'];
    $reservation_time = date("Y-m-d h:i:s");
    $selectedSeats = json_decode($_POST['selectedSeats'], true);
    $selectedFood = json_decode($_POST['selectedFood'], true);
    $logFilePath = dirname(__DIR__) . '/ptudw_dack' . '/log/file.log';

    // Ghi giá trị của $user_data vào file log
    file_put_contents($logFilePath, print_r($selectedSeats, true) . PHP_EOL, FILE_APPEND);
    // Thêm dữ liệu vào bảng reservations
    $sql = "INSERT INTO reservations (reservation_time, show_id, user_id) VALUES ('$reservation_time', '$show_id', '$_SESSION[userid]')";
    $result = chayTruyVanKhongTraVeDL($link, $sql);

    // Lấy ID của reservation vừa được tạo
    $reservation_id = mysqli_insert_id($link);

    // Thêm dữ liệu vào bảng reservation_details
    foreach ($selectedSeats as $seat) {
        $seat_id = $seat['id'];
        $sqlRsv = "INSERT INTO reservation_details (rsv_id, seat_id) VALUES ('$reservation_id', '$seat_id')";
        $resultReservationDetail = chayTruyVanKhongTraVeDL($link, $sqlRsv);
    }
    // Thêm dữ liệu vào bảng combo_order
    $sqlOrder = "INSERT INTO combo_order (reservation_id) VALUES ('$reservation_id')";
    $resultOrder = chayTruyVanKhongTraVeDL($link, $sqlOrder);

    // Lấy ID của combo_order vừa được tạo
    $combo_order_id = mysqli_insert_id($link);
    // Thêm dữ liệu vào bảng combo_order_details
    foreach ($selectedFood as $food) {
        $combo_id = $food['comboId'];
        $quantity = $food['quantity']; // Số lượng được giả định là 1, bạn có thể điều chỉnh tùy thuộc vào logic của bạn
        if ($quantity > 0) {
            $sqlComboOrder = "INSERT INTO combo_order_details (combo_order_id, combo_id, quantity) VALUES ('$combo_order_id', '$combo_id', '$quantity')";
            $resultComboOrder = chayTruyVanKhongTraVeDL($link, $sqlComboOrder);

        }
    }
    giaiPhongBoNho($link, $result);

}
include_once ("header.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <style>
    .confirm-payment-schedule {

        column-gap: 100px;
        margin: 0 auto;
        padding: 0 10px;
    }

    @media (min-width: 764px) {
        .confirm-payment-schedule {
            display: grid;
            grid-template-columns: 644px 1fr;
            width: 1200px;
            padding: unset;
        }
    }

    .confirm-payment-title {
        color: var(--Shade-900, #333);
        width: 100%;
        font: 700 36px Roboto, sans-serif;
    }

    .confirm-payment-desc {
        color: var(--Shade-600, #5a637a);
        margin-top: 32px;
        width: 100%;
        font: 400 16px/150% Roboto, sans-serif;
    }

    .detail-schedule {
        display: flex;
        max-width: 421px;
        flex-direction: column;
    }


    .film-title-label {
        color: #414a63;
        font: 400 16px/150% Roboto, sans-serif;
    }

    .film-title {
        color: #333;
        margin-top: 8px;
        font: 500 24px Roboto, sans-serif;
    }

    .film-title-container {
        display: flex;
        margin-top: 36px;
        flex-direction: column;
    }

    .divider {
        background-color: #dadfe8;
        min-height: 1px;
        margin-top: 17px;
        width: 100%;
    }

    .date-label {
        color: #414a63;
        font: 400 16px/150% Roboto, sans-serif;
    }

    .date {
        color: #333;
        margin-top: 8px;
        font: 500 24px Roboto, sans-serif;
    }

    .date-container {
        display: flex;
        margin-top: 17px;
        flex-direction: column;
    }

    .class-hours-container {
        display: flex;
        margin-top: 17px;
        gap: 20px;
    }

    .class-label {
        color: #414a63;
        font: 400 16px/150% Roboto, sans-serif;
    }

    .class {
        color: #333;
        margin-top: 8px;
        font: 500 24px Roboto, sans-serif;
    }

    .class-container {
        display: flex;
        flex-direction: column;
    }

    .hours-label {
        color: #414a63;
        font: 400 16px/150% Roboto, sans-serif;
    }

    .hours {
        color: #333;
        margin-top: 8px;
        font: 500 24px Roboto, sans-serif;
    }

    .hours-container {
        display: flex;
        flex-direction: column;
        white-space: nowrap;
    }

    .ticket-label {
        color: #414a63;
        font: 400 16px/150% Roboto, sans-serif;
    }

    .ticket {
        color: #333;
        margin-top: 8px;
        font: 500 24px Roboto, sans-serif;
    }

    .ticket-container {
        align-self: start;
        display: flex;
        margin-top: 17px;
        flex-direction: column;
    }

    .back-button {
        display: flex;
        align-items: center;
        gap: 20px;
        font-size: 24px;
        color: #5a637a;
        font-weight: 700;
        white-space: nowrap;
        justify-content: space-between;
        font-family: Roboto, sans-serif;
        width: 50px;
    }

    @media (max-width: 991px) {
        .back-button {
            margin-bottom: 10px;
        }
    }

    .back-button:hover {
        cursor: pointer;
    }

    .back-icon {
        width: 32px;
        height: 32px;
    }

    .back-text {
        margin: 0;
    }

    /* left */
    .order-summary {
        border-radius: 13px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.3);
        border: 1px solid #c4c4c4;
        background-color: #fff;
        padding: 30px 32px 48px;
        display: flex;
        flex-direction: column;
        max-width: 499px;
        justify-content: center;
    }

    @media (max-width: 991px) {
        .order-summary {
            max-width: 100%;
        }
    }

    .order-summary-title {
        color: #333;
        font: 500 24px Roboto, sans-serif;
    }

    .transaction-details-title {
        color: #333;
        margin-top: 36px;
        font: 700 16px Roboto, sans-serif;
    }

    .regular-seat {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        gap: 20px;
        font-size: 16px;
        width: 100%;
    }

    .regular-seat-label {
        color: #333;
        font-family: Roboto, sans-serif;
        font-weight: 400;
    }

    .regular-seat-details {
        display: flex;
        gap: 12px;
        text-align: right;
        align-items: center;
    }

    .regular-seat-price {
        color: #333;
        font-family: Roboto, sans-serif;
        font-weight: 400;
        line-height: 150%;
    }

    .regular-seat-quantity {
        color: #414a63;
        font-family: Roboto, sans-serif;
        font-weight: 700;
    }

    .service-fee {
        display: flex;
        justify-content: space-between;
        margin-top: 7px;
        gap: 20px;
        font-size: 16px;
        width: 100%;
    }

    .service-fee-label {
        color: #333;
        font-family: Roboto, sans-serif;
        font-weight: 400;
    }

    .service-fee-details {
        display: flex;
        text-align: right;
        white-space: nowrap;
    }

    @media (max-width: 991px) {
        .service-fee-details {
            white-space: initial;
        }
    }

    .service-fee-price {
        color: #333;
        font-family: Roboto, sans-serif;
        font-weight: 400;
        line-height: 150%;
    }

    .service-fee-quantity {
        color: #414a63;
        font-family: Roboto, sans-serif;
        font-weight: 700;
    }

    .divider {
        background-color: #bdc5d4;
        margin-top: 27px;
        height: 1px;
    }

    .promo-voucher-title {
        color: #333;
        margin-top: 35px;
        font: 700 16px Roboto, sans-serif;
    }

    .promo-tix-id {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        gap: 20px;
        font-size: 16px;
        color: #333;
        font-weight: 400;
    }

    .promo-tix-id-label {
        font-family: Roboto, sans-serif;
    }

    .promo-tix-id-amount {
        font-family: Roboto, sans-serif;
        text-align: right;
        line-height: 150%;
        justify-content: center;
    }

    .total-payment {
        display: flex;
        justify-content: space-between;
        margin-top: 18px;
        gap: 20px;
        font-size: 16px;
        color: #333;
        font-weight: 700;
    }

    .total-payment-label {
        font-family: Roboto, sans-serif;
    }

    .total-payment-amount {
        font-family: Roboto, sans-serif;
    }

    .payment-method-title {
        display: flex;
        justify-content: space-between;
        margin-top: 32px;
        gap: 20px;
        font-weight: 700;
    }

    .payment-method-label {
        color: #333;
        font: 16px Roboto, sans-serif;
    }

    .payment-method-show-all {
        color: #118eea;
        font: 12px Roboto, sans-serif;
    }

    .payment-method-show-all:hover {
        cursor: pointer;
    }

    .dana-payment {
        display: flex;
        align-self: start;
        margin-top: 24px;
        gap: 16px;
        font-size: 18px;
        color: #333;
        font-weight: 400;
        line-height: 156%;
        white-space: nowrap;
    }

    @media (max-width: 991px) {
        .dana-payment {
            white-space: initial;
        }
    }

    .dana-logo {
        width: 58px;
        aspect-ratio: 3.57;
        object-fit: auto;
        object-position: center;
        align-self: start;
    }

    .dana-label {
        font-family: Roboto, sans-serif;
    }

    .non-refundable-notice {
        color: #ff6b6b;
        margin-top: 35px;
        font: 400 12px Roboto, sans-serif;
    }

    .buy-ticket-button {
        border-radius: 8px;
        background-color: #1a2c50;
        margin-top: 25px;
        color: #ffbe00;
        text-align: center;
        padding: 16px 12px;
        font: 500 24px/133% Roboto, sans-serif;
        justify-content: center;
        cursor: pointer
    }

    .modal-popup-container {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: none;
    }

    .modal-choose-payment-container {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: none;
    }

    .open {
        display: block;
    }

    .order-food__container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        padding: 0 0 0 10px;
    }

    .order-food__container .order-food-price {
        color: #333;
        font-family: Roboto, sans-serif;
        font-weight: 400;
        line-height: 150%;
    }

    .order-food-info {
        display: flex;
        align-items: center;
        gap: 15px;

    }

    .order-food-info .order-food-quantity {
        color: #414a63;
        font-family: Roboto, sans-serif;
        font-weight: 700;
    }
    </style>

    <div class="confirm-payment-schedule">
        <div class="confirm-payment-right">
            <div class="confirm-payment-title">XÁC NHẬN THANH TOÁN</div>
            <div class="confirm-payment-desc">Xác nhận thanh toán cho số ghế bạn đã đặt</div>
            <div class="detail-schedule">
                <h2 id="detail-schedule-header">Chi tiết lịch trình</h2>

                <div class="film-title-container">
                    <p class="film-title-label">Tiêu đề phim</p>
                    <p class="film-title">

                    </p>
                </div>

                <div class="divider"></div>

                <div class="date-container">
                    <p class="date-label">Ngày</p>
                    <p class="date">
                    </p>
                </div>

                <div class="divider"></div>

                <div class="class-hours-container">
                    <div class="class-container">
                        <p class="class-label">Loại vé</p>
                        <p class="class">

                        </p>
                    </div>
                    <div class="hours-container">
                        <p class="hours-label">Thời gian</p>
                        <p class="hours">
                        </p>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="ticket-container">
                    <p class="ticket-label">Ghế đã chọn</p>
                    <p class="ticket">
                    </p>
                </div>
            </div>

            <div class="back-button">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/fa81bf27bdbcf81e4202ab55cff292aaf6de617f290bcea10f235477064da8e3?apiKey=a7b5919b608d4a8d87d14c0f93c1c4bc&"
                    alt="Back icon" class="back-icon" />
                <p class="back-text">Quay lại</p>
            </div>
        </div>
        <div class="confirm-payment-left">
            <div class="order-summary">
                <h2 class="order-summary-title">TÓM TẮT ĐẶT VÉ</h2>
                <h3 class="transaction-details-title">Chi tiết giao dịch</h3>

                <div class="regular-seat">
                    <div class="regular-seat-label">GIÁ VÉ</div>
                    <div class="regular-seat-details">
                        <div class="regular-seat-price"></div>
                        <div class="regular-seat-quantity"></div>
                    </div>
                </div>

                <div class="service-fee">
                    <div class="service-fee-label">ĐỒ ĂN</div>
                    <div class="service-fee-details">
                        <div class="service-fee-price"></div>
                        <div class="service-fee-quantity"></div>
                    </div>
                </div>

                <div class='order-food__summary'>

                </div>


                <!-- <div class="divider"></div>

                <h3 class="promo-voucher-title">Khuyến mãi & Phiếu quà tặng</h3>
                <div class="promo-tix-id">
                    <div class="promo-tix-id-label">PROMO TIX ID</div>
                    <div class="promo-tix-id-amount">- Rp. 70.000</div>
                </div> -->

                <div class="divider"></div>

                <div class="total-payment">
                    <div class="total-payment-label">Tổng thanh toán</div>
                    <div class="total-payment-amount"></div>
                </div>

                <div class="divider"></div>

                <div class="payment-method-title">
                    <div class="payment-method-label">Phương thức thanh toán</div>
                    <span class="payment-method-show-all">Xem tất cả</span>

                </div>

                <div class="dana-payment">
                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/ed129848d2cb5b1fed239fc00eb5ee9ebbef693b440002fe86edb88c63fca0c3?apiKey=a7b5919b608d4a8d87d14c0f93c1c4bc&"
                        alt="" class="dana-logo" />
                    <div class="dana-label">DANA</div>
                </div>

                <div class="non-refundable-notice">*Vé đã mua không thể hoàn lại</div>
                <button class="buy-ticket-button">MUA VÉ</button>
            </div>
        </div>
    </div>
    <div class="modal-popup-container">
        <?php include_once './popup-message.php'; ?>
    </div>
    <div class="modal-choose-payment-container">
        <?php include_once './choose-payment.php'; ?>
    </div>
    <?php include_once './footer.php'; ?>

    <!-- Popup -->
    <script>
    var selectedShow = localStorage.getItem('selectedShow') ? JSON.parse(localStorage.getItem('selectedShow')) :
        undefined;
    if (selectedShow != undefined) {
        var title = document.querySelector('.film-title-container .film-title')
        title.textContent = selectedShow?.film_name
        var date = document.querySelector('.date-container .date')
        date.textContent = selectedShow?.order_date
        var classE = document.querySelector('.class-container .class')
        classE.textContent = selectedShow?.type_room_name
        var hour = document.querySelector('.hours-container .hours')
        hour.textContent = selectedShow?.show_time
    }
    var selectedSeats = localStorage.getItem('selectedSeats') ? JSON.parse(localStorage.getItem('selectedSeats')) :
        undefined;
    var ticketElement = document.querySelector('.ticket-container .ticket')
    var priceElement = document.querySelector('.regular-seat-details .regular-seat-price');
    var countPriceElement = document.querySelector('.regular-seat-details .regular-seat-quantity');
    var totalPaymentElement = document.querySelector('.total-payment .total-payment-amount');
    if (selectedSeats != undefined) {
        const seatsShow = selectedSeats.map((i) => {
            return i.name
        })
        const priceTicket = selectedSeats[0]?.price;
        priceElement.textContent = priceTicket + " VND"

        ticketElement.textContent = seatsShow
        countPriceElement.textContent = 'X' + selectedSeats.length;
        totalPaymentElement.textContent = priceTicket * selectedSeats.length + " VND";
    }
    var selectedFood = localStorage.getItem('selectedFood') ? JSON.parse(localStorage.getItem('selectedFood')) :
        undefined;
    var foodPriceElement = document.querySelector('.service-fee-details .service-fee-price');
    var foodQuantityElement = document.querySelector('.service-fee-details .service-fee-quantity');
    if (selectedFood !== undefined) {
        foodPriceElement.textContent = selectedFood?.reduce((total, value) => {
            return total + (value?.quantity * value?.price)
        }, 0) + " VND";

    }
    var totalPaymentElement = document.querySelector('.total-payment .total-payment-amount');
    if (selectedFood != undefined || selectedSeats != undefined) {
        totalPaymentElement.textContent = selectedFood.reduce((total, value) => {
            return total + (value?.quantity * value?.price)
        }, 0) + (selectedSeats[0]?.price * selectedSeats.length) + " VND";
    }
    // Chọn container order-summary
    const orderSummaryContainer = document.querySelector('.order-food__summary');

    // Lặp qua mỗi object trong mảng và tạo thẻ div mới cho mỗi object
    if (selectedFood != undefined) {
        selectedFood.forEach(food => {
            if (food.quantity > 0) {
                // Tạo thẻ div mới
                const foodDiv = document.createElement('div');
                foodDiv.classList.add('order-food__container');

                // Tạo thẻ div cho tên món ăn
                const nameDiv = document.createElement('div');
                nameDiv.classList.add('order-food-name');
                nameDiv.textContent = food.name;
                foodDiv.appendChild(nameDiv);
                const containerInfoDiv = document.createElement('div');
                containerInfoDiv.classList.add('order-food-info');

                // Tạo thẻ div cho giá
                const priceDiv = document.createElement('div');
                priceDiv.classList.add('order-food-price');
                priceDiv.textContent = food.price + " VND";
                containerInfoDiv.appendChild(priceDiv);

                const quantityDiv = document.createElement('div');
                quantityDiv.classList.add('order-food-quantity');
                quantityDiv.textContent = "X" + food.quantity;
                containerInfoDiv.appendChild(quantityDiv);

                foodDiv.appendChild(containerInfoDiv);

                // Thêm thẻ div mới vào container order-summary
                orderSummaryContainer.appendChild(foodDiv);
            }
        });
    }

    // Open
    var backButtons = document.getElementsByClassName("back-button");
    var modalContainers = document.getElementsByClassName("modal-popup-container");

    function openBackDialog() {
        for (var modalContainer of modalContainers) {
            modalContainer.classList.add('open');
        }
    }
    for (var backButton of backButtons) {}
    backButton.addEventListener("click", openBackDialog);

    // Cancel
    var cancelButtons = document.getElementsByClassName("modal-cancel-btn");
    var modalContainers = document.getElementsByClassName("modal-popup-container");
    var exitButtons = document.getElementsByClassName("modal-icon");

    function closeBackDialog() {
        for (var modalContainer of modalContainers) {
            modalContainer.classList.remove('open');
        }
    }
    for (var cancelButton of cancelButtons) {
        cancelButton.addEventListener("click", closeBackDialog);
    }
    for (var exitButton of exitButtons) {
        exitButton.addEventListener("click", closeBackDialog);
    }

    // back
    var backButtons = document.getElementsByClassName("modal-back-btn");

    function goBackPage() {
        history.back();
    }
    for (var backButton of backButtons) {
        backButton.addEventListener("click", goBackPage);
    }
    document.querySelector('.buy-ticket-button').addEventListener("click", () => {
        const selectedFood = localStorage.getItem('selectedFood') ? JSON.parse(localStorage.getItem(
            'selectedFood')) : [];
        const selectedSeats = localStorage.getItem('selectedSeats') ? JSON.parse(localStorage.getItem(
            'selectedSeats')) : [];
        const selectedShow = localStorage.getItem('selectedShow') ? JSON.parse(localStorage.getItem(
            'selectedShow')) : undefined;
        console.log('selectedFood', selectedFood);
        console.log('selectedSeats', selectedSeats);
        console.log('selectedShow', selectedShow);

        console.log("click");
        // Prepare data to be sent
        var formData = new FormData();
        formData.append('action', 'saveReservation');
        formData.append('show_id', selectedShow?.show_id);
        formData.append('selectedSeats', JSON.stringify(selectedSeats));
        formData.append('selectedFood', JSON.stringify(selectedFood));


        // AJAX request
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.href = 'payment-success.php'
                localStorage.removeItem('selectedShow')
                localStorage.removeItem('selectedSeats')
                localStorage.removeItem('selectedFood')
            }
        };
        xhttp.open("POST", window.location.href, true);
        xhttp.send(formData); // Send form data
    });
    </script>

    <!-- Choose payment-->
    <script>
    // Open
    var showAllPaymentMethodButtons = document.getElementsByClassName("payment-method-show-all");
    var modalChoosePaymentContainers = document.getElementsByClassName("modal-choose-payment-container");

    function openShowAllPaymentMethodDialog() {
        for (var modalChoosePaymentContainer of modalChoosePaymentContainers) {
            modalChoosePaymentContainer.classList.add('open');
        }
    }
    for (var showAllPaymentMethodButton of showAllPaymentMethodButtons) {}
    showAllPaymentMethodButton.addEventListener("click", openShowAllPaymentMethodDialog);

    // Close
    var closeButtons = document.getElementsByClassName("payment-icon-close")
    var modalChoosePaymentContainers = document.getElementsByClassName("modal-choose-payment-container");

    function closeShowAllPaymentMethodDialog() {
        for (var modalChoosePaymentContainer of modalChoosePaymentContainers) {
            modalChoosePaymentContainer.classList.remove('open');
        }
    }
    for (var closeButton of closeButtons) {
        closeButton.addEventListener("click", closeShowAllPaymentMethodDialog);
    }
    </script>
</body>

</html>