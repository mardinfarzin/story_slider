jQuery(document).ready(function($) {
    $('.circle-story-slider').each(function() {
        var $slider = $(this);
        var autoplaySpeed = $slider.data('autoplay-speed') * 1000; // تبدیل ثانیه به میلی‌ثانیه
        var speed = $slider.data('speed') * 1000; // دریافت سرعت اسلایدر
        var slidesToShow = $slider.data('slides-to-show'); // دریافت تعداد اسلایدهای نمایش داده شده
        var slidesToScroll = $slider.data('slides-to-scroll'); // دریافت تعداد اسلایدهایی که در هر حرکت جابجا می‌شوند
        var sliderLength = $slider.find(".slider-item").length;

        function setSliderItemSize() {
            var get_box_width = $slider.width();
            var get_size_slider_item_in_slider_box = Math.round(get_box_width / slidesToShow);
            var slider_item_box = $slider.find(".slider-item");
            slider_item_box.css({
                width: get_size_slider_item_in_slider_box + 'px'
            });
            if(get_size_slider_item_in_slider_box > 200){
                var image_slider_box = get_size_slider_item_in_slider_box / 2 ;
                slider_item_box.find(".slider-image").css({
                    width: image_slider_box + 'px'
                });
            }
        }

        setSliderItemSize(); // برای لود اولیه

        $(window).on('resize', function() {
            setSliderItemSize(); // هنگام تغییر سایز پنجره
        });

        slidesToShow = Math.min(slidesToShow, sliderLength);
        $slider.slick({
            dots: true,
            infinite: true,
            speed: speed, // تنظیم سرعت اسلایدر
            autoplay: true, // فعال‌سازی حرکت خودکار
            autoplaySpeed: autoplaySpeed, // تنظیم سرعت حرکت خودکار
            slidesToShow: slidesToShow,  /* تعداد نمایش در هر صفحه */
            slidesToScroll: slidesToScroll, // تنظیم تعداد اسلایدهای جابجا شده
            arrows: true,
            rtl: false,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: Math.min(slidesToShow, 3), // تنظیم مقدار برای دستگاه‌های بزرگتر
                        slidesToScroll: slidesToScroll,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: Math.min(slidesToShow, 3), // تنظیم مقدار برای دستگاه‌های متوسط
                        slidesToScroll: slidesToScroll
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: Math.min(slidesToShow, 3), // تنظیم مقدار برای دستگاه‌های کوچک
                        slidesToScroll: slidesToScroll
                    }
                }
            ]
        });
    });
});
