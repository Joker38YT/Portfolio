function enClick(elem) {
    const dejàOuvert = elem.classList.contains("active");
        document.querySelectorAll(".clicable").forEach(e => e.classList.remove("active"));
    if (!dejàOuvert) {
        elem.classList.add("active");
    }
}