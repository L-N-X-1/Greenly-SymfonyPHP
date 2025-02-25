import './bootstrap.js';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin, listPlugin, timeGridPlugin],
        initialView: 'dayGridMonth',
        events: '/api/events', // Endpoint to fetch events
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        editable: true, // Allow dragging events
        selectable: true, // Allow selecting time slots
        eventClick: function(info) {
            alert('Event: ' + info.event.title);
        }
    });

    calendar.render();
});

import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
