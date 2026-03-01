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

apiClient.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        if (error.response) {
            console.error("Response error: ", error.response);
            var currentPath = window.location.pathname;
            if (error.response.status === 401 && currentPath !== '/login') {
                localStorage.removeItem('token');
                window.location.href = '/landing';
            }
        }
        return Promise.reject(error);
    }
);

export default apiClient;
