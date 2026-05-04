import axios from 'axios';

const apiClient = axios.create({
    baseURL: process.env.VUE_APP_API_URL,
    withCredentials: false,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
});

apiClient.interceptors.request.use(
    config => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

/**
 * Cek apakah pathname saat ini adalah halaman public (boleh diakses guest).
 * Public routes: /:username, /:username/:slug, /:username/other/:slug,
 *                /:username/more/:id, /:username/more-other/:id
 *
 * Halaman yang BUKAN public (auth pages, dashboard, dll) tetap redirect ke /landing
 * saat dapat 401, sesuai behavior lama.
 */
const isPublicProfileRoute = (pathname) => {
    // Halaman yang JELAS bukan public profile (whitelist halaman app/auth)
    const nonProfilePaths = [
        '/',
        '/landing',
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/resend-verify',
        '/fill-vibe',
        '/welcome',
        '/dashboard',
        '/add',
        '/add-others',
        '/about',
    ];

    if (nonProfilePaths.includes(pathname)) return false;

    // Path yang start dengan prefix berikut adalah halaman app, bukan profile public
    const nonProfilePrefixes = [
        '/setting/',
        '/message/',
        '/more/',
        '/more-other/',
    ];

    if (nonProfilePrefixes.some(prefix => pathname.startsWith(prefix))) return false;

    // Sisanya yang match pattern /:username, /:username/:slug, dst — anggap public
    // Pattern: minimal 1 segment setelah root, max 3 segment (/:user/:type/:slug)
    const segments = pathname.split('/').filter(Boolean);
    return segments.length >= 1 && segments.length <= 3;
};

apiClient.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        if (error.response) {
            console.error("Response error: ", error.response);
            const currentPath = window.location.pathname;
            const isPublicRoute = isPublicProfileRoute(currentPath);

            // Hanya redirect kalau 401 DAN bukan di public profile route DAN bukan di /login
            if (
                error.response.status === 401 &&
                currentPath !== '/login' &&
                !isPublicRoute
            ) {
                localStorage.removeItem('token');
                window.location.href = '/landing';
            }
        }
        return Promise.reject(error);
    }
);

export default apiClient;