$(function() {
    // Technique pour éviter que les éléments de la timeline apparaissent subrepticement avant le début de celle-ci (effet de clignotement) -> cf élément body dans le CSS
    gsap.set('body', {opacity: 1});

    // Gestion de la date dynamique dans le footer
    let date = document.querySelector('.footerDate');
    date.textContent = new Date().getFullYear();

    // Gestion du scrolling auto
    $(".navbar a, footer a").on("click", function(event) {
        event.preventDefault();
        let hash = this.hash;

        console.log(hash);

        $('body, html').animate({
            scrollTop: $(hash).offset().top
        }, 1000);

        $("#myNavbar").toggleClass('show');
    });

    // Gestion du formulaire
    $('#contact-form').submit( function(event) {
        event.preventDefault();

        $(".comments").empty();

        let postedData = $("#contact-form").serialize();

        $.ajax({
            type: "POST",
            url: "php/contact.php",
            data: postedData,
            dataType: "json",
            success: function (response) {
                if (response.isSuccess) {
                    // On affiche le message de succès
                    $("#contact-form").append("<div class='thank-you alert alert-success alert-dismissible fade show'><strong>Votre message a bien été envoyé.</strong> Merci de m'avoir contacté ! 😃<button type='button' class='close' data-dismiss='alert' aria-label='Fermer'><span aria-hidden='true'>&times;</span></button></div>");
                    // On réinitialise les champs du formulaire
                    $("#contact-form")[0].reset();
                } else {
                    // Sinon on fait apparaître le message d'erreur dans le <p class="comments">  du champ concerné. (Pour rappel, ce message était aussi stocké dans la variable $data au moment du traitement du formulaire par le PHP). Si un champ n'a pas d'erreur, son nomVariableError sera égal à "".
                    $("#lastName + .comments").html(response.lastNameError);
                    $("#firstName + .comments").html(response.firstNameError);
                    $("#email + .comments").html(response.emailError);
                    $("#phone + .comments").html(response.phoneError);
                    $("#subject + .comments").html(response.subjectError);
                    $("#message + .comments").html(response.messageError);
                }
            }
        });
    })

    // Gestion de l'animation sur les inputs du formulaire (je n'utilise pas de fonctions fléchées pour la compatibilité Internet Explorer)
    const inputs = document.querySelectorAll('input');
    const textarea = document.querySelector('textarea');
    const select = document.querySelector('select');

    for (let index = 0; index < inputs.length; index++) {
        let field = inputs[index];

        field.addEventListener('input', function(event) {
            if (event.target.value != "") {
                // On ajoute la classe "animation" au form-group qui est parent de l'input qui a actuellement le focus
                event.target.parentNode.classList.add('animation');
            } else if (event.target.value == "") {
                    event.target.parentNode.classList.remove('animation');
            }
        })
    }

    textarea.addEventListener('input', function(event) {
        if (event.target.value != "") {
            event.target.parentNode.classList.add('animation');
        } else if (event.target.value == "") {
            event.target.parentNode.classList.remove('animation');
        }
    })

    // Pour les éléments <select>, il vaut mieux préférer l'événement 'change' plutôt que 'input' pour la compatibilité IE & Edge
    select.addEventListener('change', function(event) {
        if (event.target.value != "") {
            event.target.parentNode.classList.add('animation');
        } else if (event.target.value == "") {
            event.target.parentNode.classList.remove('animation');
        }
    })

    // Gestion du typewriter
    const textAnim = document.querySelector('.heading h2#type-writer');
    
    new Typewriter(textAnim, {
        delay: 50,
        deleteSpeed: 30,
        loop: true
    })
    .typeString("Moi c'est Baptiste Bidaux, ")
    .pauseFor(400)
    .typeString("<strong>Développeur Web Full-Stack</strong> !")
    .pauseFor(1500)
    .deleteChars(16)
    .typeString("<span style=\"color: #4caf50; font-weight: 900;\">HTML - CSS - SCSS</span> !")
    .pauseFor(1500)
    .deleteChars(19)
    .typeString("<span style=\"color: #563d7c; font-weight: 900;\">Bootstrap</span> !")
    .pauseFor(1500)
    .deleteChars(11)
    .typeString("<span style=\"color: #777bb3; font-weight: 900;\">PHP - Symfony</span> !")
    .pauseFor(1500)
    .deleteChars(15)
    .typeString("<span style=\"color: #f7df1e; font-weight: 900;\">JavaScript</span> !")
    .pauseFor(1500)
    .deleteAll()
    .start()

    // Animations GreenSock
    var TL = gsap.timeline();
    let social = document.querySelectorAll('.share-button');

    TL
        .from('.profile-picture', {duration: 1.5, autoAlpha: 0 , x: "-70vw", ease: "back.out(2)"})
        .from('#type-writer', {duration: 1.5, autoAlpha: 0 , x: "+70vw", ease: "back.out(2)"}, "<")
        .from('.nav-item', {duration: 0.5, stagger: 0.3, y: 50, opacity: 0}, "-=0.5")
        .from(social, {duration: 2, stagger: 0.3, autoAlpha: 0, y: "+70vh"}, "<")
        .from('.bxs-chevrons-down', {duration: 2, autoAlpha: 0})

    // Reveal au Scroll
    const controller = new ScrollMagic.Controller();

    // ---------------

    skillsSection = $('section#skills');
    skillsTL = gsap.timeline();

    skillsTL
        .from('#skills .beige-divider', {duration: 1, autoAlpha: 0, y: 200})
        .from('#skills .heading', {duration: 1, autoAlpha: 0, y: 200}, "<")
        .from('.progress', {duration: 1, autoAlpha: 0, stagger: 0.2, xPercent: gsap.utils.wrap([100, -100])});

    let skillsScene = new ScrollMagic.Scene({
        triggerElement: skillsSection[0]
    })  
        // .addIndicators()
        .setTween(skillsTL)
        .reverse(false)
        .addTo(controller);

    // ---------------

    experienceSection = $('section#experience');
    experienceTL = gsap.timeline();

    experienceTL
        .from('#experience .white-divider', {duration: 1, autoAlpha: 0, y: 200})
        .from('#experience .heading', {duration: 1, autoAlpha: 0, y: 200}, "<")
        .from('.timeline', {duration: 1, opacity: 0})
        .from('.timeline-badge', {duration: 0.5, autoAlpha: 0, y: 50})
        .from('.timeline-panel', {duration: 1, autoAlpha: 0, stagger: 0.2, xPercent: gsap.utils.wrap([-200, 150, -150, 200]), yPercent: gsap.utils.wrap([100, -200, 200, -100])}, "-=1")
        .from('.timeline-heading h3', {duration: 0.3, autoAlpha: 0, y: 100}, "-=0.5")
        .from('.timeline-heading h4', {duration: 0.3, autoAlpha: 0, y: 100})
        .from('.timeline-heading p', {duration: 0.3, autoAlpha: 0, y: 100})
        .from('.timeline-body p', {duration: 0.3, autoAlpha: 0, y: 100});

    let experienceScene = new ScrollMagic.Scene({
        triggerElement: experienceSection[0]
    })
        // .addIndicators()
        .setTween(experienceTL)
        .reverse(false)
        .addTo(controller);

    // ---------------

    educationSection = $('section#education');
    educationTL = gsap.timeline();

    educationTL
        .from('#education .beige-divider', {duration: 1, autoAlpha: 0, y: 200})
        .from('#education .heading', {duration: 1, autoAlpha: 0, y: 200}, "<")
        .from('.education-block', {duration: 1.5, autoAlpha: 0, scale: 1.5, y: -400, rotation: 360, stagger: 0.5, ease: "circ.out"});

    let educationScene =  new ScrollMagic.Scene({
        triggerElement: educationSection[0]
    })
        // .addIndicators()
        .setTween(educationTL)
        .reverse(false)
        .addTo(controller);

    // ---------------

    portfolioSection = $('section#portfolio');
    portfolioTL = gsap.timeline();

    portfolioTL
        .from('#portfolio .white-divider', {duration: 1, autoAlpha: 0, y: 200})
        .from('#portfolio .heading', {duration: 1, autoAlpha: 0, y: 200}, "<")
        .from('.img-box', {duration: 1.5, scale: 0, autoAlpha: 0, stagger: 0.2});

    let portfolioScene = new ScrollMagic.Scene({
        triggerElement: portfolioSection[0]
    })
        // .addIndicators()
        .setTween(portfolioTL)
        .reverse(false)
        .addTo(controller);
        
    // ---------------

    whoAmISection = $('section#who-am-i');
    whoAmITL = gsap.timeline();

    whoAmITL
        .from('#who-am-i .beige-divider', {duration: 1, autoAlpha: 0, y: 200})
        .from('#who-am-i .heading', {duration: 1, autoAlpha: 0, y: 200}, "<")
        .from('.carousel-control-prev-icon', {duration: 2, autoAlpha: 0, x: -200})
        .from('.carousel-control-next-icon', {duration: 2, autoAlpha: 0, x: 200}, "<")
        .from('.carousel-inner', {duration: 2, autoAlpha: 0, y: 200}, "<")
        .fromTo('.carousel-indicators', {autoAlpha: 0, y:200}, {duration: 2, autoAlpha: 1, y: 0}, "<");

    let whoAmIScene = new ScrollMagic.Scene({
        triggerElement: whoAmISection[0]
    })
        // .addIndicators()
        .setTween(whoAmITL)
        .reverse(false)
        .addTo(controller);

    // ---------------

    contactSection = $('section#contact');
    contactTL = gsap.timeline();

    contactTL
        .from('#contact .white-divider', {duration: 1, autoAlpha: 0, y: 200})
        .from('#contact .heading', {duration: 1, autoAlpha: 0, y: 200}, "<")
        .from('form', {duration: 1, autoAlpha: 0, scale: 0, ease: "back.out(2)"})
        .from('.form-group', {duration: 0.5, scale: 0, stagger: 0.4})
        .from('form .row .col-12 p', {duration: 0.5, scale: 0}, "-=0.1")
        .from('input[type=submit]', {duration: 1.5, autoAlpha: 0, y: 200}, "-=0.5")
        .from('.contact-methods', {duration: 1, autoAlpha: 0, scale: 0, ease: "back.out(2)"}, 1.5)
        .from('.contact-method h4', {duration: 1, autoAlpha: 0, y: 100}, 2)
        .from('.contact-method p', {duration: 1, autoAlpha: 0, y: 100}, 2.5);

    let contactScene = new ScrollMagic.Scene({
        triggerElement: contactSection[0]
    })
        // .addIndicators()
        .setTween(contactTL)
        .reverse(false)
        .addTo(controller);
})