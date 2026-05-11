const items = document.querySelectorAll('.projet-item');
        let timer = null;
        let mouseMoved = false;

        items[0].classList.add('actif');

        items.forEach(item => {
            item.addEventListener('mouseenter', () => {
                mouseMoved = false; // reset au moment où on entre

                timer = setTimeout(() => {
                    // N'active que si la souris a bougé pendant le délai
                    if (!mouseMoved) return;
                    items.forEach(i => i.classList.remove('actif'));
                    item.classList.add('actif');
                }, 120);
            });

            item.addEventListener('mousemove', () => {
                mouseMoved = true; // la souris est en mouvement sur cet item
            });

            item.addEventListener('mouseleave', () => {
                clearTimeout(timer);
            });
        });