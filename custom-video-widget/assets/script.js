document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".widget-video").forEach(function (videoContainer) {
    const backButton = videoContainer.querySelector(".back-to-list");
    const videoItems = videoContainer.querySelectorAll(".video-item");

    if (!backButton) return;
    backButton.classList.add("hidden");

    videoItems.forEach(function (item) {
      item.addEventListener("click", function () {
        // Ẩn tất cả video khác
        videoItems.forEach(function (list) {
          list.classList.remove("active");
          list.classList.add("hidden");
          list.querySelector(".custom-video").style.display = "none";
          list.querySelector(".video-thumbnail").style.display = "flex";
          list.querySelector(".custom-video").src =
            list.querySelector(".custom-video").src; // Reset video
        });

        // Mở video hiện tại
        backButton.classList.remove("hidden");
        this.classList.remove("hidden");
        this.classList.add("active");
        this.querySelector(".custom-video").style.display = "block";
        this.querySelector(".video-thumbnail").style.display = "none";

        // **Tự động chạy video**
        const videoIframe = this.querySelector(".custom-video");
        let videoSrc = videoIframe.dataset.src || videoIframe.src; // Lưu URL gốc nếu chưa có
        videoIframe.dataset.src = videoSrc; // Lưu lại URL gốc để reset khi back
        videoIframe.src =
          videoSrc + (videoSrc.includes("?") ? "&" : "?") + "autoplay=1&mute=1"; // Autoplay + Mu
      });
    });

    backButton.addEventListener("click", function () {
      videoItems.forEach(function (list) {
        list.classList.remove("hidden", "active");
        list.querySelector(".custom-video").style.display = "none";
        list.querySelector(".video-thumbnail").style.display = "flex";
        list.querySelector(".custom-video").src =
          list.querySelector(".custom-video").src; // Reset video
      });
      backButton.classList.add("hidden");
    });
  });

  // Action for swiper
  document
    .querySelectorAll(".swiper-container")
    .forEach(function (swiperContainer) {
      var swiper = new Swiper(swiperContainer, {
        slidesPerView: 3,
        slidesPerGroup: 1,
        spaceBetween: 0,
        loopAdditionalSlides: 3, // Đảm bảo Swiper không tạo quá nhiều bản sao
        navigation: {
          nextEl: swiperContainer.querySelector(".swiper-button-next"),
          prevEl: swiperContainer.querySelector(".swiper-button-prev"),
        },
        on: {
          slideChangeTransitionEnd: function () {
            console.log("Active Index:", this.activeIndex);
            console.log("TranslateX:", this.translate);

            // Ép translateX về đúng vị trí, tránh bị lệch
            let expectedTranslateX =
              -this.activeIndex * this.slides[0].offsetWidth;
            this.setTranslate(expectedTranslateX);
            console.log("Fixed TranslateX:", expectedTranslateX);
          },
          init: function () {
            updateSlideVisibility(this);
          },
          slideChange: function () {
            updateSlideVisibility(this);
          },
        },
      });
    });

  function updateSlideVisibility(swiper) {
    swiper.slides.forEach((slide, index) => {
      if (index >= swiper.activeIndex && index < swiper.activeIndex + 3) {
        slide.classList.remove("swiper-hidden");
      } else {
        slide.classList.add("swiper-hidden");
      }
    });
  }

  // Hàm cập nhật chiều cao thumbnail theo tỷ lệ 16:9
  function updateThumbnailHeight() {
    document
      .querySelectorAll(".custom-video-container")
      .forEach((videoContainer) => {
        videoContainer
          .querySelectorAll(".video-thumbnail")
          .forEach((thumbnail) => {
            let width = thumbnail.offsetWidth; // Lấy chiều rộng thực tế
            thumbnail.style.height = (width * 9) / 16 + "px"; // Tính chiều cao theo 16:9
          });
      });
  }

  // Gọi hàm khi tải trang
  updateThumbnailHeight();
  window.addEventListener("resize", updateThumbnailHeight);
});
