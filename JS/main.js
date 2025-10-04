document.addEventListener('DOMContentLoaded', () => {
    
    // --- CUSTOM SLIDER LOGIC (with SWIPE functionality) ---
    function initializeCustomSliders() {
        document.querySelectorAll('.slider-container').forEach(container => {
            const wrapper = container.querySelector('.slider-wrapper');
            const slides = Array.from(container.querySelectorAll('.slider-item'));
            const prevBtn = container.querySelector('.prev-btn');
            const nextBtn = container.querySelector('.next-btn');
            
            if (!wrapper || slides.length <= 1) {
                if(prevBtn) prevBtn.style.display = 'none';
                if(nextBtn) nextBtn.style.display = 'none';
                return;
            }

            let currentIndex = 0;
            const slideCount = slides.length;
            let touchstartX = 0;
            let touchendX = 0;

            function updateArrows() {
                if (prevBtn && nextBtn) {
                    prevBtn.disabled = currentIndex === 0;
                    nextBtn.disabled = currentIndex >= slideCount - 1;
                }
            }

            function goToSlide(index) {
                if (window.innerWidth > 768) {
                    wrapper.style.transform = 'translateX(0)';
                    if (prevBtn && nextBtn) {
                        prevBtn.style.display = 'none';
                        nextBtn.style.display = 'none';
                    }
                    return;
                }
                
                 if (prevBtn && nextBtn) {
                    prevBtn.style.display = 'flex';
                    nextBtn.style.display = 'flex';
                }

                currentIndex = Math.max(0, Math.min(index, slideCount - 1));

                const scrollAmount = slides[currentIndex].offsetLeft;
                wrapper.style.transform = `translateX(-${scrollAmount}px)`;
                updateArrows();
            }
            
            function handleGesture() {
                if (touchendX < touchstartX - 50) { // Swiped left
                    goToSlide(currentIndex + 1);
                }
                if (touchendX > touchstartX + 50) { // Swiped right
                    goToSlide(currentIndex - 1);
                }
            }
            
            wrapper.addEventListener('touchstart', e => {
                touchstartX = e.changedTouches[0].screenX;
            }, { passive: true });

            wrapper.addEventListener('touchend', e => {
                touchendX = e.changedTouches[0].screenX;
                handleGesture();
            });

            if (nextBtn) {
                nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
            }
            
            window.addEventListener('resize', () => goToSlide(currentIndex));
            goToSlide(0);
        });
    }

    initializeCustomSliders();
    
    // --- Sign In/Sign Up Modal Functionality (remains the same) ---
    const modal = document.getElementById("signInUpModal");
    const openModalBtns = document.querySelectorAll(".signin-button");
    const closeButton = modal ? modal.querySelector(".close-button") : null;
    const signInPanel = document.getElementById("signInPanel");
    const registerPanel = document.getElementById("registerPanel");
    const switchToRegisterLinks = document.querySelectorAll(".switch-to-register");
    const switchToSignInLink = document.querySelector(".switch-to-signin");

    if (openModalBtns.length > 0 && modal) {
        openModalBtns.forEach(btn => {
            btn.onclick = function() {
                modal.style.display = "flex";
                if (signInPanel) signInPanel.classList.add("active");
                if (registerPanel) registerPanel.classList.remove("active");
            };
        });

        if(closeButton) {
            closeButton.onclick = function() {
                modal.style.display = "none";
            };
        }

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });

        if(switchToRegisterLinks) {
            switchToRegisterLinks.forEach(link => {
                link.onclick = function(event) {
                    event.preventDefault();
                    signInPanel.classList.remove("active");
                    registerPanel.classList.add("active");
                };
            });
        }

        if(switchToSignInLink){
            switchToSignInLink.onclick = function(event) {
                event.preventDefault();
                registerPanel.classList.remove("active");
                signInPanel.classList.add("active");
            };
        }
    }

    // --- FORM SUBMISSIONS (LOGIN/REGISTER) (remains the same) ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            try {
                const response = await fetch('register.php', { method: 'POST', body: new URLSearchParams(formData) });
                const data = await response.json();
                if (data.success) {
                    signInPanel.classList.add("active");
                    registerPanel.classList.remove("active");
                    registerForm.reset();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            } catch (error) { console.error('Registration error:', error); }
        });
    }

    const signInForm = document.getElementById('signInForm');
    if (signInForm) {
        signInForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(signInForm);
            try {
                const response = await fetch('login.php', { method: 'POST', body: new URLSearchParams(formData) });
                const data = await response.json();
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert(data.message);
                }
            } catch (error) { console.error('Login error:', error); }
        });
    }

    // --- REAL-TIME PASSWORD VALIDATION (remains the same) ---
    const registerPasswordInput = document.getElementById('registerPassword');
    const lengthRule = document.getElementById('length');
    const capitalRule = document.getElementById('capital');
    const specialRule = document.getElementById('special');

    if (registerPasswordInput && lengthRule && capitalRule && specialRule) {
        registerPasswordInput.addEventListener('input', () => {
            const password = registerPasswordInput.value;
            if (password.length >= 6) { lengthRule.classList.replace('invalid', 'valid'); } 
            else { lengthRule.classList.replace('valid', 'invalid'); }
            if (/[A-Z]/.test(password)) { capitalRule.classList.replace('invalid', 'valid'); } 
            else { capitalRule.classList.replace('valid', 'invalid'); }
            if (/[^A-Za-z0-9]/.test(password)) { specialRule.classList.replace('invalid', 'valid'); } 
            else { specialRule.classList.replace('valid', 'invalid'); }
        });
    }
});