document.addEventListener("DOMContentLoaded", function () {
  console.log("Chạy thành công !!");
  const videoContainer = document.querySelector(".custom-video-container");
  if (!videoContainer) return;

  const backButton = document.querySelector(".back-to-list");
  backButton.classList.add("hidden");

  document.querySelectorAll(".video-item").forEach(function (item) {
    item.addEventListener("click", function () {
      // Ẩn tất cả video khác
      document.querySelectorAll(".video-item").forEach(function (list) {
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
        videoSrc + (videoSrc.includes("?") ? "&" : "?") + "autoplay=1&mute=1"; // Autoplay + Mute
    });
  });

  backButton.addEventListener("click", function () {
    console.log("Its work!!");
    document.querySelectorAll(".video-item").forEach(function (list) {
      list.classList.remove("hidden", "active");
      list.querySelector(".custom-video").style.display = "none";
      list.querySelector(".video-thumbnail").style.display = "flex";
      list.querySelector(".custom-video").src =
        list.querySelector(".custom-video").src; // Reset video
    });
    backButton.classList.add("hidden");
  });
});
