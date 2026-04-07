document.addEventListener('DOMContentLoaded', function () {
    setupPricePreview();
    setupLiveSlots();
});

function setupPricePreview() {
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const priceOutput = document.getElementById('price-output');

    if (!startInput || !endInput || !priceOutput) {
        return;
    }

    const updatePrice = function () {
        if (!startInput.value || !endInput.value) {
            priceOutput.textContent = 'Rs. 0.00';
            return;
        }

        const start = new Date(startInput.value);
        const end = new Date(endInput.value);
        const diffHours = (end - start) / (1000 * 60 * 60);

        if (diffHours > 0) {
            priceOutput.textContent = 'Rs. ' + (diffHours * 20).toFixed(2);
        } else {
            priceOutput.textContent = 'Rs. 0.00';
        }
    };

    startInput.addEventListener('change', updatePrice);
    endInput.addEventListener('change', updatePrice);
}

function setupLiveSlots() {
    const slotGrid = document.getElementById('slot-grid');

    if (!slotGrid || !slotGrid.dataset.liveSlotsUrl) {
        return;
    }

    const renderSlots = function (slots) {
        slotGrid.innerHTML = '';

        slots.forEach(function (slot) {
            const column = document.createElement('div');
            column.className = 'col-md-6 col-xl-4';
            column.innerHTML =
                '<div class="card slot-card border-0 shadow-sm h-100">' +
                    '<div class="card-body">' +
                        '<div class="d-flex justify-content-between align-items-start mb-3">' +
                            '<div>' +
                                '<h3 class="h5 mb-1">' + escapeHtml(slot.slot_number) + '</h3>' +
                                '<p class="text-muted mb-0">Parking Slot</p>' +
                            '</div>' +
                            '<span class="badge text-bg-' + slot.status_class + '">' + escapeHtml(slot.status_label) + '</span>' +
                        '</div>' +
                        '<p class="small text-muted">Rate: Rs. 20/hour</p>' +
                        '<a href="/smart-parking/user/book_slot.php?id=' + slot.id + '" class="btn btn-primary w-100 ' + (slot.is_bookable ? '' : 'disabled') + '">' +
                            'Book This Slot' +
                        '</a>' +
                    '</div>' +
                '</div>';
            slotGrid.appendChild(column);
        });
    };

    const fetchSlots = function () {
        fetch(slotGrid.dataset.liveSlotsUrl)
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.slots) {
                    renderSlots(data.slots);
                }
            })
            .catch(function () {
            });
    };

    setInterval(fetchSlots, 30000);
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value;
    return div.innerHTML;
}
