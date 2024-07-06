<script>

    document

    document.querySelector('.menu .dropdown').addEventListener('mouseenter', function() {
        document.querySelector('.menu .dropdown-content').classList.add('d-block');
    });

    document.querySelector('.menu .dropdown').addEventListener('mouseleave', function() {
        document.querySelector('.menu .dropdown-content').classList.remove('d-block');
    });

    document.querySelector('.menu-mobile .global-menu').addEventListener('click', function() {
        let dropdownContent = document.querySelector('.menu-mobile .dropdown-content');
        let dropdownContentItems = document.querySelector('.menu-mobile .dropdown-content .items');
        let otherDropdownContentItems = document.querySelector('.menu-mobile .dropdown-content .lang');

        // Fecha o outro menu antes de abrir este menu
        otherDropdownContentItems.classList.add('d-none');

        // Fecha o conteúdo dropdown se estiver aberto
        if (!dropdownContent.classList.contains('d-block')) {
            dropdownContent.classList.add('d-block');
        }

        // Alterna o estado do menu atual
        toggleMenu(dropdownContentItems);
    });

    document.querySelector('.menu-mobile .global-icon').addEventListener('click', function() {
        let dropdownContent = document.querySelector('.menu-mobile .dropdown-content');
        let dropdownContentItems = document.querySelector('.menu-mobile .dropdown-content .lang');
        let otherDropdownContentItems = document.querySelector('.menu-mobile .dropdown-content .items');

        // Fecha o outro menu antes de abrir este menu
        otherDropdownContentItems.classList.add('d-none');

        // Fecha o conteúdo dropdown se estiver aberto
        if (!dropdownContent.classList.contains('d-block')) {
            dropdownContent.classList.add('d-block');
        }

        // Alterna o estado do menu atual
        toggleMenu(dropdownContentItems);
    });

    function toggleMenu(dropdownContentItems) {
        if (dropdownContentItems.classList.contains('d-none')) {
            dropdownContentItems.classList.remove('d-none');
        } else {
            document.querySelector('.menu-mobile .dropdown-content').classList.remove('d-block');
            dropdownContentItems.classList.add('d-none');
        }
    }

    // Start web app

    const addBtns = document.getElementsByClassName('install-app');
    error = false;
    if (!navigator.serviceWorker.controller) {
        navigator.serviceWorker.register(<?php echo '"' . asset('js/sw.js?v=1') . '"' ?>).then(function (reg) {
            console.log("Service worker has been registered for scope: " + reg.scope);
        })
    }

    function isPWA(){
        return window.matchMedia('(display-mode: standalone)').matches
    }

    function setButtonWPA(e) {
        for(let addBtn of addBtns) {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            // addBtn.style.setProperty('display', 'block', 'important');

            addBtn.addEventListener("click", (e) => {
                e.preventDefault();

                // Show the prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === "accepted") {
                    console.log("User accepted the prompt");
                } else {
                    console.log("User dismissed the prompt");
                }
                deferredPrompt = null;
                });
            });
        }
    }

    function ativeWPA(){
        let deferredPrompt;
        window.addEventListener("beforeinstallprompt", setButtonWPA);
    }

    if(!isPWA()) {
        ativeWPA();
    }

    // End web app
</script>
