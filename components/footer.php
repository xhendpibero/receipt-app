<?php
    include 'components/modal-add.php';
?>
                <footer>
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start">
                            <p>2025 &copy; Mochi Keren</p>
                        </div>
                        <div class="float-end">
                            <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                                by <a href="https://ahmadsaugi.com">Chip Koding</a></p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const alertCode = params.get('alert');

        if (!alertCode) return;  // no alert param, do nothing

        // Map codes to messages/types
        const messages = {
            'SUCCESS_ADD_RECIPE': {
            text: 'Recipe added successfully!',
            style: {background: 'linear-gradient(to right, #00b09b, #96c93d)'}
            },
            'SUCCESS_UPDATE_RECIPE': {
            text: 'Recipe updated successfully!',
            style: {background: 'linear-gradient(to right, #00b09b, #96c93d)'}
            },
            // add more codes here if needed
            // 'ERROR_SOMETHING': { text: 'Something went wrong', style: { background: 'red' } }
        };

        const cfg = messages[alertCode];
        if (cfg) {
            Toastify({
            text: cfg.text,
            duration: 3000,
            close: true,
            gravity: 'top',       // top or bottom
            position: 'right',    // left, center or right
            style: cfg.style      // custom bg color
            }).showToast();
        }

        // Remove the `alert` param so it doesn't show again on reload
        params.delete('alert');
        const newQuery = params.toString();
        const newUrl = window.location.pathname + (newQuery ? '?' + newQuery : '');
        window.history.replaceState({}, document.title, newUrl);
    </script>

    <script src="assets/vendors/toastify/toastify.js"></script>
    <script src="assets/vendors/quill/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>