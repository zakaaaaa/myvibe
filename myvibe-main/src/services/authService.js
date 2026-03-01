import apiClient from '@/services/axios';


const authService = {
    login(credentials) {
        return apiClient.post('/api/login', credentials, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    register(credentials) {
        return apiClient.post('/api/register', credentials, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    loginGoogle(credentials) {
        return apiClient.post('/api/auth/login/google', credentials, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    forgotPassword(credentials) {
        return apiClient.post('/api/forgot-password', credentials, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    resendVerify(credentials) {
        return apiClient.post('/api/email/resend', credentials, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
    },
    profile() {
        return apiClient.get('/api/user');
    },
    profile_put(data) {
        return apiClient.post('/api/user', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },
    getMBTI() {
        return apiClient.get('/api/mbti');
    },
    getZodiac() {
        return apiClient.get('/api/zodiac');
    },
    getRelationship() {
        return apiClient.get('/api/relationship');
    }

};

export default authService;
