<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./owlcarousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="./owlcarousel/assets/owl.theme.default.min.css">
    <script src="./jquery.min.js"></script>
    <script src="./owlcarousel/owl.carousel.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
.carousel-banner__container {
    width: 100%;
    margin: 0 auto;
    position: relative;
    margin-top: 30px;
    /* padding: 0 20px */
    padding-left: 8px;
    /* Thêm thuộc tính position */
}

@media (min-width: 768px) {
    .carousel-banner__container {
        max-width: 1300px;
        padding: unset;
        padding-left: unset;
    }
}

.carousel-banner__container img {
    width: 100%;
    height: 304px;
    object-fit: cover;
}

.carousel-banner__container .owl-carousel {
    position: relative;
}

/* Thêm các thuộc tính sau */
.carousel-banner__container .owl-carousel .owl-nav .owl-prev,
.carousel-banner__container .owl-carousel .owl-nav .owl-next {
    position: absolute;
    top: 38%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    z-index: 90;
    background-color: rgba(255, 255, 255, 0.5);
    /* Màu nền */
    color: black;
    /* Màu chữ */
}

.carousel-banner__container .owl-carousel .owl-nav .owl-prev {
    left: -25px;
}

.carousel-banner__container .owl-carousel .owl-nav .owl-next {
    right: -25px;
}
</style>
<?php
require_once './db_module.php';
$link = NULL;
taoKetNoi($link);
$queryfilm = "SELECT m.banner_src FROM promo_banners m
";
$resultfilm = chayTruyVanTraVeDL($link, $queryfilm);
$html = '';
while ($row = mysqli_fetch_assoc($resultfilm)) {

    $html .= '<div>
   <img class="img" src="' . $row['banner_src'] . '" />
   </div>';
}
?>

<body>
    <div class='carousel-banner__container'>
        <div class="owl-carousel">
            <?php echo $html; ?>
        </div>
    </div>
</body>

</script>

<script>
$('.carousel-banner__container .owl-carousel').owlCarousel({
    loop: true,
    margin: 20,
    navText: [
        "<i class='fas fa-chevron-left'></i>",
        "<i class='fas fa-chevron-right'></i>",
    ],
    nav: false,
    items: 1,
    dots: false,
    responsive: {
        600: {
            margin: 0,

            items: 1,
            nav: true
        }
    }
})
</script>

</html>