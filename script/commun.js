function enClick(elem) {
    const dejàOuvert = elem.classList.contains("active");
        document.querySelectorAll(".clicable").forEach(e => e.classList.remove("active"));
    if (!dejàOuvert) {
        elem.classList.add("active");
    }
}


document.querySelector('.contact-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche la page de recharger

    const form = this;
    const payload = {
        nom: form.querySelector('input[name="nom"]').value,
        email: form.querySelector('input[name="email"]').value,
        sujet: form.querySelector('input[name="sujet"]').value,
        message: form.querySelector('textarea[name="message"]').value
    };

    // On envoie à /api/contact, Nginx s'occupe de faire le pont vers le port 4000
    fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ton code d'origine pour afficher le succès
            form.style.display = 'none';
            document.getElementById('form-confirm').style.display = 'block';
        } else {
            alert("Erreur lors de la validation du message.");
        }
    })
    .catch(error => {
        console.error("Erreur réseau :", error);
        alert("Impossible de joindre le système de transmission.");
    });
});