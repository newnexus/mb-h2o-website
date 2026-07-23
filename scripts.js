const header = document.getElementById('header');
let lastScroll = 0;
let mouseY = 0;
let lastMouseMoveTime = 0;

// Track mouse position (desktop only)
document.addEventListener('mousemove', (e) => {
    mouseY = e.clientY + window.scrollY;
    lastMouseMoveTime = Date.now();
    if (e.clientY < 50) {
        header.classList.remove('hidden');
    }
    updateSectionOpacity();
    updateFaqExpansion();
});

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    if (currentScroll > 100 && currentScroll > lastScroll) {
        header.classList.add('hidden');
    } else {
        header.classList.remove('hidden');
    }
    lastScroll = currentScroll;
    updateSectionOpacity();
    updateFaqExpansion();
});

// Mobile menu toggle (fresh implementation)
const menuToggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('nav');
const body = document.body;

menuToggle.addEventListener('click', () => {
    const isOpen = nav.classList.contains('open');
    nav.classList.toggle('open', !isOpen);
    body.classList.toggle('menu-open', !isOpen);
    // Optional: Add/remove icon rotation for visual feedback
    menuToggle.querySelector('img').style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
});

// Close menu when clicking a link (improves UX)
nav.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        nav.classList.remove('open');
        body.classList.remove('menu-open');
        menuToggle.querySelector('img').style.transform = 'rotate(0deg)';
    });
});

// Close on resize to desktop (if switching views)
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        nav.classList.remove('open');
        body.classList.remove('menu-open');
        menuToggle.querySelector('img').style.transform = 'rotate(0deg)';
    }
    updateSectionOpacity();
    updateFaqExpansion();
});

const carousels = document.querySelectorAll('.carousel');
carousels.forEach(carousel => {
    const imgs = carousel.querySelectorAll('img');
    const bullets = carousel.parentElement.querySelectorAll('.carousel-bullet');
    let current = 0;

    function showImage(index) {
        imgs.forEach((img, i) => {
            img.classList.toggle('active', i === index);
        });
        bullets.forEach((bullet, i) => {
            bullet.classList.toggle('active', i === index);
        });
        current = index;
    }

    bullets.forEach((bullet, index) => {
        bullet.addEventListener('click', () => {
            showImage(index);
        });
    });

    setInterval(() => {
        showImage((current + 1) % imgs.length);
    }, 3000);
});

const thumbnails = document.querySelectorAll('.youtube-thumbnail');
thumbnails.forEach(thumb => {
    const originalContent = thumb.innerHTML;
    thumb.addEventListener('click', (e) => {
        if (e.target.classList.contains('youtube-close')) return;
        const id = thumb.dataset.youtubeId;
        thumb.innerHTML = `
            <div class="youtube-video-container">
                <iframe width="100%" height="315" src="https://www.youtube.com/embed/${id}" frameborder="0" allowfullscreen></iframe>
                <button class="youtube-close">×</button>
            </div>`;
        const closeButton = thumb.querySelector('.youtube-close');
        closeButton.addEventListener('click', () => {
            thumb.innerHTML = originalContent;
            thumb.addEventListener('click', (e) => {
                if (e.target.classList.contains('youtube-close')) return;
                thumb.innerHTML = `
                    <div class="youtube-video-container">
                        <iframe width="100%" height="315" src="https://www.youtube.com/embed/${id}" frameborder="0" allowfullscreen></iframe>
                        <button class="youtube-close">×</button>
                    </div>`;
                const newCloseButton = thumb.querySelector('.youtube-close');
                newCloseButton.addEventListener('click', () => {
                    thumb.innerHTML = originalContent;
                });
            });
        });
    });
});

function updateSectionOpacity() {
    const sections = document.querySelectorAll('.section-container');
    let closestSection = null;
    let minDistance = Infinity;

    // Detect if mobile (or no recent mouse activity)
    const isMobile = window.innerWidth < 768 || (Date.now() - lastMouseMoveTime > 1000); // Fallback after 1s inactivity
    const focusY = isMobile ? (window.scrollY + window.innerHeight / 2) : mouseY;

    sections.forEach(section => {
        const rect = section.getBoundingClientRect();
        const sectionTop = rect.top + window.scrollY;
        const sectionHeight = rect.height;
        const sectionCenter = sectionTop + sectionHeight / 2;
        const distance = Math.abs(focusY - sectionCenter);

        if (distance < minDistance) {
            minDistance = distance;
            closestSection = section;
        }
    });

    sections.forEach(section => {
        const rect = section.getBoundingClientRect();
        const sectionTop = rect.top + window.scrollY;
        const sectionHeight = rect.height;
        const sectionCenter = sectionTop + sectionHeight / 2;
        const distance = Math.abs(focusY - sectionCenter);
        const maxDistance = window.innerHeight / 2;
        const opacity = 1 - (distance / maxDistance) * 0.4;
        section.style.opacity = Math.max(0.6, Math.min(1, opacity));

        const elements = section.querySelectorAll('.media img, .media video, .media .youtube-thumbnail, .text-content h2, .text-content p');
        if (section === closestSection) {
            section.classList.add('animate');
            elements.forEach(element => element.classList.add('animate'));
        } else {
            section.classList.remove('animate');
            elements.forEach(element => element.classList.remove('animate'));
        }
    });
}

function updateFaqExpansion() {
    const faqs = document.querySelectorAll('.faq-container');
    let closestFaq = null;
    let minDistance = Infinity;

    // Detect if mobile (or no recent mouse activity)
    const isMobile = window.innerWidth < 768 || (Date.now() - lastMouseMoveTime > 1000);
    const focusY = isMobile ? (window.scrollY + window.innerHeight / 2) : mouseY;

    faqs.forEach(faq => {
        const rect = faq.getBoundingClientRect();
        const faqTop = rect.top + window.scrollY;
        const faqHeight = rect.height;
        const faqCenter = faqTop + faqHeight / 2;
        const distance = Math.abs(focusY - faqCenter);

        if (distance < minDistance) {
            minDistance = distance;
            closestFaq = faq;
        }

        // Ensure all FAQs are visible by default
        faq.classList.add('animate');
    });

    faqs.forEach(faq => {
        const answer = faq.querySelector('.faq-answer');
        const icon = faq.querySelector('.faq-foldout-icon');
        const questionContainer = faq.querySelector('.faq-question-container');
        const isManuallyExpanded = questionContainer.classList.contains('manually-expanded');

        // Only update non-manually expanded FAQs
        if (!isManuallyExpanded) {
            if (faq === closestFaq) {
                answer.classList.add('expanded', 'animate');
                icon.classList.add('expanded');
            } else {
                answer.classList.remove('expanded', 'animate');
                icon.classList.remove('expanded');
            }
        }
    });
}

const faqQuestions = document.querySelectorAll('.faq-question-container');
faqQuestions.forEach(question => {
    question.addEventListener('click', () => {
        const faqContainer = question.closest('.faq-container');
        const answer = faqContainer.querySelector('.faq-answer');
        const icon = question.querySelector('.faq-foldout-icon');
        const isExpanded = answer.classList.contains('expanded');

        // Collapse all FAQs
        document.querySelectorAll('.faq-container').forEach(otherFaq => {
            const otherAnswer = otherFaq.querySelector('.faq-answer');
            const otherIcon = otherFaq.querySelector('.faq-foldout-icon');
            const otherQuestion = otherFaq.querySelector('.faq-question-container');
            otherAnswer.classList.remove('expanded', 'animate');
            otherIcon.classList.remove('expanded');
            otherQuestion.classList.remove('manually-expanded');
        });

        // Toggle the clicked FAQ
        if (!isExpanded) {
            answer.classList.add('expanded', 'animate');
            icon.classList.add('expanded');
            question.classList.add('manually-expanded');

            // Scroll to the FAQ
            const rect = faqContainer.getBoundingClientRect();
            const scrollTop = window.pageYOffset + rect.top - (window.innerHeight - rect.height) / 2;
            window.scrollTo({ top: scrollTop, behavior: 'smooth' });
        }
    });
});

// Initial calls to set up animations
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.faq-container').forEach(faq => {
        faq.classList.add('animate'); // Ensure all FAQs are visible on load
    });
    updateSectionOpacity();
    updateFaqExpansion();
});