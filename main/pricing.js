/* pricing.js */
document.addEventListener('DOMContentLoaded', () => {
    const billingCycleToggle = document.getElementById('billingCycle');
    const monthlyLabels = document.querySelectorAll('[data-monthly]');
    const yearlyLabels = document.querySelectorAll('[data-yearly]');
    const monthlySpan = document.querySelector('.toggle-label.monthly');
    const yearlySpan = document.querySelector('.toggle-label.yearly');
    const pricingCards = document.querySelectorAll('.pricing-card');
    const faqItems = document.querySelectorAll('.faq-item');

    // Initial load animation for hero section
    const heroTitle = document.querySelector('.pricing-header h1');
    const heroParagraph = document.querySelector('.pricing-header p');
    const billingToggleDiv = document.querySelector('.pricing-header .billing-toggle');

    if (heroTitle) {
        heroTitle.classList.add('animate'); // You'll need to define CSS for this in pricing.css
    }
    if (heroParagraph) {
        setTimeout(() => heroParagraph.classList.add('animate'), 300);
    }
    if (billingToggleDiv) {
        setTimeout(() => billingToggleDiv.classList.add('animate'), 600);
    }

    // Toggle Billing Cycle Functionality
    if (billingCycleToggle && monthlyLabels && yearlyLabels && monthlySpan && yearlySpan) {
        billingCycleToggle.addEventListener('change', () => {
            const isYearly = billingCycleToggle.checked;

            monthlyLabels.forEach(label => {
                const amount = label.dataset.monthly;
                const amountSpan = label.querySelector('.amount');
                if (amountSpan) {
                    amountSpan.textContent = amount;
                }
                const periodSpan = label.querySelector('.period');
                if (periodSpan) {
                    periodSpan.textContent = '/ month';
                }
                label.classList.toggle('active-monthly', !isYearly);
            });

            yearlyLabels.forEach(label => {
                const amount = label.dataset.yearly;
                const amountSpan = label.querySelector('.amount');
                if (amountSpan) {
                    amountSpan.textContent = amount;
                }
                const periodSpan = label.querySelector('.period');
                if (periodSpan) {
                    periodSpan.textContent = '/ year';
                }
                label.classList.toggle('active-yearly', isYearly);
            });

            monthlySpan.classList.toggle('active', !isYearly);
            yearlySpan.classList.toggle('active', isYearly);

            // Update the displayed price on the cards
            pricingCards.forEach(card => {
                const priceSpan = card.querySelector('.price .amount');
                const periodSpan = card.querySelector('.price .period');
                if (priceSpan && periodSpan) {
                    priceSpan.textContent = isYearly ? priceSpan.dataset.yearly : priceSpan.dataset.monthly;
                    periodSpan.textContent = isYearly ? '/ year' : '/ month';
                }
            });
        });

        // Initialize based on the 'Yearly' active class in HTML
        if (yearlySpan && yearlySpan.classList.contains('active')) {
            billingCycleToggle.checked = true;
            monthlyLabels.forEach(label => {
                const amountSpan = label.querySelector('.amount');
                if (amountSpan) {
                    amountSpan.textContent = label.dataset.monthly;
                }
                const periodSpan = label.querySelector('.period');
                if (periodSpan) {
                    periodSpan.textContent = '/ month';
                }
            });
            yearlyLabels.forEach(label => {
                const amountSpan = label.querySelector('.amount');
                if (amountSpan) {
                    amountSpan.textContent = label.dataset.yearly;
                }
                const periodSpan = label.querySelector('.period');
                if (periodSpan) {
                    periodSpan.textContent = '/ year';
                }
            });
            monthlySpan.classList.remove('active');
            yearlySpan.classList.add('active');
        } else {
            billingCycleToggle.checked = false;
            monthlyLabels.forEach(label => {
                const amountSpan = label.querySelector('.amount');
                if (amountSpan) {
                    amountSpan.textContent = label.dataset.monthly;
                }
                const periodSpan = label.querySelector('.period');
                if (periodSpan) {
                    periodSpan.textContent = '/ month';
                }
            });
            yearlyLabels.forEach(label => {
                const amountSpan = label.querySelector('.amount');
                if (amountSpan) {
                    amountSpan.textContent = label.dataset.yearly;
                }
                const periodSpan = label.querySelector('.period');
                if (periodSpan) {
                    periodSpan.textContent = '/ year';
                }
            });
            monthlySpan.classList.add('active');
            yearlySpan.classList.remove('active');
        }
    }

    // FAQ Accordion Functionality
    if (faqItems) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                item.classList.toggle('open');
                const answer = item.querySelector('.faq-answer');
                if (item.classList.contains('open')) {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    answer.style.padding = '1rem';
                } else {
                    answer.style.maxHeight = '0';
                    answer.style.padding = '0 1rem';
                }
            });
        });
    }
});