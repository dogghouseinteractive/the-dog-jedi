<?php
class Calendar {

    // Class variables
    private $active_year, $active_month, $active_day;
    private $events = [];
    private $pdo;
    private $uid;
    private $size;

    // Initialize the class variables
    public function __construct($date = null, $uid = 1, $size = 'normal') {
        // The specified year
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        // The specified month
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        // The specified day
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
        // Unique ID
        $this->uid = $uid;
        // Calendar size (normal|mini)
        $this->size = $size;
    }

    // Connect to database function using the PDO interface
    public function connect_to_database($dbhost, $dbuser, $dbpass, $dbname, $dbcharset = 'utf8') {
        try {
            // Connect to database using the PDO interface
            $this->pdo = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname . ';charset=' . $dbcharset, $dbuser, $dbpass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // If there is an error with the connection, stop the script and output the error.
            exit('Failed to connect to database!');
        }
        // update event list
        $this->update_events();      
    }

    // Retrieve all the events from the database based on the "uid" column (Unique ID)
    public function update_events() {
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE uid = ?');
        $stmt->execute([ $this->uid ]);
        $this->events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add event to the database and update the "events" array
    public function add_event($title, $description, $datestart, $dateend, $color = '') {
        // Insert into database
        $stmt = $this->pdo->prepare('INSERT INTO events (title, description, color, datestart, dateend, uid) VALUES (?,?,?,?,?,?)');
        $stmt->execute([ $title, $description, $color, $datestart, $dateend, $this->uid ]);
        // Retrieve the ID
        $id = $this->pdo->lastInsertId();
        // Add to the list of events
        $this->events[] = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'color' => $color,
            'datestart' => $datestart,
            'dateend' => $dateend,
            'uid' => $this->uid
        ];
        return true;    
    }

    // Update event function
    public function update_event($id, $title, $description, $datestart, $dateend, $color = '') {
        // Check if the event exists
        $event_exists = false;
        // Iterate all the events
        foreach ($this->events as &$event) {
            // Compare event ID with the specified ID
            if ($event['id'] == $id) {
                // Event exists... Update the event
                $event_exists = true;
                $event['title'] = $title;
                $event['description'] = $description;
                $event['datestart'] = $datestart;
                $event['dateend'] = $dateend;
                $event['color'] = $color;
            }
        }
        if (!$event_exists) {
            // Event doesn't exists! Return false
            return false;
        }
        // Update the event in the database
        $stmt = $this->pdo->prepare('UPDATE events SET title = ?, description = ?, color = ?, datestart = ?, dateend = ? WHERE id = ?');
        $stmt->execute([ $title, $description, $color, $datestart, $dateend, $id ]);
        return true;    
    }

    // Delete event function
    public function delete_event($id) {
        // Check if the event exists
        $event_exists = false;
        // Iterate all the events
        foreach ($this->events as &$event) {
            if ($event['id'] == $id) {
                // Event exists, delete the event
                $event_exists = true;
                unset($event);
            }
        }
        if (!$event_exists) {
            // Event doesn't exists! Return false
            return false;
        }
        // Delete the event from the database
        $stmt = $this->pdo->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([ $id ]);
        return true;    
    }

    // Function that returns all events by a specified date
    public function list_events_by_date($date) {
        // New events list
        $events_list = [];
        // Iterate all the events
        foreach ($this->events as $event) {
            // Determine the event date
            $event_date = date('y-m-d', strtotime($date));
            // Compare the event "dateend" and "dateend" with the date above
            if ($event_date >= date('y-m-d', strtotime($event['datestart'])) && $event_date <= date('y-m-d', strtotime($event['dateend']))) {
                // Add event to new array
                $events_list[] = $event;
            }
        }
        // Sort the new array by the "datestart" column
        array_multisort(array_column($events_list, 'datestart'), SORT_ASC, $events_list);
        // Return array
        return $events_list;
    }

    // Function that returns all the events by a specified date in HTML format
    public function list_events_by_date_html($date) {
        // Retrieve events array
        $events_list = $this->list_events_by_date($date);
        // HTML variable
        $html = '';
        // Make events array is not empty
        if ($events_list) {
            // Add the events container
            $html .= '<div class="events">';
            // Iterate the events array
            foreach ($events_list as $event) {
                // Determine the specified date
                $event_date = date('y-m-d', strtotime($date));
                // Event template
                $html .= '<div class="event" data-id="' . $event['id'] . '" data-title="' . $event['title'] . '" data-start="' . $event['datestart'] . '" data-end="' . $event['dateend'] . '" data-color="' . $event['color'] . '">';
                $html .= '<h5><i class="date">' . ($event_date == date('y-m-d', strtotime($event['datestart'])) ? date('H:ia', strtotime($event['datestart'])) : 'Ongoing') . '</i> &mdash; ' . htmlspecialchars($event['title'], ENT_QUOTES) . ' <i class="edit fas fa-pen fa-xs" title="Edit Event"></i> <i class="delete fas fa-trash fa-xs" title="Delete Event"></i></h5>';
                $html .= '<p class="description">' . htmlspecialchars($event['description'], ENT_QUOTES) . '</p>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        // Return the events HTML template
        return $html;
    }

    // Determine event position
    private function determine_event_pos($event, $pos = 0) {
        // If the event position is already declared, return the existing position
        if (isset($event['pos'])) return $event['pos'];
        // Iterate the events array
        foreach ($this->events as $e) {
            // Determine the event dates
            $event_date_start = date('y-m-d', strtotime($e['datestart']));
            $event_date_end = date('y-m-d', strtotime($e['dateend']));
            $event2_date_start = date('y-m-d', strtotime($event['datestart']));
            $event2_date_end = date('y-m-d', strtotime($event['dateend']));
            // Compare event dates
            if ($event2_date_start >= $event_date_start && $event2_date_start <= $event_date_end) {
                // Does the event position already exist?
                if (isset($e['pos']) && $e['pos'] == $pos) {
                    // If so, increment the position
                    return $this->determine_event_pos($event, $pos+1);
                }
            }
        }
        // Return the new position
        return $pos;
    }

    // Calendar template
    public function __toString() {
        // Determine the number of days in the active month based on the active_* variables
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        // Determine the number of days last month
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        // Do not modify the $days variable as it is used to determine the first day of the week
        // $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $days = [0 => 'Mon', 1 => 'Tue', 2 => 'Wed', 3 => 'Thu', 4 => 'Fri', 5 => 'Sat', 6 => 'Sun'];
        // If you need to translate the day names, you can change the values below and not the values in the above array.
        $translated_days = [0 => 'Mon', 1 => 'Tue', 2 => 'Wed', 3 => 'Thu', 4 => 'Fri', 5 => 'Sat', 6 => 'Sun'];
        if ($this->size == 'mini') {
            $translated_days = [0 => 'M', 1 => 'T', 2 => 'W', 3 => 'T', 4 => 'F', 5 => 'S', 6 => 'S'];
        }
        // Determine the first day of the week
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);
        // Template code
        $html = '<div class="calendar ' . $this->size . '">';
        $html .= '<div class="header">';
        $html .= '<div class="month-year">';
        $html .= '<a href="#" class="prev" data-date="' . date('Y-m-01', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day . '-1 month')) . '"><i class="fas fa-angle-double-left"></i></a>';
        $html .= '<a href="#" class="current">' . date('F Y', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day)) . '</a>';
        $html .= '<a href="#" class="next" data-date="' . date('Y-m-01', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day . '+1 month')) . '"><i class="fas fa-angle-double-right"></i></a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="days">';
        // Populate the translated days
        foreach ($translated_days as $day) {
            $html .= '
                <div class="day_name">
                    ' . $day . '
                </div>
            ';
        }
        // Add the ignore containers to the start of the calendar
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="day_num ignore">
                    ' . ($num_days_last_month-$i+1) . '
                </div>
            ';
        }
        // Iterate the number of days
        for ($i = 1; $i <= $num_days; $i++) {
            // Set the selected day
            $selected = '';
            if ($i == $this->active_day) {
                $selected = ' selected';
            }
            // Day container
            $html .= '<div class="day_num' . $selected . '" data-date="' . date('Y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i)) . '">';
            // Day number
            $html .= '<span>' . $i . '</span>';
            // All the below event templates will appear in the following array; based on the event position
            $event_template_array = [];
            // Does the day have events
            $has_events = false;
            // The max event position for the current day
            $max_pos = 0;
            // Iterate all the events
            foreach ($this->events as &$event) {
                // Determine the event dates
                $event_start_date = new DateTime($event['datestart']);
                $event_start_date->setTime(0, 0, 0);
                $event_end_date = new DateTime($event['dateend']);
                $event_end_date->setTime(0, 0, 0);
                // The number of days the event lasts
                $event_num_days = $event_start_date->diff($event_end_date)->format("%r%a");
                // Iterate the number of event days
                for ($d = 0; $d <= $event_num_days; $d++) {
                    // Determine the active date
                    $active_date = date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day'));
                    // Event start date
                    $event_date = date('y-m-d', strtotime($event['datestart']));
                    // Compare dates
                    if ($active_date == $event_date) {
                        // Determine the event position
                        $event['pos'] = $this->determine_event_pos($event);
                        // The current CSS class
                        $current = '';
                        if ($d == 0 && $event_num_days > 0) {
                            $current = ' event-start';
                        }
                        if ($d > 0 && $d < $event_num_days) {
                            $current = ' event-ongoing';
                        }
                        if ($d > 0 && $event_num_days == $d) {
                            $current = ' event-end';
                        }
                        // The event template                      
                        $event_template_array[$event['pos']] = '<div data-pos="' . $event['pos'] . '" class="event' . $current . '"' . ($event['color'] ? ' style="order:' . $event['pos'] . ';background-color:' . htmlspecialchars($event['color'], ENT_QUOTES) . '"' : '') . '>';
                        $event_template_array[$event['pos']] .= $this->size != 'mini' ? htmlspecialchars($event['title'], ENT_QUOTES) : '';
                        $event_template_array[$event['pos']] .= '</div>';
                        // Event exists, so update the variable
                        $has_events = true;
                        // Update the maximum position variable
                        $max_pos = $event['pos'] > $max_pos ? $event['pos'] : $max_pos;
                    }
                }
            }
            // Check if the day has events
            if ($has_events) { 
                // Add all event templates to the main template
                for ($p = 0; $p <= $max_pos; $p++) {
                    if (isset($event_template_array[$p])) {
                        $html .= $event_template_array[$p];
                    } else {
                        $html .= '<div class="event" style="order:' . $p . '"></div>';
                    }
               }
            }
            $html .= '</div>';
        }
        // Add the ignore containers to the end of the calendar
        for ($i = 1; $i <= (42-$num_days-max($first_day_of_week, 0)); $i++) {
            $html .= '
                <div class="day_num ignore">
                    ' . $i . '
                </div>
            ';
        }
        $html .= '</div>';
        $html .= '</div>';
        // Return the template code
        return $html;
    }

}
?>