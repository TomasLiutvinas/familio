<style>
    /* Force darker red for action links like EDIT */
    .fi-ta-text-item a,
    .fi-ta-actions a,
    .fi-link,
    [x-data] a[class*="fi-link"] {
        color: rgb(185, 28, 28) !important;
    }

    .fi-ta-text-item a:hover,
    .fi-ta-actions a:hover,
    .fi-link:hover,
    [x-data] a[class*="fi-link"]:hover {
        color: rgb(220, 38, 38) !important;
    }

    /* Override fixed height constraint */
    .fi-logo {
        height: auto !important;
        min-height: auto !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Subtle fade-in animation for Portal text */
    @keyframes fade-pulse {
        0%, 100% {
            opacity: 0.8;
        }
        50% {
            opacity: 1;
        }
    }

    .portal-text {
        animation: fade-pulse 3s ease-in-out infinite;
    }

    /* Brand container with vertical centering and nudge */
    .brand-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 3px;
    }

    /* Larger branding on login page only */
    .fi-simple-page .brand-container .brand-familio {
        font-size: 36px !important;
    }

    .fi-simple-page .brand-container .brand-portal {
        font-size: 33px !important;
    }
</style>
</text>

<div class="brand-container">
    <span style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 50%, #991b1b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 2px 8px rgba(153, 27, 27, 0.3); font-size: 24px; line-height: 1;" class="brand-familio font-black tracking-tight">
        Familio
    </span>
    <span class="brand-portal portal-text font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase leading-none" style="font-size: 22px; line-height: 1;">
        Portal
    </span>
</div>
</text>
