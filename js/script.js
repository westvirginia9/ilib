document.addEventListener("DOMContentLoaded", function() {
    var galleryItems = document.querySelectorAll(".gallery-item");
    var maxImagesToShow = 5;
    var totalItems = galleryItems.length;
    var startIndex = 0;
    var lastScrollTime = 0;
    var scrollDelay = 300; // Delay in milliseconds
  
    function showImagesFromIndex(startIndex) {
      var imageGallery = document.querySelector(".image-gallery");
      imageGallery.innerHTML = ""; // Clear gallery content
  
      for (var i = 0; i < maxImagesToShow; i++) {
        var index = (startIndex + i) % totalItems;
        imageGallery.appendChild(galleryItems[index].cloneNode(true));
      }
    }
  
    showImagesFromIndex(startIndex);
  
    document.querySelector(".image-gallery").addEventListener("wheel", function(event) {
      var currentTime = new Date().getTime();
      if (currentTime - lastScrollTime > scrollDelay) {
        if (event.deltaY < 0) {
          // Scroll up
          startIndex = (startIndex - 1 + totalItems) % totalItems;
        } else {
          // Scroll down
          startIndex = (startIndex + 1) % totalItems;
        }
        showImagesFromIndex(startIndex);
        lastScrollTime = currentTime;
      }
    });
  
    // Event listener untuk tombol keyboard
    document.addEventListener("keydown", function(event) {
      switch(event.key) {
        case "ArrowLeft":
          // Tombol panah kiri (Previous)
          startIndex = (startIndex - 1 + totalItems) % totalItems;
          showImagesFromIndex(startIndex);
          break;
        case "ArrowRight":
          // Tombol panah kanan (Next)
          startIndex = (startIndex + 1) % totalItems;
          showImagesFromIndex(startIndex);
          break;
        case "Home":
          // Tombol Home (<)
          startIndex = 0;
          showImagesFromIndex(startIndex);
          break;
        case "End":
          // Tombol End (>)
          startIndex = totalItems - maxImagesToShow;
          if (startIndex < 0) startIndex = 0;
          showImagesFromIndex(startIndex);
          break;
      }
    });
  });
  