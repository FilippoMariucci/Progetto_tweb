
$(document).ready(function() {
    console.log('pages.azienda caricato');
    
    const currentRoute = window.LaravelApp?.route || '';
    if (currentRoute !== 'azienda') {
        return;
    }
    
    const pageData = window.PageData || {};
    let selectedProducts = [];
    
    // Il tuo codice JavaScript qui...
    $(document).ready(function() {
    console.log('Pagina azienda caricata');
    
    // Animazione contatori (effetto incremento)
    function animateCounters() {
        $('.display-4, h3').each(function() {
            const $this = $(this);
            const text = $this.text();
            const match = text.match(/(\d+)\+?/);
            
            if (match) {
                const finalNumber = parseInt(match[1]);
                const duration = 2000; // 2 secondi
                const increment = finalNumber / (duration / 50);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= finalNumber) {
                        current = finalNumber;
                        clearInterval(timer);
                    }
                    $this.text(Math.floor(current) + (text.includes('+') ? '+' : ''));
                }, 50);
            }
        });
    }
    
    // Avvia animazione contatori quando sono visibili
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect(); // Esegui solo una volta
            }
        });
    });
    
    const numbersSection = document.querySelector('.bg-primary');
    if (numbersSection) {
        observer.observe(numbersSection);
    }
    
    // Smooth scrolling per i link interni
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });
    
    // Tracciamento interazioni
    $('.btn').on('click', function() {
        const btnText = $(this).text().trim();
        console.log('Pulsante cliccato:', btnText);
    });
});
});