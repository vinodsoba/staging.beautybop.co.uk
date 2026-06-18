define([], function () {

    'use strict';

    window.addEventListener('load', function () {

        const toggle  = document.getElementById('mobile-menu-toggle');
        const menu    = document.getElementById('bb-mobile-menu');
        const overlay = document.getElementById('bb-mobile-overlay');
        const close   = document.getElementById('bb-mobile-menu-close');

        console.log({
            toggle,
            menu,
            overlay,
            close
        });

        if (toggle && menu && overlay) {

            toggle.addEventListener('click', function (e) {

                e.preventDefault();

                menu.classList.remove('hidden');
                overlay.classList.remove('hidden');

            });

        }

        if (close && menu && overlay) {

            close.addEventListener('click', function () {

                menu.classList.add('hidden');
                overlay.classList.add('hidden');

            });

        }

        if (overlay && menu) {

            overlay.addEventListener('click', function () {

                menu.classList.add('hidden');
                overlay.classList.add('hidden');

            });

        }


        // search form toggle
        const mobileSearchToggle = document.getElementById('mobile-search-toggle');
        const desktopSearchToggle = document.getElementById('desktop-search-toggle');

        const mobileSearchPanel = document.getElementById('mobile-search-panel');
        const desktopSearchPanel = document.getElementById('desktop-search-panel');
                
        if (mobileSearchToggle && mobileSearchPanel) {

            mobileSearchToggle.addEventListener('click', function () {

                mobileSearchPanel.classList.toggle('max-h-0');
                mobileSearchPanel.classList.toggle('max-h-32');

            });

        }

        if (desktopSearchToggle && desktopSearchPanel) {

            desktopSearchToggle.addEventListener('click', function () {

                desktopSearchPanel.classList.toggle('max-h-0');
                desktopSearchPanel.classList.toggle('max-h-32');

            });

        }

    });

});
