<?php 
    $categories = getCategories($pdo);
?>

        <?php if (!empty($_SESSION['user_id'])) { ?>
            <!--scrolling content Modal -->
            <form  action="api/add_recipe.php" method="post" id="recipeForm">
            <div class="modal fade" id="recipeAddForm" tabindex="-1" role="dialog"
                aria-labelledby="recipeAddFormTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="recipeAddFormTitle">
                                Add Recipe</h5>
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
                                    <div id="addRecipe"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Upload Images (max 3)</label>
                                    <input
                                    type="file"
                                    id="imageInput"
                                    class="form-control"
                                    accept=".png,.jpg,.jpeg"
                                    multiple
                                    >
                                </div>

                                <div id="preview" class="d-flex flex-wrap gap-3 mb-3"></div>

                                <input
                                    type="hidden"
                                    name="images_base64"
                                    id="imagesBase64"
                                >
                                
                                <div id="formError" class="text-danger mt-2" style="display:none;"></div>
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
document.addEventListener('DOMContentLoaded', () => {
  
    var snow = new Quill('#addRecipe', {
        theme: 'snow'
    });

  const maxFiles     = 3;
  const maxSize      = 1 * 1024 * 1024; // 1 MB
  const allowedTypes = ['image/png','image/jpeg','image/jpg'];

  const input         = document.getElementById('imageInput');
  const preview       = document.getElementById('preview');
  const hiddenInput   = document.getElementById('imagesBase64');
  const form          = document.getElementById('recipeForm');
  const errorDiv      = document.getElementById('formError');

  // Array to hold base64 strings
  let images = [];

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
        // evt.target.result is base64 string
        images.push(evt.target.result);
        renderPreviews();
      };
      reader.readAsDataURL(file);
    });

    // Reset input so the same file can be re-selected if removed
    e.target.value = '';
  }
  
  function renderPreviews() {
    preview.innerHTML = '';
    images.forEach((b64, idx) => {
      const wrapper = document.createElement('div');
      wrapper.className = 'position-relative';

      // Image tag
      const img = document.createElement('img');
      img.src = b64;
      img.style.maxWidth = '100px';
      img.style.maxHeight = '100px';
      img.className = 'img-thumbnail';

      // Remove button
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

  form.addEventListener('submit', async e => {
    e.preventDefault();
    errorDiv.style.display = 'none';

    const name   = form.name.value.trim();
    const description = snow.root.innerHTML.trim();
    const category_id = form.category_id.value.trim();

    if (!name || !description || !category_id) {
      errorDiv.textContent = 'Please fill in all field name and description and category.';
      errorDiv.style.display = 'block';
      return;
    }

    // Prepare payload
    const payload = {
      name,
      description,
      images,
      category_id
    };

    try {
      const res = await fetch('api/add_recipe.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        // Redirect to dashboard or show success
        window.location.href = 'my-recipe?alert=SUCCESS_ADD_RECIPE';
      } else {
        Toastify({
            text: "Error add new recipe!",
            duration: 3000
        }).showToast();
        throw new Error(data.message || 'Unknown error');
      }
    } catch (err) {
      errorDiv.textContent = err.message;
      errorDiv.style.display = 'block';
    }
  });
});
</script>