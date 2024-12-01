class SimpleWire {
    constructor() {
        this.setupEventListeners();
        this.processingForms = new Set();
        this.setupNavigation();
    }

    setupEventListeners() {
        // Handle click events
        document.addEventListener('click', (e) => {
            const clickTarget = e.target.closest('[wire\\:click]');
            if (clickTarget) {
                const action = clickTarget.getAttribute('wire:click');
                this.handleEvent(action, {}, clickTarget);
            }
        });

        // Handle input events
        document.addEventListener('input', (e) => {
            const modelTarget = e.target.closest('[wire\\:model]');
            if (modelTarget) {
                const action = 'count';
                this.handleEvent(action, { value: e.target.value });
            }
        });

        // Handle form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('[wire\\:submit]');
            if (form) {
                e.preventDefault();
                const action = form.getAttribute('wire:submit');
                
                // Collect form data
                const formData = {};
                form.querySelectorAll('[wire\\:model]').forEach(input => {
                    const model = input.getAttribute('wire:model');
                    formData[model] = input.value;
                });

                this.handleEvent(action, formData, form);
            }
        });
    }

    setupNavigation() {
        // Handle wire:navigate links
        document.addEventListener('click', (e) => {
            const navLink = e.target.closest('[wire\\:navigate]');
            if (navLink) {
                e.preventDefault();
                const component = navLink.getAttribute('wire:navigate');
                this.navigate(component);
                
                // Update URL without reload
                const url = navLink.href;
                window.history.pushState({ component }, '', url);
            }
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.component) {
                this.navigate(e.state.component);
            }
        });
    }

    async navigate(component) {
        try {
            const response = await fetch('/public/handle-wire.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'navigate',
                    data: { component }
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            this.updateMainContent(html);
        } catch (error) {
            console.error('Error:', error);
        }
    }

    updateMainContent(html) {
        const mainContent = document.querySelector('[wire\\:content]');
        if (mainContent) {
            mainContent.innerHTML = html;
        }
    }

    async handleEvent(action, data = {}, element = null) {
        try {
            // Handle loading states
            if (element) {
                this.setLoadingState(element, true);
                if (element.tagName === 'FORM') {
                    this.setFormProcessing(element, true);
                }
            }

            const response = await fetch('/public/handle-wire.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: action,
                    data: data
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            this.updateDOM(html);
        } catch (error) {
            console.error('Error:', error);
        } finally {
            // Reset loading states
            if (element) {
                this.setLoadingState(element, false);
                if (element.tagName === 'FORM') {
                    this.setFormProcessing(element, false);
                }
            }
        }
    }

    setLoadingState(element, isLoading) {
        if (isLoading) {
            element.setAttribute('wire:loading', '');
            // Disable the element while loading
            if (element.tagName !== 'FORM') {
                element.disabled = true;
            }
        } else {
            element.removeAttribute('wire:loading');
            if (element.tagName !== 'FORM') {
                element.disabled = false;
            }
        }
    }

    setFormProcessing(form, isProcessing) {
        if (isProcessing) {
            this.processingForms.add(form);
            form.setAttribute('wire:processing', '');
            // Disable all form elements
            form.querySelectorAll('input, textarea, select, button').forEach(el => {
                el.disabled = true;
            });
        } else {
            this.processingForms.delete(form);
            form.removeAttribute('wire:processing');
            // Re-enable all form elements
            form.querySelectorAll('input, textarea, select, button').forEach(el => {
                el.disabled = false;
            });
        }
    }

    updateDOM(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const newElement = tempDiv.firstElementChild;
        
        // Find the matching element to replace
        const targetElement = document.querySelector(`.${newElement.className}`);
        if (targetElement) {
            // Preserve loading states for forms
            if (targetElement.tagName === 'FORM' && this.processingForms.has(targetElement)) {
                this.setFormProcessing(newElement, true);
            }
            targetElement.replaceWith(newElement);
        } else {
            // If no matching element found, update the content area
            const contentArea = document.querySelector('[wire\\:content]');
            if (contentArea) {
                contentArea.innerHTML = html;
            }
        }
    }
}

// Initialize SimpleWire when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.wire = new SimpleWire();
});