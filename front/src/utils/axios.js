import axios from 'axios';
import { API_LOGIN_ROUTE } from './apiRoutes.js';
import { getAccessToken, getRefreshToken, storeCredentials, eraseCredentials, eraseAccessToken} from './localStorage.js';

const HTTP_UNAUTHORIZED_CODE = 401;

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/ld+json',
        'Accept': 'application/ld+json',
    },
});

const SetUpInstance = () => {
    instance.interceptors.request.use(
        config => {
            const token = getAccessToken();
            if (token) {
                config.headers['Authorization'] = 'Bearer ' + token;
            }
            return config;
        },
        error => {
            Promise.reject(error)
        }
    );

    instance.interceptors.response.use(
        (response) => {
            return response;
        },
        async (error) => {
            if (
                error.response.status === HTTP_UNAUTHORIZED_CODE
                && !error.config._retry
                && error.config.url !== API_LOGIN_ROUTE
            ) {
                error.config._retry = true;
                try {
                    eraseAccessToken();
                    error.config.headers['Authorization'] = null;
                    const refreshedToken = await refreshAccessToken();
                    error.config.headers['Authorization'] = 'Bearer ' + refreshedToken;
                    return instance(error.config);
                } catch (e) {
                    eraseCredentials();
                    return Promise.reject(e);
                }
            }
            return Promise.reject(error);
        }
    );

    const refreshAccessToken = async () => {
        const refreshToken = getRefreshToken();

        if (!refreshToken) {
            eraseCredentials();
            throw new Error('No refresh token available');
        }

        try {
            const response = await instance.post('/token/refresh', {
                refresh_token: refreshToken,
            });
            const newAccessToken = response.data;

            storeCredentials(newAccessToken.token, newAccessToken.refresh_token);

            return newAccessToken;
        } catch (error) {
            eraseCredentials();
            throw error;
        }
    };

    return instance;
}

export default SetUpInstance;
