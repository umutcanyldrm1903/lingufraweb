(() => {
    const revealItems = document.querySelectorAll('[data-lfps-reveal]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.18 });

    revealItems.forEach((item) => observer.observe(item));

    const modal = document.getElementById('lfpsVideoModal');
    const modalTitle = document.getElementById('lfpsVideoTitle');
    const player = document.getElementById('lfpsVideoPlayer');

    if (!modal || !modalTitle || !player) {
        return;
    }

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        player.pause();
        player.removeAttribute('src');
        player.removeAttribute('poster');
        player.load();
        document.body.style.overflow = '';
    };

    document.querySelectorAll('.lfps-video-trigger').forEach((button) => {
        button.addEventListener('click', () => {
            const url = button.getAttribute('data-video-url');
            const title = button.getAttribute('data-video-title') || 'Video';
            const poster = button.getAttribute('data-video-poster') || '';

            if (!url) {
                return;
            }

            modalTitle.textContent = title;
            player.src = url;

            if (poster) {
                player.poster = poster;
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            player.play().catch(() => {});
        });
    });

    modal.querySelectorAll('[data-video-close]').forEach((element) => {
        element.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
