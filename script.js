document.addEventListener('DOMContentLoaded', function() {
    var options = {
        strings: ["Bienvenue!", "Je suis Développeur Web.", "Découvrez mon portfolio."], // Les phrases qui défileront
        typeSpeed: 70,  // Vitesse de frappe (en ms)
        backSpeed: 50,  // Vitesse d'effacement (en ms)
        backDelay: 1500, // Délai avant l'effacement après la frappe complète (en ms)
        startDelay: 500, // Délai avant de commencer à taper
        loop: true // Répéter le cycle
    };

    var typed = new Typed('.typed-text', options);
});