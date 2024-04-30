document.addEventListener('DOMContentLoaded', async function (e) {
        const backBtn = document.querySelector('#back');
        const forwardBtn = document.querySelector('#forward');
        const newFile = document.querySelector('#newFile');
        const newFolder = document.querySelector('#newFolder');
        const deleteFile = document.querySelector('#deleteFile');
        const sortByNameBtn = document.querySelector('#sortByName');
        const sortByDateBtn = document.querySelector('#sortByDate');
        const sortBySizeBtn = document.querySelector('#sortBySize');
        const resetFilterBtn = document.querySelector('#resetFilter');
        const sortByAscBtn = document.querySelector('#sortByAsc');
        const sortByDescBtn = document.querySelector('#sortByDesc');

        function resetFilterURLParams() {
                window.location.href = window.location.href.replace(/([?&])(sortBy)=(date|size|name)/g, '');
        }

        function sortBy(criteria) {
                // Use URLSearchParams for cleaner manipulation
                const url = new URL(window.location.href);

                // Delete any existing sortBy parameters
                url.searchParams.delete('sortBy');

                // Set the new sortBy parameter
                url.searchParams.set('sortBy', criteria);

                // Update the window location with the modified URL
                window.location.href = url.toString();
        }

        function sortOrder(criteria) {
                // Use URLSearchParams for cleaner manipulation
                const url = new URL(window.location.href);

                // Delete any existing sortBy parameters
                url.searchParams.delete('sortOrder');

                // Set the new sortBy parameter
                url.searchParams.set('sortOrder', criteria);

                // Update the window location with the modified URL
                window.location.href = url.toString();
        }

        backBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.history.back();
        });

        forwardBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.history.forward();
        });

        sortByNameBtn.addEventListener('click', function (e) {
                e.preventDefault();

                sortBy('name');
        });

        sortByDateBtn.addEventListener('click', function (e) {
                e.preventDefault();

                sortBy('date');
        });

        sortBySizeBtn.addEventListener('click', function (e) {
                e.preventDefault();

                sortBy('size');
        });

        resetFilterBtn.addEventListener('click', function (e) {
                e.preventDefault();
                resetFilterURLParams();
        });

        sortByAscBtn.addEventListener('click', function (e) {
                e.preventDefault();

                sortOrder('asc');
        });

        sortByDescBtn.addEventListener('click', function (e) {
                e.preventDefault();

                sortOrder('desc');
        });

        const tags = await fetch('http://localhost:8000/Controller/Tag/GetAll.php')
            .then(response => response.json());

        if(tags.length > 0) {
                tags.map(tag => {
                        const el = document.getElementById(tag.path + "&tag_name=" + tag.tag_name);
                        el.addEventListener('click', function (e) {
                                e.preventDefault();
                                const modal = document.getElementById(tag.path + "&tag_name=" + tag.tag_name + "&modal");
                                modal.classList.remove('hidden');

                                modal.onmouseleave = function () {
                                        modal.classList.add('hidden');
                                }
                        });
                });
        }
});