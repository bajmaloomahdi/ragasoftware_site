import axios from 'axios';

/**
 * Axios instance for the few admin interactions that need raw JSON rather
 * than an Inertia visit (MediaPicker browsing, async selects). CSRF + the
 * X-Requested-With header are attached so Laravel treats it as AJAX.
 */
const http = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
    withCredentials: true,
});

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    http.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

export default http;
