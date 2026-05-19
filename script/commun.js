function enClick(elem) {
    const dejàOuvert = elem.classList.contains("active");
        document.querySelectorAll(".clicable").forEach(e => e.classList.remove("active"));
    if (!dejàOuvert) {
        elem.classList.add("active");
    }
}


document.querySelector('.contact-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Bloque le rechargement de la page HTML

    const form = this;
    
    // Organisation des données du formulaire dans un objet propre
    const payload = {
        nom: form.querySelector('input[name="nom"]').value,
        email: form.querySelector('input[name="email"]').value,
        sujet: form.querySelector('input[name="sujet"]').value,
        message: form.querySelector('textarea[name="message"]').value
    };

    // Envoi de la requête au fichier PHP localisé sur ton serveur Apache
    fetch("contact.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.style.display = 'none';
            document.getElementById('form-confirm').style.display = 'block';
        } else {
            alert("Erreur du serveur de contact : " + data.error);
        }
    })
    .catch(error => {
        console.error("Erreur réseau :", error);
        alert("Impossible de joindre le système de transmission du formulaire.");
    });
});