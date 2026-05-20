function enClick(elem) {
    const dejàOuvert = elem.classList.contains("active");
    document.querySelectorAll(".clicable").forEach(e => e.classList.remove("active"));
    if (!dejàOuvert) {
        elem.classList.add("active");
    }
}


document.querySelector('.contact-form').addEventListener('submit', function (e) {
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
        .then(response => {
            // Si le serveur renvoie une erreur (400, 429, 503...), on extrait quand même le JSON
            return response.json().then(data => {
                if (!response.ok) {
                    // Si la réponse n'est pas "OK", on rejette avec le message du serveur
                    throw new Error(data.error || "Erreur inconnue");
                }
                return data;
            });
        })
        .then(data => {
            if (data.success) {
                form.style.display = 'none';
                document.getElementById('form-confirm').style.display = 'block';
            }
        })
        .catch(error => {
            console.error("Erreur :", error);
            // ICI : Ça va maintenant afficher le VRAI message d'erreur configuré dans Node.js !
            alert(error.message);
        });
});