let fetch = wp.apiFetch;
let { __ } = wp.i18n;

(function() {

    //After the dom content is loaded, initialize and fetch the data
    window.addEventListener('DOMContentLoaded', () => {
        //initialize the script
        SpaceX.init();

        //fetch data and display dom
        SpaceX.fetchData();
    });

    // SpaceX Object
    let SpaceX = {
        data: {},
        path: '/spx/v1/capsules',

        /***
         * initialize all the stuff
         *
         */
        init: function () {

            let searchForm     = document.querySelector('.spx-grid-searchbox');
            let pagiBtns       = document.querySelectorAll('.spx-grid-pagination-button');
            let gridItem       = document.querySelectorAll('.spx-grid-item');
            let popupCloseBtn  = document.querySelector('.spx-pupup-close-btn');

            //submit event
            searchForm.addEventListener('submit', this.searchFormSubmit);

            //close popup event
            popupCloseBtn.addEventListener('click', this.closePopup);

            //pagination event
            if( pagiBtns.length > 0 ) {
                Array.from(pagiBtns).forEach(function(pagiBtn) {
                    pagiBtn.addEventListener('click', SpaceX.paginate);
                })
            }

            //grid item event
            if( gridItem.length > 0 ) {
                Array.from(gridItem).forEach(function(item) {
                    item.addEventListener('click', SpaceX.showPopup);
                })
            }

        },

        /***
         * Create an grid item dom and append to the list
         *
         * @param item single data item object
         */
        createItem: async function (item) {

            // grid items selector
            let gridItems = document.querySelector('.spx-grid-items');

            // create grid item dom
            let gridItem = document.createElement('div');
            gridItem.classList.add('spx-grid-item');

            if( item.launch.links ) {
                // create image dom
                let itemImage = document.createElement('img');
                itemImage.setAttribute('src', item.launch.links.patch.small);

                //append item image
                gridItem.appendChild(itemImage);
            }

            // create input hidden dom to store the item data
            let input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.classList.add('spx-grid-item-data');
                input.value = JSON.stringify(item);

                //append input hidden item data
                gridItem.appendChild(input);

            // create grid item content dom
            let gridItemContent = document.createElement('div');
            gridItemContent.classList.add('spx-grid-item-content');

            // create item heading dom
            let gridItemHeading = document.createElement('h3');
            gridItemHeading.innerText = __("Launch: ") + item.launch.name;

            // create item paragraph dom
            let gridItemPara = document.createElement('p');
            gridItemPara.innerText = __("Capsule Serial: ") + item.capsule.serial;

            //append heading and content inside content div
            gridItemContent.appendChild(gridItemHeading).appendChild(gridItemPara);

            //append item content inside item div
            gridItem.appendChild(gridItemContent);

            //append item inside the items div
            gridItems.appendChild(gridItem);
        },

        /***
         * Create the pagination DOM and display
         *
         * @param data api response data object
         */
        createPagination: function(data) {

            //select pagination wrapper
            let pagiWrap = document.querySelector('.spx-grid-pagination-wrapper');
            pagiWrap.innerHTML = '';

            //create pagination dom
            let pagiBtns = document.createElement('div');
            pagiBtns.classList.add('spx-grid-pagination-buttons');

            //create prev button dom
            if( data.hasPrevPage ) {

                let prevBtn = document.createElement('button');
                prevBtn.classList.add('spx-grid-pagination-button', 'spx-grid-pagination-prev-button');
                prevBtn.innerText = __( 'Prev', "spacex-craft");

                //set prev page number
                let prevNumber = data.page - 1;
                prevBtn.setAttribute('data-page', prevNumber);

                //append prev button
                pagiBtns.append(prevBtn);
            }

            //create other buttons dom
            if( data.totalPages && data.page ) {
                let pageDiff = data.totalPages - data.page;

                if( pageDiff > 0 ) {
                    console.log(data.page + ' - diff: ' + pageDiff);
                    for( let i = data.page; i < data.page + 3; i++ ) {

                        // don't create paginate button if current page number is greater the total page number, stop at the final page
                        if( i >= data.totalPages ) {
                            break;
                        }

                        //create pagination normal button
                        let pagiBtn = document.createElement('button');
                        pagiBtn.classList.add('spx-grid-pagination-button');
                        pagiBtn.innerText = i;
                        pagiBtn.setAttribute('data-page', i);

                        if( i === data.page ) {
                            pagiBtn.classList.add('spx-grid-pagination-button-active');
                        }

                        //append pagination normal button
                        pagiBtns.append(pagiBtn);
                    }
                }
            }

            //create next button dom
            if( data.hasNextPage ) {

                let nextBtn = document.createElement('button');
                nextBtn.classList.add('spx-grid-pagination-button', 'spx-grid-pagination-next-button');
                nextBtn.innerText = __( 'Next', "spacex-craft");

                //set next page number
                let nextNumber = data.page + 1;
                nextBtn.setAttribute('data-page', nextNumber);

                //append next button
                pagiBtns.append(nextBtn);
            }

            //append pagination buttons
            pagiWrap.append(pagiBtns);

            //render and initialize
            SpaceX.init();
        },

        /***
         * Fetch API data and display the grid
         */
        fetchData: async function () {

            await fetch( {
                path: this.path,
                method: 'GET',
            } ).then(
                async ( result ) => {
                    this.data = await result.data;

                    // Display the grid items after fetching data
                    let gridItems = document.querySelector('.spx-grid-items');
                    gridItems.innerHTML = '';

                    if( result.data.docs.length > 0 ) {

                        await Promise.all(result.data.docs.map(async (item) => {

                            //launch id
                            let launch_id = item.launches[0];

                            // getting launches data api path
                            let path = "/spx/v1/launches?id=" + launch_id;

                            // single fetch api arguments
                            let args = {
                                path: path
                            };

                            // get launch data of current capsule
                            let launchResponse = await SpaceX.singleFetch(args);

                            // when empty response, skip it
                            if( !launchResponse ) {
                                return false;

                                if( launchResponse.links === null ) {
                                    return false;
                                }
                            }
                            
                            let data = {
                                capsule: item,
                                launch: launchResponse
                            };

                            await SpaceX.createItem(data);
                        }));

                        await SpaceX.createPagination(result.data);

                    } else {
                        //create error message dom
                        let gridItem = document.createElement('p');
                        gridItem.classList.add('spx-item-message');
                        gridItem.innerText = __( "Nothing Found!", "spacex-craft");

                        // append error message dom
                        gridItems.append(gridItem);
                    }
                },
                ( error ) => {
                    console.log(error);
                }
            );

        },

        /**
         * Fetch API for single data
         *
         * @param args api arguments
         * @returns mixed
         */
        singleFetch: async function (args) {

            let data = {};

            await fetch( {
                path: args.path,
                method: 'GET',
            } ).then(
                async ( result ) => {
                    data = await result.data;
                },
                ( error ) => {
                    console.log(error);
                }
            );

            return data;
        },

        /***
         * Submitting the search form, fetch searched data
         *
         * @param event
         */
        searchFormSubmit: function (event) {
            event.preventDefault();

            //select filter name and value
            let searchInput     = document.querySelector('#spx-search-input');
            let searchFilter    = document.querySelector('#spx-filter-rocket');

            if( searchInput.value.length > 0 && searchFilter.value.length > 0 ) {
                //get filter name & value
                let filterVal = searchInput.value;
                let filterBy = searchFilter.value;

                //searched api path
                SpaceX.path = '/spx/v1/capsules?search=yes&filter=' + filterBy + '&filter-value=' + filterVal;

                //fetch searched data
                SpaceX.fetchData();
            }
        },

        /***
         * Paginate the grid, all the pagination will work here
         *
         * @param event
         */
        paginate: function (event) {
            event.preventDefault();

            //select current page number
            let page = event.target.getAttribute('data-page');

            if( page ) {
                SpaceX.path = '/spx/v1/capsules?page=' + page;
            }

            //fetch paginate data
            SpaceX.fetchData();
        },

        /**
         * Show popup
         *
         * @param event
         */
        showPopup: function (event) {
            event.preventDefault();
            event.stopPropagation();

            let data = event.target.closest('.spx-grid-item').querySelector('.spx-grid-item-data');
                data = JSON.parse(data.value);

            //when capsule and launch data is empty, do nothing
            if( !data.capsule && ! data.launch ) {
                return;
            }

            //add launch image
            let img = document.querySelector('.spx-grid-popup-box img');
                img.setAttribute('src', data.launch.links.patch.small);

            //add capsule serial
            let capsuleName = document.querySelector('.spx-grid-popup-box-content h2');
                capsuleName.innerText = data.capsule.serial;

            //add launch name
            let launchName = document.querySelector('.spx-popup-launch-name-value');
                launchName.innerText = data.launch.name;

            //add capsule status
            let capsuleStatus = document.querySelector('.spx-popup-status-value');
                capsuleStatus.innerText = data.capsule.status;

            //add launch details
            let launchDetails = document.querySelector('.spx-grid-popup-box-content ul li p');
                launchDetails.innerText = data.launch.details;

            //show popup by removing hide class
            let popup = document.querySelector('.spx-grid-popup-wrapper');
                popup.classList.remove('spx-hide');

        },

        /**
         * Close popup
         *
         * @param event
         */
        closePopup: function(event) {
            event.preventDefault();

            let popup = event.target.closest('.spx-grid-popup-wrapper');
                popup.classList.add('spx-hide');

        }
    }

})()