import './bootstrap';


document.addEventListener('DOMContentLoaded', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                console.log('User location:', position.coords.latitude, position.coords.longitude);

            },
            function (error) {
                console.error('Error getting location:', error);
            }
        );
    } else {
        console.log('Geolocation is not supported by this browser.');
    }
});