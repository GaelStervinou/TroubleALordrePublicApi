import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getUserRates = async (userId, page = 1) => {
    try {
        const response = await http.get(`/users/${userId}/rates?page=${page}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getUserServicesRates = async (userId, page = 1) => {
    try {
        const response = await http.get(`/users/${userId}/services/rates?page=${page}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}