document.querySelector('.logout-button').addEventListener('click', function(event) {
    event.preventDefault();  // Prevent the default logout behavior

    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to log out?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, log me out',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = this.href; // Proceed with logout if confirmed
        }
    });
});
