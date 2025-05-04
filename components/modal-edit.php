<?php 
    $categories = getCategories($pdo);
?>

        <?php if (!empty($_SESSION['user_id'])) { ?>
            <!--scrolling content Modal -->
            <form action="api/edit_recipe.php" method="post" id="recipeEditForm">
            <div class="modal fade" id="editForm" tabindex="-1" role="dialog"
                aria-labelledby="recipeEditFormTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="recipeEditFormTitle">
                                Edit Recipe</h5>
                            <button type="button" class="close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-label">Name Recipe</label>
                                    <input type="text" placeholder="Name Recipe" name="name" class="form-control">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" name="category_id" aria-label="Default select example">
                                        <option selected>Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Detail Recipe</label>
                                    <div id="editRecipe"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Upload Images (max 3)</label>
                                    <input
                                    type="file"
                                    id="imageInput-edit"
                                    class="form-control"
                                    accept=".png,.jpg,.jpeg"
                                    multiple
                                    >
                                </div>

                                <div id="preview-edit" class="d-flex flex-wrap gap-3 mb-3"></div>

                                <input
                                    type="hidden"
                                    name="images_base64"
                                    id="imagesBase64"
                                >
                                
                                <div id="formError-edit" class="text-danger mt-2" style="display:none;"></div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary"
                                data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Submit</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <?php } ?>

<script>
window.openEditModal = null; // Initialize it
document.addEventListener('DOMContentLoaded', () => {

    // Initialize Quill
    var quillEdit = new Quill('#editRecipe', {
        theme: 'snow'
    });

    // Global variables
    const maxFiles = 3;
    const maxSize = 1 * 1024 * 1024; // 1 MB
    const allowedTypes = ['image/png','image/jpeg','image/jpg'];
    let currentRecipeId = null;
    let images = [];

    // Function to open edit modal
    // async function openEditModal(e) {
    window.openEditModal = async (recipe) => {
        currentRecipeId = recipe.id;
        
        // Reset form and errors
        document.getElementById('formError-edit').style.display = 'none';
        document.getElementById('preview-edit').innerHTML = '';
        images = [];

        // Populate form fields
        const form = document.getElementById('recipeEditForm');
        form.querySelector('[name="name"]').value = recipe.name;
        form.querySelector('[name="category_id"]').value = recipe.category_id;
        
        // Set Quill content
        quillEdit.root.innerHTML = recipe.description;

        // Handle existing images
        if (recipe.image_urls) {
            const existingImages = recipe.image_urls.split('|||');
            images = existingImages;
            renderPreviews();
        }

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById('editForm'));
        editModal.show();
    };

    // Image handling functions
    const input = document.getElementById('imageInput-edit');
    const preview = document.getElementById('preview-edit');
    
    input.addEventListener('change', handleFiles);

    function handleFiles(e) {
        const files = Array.from(e.target.files);

        files.forEach(file => {
            if (images.length >= maxFiles) {
                alert(`You can upload up to ${maxFiles} images.`);
                return;
            }
            if (!allowedTypes.includes(file.type)) {
                alert(`Unsupported type: ${file.type}`);
                return;
            }
            if (file.size > maxSize) {
                alert(`"${file.name}" is too large. Max size is 1 MB.`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(evt) {
                images.push(evt.target.result);
                renderPreviews();
            };
            reader.readAsDataURL(file);
        });

        e.target.value = '';
    }

    function renderPreviews() {
        preview.innerHTML = '';
        images.forEach((src, idx) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative';

            const img = document.createElement('img');
            img.src = src;
            img.style.maxWidth = '100px';
            img.style.maxHeight = '100px';
            img.className = 'img-thumbnail';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = '&times;';
            btn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
            btn.onclick = () => {
                images.splice(idx, 1);
                renderPreviews();
            };

            wrapper.appendChild(img);
            wrapper.appendChild(btn);
            preview.appendChild(wrapper);
        });
    }

    // Form submission
    document.getElementById('recipeEditForm').addEventListener('submit', async e => {
        e.preventDefault();
        const errorDiv = document.getElementById('formError');
        errorDiv.style.display = 'none';

        const formData = new FormData(e.target);
        const payload = {
            id: currentRecipeId,
            name: formData.get('name').trim(),
            description: quillEdit.root.innerHTML.trim(),
            category_id: formData.get('category_id').trim(),
            images: images
        };

        if (!payload.name || !payload.description || !payload.category_id) {
            errorDiv.textContent = 'Please fill in all required fields.';
            errorDiv.style.display = 'block';
            return;
        }

        try {
            const res = await fetch('api/edit_recipe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            
            const data = await res.json();
            
            if (data.success) {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('editForm')).hide();
                window.location.href = 'my-recipe?alert=SUCCESS_UPDATE_RECIPE';
            } else {
                throw new Error(data.message || 'Failed to update recipe');
            }
        } catch (err) {
            errorDiv.textContent = err.message;
            errorDiv.style.display = 'block';
            
            Toastify({
                text: "Error updating recipe!",
                duration: 3000,
                style: { background: 'linear-gradient(to right, #ff5f6d, #ffc371)' }
            }).showToast();
        }
    });
  });
</script>