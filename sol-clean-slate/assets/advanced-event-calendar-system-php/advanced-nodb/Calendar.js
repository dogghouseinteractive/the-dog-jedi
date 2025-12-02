'use strict';
class Calendar {

    constructor(options) {
        // Declare the default options
        let defaults = {
            uid: 1,
            container: document.querySelector('.calendar-container'),
            php_file_url: '',
            current_date: new Date().toISOString().substring(0, 10),
            size: 'auto',
            expanded_list: false
        };
        // Declare the calendar options
        this.options = Object.assign(defaults, options);
        // Modal is not currently open
        this.isModalOpen = false;
        // Fetch the calendar
        this.fetchCalendar();
    }

    // Fetch the calendar using AJAX
    fetchCalendar() {
        // Add the loading state
        this.addLoaderIcon();
        // Fetch the calendar
        fetch(this.ajaxUrl, { cache: 'no-store' }).then(response => response.text()).then(data => {
            // Load complete
            // Ouput the response
            let temp = document.createElement('div');
            temp.innerHTML = data;
            this.container.innerHTML = temp.querySelector('.calendar').outerHTML;
            if (temp.querySelector('.calendar-expanded-view')) {
                this.container.innerHTML += temp.querySelector('.calendar-expanded-view').outerHTML;
            }
            // Determine the calendar size
            this.container.querySelector('.calendar').classList.remove('normal', 'mini', 'auto');
            this.container.querySelector('.calendar').classList.add(this.size);
            // Determine the expanded view size
            if (this.container.querySelector('.calendar-expanded-view')) {
                this.container.querySelector('.calendar-expanded-view').classList.remove('normal', 'mini', 'auto');
                this.container.querySelector('.calendar-expanded-view').classList.add(this.size);
            }
            // Trigger the event handlers
            this._eventHandlers();
            // Remove the loading state
            this.removeLoaderIcon();
					
						const rgb2hex = (rgb) => `#${rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/).slice(1).map(n => parseInt(n, 10).toString(16).padStart(2, '0')).join('')}`
			
						setTimeout(function() {
							jQuery('.calendar-expanded-view .event').each(function() {
								var bookingColor = rgb2hex(jQuery(this).find('.day').css('border-right-color'));
								jQuery(this).find('h3.title').css('color', bookingColor);
								console.log(rgb2hex(jQuery(this).find('h3.title').css('color')));
							});
						}, 1000);
        });
    }

    // Determine the AJAX URL
    get ajaxUrl() {
        let url = `${this.phpFileUrl}${this.phpFileUrl.includes('?') ? '&' : '?'}uid=${this.uid}`;
        url += 'current_date' in this.options ? `&current_date=${this.currentDate}` : '';
        url += 'size' in this.options ? `&size=${this.size}` : '';
        url += this.expandedList ? `&expanded_list=${this.expandedList}` : '';
        return url;       
    }

    // Get: Unique ID
    get uid() {
        return this.options.uid;
    }

    // Set: Unique ID
    set uid(value) {
        this.options.uid = value;
    }

    // Get: PHP calendar file URL
    get phpFileUrl() {
        return this.options.php_file_url;
    }

    // Set: PHP calendar file URL
    set phpFileUrl(value) {
        this.options.php_file_url = value;
    }

    // Get: HTML DOM calendar container
    get container() {
        return this.options.container;
    }

    // Set: HTML DOM calendar container
    set container(value) {
        this.options.container = value;
    }

    // Get: current calendar date
    get currentDate() {
        return this.options.current_date;
    }

    // Set: current calendar date
    set currentDate(value) {
        this.options.current_date = value;
    }

    // Get: calendar size (normal|mini)
    get size() {
        return this.options.size;
    }

    // Set: calendar size (normal|mini)
    set size(value) {
        this.options.size = value;
    }

    // Get: expanded list (true|false)
    get expandedList() {
        return this.options.expanded_list;
    }

    // Set: expanded list (true|false)
    set expandedList(value) {
        this.options.expanded_list = value;
    }

    // Function that will open the date select modal
    openDateSelectModal(x, y, currentDate) {
        // If there is already a modal open, return false
        if (this.isModalOpen) {
            return false;
        }
        // Update the isModalOpen var
        this.isModalOpen = true;
        // Update the calendar CSS opacity property
        document.querySelector('.calendar').style.opacity = '.3';
        // Add the date select template modal to the HTML document
        document.body.insertAdjacentHTML('beforeend', `
            <div class="calendar-date-modal">
                <div class="calendar-event-modal-header">
                    <h5>Select Date</h5>
                    <a href="#" class="close"><i class="fa-solid fa-xmark"></i></a>
                </div>
                <div class="calendar-event-modal-content date-select">
                    <h5>Month</h5>
                    <h5>Year</h5>
                    <div class="months"></div>
                    <div class="years"></div>
                </div>
                <div class="calendar-event-modal-footer">
                    <a href="#" class="save">Save</a>
                    <a href="#" class="close">Close</a>
                </div>
            </div>
        `);
        // Select the above modal
        let modalElement = document.querySelector('.calendar-date-modal');
        // Retrieve the modal rect properties
        let rect = modalElement.getBoundingClientRect();
        // Position the modal (center center)
        modalElement.style.top = parseInt(y-(rect.height/2)) + 'px';
        modalElement.style.left = parseInt(x-(rect.width/2)) + 'px';
        // Determine the current month
        let currentMonth = new Date(currentDate).getMonth() + 1;
        // Iterate every month in the year and add the month to the modal
        for (let month = 1; month <= 12; month++) {
            modalElement.querySelector('.months').insertAdjacentHTML('beforeend', `
                <div class="month${month==currentMonth?' active':''}">${month}</div>
            `);
        }
        // Start year; deduct 40 years from the current year
        let startYear = new Date().getFullYear()-40;
        // End year; add 40 years to the current year
        let endYear = new Date().getFullYear()+40;
        // Current year
        let currentYear = new Date(currentDate).getFullYear();
        // Iterate from the start year to the end year and add the year to the modal
        for (let year = startYear; year <= endYear; year++) {
            modalElement.querySelector('.years').insertAdjacentHTML('beforeend', `
                <div class="year${year==currentYear?' active':''}">${year}</div>
            `);
        }
        // Iterate all months in the modal and add the onclick event, which will add the "active" css class to the corresponding month
        modalElement.querySelectorAll('.month').forEach(element => {
            element.onclick = () => {
                modalElement.querySelectorAll('.month').forEach(element => element.classList.remove('active'));
                element.classList.add('active');
            };
        });
        // Iterate all years in the modal and add the onclick event, which will add the "active" css class to the corresponding year
        modalElement.querySelectorAll('.year').forEach(element => {
            element.onclick = () => {
                modalElement.querySelectorAll('.year').forEach(element => element.classList.remove('active'));
                element.classList.add('active');
            };
        });
        // Position the modal scroll bars
        modalElement.querySelector('.month.active').parentNode.scrollTop = modalElement.querySelector('.month.active').offsetTop - 100;
        modalElement.querySelector('.year.active').parentNode.scrollTop = modalElement.querySelector('.year.active').offsetTop - 100;
        // Save the selected month and year
        modalElement.querySelector('.save').onclick = event => {
            event.preventDefault();
            // Update the current date
            this.currentDate = modalElement.querySelector('.year.active').innerHTML + '-' + modalElement.querySelector('.month.active').innerHTML + '-01';
            // Remove the modal
            modalElement.remove();
            // Update the calendar CSS opacity property
            document.querySelector('.calendar').style.opacity = '1';
            // Fetch the calendar
            this.fetchCalendar();
            // Modal is no longer open
            this.isModalOpen = false;
        };
        this.closeEventHandler(modalElement);
    }

    // Function that will open the event list modal
    openEventModal(x, y, startDate, dateLabel, element) {
        console.log(element);
        // If there is already a modal open, return false
        if (this.isModalOpen) {
            return false;
        }
        // Update the isModalOpen var
        this.isModalOpen = true;
        // Update the calendar CSS opacity property
        document.querySelector('.calendar').style.opacity = '.3';
        // Declare the events list
        let eventsList = '<div class="events">';
        let hasEvents = false;
        // Iterate the events array
        element.querySelectorAll('.days .event').forEach(element => {
            if (element.dataset.datestart) {
                eventsList += '<div class="event">';
                eventsList += '<div class="details"><i class="date" style="border-right:3px solid ' + element.dataset.color + '">' + (startDate == element.dataset.datestart.split(' ')[0] ? this.formatAMPM(new Date(element.dataset.datestart)) : 'Ongoing') + '</i> <div class="title">' + element.dataset.title + '</div></div>';
                if (element.dataset.description) {
                    eventsList += '<p class="description">' + element.dataset.description + '</p>';
                }
                eventsList += '</div>';
                hasEvents = true;
            }
				});
        if (!hasEvents) {
            eventsList += '<p>There are no events.</p>'
        }
        eventsList += '</div>';
        // Add the date select template modal to the HTML document
        document.body.insertAdjacentHTML('beforeend', `
            <div class="calendar-event-modal">
                <div class="calendar-event-modal-header">
                    <h5>${dateLabel}</h5>
                    <a href="#" class="close"><i class="fa-solid fa-xmark"></i></a>
                </div>
                <div class="calendar-event-modal-content">
                ${eventsList}
                </div>
                <div class="calendar-event-modal-footer">
                    <a href="#" class="close">Close</a>
                </div>
            </div>
        `);
        // Select the above modal
        let modalElement = document.querySelector('.calendar-event-modal');
        // Retrieve the modal rect properties
        let rect = modalElement.getBoundingClientRect();
        // Position the modal (center center)
        modalElement.style.top = parseInt(y-(rect.height/2)) + 'px';
        modalElement.style.left = parseInt(x-(rect.width/2)) + 'px';  
        // Close button onclick event
        this.closeEventHandler(modalElement);
    }

    closeEventHandler(modalElement) {
        // Close button onclick event
        modalElement.querySelectorAll('.close').forEach(element => element.onclick = event => {
            event.preventDefault();
            // Remove the modal
            modalElement.remove();
            // Update the calendar CSS opacity property
            document.querySelector('.calendar').style.opacity = '1';
            // Modal is no longer open
            this.isModalOpen = false;
        });
    }

    // Format time
    formatAMPM(date) { 
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12;
        minutes = minutes < 10 ? '0'+minutes : minutes;
        return hours + ':' + minutes + ' ' + ampm;
    }

    // Function that will add the loading state
    addLoaderIcon() {
        // If the loading state has already been intialized, return and prevent further execution
        if (document.querySelector('.calendar-loader') || !document.querySelector('.calendar')) {
            return;
        }
        // Update the calendar CSS opacity property
        document.querySelector('.calendar').style.opacity = '.3';
        // Add the loader element to the HTML document
        document.body.insertAdjacentHTML('beforeend', `
            <div class="calendar-loader">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </div>
        `);
        // Select the loader element
        let loaderElement = document.querySelector('.calendar-loader');
        // Retrieve the loader rect properties
        let rect = loaderElement.getBoundingClientRect();
        // Retrieve the calendar rect properties
        let calendarRect = document.querySelector('.calendar').getBoundingClientRect();
        // Position the loader element (center center)
        let x = (calendarRect.width / 2) + calendarRect.x;
        let y = (calendarRect.height / 2) + calendarRect.y;
        loaderElement.style.top = parseInt(y-(rect.height/2)) + 'px';
        loaderElement.style.left = parseInt(x-(rect.width/2)) + 'px';          
    }

    // Function that will remove the loading state
    removeLoaderIcon() {
        if (document.querySelector('.calendar-loader')) {
            document.querySelector('.calendar-loader').remove();
        }
    }

    // Event handlers for all calendar elements
    _eventHandlers() {
        // Retrieve the calendar rect properties
        let rect = document.querySelector('.calendar').getBoundingClientRect();
        let x = (rect.width / 2) + rect.x;
        let y = (rect.height / 2) + rect.y;
        // Calendar month previous button onclick event
        document.querySelector('.calendar .header .prev').onclick = event => {
            event.preventDefault();
            // Update the current date
            this.currentDate = document.querySelector('.calendar .header .prev').dataset.date;
            // Fetch calendar
            this.fetchCalendar();
        };
        // Calendar month next button onclick event
        document.querySelector('.calendar .header .next').onclick = event => {
            event.preventDefault();
            // Update the current date
            this.currentDate = document.querySelector('.calendar .header .next').dataset.date;
            // Fetch calendar
            this.fetchCalendar();
        };
        // Calendar month next button onclick event
        document.querySelector('.calendar .header .today').onclick = event => {
            event.preventDefault();
            // Update the current date
            this.currentDate = new Date().toISOString().substring(0, 10);
            // Fetch calendar
            this.fetchCalendar();
        };
        // Refresh the calendar
        document.querySelector('.calendar .header .refresh').onclick = event => {
            event.preventDefault();
            // Fetch calendar
            this.fetchCalendar();
        };
        // Calendar month current button onclick event
        document.querySelector('.calendar .header .current').onclick = event => {
            event.preventDefault();
            // Open the date select modal
            this.openDateSelectModal(x, y, this.currentDate);
        };
        // Iterate all the day elements, exluding the ignored elements
        document.querySelectorAll('.calendar .day_num:not(.ignore)').forEach(element => {
            // Add onclick event
            element.onclick = () => {
                // If there is already a modal open, return and prevent further execution
                if (this.isModalOpen) {
                    return;
                }
                // Add the loading state
                this.addLoaderIcon();
                // Retrieve all events for the selected day
                fetch(this.ajaxUrl + '&events_list=' + element.dataset.date, { cache: 'no-store' }).then(response => response.text()).then(() => {
                    // Remove the loading state element
                    this.removeLoaderIcon();
                    // Open the events list modal
                    this.openEventModal(x, y, element.dataset.date, element.dataset.label, element);
                });
            };
        });
    }

}