function enClick(elem) {
    const dejàOuvert = elem.classList.contains("active");
        document.querySelectorAll(".clicable").forEach(e => e.classList.remove("active"));
    if (!dejàOuvert) {
        elem.classList.add("active");
    }
}


document.querySelector('.contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    this.style.display = 'none';
    document.getElementById('form-confirm').style.display = 'block';
});