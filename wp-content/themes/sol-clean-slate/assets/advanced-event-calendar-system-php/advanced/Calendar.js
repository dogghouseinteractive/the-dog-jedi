"use strict";
class Calendar {

    constructor(options) {
        // Declare the default options
        let defaults = {
            uid: 1,
            container: document.querySelector('.calendar-container'),
            php_file_url: 'Calendar.php',
            current_date: new Date().toISOString().substring(0, 10),
            size: 'normal'
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
            this.container.innerHTML = data;
            // Trigger the event handlers
            this._eventHandlers();
            // Remove the loading state
            this.removeLoaderIcon();
        });
    }

    // Determine the AJAX URL
    get ajaxUrl() {
        let url = `${this.phpFileUrl}?uid=${this.uid}`;
        url += 'current_date' in this.options ? `&current_date=${this.currentDate}` : '';
        url += 'size' in this.options ? `&size=${this.size}` : '';
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
                <h5>Month</h5>
                <h5>Year</h5>
                <div class="months"></div>
                <div class="years"></div>
                <a href="#" class="save">Save</a>
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
        // Start year; deduct 100 years from the current year
        let startYear = new Date().getFullYear()-100;
        // End year; add 50 years to the current year
        let endYear = new Date().getFullYear()+50;
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
        modalElement.querySelector('.year.active').scrollIntoView();
        modalElement.querySelector('.month.active').scrollIntoView();
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
    }

    // Function that will open the event list modal
    openEventModal(x, y, startDate, endDate, eventsList) {
        // If there is already a modal open, return false
        if (this.isModalOpen) {
            return false;
        }
        // Update the isModalOpen var
        this.isModalOpen = true;
        // Update the calendar CSS opacity property
        document.querySelector('.calendar').style.opacity = '.3';
        // Declare the modal title, which will consist of the date
        let dateTitle = new Date(startDate);
        dateTitle = dateTitle.getDate() + ' ' + dateTitle.toLocaleString('default', { month: 'long' }) + ' ' + dateTitle.getFullYear();
        // Add the date select template modal to the HTML document
        document.body.insertAdjacentHTML('beforeend', `
            <div class="calendar-event-modal">
                <h5>${dateTitle}</h5>
                ${eventsList}
                <a href="#" class="add_event">Add Event</a>
                <a href="#" class="close">Close</a>
            </div>
        `);
        // Select the above modal
        let modalElement = document.querySelector('.calendar-event-modal');
        // Retrieve the modal rect properties
        let rect = modalElement.getBoundingClientRect();
        // Position the modal (center center)
        modalElement.style.top = parseInt(y-(rect.height/2)) + 'px';
        modalElement.style.left = parseInt(x-(rect.width/2)) + 'px';  
        // Retrieve the calendar rect properties
        let calendar_rect = document.querySelector('.calendar').getBoundingClientRect();
        let calendar_x = (calendar_rect.width / 2) + calendar_rect.x;
        let calendar_y = (calendar_rect.height / 2) + calendar_rect.y;
        // Iterate all events   
        modalElement.querySelectorAll('.events .event').forEach(element => {
            // Edit button onclick event
            element.querySelector(".edit").onclick = event => {
                event.preventDefault();
                // Remove the current modal
                modalElement.remove();
                // Modal is no longer open
                this.isModalOpen = false;
                // Edit object
                let editObj = {
                    id: element.dataset.id,
                    title: element.dataset.title,
                    datestart: element.dataset.start,
                    dateend: element.dataset.end,
                    color: element.dataset.color,
                    description: element.querySelector(".description").innerHTML
                };
                // Open the add event modal
                this.openAddEventModal(calendar_x, calendar_y, startDate, endDate, editObj);               
            };
            // Delete button onclick event
            element.querySelector(".delete").onclick = event => {
                event.preventDefault();
                // Remove the current modal
                modalElement.remove();
                // Modal is no longer open
                this.isModalOpen = false;
                // Open the delete event modal
                this.openDeleteEventModal(calendar_x, calendar_y, element.dataset.id);               
            };
        });
        // Add event button onclick event
        modalElement.querySelector('.add_event').onclick = event => {
            event.preventDefault();
            // Remove the current modal
            modalElement.remove();
            // Modal is no longer open
            this.isModalOpen = false;
            // Open the add event modal
            this.openAddEventModal(calendar_x, calendar_y, startDate, endDate);
        };  
        // Close button onclick event
        modalElement.querySelector('.close').onclick = event => {
            event.preventDefault();
            // Remove the modal
            modalElement.remove();
            // Update the calendar CSS opacity property
            document.querySelector('.calendar').style.opacity = '1';
            // Modal is no longer open
            this.isModalOpen = false;
        };
    }

    // Function that will open the add event modal 
    openAddEventModal(x, y, startDate, endDate, edit) {
        // If there is already a modal open, return false
        if (this.isModalOpen) {
            return false;
        }
        // Update the isModalOpen var
        this.isModalOpen = true;
        // Update the calendar CSS opacity property
        document.querySelector('.calendar').style.opacity = '.3';
        // Create the date variables
        let startDateStr, endDateStr, t;
        // If editing an event
        if (edit) {
            // Update the start date string
            t = edit.datestart.split(/[- :]/);
            startDateStr = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5])).toISOString();
        } else {
            startDateStr = new Date(startDate).toISOString();
        }
        if (edit) {
            // Update the end date string
            t = edit.dateend.split(/[- :]/);
            endDateStr = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5])).toISOString();           
        } else {
            endDateStr = new Date(endDate).toISOString();
        }
        startDateStr = startDateStr.substring(0,startDateStr.length-1);
        endDateStr = endDateStr.substring(0,endDateStr.length-1);
        // Add the add event modal template to the HTML document
        document.body.insertAdjacentHTML('beforeend', `
            <div class="calendar-add-event-modal">
                <form>
                    <label for="title">Title</label>
                    <input id="title" name="title" type="text" placeholder="Title" value="${edit ? edit.title : ''}">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Description">${edit ? edit.description : ''}</textarea>
                    <label for="startdate">Start Date</label>
                    <input id="startdate" name="startdate" type="datetime-local" value="${startDateStr}">
                    <label for="enddate">End Date</label>
                    <input id="enddate" name="enddate" type="datetime-local" value="${endDateStr}">
                    <label for="color">Color</label>
                    <input id="color" name="color" type="color" placeholder="Color" value="${edit ? edit.color : '#5373ae'}" list="presetColors">
                    <datalist id="presetColors">
                        <option>#5373ae</option>
                        <option>#ae5353</option>
                        <option>#9153ae</option>
                        <option>#53ae6d</option>
                        <option>#ae8653</option>
                    </datalist>
                    <input type="hidden" name="eventid" value="${edit ? edit.id : ''}">                  
                    <span id="msg"></span>                                    
                </form>
                <a href="#" class="add_event">${edit ? 'Update' : 'Add'} Event</a>
                <a href="#" class="close">Cancel</a>
            </div>
        `);
        // Select the modal element
        let modalElement = document.querySelector('.calendar-add-event-modal');
        // Retrieve the modal rect properties
        let rect = modalElement.getBoundingClientRect();
        // Position the modal (center center)
        modalElement.style.top = parseInt(y-(rect.height/2)) + 'px';
        modalElement.style.left = parseInt(x-(rect.width/2)) + 'px';   
        // Add event button onclick event
        modalElement.querySelector('.add_event').onclick = event => {
            event.preventDefault();
            // Disable the button
            modalElement.querySelector('.add_event').disabled = true;
            // Use AJAX to add a new event to the calendar
            fetch(this.ajaxUrl, { cache: 'no-store', method: 'POST', body: new FormData(modalElement.querySelector('form')) }).then(response => response.text()).then(data => {
                // Check if the response us "success"
                if (data.includes('success')) {
                    // Remove the modal
                    modalElement.remove();
                    // Fetch the calendar
                    this.fetchCalendar();
                    // Modal is no longer open
                    this.isModalOpen = false;  
                } else {
                    // Something went wrong... output the errors
                    modalElement.querySelector('#msg').innerHTML = data;
                    // Enable the button
                    modalElement.querySelector('.add_event').disabled = false;
                }
            });
        };
        // Close modal onclick event
        modalElement.querySelector('.close').onclick = event => {
            event.preventDefault();
            // Remove the current modal
            modalElement.remove();
            // Update the calendar CSS opacity property
            document.querySelector('.calendar').style.opacity = '1';
            // Modal is no longer open
            this.isModalOpen = false;
        };      
    }

    // Function that will open the delete event modal
    openDeleteEventModal(x, y, id) {
        // If there is already a modal open, return false
        if (this.isModalOpen) {
            return false;
        }
        // Update the isModalOpen var
        this.isModalOpen = true;
        // Update the calendar CSS opacity property
        document.querySelector('.calendar').style.opacity = '.3';
        // Add the delete event modal template to the HTML document
        document.body.insertAdjacentHTML('beforeend', `
            <div class="calendar-delete-event-modal">
                <p>Are you sure you want to delete this event?</p>
                <a href="#" class="delete_event">Delete</a>
                <a href="#" class="close">Cancel</a>
            </div>
        `);
        // Select the modal element
        let modalElement = document.querySelector('.calendar-delete-event-modal');
        // Retrieve the modal rect properties
        let rect = modalElement.getBoundingClientRect();
        // Position the modal (center center)
        modalElement.style.top = parseInt(y-(rect.height/2)) + 'px';
        modalElement.style.left = parseInt(x-(rect.width/2)) + 'px';   
        // Delete event button onclick event
        modalElement.querySelector('.delete_event').onclick = event => {
            event.preventDefault();
            // Disable the button
            modalElement.querySelector('.delete_event').disabled = true;
            // Use AJAX to delete the event
            fetch(this.ajaxUrl + "&delete_event=" + id, { cache: 'no-store' }).then(response => response.text()).then(data => {
                // Remove the modal
                modalElement.remove();
                // Fetch the calendar
                this.fetchCalendar();
                // Modal is no longer open
                this.isModalOpen = false;  
            });
        };
        // Close button onclick event
        modalElement.querySelector('.close').onclick = event => {
            event.preventDefault();
            // Remove the modal
            modalElement.remove();
            // Update the calendar CSS opacity property
            document.querySelector('.calendar').style.opacity = '1';
            // Modal is no longer open
            this.isModalOpen = false;
        };      
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
                fetch(this.ajaxUrl + "&events_list=" + element.dataset.date, { cache: 'no-store' }).then(response => response.text()).then(data => {
                    // Remove the loading state element
                    this.removeLoaderIcon();
                    // Open the events list modal
                    this.openEventModal(x, y, element.dataset.date, element.dataset.date, data);
                });
            };
        });
    }

}