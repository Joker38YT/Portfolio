function enClick(elem) {
    const dejàOuvert = elem.classList.contains("active");
    document.querySelectorAll(".clicable").forEach(e => e.classList.remove("active"));
    if (!dejàOuvert) {
        elem.classList.add("active");
    }
    setTimeout(() => {
        elem.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }, 10);
}

document.addEventListener("DOMContentLoaded", () => {
    // Sélectionne tous les liens qui entourent un joystick
    const links = document.querySelectorAll("a:has(.joystick)");

    links.forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault(); // Empêche la redirection immédiate

            const joystick = link.querySelector(".joystick");
            
            // Ajoute une classe temporaire pour simuler l'inclinaison
            joystick.classList.add("active");

            // Attend la durée de la transition CSS (150ms) avant de naviguer
            setTimeout(() => {
                window.location.href = link.href;
            }, 150);
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.contact-form');
    
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('contact.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.style.display = 'none';
                    document.getElementById('form-confirm').style.display = 'block';
                } else {
                    alert('Erreur : ' + (result.message || "Erreur lors de l'envoi"));
                }
            } catch (error) {
                console.error("Erreur :", error);
                alert("Erreur JS/Réseau : " + error.message);
            }
        });
    }
});