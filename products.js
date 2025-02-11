// JavaScript for managing the product category filters and active class toggling
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.laptop-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove 'active' class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));

            // Add 'active' class to the clicked button
            button.classList.add('active');

            // Get the category to filter products
            const category = button.textContent.trim().toLowerCase();
            filterProducts(category);
        });
    });
});

// Function to filter products by category
function filterProducts(category) {
    // Get all the product sections (phones, laptops, etc.)
    const phoneSection = document.getElementById('phones');
    const laptopSection = document.getElementById('laptops');

    // Hide all sections initially
    phoneSection.style.display = 'none';
    laptopSection.style.display = 'none';

    // Show only the relevant section
    if (category === 'téléphones' || category === 'tous') {
        phoneSection.style.display = 'block';
    }
    if (category === 'ordinateurs portables' || category === 'tous') {
        laptopSection.style.display = 'block';
    }
}
