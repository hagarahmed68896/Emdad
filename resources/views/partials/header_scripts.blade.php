<!-- Tailwind + Alpine.js -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="//unpkg.com/alpinejs" defer></script>

<script>
    function imageUploadComponent() {
        return {
            showUploadModal: false,
            imageFile: null,
            imagePreview: null,
            imageUrl: '',

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith("image/")) {
                    this.imageFile = file;
                    this.imagePreview = URL.createObjectURL(file);
                } else {
                    alert("الرجاء اختيار صورة صحيحة.");
                }
            },

            handleDrop(event) {
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith("image/")) {
                        this.imageFile = file;
                        this.imagePreview = URL.createObjectURL(file);
                    } else {
                        alert("الرجاء اختيار صورة صحيحة.");
                    }
                }
            },

            submitImage() {
                if (!this.imageFile) {
                    alert("الرجاء تحميل صورة قبل البحث.");
                    return;
                }

                // You can upload or use imageFile here
                this.imageFile = null;
                this.imagePreview = null;
                this.showUploadModal = false;
            }
        }
    }
</script>
<script>
    document.getElementById('language-icon').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default anchor behavior
        var dropdown = document.getElementById('language-dropdown');
        dropdown.classList.toggle('show'); // Toggle the dropdown visibility
    });
    // Optional: Close the dropdown if clicking outside of it
    window.onclick = function(event) {
        if (!event.target.matches('#language-icon') && !event.target.matches('#language-button')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/flowbite@1.7.0/dist/flowbite.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchBoxComponent', () => ({}));

        Alpine.data('imageUploadComponent', () => ({
            showUploadModal: false,
            imagePreview: null,
            uploadedFile: null,
            imageUrl: '',

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.imagePreview = null;
                    this.uploadedFile = null;
                }
            },

            handleDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            submitImage() {
                // This function is triggered by the "Search" button in the modal.
                // It needs to submit the image (either file or URL) to the backend.

                const form = document.querySelector(
                    '.main-search-form'); // Give your form a class for easy selection
                const formData = new FormData(form); // Get existing form data

                if (this.uploadedFile) {
                    formData.append('search_image', this.uploadedFile);
                } else if (this.imageUrl) {
                    formData.append('image_url', this.imageUrl);
                }

                // If you want to use the main search form's query and categories,
                // you need to append them as well.
                const textQueryInput = form.querySelector('input[name="query"]');
                if (textQueryInput && textQueryInput.value) {
                    formData.append('query', textQueryInput.value);
                }

                // Get selected categories from the dropdown
                const categoryCheckboxes = form.querySelectorAll(
                    'input[name="search_categories[]"]:checked');
                categoryCheckboxes.forEach(checkbox => {
                    formData.append('search_categories[]', checkbox.value);
                });


                // Submit the form using Fetch API because a file upload requires specific headers
                fetch(form.action, {
                        method: form.method,
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text(); // Or .json() if your backend returns JSON
                    })
                    .then(html => {
                        // Assuming the backend returns the HTML of the results page,
                        // you can replace the current content or redirect.
                        // For a full page reload to the search results:
                        window.location.href = form.action + '?' + new URLSearchParams(formData)
                            .toString();
                    })
                    .catch(error => {
                        console.error('Error during image search submission:', error);
                        alert('An error occurred during image search. Please try again.');
                    });

                this.showUploadModal = false; // Close the modal after submission
                this.imagePreview = null; // Clear preview
                this.uploadedFile = null; // Clear uploaded file
                this.imageUrl = ''; // Clear URL
            },
        }));
    });
</script>
<script>
    document.addEventListener('alpine:init', () => {
        // Main search box component (no significant changes here)
        Alpine.data('searchBoxComponent', () => ({
            // This component largely acts as a container; nested x-data handles specifics.
        }));

        // Image upload component (minor adjustment to trigger recent search add)
        Alpine.data('imageUploadComponent', () => ({
            showUploadModal: false,
            imagePreview: null,
            uploadedFile: null,
            imageUrl: '',

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.imagePreview = null;
                    this.uploadedFile = null;
                }
            },

            handleDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            submitImage() {
                const form = document.querySelector('.main-search-form');
                const formData = new FormData(form);

                if (this.uploadedFile) {
                    formData.append('search_image', this.uploadedFile);
                } else if (this.imageUrl) {
                    formData.append('image_url', this.imageUrl);
                }

                const textQueryInput = form.querySelector('input[name="query"]');
                if (textQueryInput && textQueryInput.value) {
                    formData.append('query', textQueryInput.value);
                    // Crucial: Manually trigger addRecentSearch on the main search input's Alpine data
                    // Find the Alpine data scope for the search input
                    const searchInputXData = textQueryInput.closest('[x-data]')._x_dataStack[0];
                    if (searchInputXData && typeof searchInputXData.addRecentSearch ===
                        'function') {
                        searchInputXData.addRecentSearch(textQueryInput.value);
                    }
                }

                const categoryCheckboxes = form.querySelectorAll(
                    'input[name="search_categories[]"]:checked');
                categoryCheckboxes.forEach(checkbox => {
                    formData.append('search_categories[]', checkbox.value);
                });

                fetch(form.action, {
                        method: form.method,
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        window.location.href = form.action + '?' + new URLSearchParams(formData)
                            .toString();
                    })
                    .catch(error => {
                        console.error('Error during image search submission:', error);
                        alert('An error occurred during image search. Please try again.');
                    });

                this.showUploadModal = false;
                this.imagePreview = null;
                this.uploadedFile = null;
                this.imageUrl = '';
            },
        }));
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileImage = document.getElementById('profileImage');
        const dropdownMenu = document.getElementById('dropdownMenu');
        profileImage.addEventListener('click', function() {
            // Toggle the dropdown menu
            const dropdown = new bootstrap.Dropdown(dropdownMenu);
            dropdown.toggle();
        });
    });
</script>
    <script src="//unpkg.com/alpinejs" defer></script>

<style>
    [x-cloak] {
        display: none !important;
    }

    .category-button,
    #mainDropdownButton {
        /* padding: 8px 21.5px; */
        background-color: #EDEDED;
        color: #212121;
        border-radius: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        height: 40px;
    }

    .category-button:hover,
    #mainDropdownButton:hover {
        background-color: #185D31;
        color: white;
    }

    .category-button.active,
    #mainDropdownButton.active {
        background-color: #185D31;
        color: white;
    }

    .profile-menu {
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        background-color: #fff;
        text-align: right;
        position: absolute !important;
        transform: translate(0, 0) !important;
        top: 100% !important;
        left: 0 !important;
    }

    .dropdown-item {
        font-size: 14px;
        border-radius: 12PX;
        padding-top: 12px;
        padding-right: 10px;
        color: #696969;
        font-weight: 400;
        height: 49px;
    }

    .dropdown-item:hover {
        background-color: #185D31;
        color: #FFFFFF;
        font-weight: 500;
    }

    #all_Categories:hover {
        color: #FFFFFF;
    }

    @media(max-width:1450px) {

        .search,
        .deliver,
        .language,
        .categories {
            display: none;
        }

        #dropdownMenuIconButton,
        .search_menu_small {
            display: flex;
        }

    }
</style>
<style>
    .responsive-dropdown {
        position: absolute;
        top: 100%;
        width: 314px;
        z-index: 1002;
    }

    .left-align {
        left: 100%;
        margin-left: 0.25rem;
    }

    .right-align {
        right: 100%;
        margin-right: 0.25rem;
    }

    @media (max-width: 700px) {
        .responsive-dropdown {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin: 0 !important;
        }

        .left-align,
        .right-align {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin: 0 !important;
        }
    }
</style>

