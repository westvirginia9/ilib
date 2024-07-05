document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");
  
    function showBookDetails(book) {
      console.log(book); // Debugging line to ensure book details are correct
      document.getElementById('bookTitle').innerText = book.title;
      document.getElementById('bookAuthor').innerText = "Penulis: " + book.author_name;
      document.getElementById('bookGenre').innerText = "Genre: " + book.genre;
      document.getElementById('bookDate').innerText = "Terunggah: " + book.created_at;
  
      document.getElementById('readButton').onclick = function() {
        Swal.fire({
          title: 'Anda membaca preview buku',
          text: book.title,
          icon: 'info',
          confirmButtonText: 'OK'
        });
      };
  
      document.getElementById('rentButton').onclick = function() {
        Swal.fire({
          title: 'Mulai Pembayaran',
          text: 'Anda akan diarahkan ke halaman pembayaran.',
          icon: 'info',
          confirmButtonText: 'Lanjutkan'
        }).then(() => {
          snap.pay('<?php echo $snapToken; ?>', {
            onSuccess: function(result){
              Swal.fire({
                title: 'Pembayaran berhasil',
                text: 'Anda sekarang bisa membaca buku ini.',
                icon: 'success',
                confirmButtonText: 'OK'
              }).then(() => {
                window.location.href = '../read-book.php?book_id=' + book.id + '&page=1';
              });
            },
            onPending: function(result){
              Swal.fire({
                title: 'Menunggu pembayaran',
                text: 'Pembayaran Anda sedang diproses.',
                icon: 'warning',
                confirmButtonText: 'OK'
              });
              console.log(result);
            },
            onError: function(result){
              Swal.fire({
                title: 'Pembayaran gagal',
                text: 'Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK'
              });
              console.log(result);
            },
            onClose: function(){
              Swal.fire({
                title: 'Pembayaran ditutup',
                text: 'Anda menutup popup tanpa menyelesaikan pembayaran.',
                icon: 'info',
                confirmButtonText: 'OK'
              });
            }
          });
        });
      };
  
      document.getElementById('bookModal').style.display = "block";
    }
  
    function closeModal() {
      document.getElementById('bookModal').style.display = "none";
    }
  
    window.onclick = function(event) {
      if (event.target == document.getElementById('bookModal')) {
        closeModal();
      }
    }
  
    // Ensure this function is globally accessible
    window.showBookDetails = showBookDetails;
  });
  