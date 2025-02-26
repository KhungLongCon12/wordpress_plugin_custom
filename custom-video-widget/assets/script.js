document.addEventListener("DOMContentLoaded", function () {
  console.log("Chạy thành công !!");

  document.querySelectorAll(".widget-video").forEach(function (videoContainer) {
    const backButton = videoContainer.querySelector(".back-to-list");
    const widgetID = videoContainer.querySelector(".custom-video-container")
      .dataset.widgetId;
    const videoItems = videoContainer.querySelectorAll(".video-item");

    console.log("videoItems", videoItems);

    if (!backButton) return;
    backButton.classList.add("hidden");

    videoItems.forEach(function (item) {
      item.addEventListener("click", function () {
        console.log(`Click vào video: ${widgetID}`);
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
      console.log(`Click vào nút back: ${widgetID}`);
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
