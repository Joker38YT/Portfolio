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


document.querySelector('.contact-form').addEventListener('submit', function (e) {
    e.preventDefault(); 

    const form = this;
    const payload = {
        nom: form.querySelector('input[name="nom"]').value,
        email: form.querySelector('input[name="email"]').value,
        sujet: form.querySelector('input[name="sujet"]').value,
        message: form.querySelector('textarea[name="message"]').value
    };

    fetch("/api/contact/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
        .then(response => {
          
            return response.json().then(data => {
                if (!response.ok) {
                    
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
          
            alert(error.message);
        });
});